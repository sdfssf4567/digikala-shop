<?php
/**
 * ===============================================
 * کنترلر علاقه‌مندی‌ها
 * ===============================================
 */

class WishlistController
{
    /**
     * نمایش لیست علاقه‌مندی کاربر
     */
    public function index(): void
    {
        require_login();

        $products  = Wishlist::forUser((int)$_SESSION['user_id']);
        $pageTitle = 'علاقه‌مندی‌های من';
        $activeTab = 'wishlist';

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/wishlist.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * افزودن/حذف از علاقه‌مندی (Ajax)
     */
    public function toggle(): void
    {
        if (!is_logged_in()) {
            json_response(['ok' => false, 'need_login' => true, 'login_url' => url('login')], 401);
        }

        csrf_check_or_die();

        $productId = (int)($_POST['product_id'] ?? 0);
        $result    = Wishlist::toggle((int)$_SESSION['user_id'], $productId);

        if ($result['ok']) {
            json_response([
                'ok'          => true,
                'in_wishlist' => $result['in_wishlist'],
                'message'     => $result['in_wishlist'] ? 'به علاقه‌مندی‌ها اضافه شد.' : 'از علاقه‌مندی‌ها حذف شد.',
            ]);
        }
        json_response(['ok' => false, 'error' => $result['error'] ?? 'خطا'], 400);
    }
}
