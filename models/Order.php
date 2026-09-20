<?php
/**
 * ===============================================
 * مدل سفارش‌ها - ثبت سفارش با تراکنش، پیگیری وضعیت
 * ===============================================
 */

class Order
{
    /**
     * ثبت سفارش جدید از روی سبد خرید کاربر
     * کل عملیات در یک تراکنش انجام می‌شود تا داده‌ها ناسازگار نشوند
     */
    public static function create(int $userId, array $formData, array $cartItems): array
    {
        if (empty($cartItems)) {
            return ['ok' => false, 'error' => 'سبد خرید شما خالی است.'];
        }

        $db = Database::getInstance();

        // محاسبه مبالغ
        $totalAmount    = 0;
        $discountAmount = 0;
        foreach ($cartItems as $item) {
            $final   = discounted_price($item['price'], $item['discount_percent']);
            $lineSum = $final * $item['quantity'];
            $totalAmount    += $lineSum;
            $discountAmount += ((int)$item['price'] - $final) * $item['quantity'];
        }
        $finalAmount = $totalAmount; // هزینه ارسال رایگان فرض شده است

        $orderNumber = 'DS-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        try {
            $db->beginTransaction();

            // درج سفارش اصلی
            $db->query(
                "INSERT INTO orders (user_id, order_number, total_amount, discount_amount, final_amount,
                    status, receiver_name, phone, province, city, postal_code, address, note, payment_method)
                 VALUES (:user_id, :order_number, :total_amount, :discount_amount, :final_amount,
                    'pending', :receiver_name, :phone, :province, :city, :postal_code, :address, :note, :payment_method)",
                [
                    'user_id'        => $userId,
                    'order_number'   => $orderNumber,
                    'total_amount'   => $totalAmount,
                    'discount_amount'=> $discountAmount,
                    'final_amount'   => $finalAmount,
                    'receiver_name'  => $formData['receiver_name'],
                    'phone'          => $formData['phone'],
                    'province'       => $formData['province'],
                    'city'           => $formData['city'],
                    'postal_code'    => $formData['postal_code'],
                    'address'        => $formData['address'],
                    'note'           => $formData['note'],
                    'payment_method' => $formData['payment_method'],
                ]
            );
            $orderId = $db->lastInsertId();

            // درج اقلام سفارش + کم شدن موجودی انبار
            foreach ($cartItems as $item) {
                $final = discounted_price($item['price'], $item['discount_percent']);
                $db->query(
                    "INSERT INTO order_items (order_id, product_id, product_title, image, price, quantity)
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $orderId,
                        $item['product_id'],
                        $item['title'],
                        Product::mainImage((int)$item['product_id']),
                        $final,
                        $item['quantity'],
                    ]
                );
                // کاهش موجودی محصول
                $db->query(
                    'UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?',
                    [$item['quantity'], $item['product_id']]
                );
            }

            // پاک کردن سبد خرید کاربر
            $db->query('DELETE FROM cart WHERE user_id = ?', [$userId]);

            $db->commit();

            return ['ok' => true, 'order_id' => $orderId, 'order_number' => $orderNumber];

        } catch (Throwable $e) {
            $db->rollBack();
            error_log('Order Error: ' . $e->getMessage());
            return ['ok' => false, 'error' => 'خطا در ثبت سفارش. لطفاً دوباره تلاش کنید.'];
        }
    }

    /**
     * سفارش‌های یک کاربر (به ترتیب جدیدترین)
     */
    public static function forUser(int $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT o.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS items_count
             FROM orders o WHERE o.user_id = ? ORDER BY o.id DESC',
            [$userId]
        );
    }

    /**
     * پیدا کردن سفارش با شناسه (با کنترل مالکیت)
     */
    public static function findForUser(int $orderId, int $userId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch('SELECT * FROM orders WHERE id = ? AND user_id = ?', [$orderId, $userId]);
    }

    /**
     * اقلام سفارش
     */
    public static function items(int $orderId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll('SELECT * FROM order_items WHERE order_id = ?', [$orderId]);
    }

    /* --------- متدهای پنل مدیریت --------- */

    /**
     * همه سفارش‌ها با نام کاربر
     */
    public static function allForAdmin(string $status = ''): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT o.*, u.full_name AS customer_name, u.email AS customer_email
                FROM orders o LEFT JOIN users u ON u.id = o.user_id';
        $params = [];
        if ($status !== '') {
            $sql .= ' WHERE o.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY o.id DESC';
        return $db->fetchAll($sql, $params);
    }

    /**
     * پیدا کردن سفارش برای ادمین
     */
    public static function find(int $orderId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            'SELECT o.*, u.full_name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
             FROM orders o LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id = ?',
            [$orderId]
        );
    }

    /**
     * تغییر وضعیت سفارش
     */
    public static function changeStatus(int $orderId, string $status): bool
    {
        $allowed = ['pending', 'shipped', 'delivered', 'canceled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        Database::getInstance()->query('UPDATE orders SET status = ? WHERE id = ?', [$status, $orderId]);
        return true;
    }
}
