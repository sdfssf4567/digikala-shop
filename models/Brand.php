<?php
/**
 * ===============================================
 * مدل برندها
 * ===============================================
 */

class Brand
{
    /**
     * همه برندهای فعال
     */
    public static function all(bool $onlyActive = true): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT * FROM brands';
        if ($onlyActive) {
            $sql .= ' WHERE status = 1';
        }
        $sql .= ' ORDER BY name ASC';
        return $db->fetchAll($sql);
    }

    /**
     * پیدا کردن برند با شناسه
     */
    public static function find(int $id): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM brands WHERE id = ?', [$id]);
    }

    /* --------- متدهای پنل مدیریت --------- */

    /**
     * افزودن برند جدید
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $db->query('INSERT INTO brands (name, slug, status) VALUES (?, ?, ?)', [
            $data['name'], $data['slug'], $data['status']
        ]);
        return $db->lastInsertId();
    }

    /**
     * ویرایش برند
     */
    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $db->query('UPDATE brands SET name = ?, slug = ?, status = ? WHERE id = ?', [
            $data['name'], $data['slug'], $data['status'], $id
        ]);
    }

    /**
     * حذف برند
     */
    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        $db->query('UPDATE products SET brand_id = NULL WHERE brand_id = ?', [$id]);
        $db->query('DELETE FROM brands WHERE id = ?', [$id]);
    }
}
