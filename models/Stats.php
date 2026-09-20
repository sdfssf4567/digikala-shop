<?php
/**
 * ===============================================
 * مدل آمار و گزارش‌های مدیریتی (داشبورد و گزارش فروش)
 * ===============================================
 */

class Stats
{
    /**
     * خلاصه آمار کلی داشبورد
     */
    public static function overview(): array
    {
        $db = Database::getInstance();
        return [
            'total_sales'   => (int)$db->fetchColumn(
                "SELECT COALESCE(SUM(final_amount), 0) FROM orders WHERE status != 'canceled'"),
            'orders_count'  => (int)$db->fetchColumn('SELECT COUNT(*) FROM orders'),
            'users_count'   => (int)$db->fetchColumn('SELECT COUNT(*) FROM users'),
            'products_count'=> (int)$db->fetchColumn('SELECT COUNT(*) FROM products'),
            'pending_orders'=> (int)$db->fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
            'pending_comments' => Comment::pendingCount(),
            'low_stock'     => (int)$db->fetchColumn('SELECT COUNT(*) FROM products WHERE stock <= 3'),
        ];
    }

    /**
     * فروش ۱۴ روز اخیر برای نمودار داشبورد
     */
    public static function salesLast14Days(): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            "SELECT DATE(created_at) AS day, COALESCE(SUM(final_amount), 0) AS total
             FROM orders
             WHERE status != 'canceled' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY DATE(created_at)
             ORDER BY day ASC"
        );
        // پر کردن روزهای خالی با صفر برای نمودار پیوسته
        $labels = [];
        $values = [];
        $map = [];
        foreach ($rows as $row) {
            $map[$row['day']] = (int)$row['total'];
        }
        for ($i = 13; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i day"));
            $labels[] = fa_date($day);
            $values[] = $map[$day] ?? 0;
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * پرفروش‌ترین محصولات (گزارش)
     */
    public static function topProducts(int $limit = 10): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT oi.product_id, oi.product_title,
                    SUM(oi.quantity) AS sold_qty,
                    SUM(oi.price * oi.quantity) AS revenue
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id AND o.status != 'canceled'
             GROUP BY oi.product_id, oi.product_title
             ORDER BY sold_qty DESC
             LIMIT " . (int)$limit
        );
    }

    /**
     * فروش به تفکیک دسته‌بندی (گزارش)
     */
    public static function salesByCategory(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT c.name AS category_name,
                    COALESCE(SUM(oi.price * oi.quantity), 0) AS revenue,
                    COALESCE(SUM(oi.quantity), 0) AS sold_qty
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id AND o.status != 'canceled'
             INNER JOIN products p ON p.id = oi.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             GROUP BY c.id, c.name
             ORDER BY revenue DESC"
        );
    }

    /**
     * فروش به تفکیک وضعیت سفارش
     */
    public static function ordersByStatus(): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            'SELECT status, COUNT(*) AS cnt, COALESCE(SUM(final_amount), 0) AS total FROM orders GROUP BY status'
        );
        $result = [];
        foreach ($rows as $row) {
            $result[$row['status']] = [
                'count' => (int)$row['cnt'],
                'total' => (int)$row['total'],
                'label' => order_status_label($row['status']),
            ];
        }
        return $result;
    }

    /**
     * آخرین سفارش‌ها (داشبورد ادمین)
     */
    public static function latestOrders(int $limit = 8): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT o.*, u.full_name AS customer_name
             FROM orders o LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.id DESC
             LIMIT " . (int)$limit
        );
    }
}
