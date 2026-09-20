<?php
/**
 * ===============================================
 * مدل سبد خرید - ذخیره در دیتابیس برای کاربران لاگین شده
 * (مانند دیجی‌کالا، برای افزودن به سبد باید وارد حساب شوید)
 * ===============================================
 */

class Cart
{
    /**
     * اقلام سبد خرید کاربر با اطلاعات کامل محصول
     */
    public static function items(int $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT cart.id AS cart_id, cart.quantity,
                    p.id AS product_id, p.title, p.price, p.discount_percent, p.stock, p.status,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM cart
             INNER JOIN products p ON p.id = cart.product_id
             WHERE cart.user_id = ?
             ORDER BY cart.created_at DESC",
            [$userId]
        );
    }

    /**
     * افزودن محصول به سبد (یا افزایش تعداد اگر قبلاً باشد)
     */
    public static function add(int $userId, int $productId, int $quantity = 1): array
    {
        $db = Database::getInstance();

        // بررسی وجود و موجود بودن محصول
        $product = $db->fetch('SELECT id, title, stock, status FROM products WHERE id = ?', [$productId]);
        if (!$product || (int)$product['status'] !== 1) {
            return ['ok' => false, 'error' => 'این محصول یافت نشد.'];
        }
        if ((int)$product['stock'] < 1) {
            return ['ok' => false, 'error' => 'موجودی این محصول تمام شده است.'];
        }

        $existing = $db->fetch(
            'SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?',
            [$userId, $productId]
        );

        if ($existing) {
            $newQty = (int)$existing['quantity'] + max(1, $quantity);
            // تعداد نباید از موجودی انبار بیشتر شود
            $newQty = min($newQty, (int)$product['stock']);
            $db->query('UPDATE cart SET quantity = ? WHERE id = ?', [$newQty, $existing['id']]);
        } else {
            $quantity = max(1, min($quantity, (int)$product['stock']));
            $db->query(
                'INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)',
                [$userId, $productId, $quantity]
            );
        }

        return ['ok' => true, 'count' => self::count($userId)];
    }

    /**
     * تغییر تعداد یک قلم سبد
     */
    public static function updateQuantity(int $userId, int $cartId, int $quantity): array
    {
        $db = Database::getInstance();
        $row = $db->fetch(
            'SELECT cart.id, cart.product_id, p.stock FROM cart
             INNER JOIN products p ON p.id = cart.product_id
             WHERE cart.id = ? AND cart.user_id = ?',
            [$cartId, $userId]
        );
        if (!$row) {
            return ['ok' => false, 'error' => 'این قلم در سبد شما یافت نشد.'];
        }
        if ($quantity < 1) {
            return ['ok' => false, 'error' => 'تعداد باید حداقل ۱ باشد.'];
        }
        if ($quantity > (int)$row['stock']) {
            return ['ok' => false, 'error' => 'تعداد درخواستی بیشتر از موجودی انبار است.'];
        }
        $db->query('UPDATE cart SET quantity = ? WHERE id = ?', [$quantity, $cartId]);
        return ['ok' => true];
    }

    /**
     * حذف یک قلم از سبد
     */
    public static function remove(int $userId, int $cartId): void
    {
        Database::getInstance()->query('DELETE FROM cart WHERE id = ? AND user_id = ?', [$cartId, $userId]);
    }

    /**
     * خالی کردن کل سبد کاربر
     */
    public static function clear(int $userId): void
    {
        Database::getInstance()->query('DELETE FROM cart WHERE user_id = ?', [$userId]);
    }

    /**
     * تعداد اقلام سبد (برای نمایش در آیکون هدر)
     */
    public static function count(int $userId): int
    {
        return (int)Database::getInstance()->fetchColumn(
            'SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?',
            [$userId]
        );
    }

    /**
     * جمع کل مبلغ سبد
     */
    public static function totals(array $items): array
    {
        $sum = 0;
        $savings = 0;
        foreach ($items as $item) {
            $final = discounted_price($item['price'], $item['discount_percent']);
            $sum     += $final * (int)$item['quantity'];
            $savings += ((int)$item['price'] - $final) * (int)$item['quantity'];
        }
        return ['sum' => $sum, 'savings' => $savings, 'payable' => $sum];
    }
}
