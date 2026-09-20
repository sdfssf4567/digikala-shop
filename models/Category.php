<?php
/**
 * ===============================================
 * مدل دسته‌بندی محصولات
 * ===============================================
 */

class Category
{
    /**
     * همه دسته‌بندی‌های فروشگاه (با تعداد محصولات)
     */
    public static function all(bool $onlyActive = true): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT c.*, p.name AS parent_name,
                (SELECT COUNT(*) FROM products pr WHERE pr.category_id = c.id AND pr.status = 1) AS product_count
                FROM categories c
                LEFT JOIN categories p ON p.id = c.parent_id';
        if ($onlyActive) {
            $sql .= ' WHERE c.status = 1';
        }
        $sql .= ' ORDER BY c.sort_order ASC, c.id ASC';
        return $db->fetchAll($sql);
    }

    /**
     * دسته‌بندی اصلی (بدون والد) برای منو
     */
    public static function mainCategories(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT * FROM categories WHERE status = 1 AND (parent_id IS NULL OR parent_id = 0)
             ORDER BY sort_order ASC, id ASC'
        );
    }

    /**
     * پیدا کردن دسته‌بندی با شناسه
     */
    public static function find(int $id): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    /**
     * پیدا کردن دسته‌بندی با نامک (slug)
     */
    public static function findBySlug(string $slug): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM categories WHERE slug = ? AND status = 1', [$slug]);
    }

    /**
     * تعداد محصولات فعال هر دسته‌بندی
     */
    public static function productCounts(): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            "SELECT category_id, COUNT(*) AS cnt FROM products WHERE status = 1 GROUP BY category_id"
        );
        $counts = [];
        foreach ($rows as $row) {
            $counts[(int)$row['category_id']] = (int)$row['cnt'];
        }
        return $counts;
    }

    /* --------- متدهای پنل مدیریت --------- */

    /**
     * افزودن دسته‌بندی جدید
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $db->query(
            'INSERT INTO categories (name, slug, parent_id, icon, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)',
            [$data['name'], $data['slug'], $data['parent_id'] ?: null, $data['icon'], $data['sort_order'], $data['status']]
        );
        return $db->lastInsertId();
    }

    /**
     * ویرایش دسته‌بندی
     */
    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $db->query(
            'UPDATE categories SET name = ?, slug = ?, parent_id = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?',
            [$data['name'], $data['slug'], $data['parent_id'] ?: null, $data['icon'], $data['sort_order'], $data['status'], $id]
        );
    }

    /**
     * حذف دسته‌بندی (محصولات به «بدون دسته» منتقل می‌شوند)
     */
    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        $db->query('UPDATE products SET category_id = NULL WHERE category_id = ?', [$id]);
        $db->query('UPDATE categories SET parent_id = NULL WHERE parent_id = ?', [$id]);
        $db->query('DELETE FROM categories WHERE id = ?', [$id]);
    }
}
