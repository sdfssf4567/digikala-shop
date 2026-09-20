<?php
/**
 * ===============================================
 * کنترلر سبد خرید - تمام عملیات با Ajax انجام می‌شود
 * برای استفاده از سبد خرید، ورود کاربر الزامی است (مانند دیجی‌کالا)
 * ===============================================
 */

class CartController
{
    /**
     * نمایش صفحه سبد خرید
     */
    public function index(): void
    {
        require_login();

        $userId = (int)$_SESSION['user_id'];
        $items  = Cart::items($userId);
        $totals = Cart::totals($items);

        $pageTitle = 'سبد خرید';

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/cart.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * افزودن به سبد (Ajax)
     */
    public function add(): void
    {
        if (!is_logged_in()) {
            json_response(['ok' => false, 'need_login' => true, 'login_url' => url('login')], 401);
        }

        csrf_check_or_die();

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int)en_num((string)($_POST['quantity'] ?? '1')));

        $result = Cart::add((int)$_SESSION['user_id'], $productId, $quantity);

        if ($result['ok']) {
            json_response([
                'ok'       => true,
                'message'  => 'محصول به سبد خرید اضافه شد.',
                'count'    => $result['count'],
                'cart_url' => url('cart'),
            ]);
        } else {
            json_response(['ok' => false, 'error' => $result['error'] ?? 'خطا در افزودن به سبد.'], 400);
        }
    }

    /**
     * تغییر تعداد قلم سبد (Ajax)
     */
    public function update(): void
    {
        require_login();
        csrf_check_or_die();

        $cartId   = (int)($_POST['cart_id'] ?? 0);
        $quantity = (int)en_num((string)($_POST['quantity'] ?? '1'));

        $result = Cart::updateQuantity((int)$_SESSION['user_id'], $cartId, $quantity);
        if ($result['ok']) {
            // محاسبه مجدد جمع کل برای به‌روزرسانی لحظه‌ای صفحه
            $userId = (int)$_SESSION['user_id'];
            $items  = Cart::items($userId);
            $totals = Cart::totals($items);
            json_response([
                'ok'      => true,
                'count'   => Cart::count($userId),
                'sum'     => fa_price($totals['sum']),
                'savings' => fa_price($totals['savings']),
                'payable' => fa_price($totals['payable']),
            ]);
        } else {
            json_response(['ok' => false, 'error' => $result['error']], 400);
        }
    }

    /**
     * حذف قلم از سبد (Ajax)
     */
    public function remove(): void
    {
        require_login();
        csrf_check_or_die();

        $cartId = (int)($_POST['cart_id'] ?? 0);
        Cart::remove((int)$_SESSION['user_id'], $cartId);

        $userId = (int)$_SESSION['user_id'];
        $count  = Cart::count($userId);
        $items  = Cart::items($userId);
        $totals = Cart::totals($items);

        json_response([
            'ok'      => true,
            'empty'   => count($items) === 0,
            'count'   => $count,
            'sum'     => fa_price($totals['sum']),
            'savings' => fa_price($totals['savings']),
            'payable' => fa_price($totals['payable']),
        ]);
    }

    /**
     * فقط شمارش اقلام سبد (برای هدر)
     */
    public function count(): void
    {
        if (!is_logged_in()) {
            json_response(['count' => 0]);
        }
        json_response(['count' => Cart::count((int)$_SESSION['user_id'])]);
    }
}
