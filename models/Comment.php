<?php
/**
 * ===============================================
 * مدل نظرات و امتیازدهی محصولات
 * نظرات جدید ابتدا «در انتظار تایید» هستند و
 * پس از تایید مدیر در سایت نمایش داده می‌شوند
 * ===============================================
 */

class Comment
{
    /**
     * نظرات تایید شده یک محصول
     */
    public static function forProduct(int $productId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT c.*, u.full_name AS user_name
             FROM comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.product_id = ? AND c.status = 'approved'
             ORDER BY c.id DESC",
            [$productId]
        );
    }

    /**
     * ثبت نظر جدید توسط کاربر
     */
    public static function add(int $productId, int $userId, array $data): array
    {
        $title = trim($data['title'] ?? '');
        $body  = trim($data['body'] ?? '');
        $rating = (int)en_num((string)($data['rating'] ?? '5'));

        $errors = [];
        if (mb_strlen($body) < 10) {
            $errors['body'] = 'متن نظر باید حداقل ۱۰ حرف باشد.';
        }
        if ($rating < 1 || $rating > 5) {
            $rating = 5;
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $db = Database::getInstance();
        $db->query(
            "INSERT INTO comments (product_id, user_id, title, body, rating, status)
             VALUES (?, ?, ?, ?, ?, 'pending')",
            [$productId, $userId, $title, $body, $rating]
        );
        return ['ok' => true];
    }

    /* --------- متدهای پنل مدیریت --------- */

    /**
     * همه نظرات با عنوان محصول و نام کاربر
     */
    public static function allForAdmin(string $status = ''): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT c.*, p.title AS product_title, u.full_name AS user_name
                FROM comments c
                LEFT JOIN products p ON p.id = c.product_id
                LEFT JOIN users u ON u.id = c.user_id';
        $params = [];
        if ($status !== '') {
            $sql .= ' WHERE c.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY c.id DESC';
        return $db->fetchAll($sql, $params);
    }

    /**
     * تغییر وضعیت نظر (تایید / رد)
     */
    public static function changeStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return false;
        }
        Database::getInstance()->query('UPDATE comments SET status = ? WHERE id = ?', [$status, $id]);
        return true;
    }

    /**
     * حذف نظر
     */
    public static function delete(int $id): void
    {
        Database::getInstance()->query('DELETE FROM comments WHERE id = ?', [$id]);
    }

    /**
     * تعداد نظرات در انتظار تایید (برای داشبورد ادمین)
     */
    public static function pendingCount(): int
    {
        return (int)Database::getInstance()->fetchColumn(
            "SELECT COUNT(*) FROM comments WHERE status = 'pending'"
        );
    }
}
