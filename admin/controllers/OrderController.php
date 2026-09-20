<?php
/**
 * ===============================================
 * کنترلر مدیریت سفارش‌ها در پنل ادمین
 * وضعیت‌ها: در انتظار پردازش / ارسال شده / تحویل داده شده / لغو
 * ===============================================
 */

class OrderController
{
    /**
     * لیست سفارش‌ها با امکان فیلتر وضعیت
     */
    public function list(): void
    {
        $status = input('status', '');
        if (!in_array($status, ['pending', 'shipped', 'delivered', 'canceled'], true)) {
            $status = '';
        }

        $orders    = Order::allForAdmin($status);
        $pageTitle = 'مدیریت سفارش‌ها';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/orders.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * جزئیات یک سفارش
     */
    public function view(): void
    {
        $id = (int)input('id', '0');
        $order = Order::find($id);

        if (!$order) {
            set_flash('danger', 'سفارش یافت نشد.');
            redirect(admin_url('orders'));
        }

        $orderItems = Order::items($id);
        $pageTitle  = 'سفارش ' . $order['order_number'];

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/order_detail.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * تغییر وضعیت سفارش
     */
    public function changeStatus(): void
    {
        csrf_check_or_die();

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id && Order::changeStatus($id, $status)) {
            set_flash('success', 'وضعیت سفارش به «' . order_status_label($status) . '» تغییر کرد.');
        } else {
            set_flash('danger', 'وضعیت انتخاب شده معتبر نیست.');
        }

        redirect($_POST['return_url'] ?? admin_url('orders'));
    }
}
