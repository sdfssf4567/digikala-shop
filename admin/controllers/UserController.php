<?php
/**
 * ===============================================
 * کنترلر مدیریت کاربران در پنل ادمین
 * ===============================================
 */

class UserController
{
    /**
     * لیست کاربران
     */
    public function list(): void
    {
        $users     = User::allForAdmin();
        $pageTitle = 'مدیریت کاربران';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/users.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * حذف کاربر (کاربران مدیر قابل حذف نیستند)
     */
    public function delete(): void
    {
        csrf_check_or_die();

        $id = (int)($_POST['id'] ?? 0);
        if ($id && User::delete($id)) {
            set_flash('success', 'کاربر حذف شد.');
        } else {
            set_flash('danger', 'امکان حذف این کاربر وجود ندارد (حساب‌های مدیریتی قابل حذف نیستند).');
        }
        redirect(admin_url('users'));
    }
}
