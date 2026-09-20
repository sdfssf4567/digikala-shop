<?php
/**
 * ===============================================
 * کنترلر محصولات - لیست با فیلتر و صفحه جزئیات
 * ===============================================
 */

class ProductController
{
    /**
     * صفحه لیست محصولات با فیلتر دسته‌بندی/برند/قیمت و مرتب‌سازی
     */
    public function index(): void
    {
        $db = Database::getInstance();

        // دریافت فیلترها از آدرس
        $catSlug = input('cat');
        $filters = [
            'category_id'    => 0,
            'brand_ids'      => $_GET['brands'] ?? [],
            'min_price'      => input('min_price'),
            'max_price'      => input('max_price'),
            'q'              => input('q'),
            'only_available' => !empty($_GET['available']),
        ];

        // تشخیص دسته‌بندی از روی نامک
        $currentCategory = null;
        if ($catSlug !== '') {
            $currentCategory = Category::findBySlug($catSlug);
            if ($currentCategory) {
                $filters['category_id'] = (int)$currentCategory['id'];
            }
        }

        // فقط شناسه‌های عددی برندها مجاز هستند
        if (!is_array($filters['brand_ids'])) {
            $filters['brand_ids'] = [];
        }

        // مرتب‌سازی مجاز
        $sort = input('sort', 'newest');
        if (!in_array($sort, ['newest', 'cheap', 'expensive', 'popular', 'discount'], true)) {
            $sort = 'newest';
        }

        // صفحه‌بندی
        $page = max(1, (int)en_num(input('page', '1')));

        $result = Product::filter($filters, $sort, $page, 12);

        // داده‌های ستون فیلتر
        $categories     = Category::mainCategories();
        $brands         = Brand::all();
        $categoryCounts = Category::productCounts();

        // عنوان صفحه
        $pageTitle = $currentCategory ? 'خرید ' . $currentCategory['name'] : 'همه محصولات';
        if ($filters['q'] !== '') {
            $pageTitle = 'نتایج جستجو برای «' . $filters['q'] . '»';
        }

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/products.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * صفحه جزئیات محصول
     */
    public function show(): void
    {
        $id = (int)input('id', '0');
        $product = Product::find($id);

        if (!$product) {
            http_response_code(404);
            $pageTitle = 'محصول پیدا نشد';
            $flash = get_flash();
            include APP_ROOT . '/views/layouts/header.php';
            include APP_ROOT . '/views/error.php';
            include APP_ROOT . '/views/layouts/footer.php';
            return;
        }

        // افزایش بازدید
        Product::incrementViews($id);

        $images     = Product::images($id);
        $rating     = Product::rating($id);
        $comments   = Comment::forProduct($id);
        $related    = Product::related($product, 4);
        $inWishlist = is_logged_in() && Wishlist::exists($_SESSION['user_id'], $id);

        // مشخصات فنی - ذخیره شده به فرمت JSON
        $specs = json_decode($product['specifications'] ?? '', true) ?: [];

        $pageTitle = $product['title'];

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/product.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }
}
