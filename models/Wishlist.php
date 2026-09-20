<?php
/**
 * ===============================================
 * مدل علاقه‌مندی‌ها (لیست علاقه‌مندی کاربر)
 * ===============================================
 */

class Wishlist
{
    /**
     * محصولات علاقه‌مندی کاربر
     */
    public static function forUser(int $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT w.id AS wishlist_id, p.id AS product_id, p.title, p.price, p.discount_percent, p.stock, p.status,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM wishlist w
             INNER JOIN products p ON p.id = w.product_id
             WHERE w.user_id = ?
             ORDER BY w.created_at DESC",
            [$userId]
        );
    }

    /**
     * آیا این محصول در علاقه‌مندی کاربر هست؟
     */
    public static function exists(int $userId, int $productId): bool
    {
        return (bool)Database::getInstance()->fetchColumn(
            'SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
    }

    /**
     * افزودن/حذف از علاقه‌مندی (کلید تغییر وضعیت)
     */
    public static function toggle(int $userId, int $productId): array
    {
        $db = Database::getInstance();
        // بررسی معتبر بودن محصول
        $product = $db->fetch('SELECT id FROM products WHERE id = ? AND status = 1', [$productId]);
        if (!$product) {
            return ['ok' => false, 'error' => 'محصول یافت نشد.'];
        }

        if (self::exists($userId, $productId)) {
            $db->query('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?', [$userId, $productId]);
            return ['ok' => true, 'in_wishlist' => false];
        }

        try {
            $db->query('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)', [$userId, $productId]);
        } catch (Throwable $e) {
            // اگر همزمان اضافه شده بود، خطای کلید یکتا نادیده گرفته می‌شود
        }
        return ['ok' => true, 'in_wishlist' => true];
    }

    /**
     * حذف یک قلم از علاقه‌مندی
     */
    public static function remove(int $userId, int $wishlistId): void
    {
        Database::getInstance()->query('DELETE FROM wishlist WHERE id = ? AND user_id = ?', [$wishlistId, $userId]);
    }
}
