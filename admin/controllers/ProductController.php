<?php
/**
 * ===============================================
 * کنترلر مدیریت محصولات در پنل ادمین
 * CRUD کامل + آپلود چند تصویر برای هر محصول
 * ===============================================
 */

class ProductController
{
    /**
     * لیست همه محصولات
     */
    public function list(): void
    {
        $q          = input('q', '');
        $categoryId = (int)input('category', '0');

        $products   = Product::allForAdmin($q, $categoryId);
        $categories = Category::all(false);

        $pageTitle = 'مدیریت محصولات';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/products.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * فرم افزودن / ویرایش محصول
     */
    public function edit(): void
    {
        $id = (int)input('id', '0');

        // در حالت ویرایش، محصول خوانده می‌شود؛ در حالت افزودن مقادیر خالی
        $product = $id ? Product::findAny($id) : null;
        if ($id && !$product) {
            set_flash('danger', 'محصول یافت نشد.');
            redirect(admin_url('products'));
        }

        $images     = $id ? Product::images($id) : [];
        $categories = Category::all(false);
        $brands     = Brand::all(false);
        $specs      = [];

        // تبدیل مشخصات JSON به فرم قابل ویرایش (سطرهایی با فرمت «عنوان|مقدار»)
        if ($product && !empty($product['specifications'])) {
            $decoded = json_decode($product['specifications'], true) ?: [];
            foreach ($decoded as $key => $value) {
                $specs[] = $key . '|' . $value;
            }
        }

        $pageTitle = $product ? 'ویرایش محصول' : 'افزودن محصول';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/product_form.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * ذخیره محصول (افزودن یا ویرایش) + آپلود تصاویر
     */
    public function save(): void
    {
        csrf_check_or_die();

        $id = (int)($_POST['id'] ?? 0);

        // اعتبارسنجی ورودی‌ها
        $errors = [];
        $title  = trim($_POST['title'] ?? '');
        $price  = (int)en_num($_POST['price'] ?? '0');
        $stock  = (int)en_num($_POST['stock'] ?? '0');
        $discount = max(0, min(100, (int)en_num($_POST['discount_percent'] ?? '0')));
        $categoryId = (int)($_POST['category_id'] ?? 0);

        if (mb_strlen($title) < 3) {
            $errors[] = 'عنوان محصول باید حداقل ۳ حرف باشد.';
        }
        if ($price < 1000) {
            $errors[] = 'قیمت محصول را به تومان وارد کنید.';
        }
        if ($categoryId < 1) {
            $errors[] = 'دسته‌بندی محصول را انتخاب کنید.';
        }
        if ($errors) {
            foreach ($errors as $error) {
                set_flash('danger', $error);
            }
            redirect($id ? admin_url('product-edit', ['id' => $id]) : admin_url('product-edit'));
        }

        // ساخت نامک یکتا از عنوان
        $slug = slugify($_POST['slug'] ?? $title);
        $db = Database::getInstance();
        $checkSlug = $db->fetch('SELECT id FROM products WHERE slug = ? AND id != ?', [$slug, $id]);
        if ($checkSlug) {
            $slug = $slug . '-' . time();
        }

        // جمع‌آوری داده‌های محصول
        $data = [
            'title'             => $title,
            'slug'              => $slug,
            'category_id'       => $categoryId,
            'brand_id'          => (int)($_POST['brand_id'] ?? 0) ?: null,
            'price'             => $price,
            'discount_percent'  => $discount,
            'stock'             => $stock,
            'short_description' => trim($_POST['short_description'] ?? ''),
            'description'       => trim($_POST['description'] ?? ''),
            'specifications'    => $this->parseSpecs($_POST['specs'] ?? []),
            'is_new'            => !empty($_POST['is_new']) ? 1 : 0,
            'is_special'        => !empty($_POST['is_special']) ? 1 : 0,
            'status'            => !empty($_POST['status']) ? 1 : 0,
        ];

        // ذخیره محصول
        if ($id) {
            Product::update($id, $data);
            $productId = $id;
            $message = 'محصول با موفقیت ویرایش شد.';
        } else {
            $productId = Product::create($data);
            $message = 'محصول جدید با موفقیت اضافه شد.';
        }

        // آپلود تصاویر جدید (چندتایی)
        if (!empty($_FILES['images']['name'][0])) {
            $this->uploadImages($productId, $_FILES['images']);
        }

        set_flash('success', $message);
        redirect(admin_url('product-edit', ['id' => $productId]));
    }

    /**
     * حذف محصول
     */
    public function delete(): void
    {
        csrf_check_or_die();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Product::delete($id);
            set_flash('success', 'محصول حذف شد.');
        }
        redirect(admin_url('products'));
    }

    /**
     * حذف یک تصویر از گالری محصول
     */
    public function deleteImage(): void
    {
        csrf_check_or_die();
        $imageId   = (int)($_POST['image_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        Product::deleteImage($imageId);
        set_flash('success', 'تصویر حذف شد.');
        redirect(admin_url('product-edit', ['id' => $productId]));
    }

    /**
     * پردازش آپلود چند تصویر با اعتبارسنجی امنیتی
     */
    private function uploadImages(int $productId, array $files): void
    {
        $count = count($files['name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        for ($i = 0; $i < $count; $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpPath  = $files['tmp_name'][$i];
            $fileSize = (int)$files['size'][$i];

            // بررسی نوع واقعی فایل (نه فقط پسوند)
            $mime = mime_content_type($tmpPath) ?: '';
            if (!in_array($mime, $allowedTypes, true)) {
                set_flash('danger', 'فرمت فایل «' . e($files['name'][$i]) . '» مجاز نیست. فقط JPG، PNG، WebP و GIF.');
                continue;
            }
            // حداکثر حجم ۵ مگابایت
            if ($fileSize > 5 * 1024 * 1024) {
                set_flash('danger', 'حجم فایل «' . e($files['name'][$i]) . '» بیش از ۵ مگابایت است.');
                continue;
            }

            // ساخت نام یکتا و انتقال به پوشه محصولات
            $ext      = match ($mime) {
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
                default      => 'jpg',
            };
            $fileName = 'product-' . $productId . '-' . time() . '-' . ($i + 1) . '.' . $ext;
            $target   = UPLOAD_PATH_PRODUCTS . '/' . $fileName;

            if (move_uploaded_file($tmpPath, $target)) {
                Product::addImage($productId, 'assets/images/products/' . $fileName);
            }
        }
    }

    /**
     * تبدیل سطرهای مشخصات فرم (فرمت «عنوان|مقدار») به JSON
     */
    private function parseSpecs(array $rows): string
    {
        $specs = [];
        foreach ($rows as $row) {
            $row = trim((string)$row);
            if ($row === '' || !str_contains($row, '|')) {
                continue;
            }
            [$key, $value] = array_pad(explode('|', $row, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if ($key !== '' && $value !== '') {
                $specs[$key] = $value;
            }
        }
        return $specs ? json_encode($specs, JSON_UNESCAPED_UNICODE) : '';
    }
}
