<?php
/**
 * ===============================================
 * کنترلر حساب کاربری - پروفایل، سفارش‌ها
 * ===============================================
 */

class UserController
{
    /**
     * صفحه پروفایل - ویرایش اطلاعات و تغییر رمز عبور
     */
    public function profile(): void
    {
        require_login();

        $user      = current_user();
        $pageTitle = 'پروفایل کاربری';
        $activeTab = 'profile';

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/profile.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * ذخیره ویرایش اطلاعات پروفایل
     */
    public function updateProfile(): void
    {
        require_login();
        csrf_check_or_die();

        // اگر فرم تغییر رمز عبور ارسال شده باشد
        if (!empty($_POST['action']) && $_POST['action'] === 'password') {
            $result = User::changePassword(
                (int)$_SESSION['user_id'],
                $_POST['current_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['new_password_confirm'] ?? ''
            );
            set_flash($result['ok'] ? 'success' : 'danger', $result['ok'] ? 'رمز عبور با موفقیت تغییر کرد.' : $result['error']);
            redirect(url('profile'));
        }

        // ویرایش اطلاعات شخصی
        $result = User::updateProfile((int)$_SESSION['user_id'], $_POST);

        if ($result['ok']) {
            $_SESSION['user_name'] = trim($_POST['full_name'] ?? '');
            set_flash('success', 'اطلاعات حساب با موفقیت به‌روزرسانی شد.');
        } else {
            foreach ($result['errors'] as $error) {
                set_flash('danger', $error);
            }
        }
        redirect(url('profile'));
    }

    /**
     * لیست سفارش‌های کاربر
     */
    public function orders(): void
    {
        require_login();

        $orders    = Order::forUser((int)$_SESSION['user_id']);
        $pageTitle = 'سفارش‌های من';
        $activeTab = 'orders';

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/profile.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * جزئیات یک سفارش (فقط برای مالک سفارش)
     */
    public function orderDetail(): void
    {
        require_login();

        $orderId = (int)input('id', '0');
        $order   = Order::findForUser($orderId, (int)$_SESSION['user_id']);

        if (!$order) {
            set_flash('danger', 'سفارش مورد نظر یافت نشد.');
            redirect(url('orders'));
        }

        $orderItems = Order::items($orderId);
        $pageTitle  = 'سفارش ' . $order['order_number'];

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/order_detail.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }
}
