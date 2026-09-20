<?php
/**
 * ===============================================
 * کنترلر فرایند پرداخت (تسویه حساب چند مرحله‌ای)
 * مرحله ۱: سبد خرید | مرحله ۲: اطلاعات ارسال | مرحله ۳: پرداخت
 * ===============================================
 */

class CheckoutController
{
    /**
     * نمایش فرم اطلاعات ارسال و پرداخت
     */
    public function index(): void
    {
        require_login();

        $userId = (int)$_SESSION['user_id'];
        $user   = User::find($userId);
        $items  = Cart::items($userId);

        // سبد خالی؟ برمی‌گردیم به صفحه سبد خرید
        if (empty($items)) {
            redirect(url('cart'));
        }

        $totals = Cart::totals($items);

        $pageTitle = 'تسویه حساب';

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/checkout.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * ثبت نهایی سفارش
     */
    public function submit(): void
    {
        require_login();
        csrf_check_or_die();

        $userId = (int)$_SESSION['user_id'];
        $items  = Cart::items($userId);

        if (empty($items)) {
            redirect(url('cart'));
        }

        // دریافت و پاکسازی ورودی‌های فرم
        $data = [
            'receiver_name'  => trim($_POST['receiver_name'] ?? ''),
            'phone'          => en_num(trim($_POST['phone'] ?? '')),
            'province'       => trim($_POST['province'] ?? ''),
            'city'           => trim($_POST['city'] ?? ''),
            'postal_code'    => en_num(trim($_POST['postal_code'] ?? '')),
            'address'        => trim($_POST['address'] ?? ''),
            'note'           => trim($_POST['note'] ?? ''),
            'payment_method' => ($_POST['payment_method'] ?? 'online') === 'cod' ? 'cod' : 'online',
        ];

        // اعتبارسنجی سمت سرور
        $errors = [];
        if (mb_strlen($data['receiver_name']) < 3) {
            $errors['receiver_name'] = 'نام گیرنده را کامل وارد کنید.';
        }
        if (!preg_match('/^09\d{9}$/', $data['phone'])) {
            $errors['phone'] = 'شماره موبایل گیرنده باید به شکل ۰۹xxxxxxxxx باشد.';
        }
        if ($data['province'] === '') {
            $errors['province'] = 'استان را وارد کنید.';
        }
        if ($data['city'] === '') {
            $errors['city'] = 'شهر را وارد کنید.';
        }
        if (!preg_match('/^\d{10}$/', $data['postal_code'])) {
            $errors['postal_code'] = 'کد پستی باید ۱۰ رقم باشد.';
        }
        if (mb_strlen($data['address']) < 10) {
            $errors['address'] = 'نشانی کامل پستی را وارد کنید (حداقل ۱۰ حرف).';
        }

        if ($errors) {
            set_old(array_merge($data, ['errors' => $errors]));
            set_flash('danger', 'لطفاً خطاهای فرم را برطرف کنید.');
            redirect(url('checkout'));
        }

        clear_old();

        // ثبت سفارش در دیتابیس (با تراکنش)
        $result = Order::create($userId, $data, $items);

        if ($result['ok']) {
            set_flash('success', 'سفارش شما با موفقیت ثبت شد. شماره سفارش: ' . $result['order_number']);
            redirect(url('order/view', ['id' => $result['order_id']]));
        }

        set_flash('danger', $result['error']);
        redirect(url('checkout'));
    }
}
