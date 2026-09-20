<?php
/**
 * ===============================================
 * مدل بنرها و اسلایدرهای صفحه اصلی
 * ===============================================
 */

class Banner
{
    /**
     * بنرهای اسلایدر صفحه اصلی
     */
    public static function slider(): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT * FROM banners WHERE position = 'slider' AND status = 1 ORDER BY sort_order ASC, id ASC"
        );
    }

    /**
     * بنرهای وسط صفحه (کنار هم)
     */
    public static function middle(): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT * FROM banners WHERE position = 'middle' AND status = 1 ORDER BY sort_order ASC, id ASC"
        );
    }

    /* --------- متدهای پنل مدیریت --------- */

    /**
     * همه بنرها برای مدیریت
     */
    public static function allForAdmin(): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM banners ORDER BY sort_order ASC, id ASC'
        );
    }

    /**
     * پیدا کردن بنر با شناسه
     */
    public static function find(int $id): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM banners WHERE id = ?', [$id]);
    }

    /**
     * افزودن بنر جدید
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $db->query(
            'INSERT INTO banners (title, image, link, position, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)',
            [$data['title'], $data['image'], $data['link'], $data['position'], $data['sort_order'], $data['status']]
        );
        return $db->lastInsertId();
    }

    /**
     * ویرایش بنر
     */
    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $db->query(
            'UPDATE banners SET title = ?, link = ?, position = ?, sort_order = ?, status = ? WHERE id = ?',
            [$data['title'], $data['link'], $data['position'], $data['sort_order'], $data['status'], $id]
        );
    }

    /**
     * حذف بنر به همراه فایل تصویر
     */
    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        $banner = self::find($id);
        if ($banner) {
            $path = APP_ROOT . '/' . ltrim($banner['image'], '/');
            if (is_file($path)) {
                @unlink($path);
            }
            $db->query('DELETE FROM banners WHERE id = ?', [$id]);
        }
    }

    /**
     * تغییر وضعیت فعال/غیرفعال بنر
     */
    public static function toggleStatus(int $id): void
    {
        Database::getInstance()->query('UPDATE banners SET status = 1 - status WHERE id = ?', [$id]);
    }
}
