<?php
/**
 * ===============================================
 * کنترلر مدیریت بنرها و اسلایدر صفحه اصلی
 * ===============================================
 */

class BannerController
{
    /**
     * لیست بنرها + فرم افزودن
     */
    public function index(): void
    {
        $banners   = Banner::allForAdmin();
        $pageTitle = 'مدیریت بنرها';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/banners.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * افزودن بنر جدید با آپلود تصویر
     */
    public function save(): void
    {
        csrf_check_or_die();

        $title = trim($_POST['title'] ?? '');
        $link  = trim($_POST['link'] ?? '');

        if (mb_strlen($title) < 2) {
            set_flash('danger', 'عنوان بنر را وارد کنید.');
            redirect(admin_url('banners'));
        }

        // اعتبارسنجی و آپلود تصویر بنر
        if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            set_flash('danger', 'تصویر بنر را انتخاب کنید.');
            redirect(admin_url('banners'));
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($_FILES['image']['tmp_name']) ?: '';
        if (!in_array($mime, $allowedTypes, true)) {
            set_flash('danger', 'فرمت تصویر مجاز نیست. فقط JPG، PNG، WebP و GIF.');
            redirect(admin_url('banners'));
        }
        if ((int)$_FILES['image']['size'] > 5 * 1024 * 1024) {
            set_flash('danger', 'حجم تصویر بیش از ۵ مگابایت است.');
            redirect(admin_url('banners'));
        }

        $ext      = match ($mime) {
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => 'jpg',
        };
        $fileName = 'banner-' . time() . '.' . $ext;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH_BANNERS . '/' . $fileName)) {
            set_flash('danger', 'خطا در ذخیره تصویر. دسترسی پوشه assets/images/banners را بررسی کنید.');
            redirect(admin_url('banners'));
        }

        Banner::create([
            'title'      => $title,
            'image'      => 'assets/images/banners/' . $fileName,
            'link'       => $link,
            'position'   => ($_POST['position'] ?? 'slider') === 'middle' ? 'middle' : 'slider',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => !empty($_POST['status']) ? 1 : 0,
        ]);

        set_flash('success', 'بنر جدید اضافه شد.');
        redirect(admin_url('banners'));
    }

    /**
     * حذف بنر
     */
    public function delete(): void
    {
        csrf_check_or_die();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Banner::delete($id);
            set_flash('success', 'بنر حذف شد.');
        }
        redirect(admin_url('banners'));
    }

    /**
     * فعال/غیرفعال کردن بنر
     */
    public function toggle(): void
    {
        csrf_check_or_die();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Banner::toggleStatus($id);
        }
        redirect(admin_url('banners'));
    }
}
