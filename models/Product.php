<?php
/**
 * ===============================================
 * مدل محصول - تمام کوئری‌های مربوط به محصولات
 * ===============================================
 */

class Product
{
    /**
     * دریافت لیست محصولات با فیلتر و مرتب‌سازی و صفحه‌بندی
     *
     * @param array $filters فیلترها (category, brands, min_price, max_price, q, only_available)
     * @param string $sort نوع مرتب‌سازی
     * @param int $page شماره صفحه
     * @param int $perPage تعداد در هر صفحه
     * @return array ['items' => [], 'total' => int, 'pages' => int]
     */
    public static function filter(array $filters = [], string $sort = 'newest', int $page = 1, int $perPage = 12): array
    {
        $db = Database::getInstance();
        $where  = ['p.status = 1'];
        $params = [];

        // فیلتر دسته‌بندی (شامل زیر دسته‌ها)
        if (!empty($filters['category_id'])) {
            $where[] = '(p.category_id = ? OR c.parent_id = ?)';
            $params[] = $filters['category_id'];
            $params[] = $filters['category_id'];
        }

        // فیلتر برندها (چندتایی)
        if (!empty($filters['brand_ids']) && is_array($filters['brand_ids'])) {
            $validIds = array_values(array_filter(array_map('intval', $filters['brand_ids'])));
            if ($validIds) {
                $placeholders = implode(',', array_fill(0, count($validIds), '?'));
                $where[] = "p.brand_id IN ($placeholders)";
                $params = array_merge($params, $validIds);
            }
        }

        // فیلتر محدوده قیمت
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'p.price >= ?';
            $params[] = (int)en_num($filters['min_price']);
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'p.price <= ?';
            $params[] = (int)en_num($filters['max_price']);
        }

        // فقط کالاهای موجود
        if (!empty($filters['only_available'])) {
            $where[] = 'p.stock > 0';
        }

        // جستجوی متنی
        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE ? OR p.short_description LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            $params[] = $q;
            $params[] = $q;
        }

        $whereSql = implode(' AND ', $where);

        // تعیین ترتیب نتایج
        $orderBy = match ($sort) {
            'cheap'     => 'p.price ASC',
            'expensive' => 'p.price DESC',
            'popular'   => 'p.views DESC',
            'discount'  => 'p.discount_percent DESC',
            default     => 'p.created_at DESC, p.id DESC',
        };

        // شمارش کل نتایج برای صفحه‌بندی
        $total = (int)$db->fetchColumn(
            "SELECT COUNT(*)
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE $whereSql",
            $params
        );

        $pages   = max(1, (int)ceil($total / $perPage));
        $page    = max(1, min($page, $pages));
        $offset  = ($page - 1) * $perPage;

        $items = $db->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug, b.name AS brand_name,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE $whereSql
             ORDER BY $orderBy
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    /**
     * پیدا کردن محصول بر اساس شناسه
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug, b.name AS brand_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.id = ? AND p.status = 1",
            [$id]
        );
    }

    /**
     * تصاویر گالری محصول
     */
    public static function images(int $productId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC',
            [$productId]
        );
    }

    /**
     * تصویر اصلی محصول
     */
    public static function mainImage(int $productId): string
    {
        $images = self::images($productId);
        return $images[0]['image'] ?? 'assets/images/products/placeholder.svg';
    }

    /**
     * جدیدترین محصولات صفحه اصلی
     */
    public static function latest(int $limit = 8): array
    {
        return self::filterSorted('p.created_at DESC, p.id DESC', $limit);
    }

    /**
     * پرفروش‌ترین محصولات (بر اساس تعداد فروش در سفارش‌ها)
     */
    public static function bestSellers(int $limit = 8): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.*, c.name AS category_name, b.name AS brand_name,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image,
                    COALESCE(SUM(oi.quantity), 0) AS sold_count
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN order_items oi ON oi.product_id = p.id
             WHERE p.status = 1
             GROUP BY p.id
             ORDER BY sold_count DESC, p.views DESC
             LIMIT " . (int)$limit
        );
    }

    /**
     * پیشنهادهای شگفت‌انگیز (محصولات دارای تخفیف)
     */
    public static function specialOffers(int $limit = 8): array
    {
        return self::filterSorted('p.discount_percent DESC', $limit, 'p.is_special = 1 AND p.discount_percent > 0');
    }

    /**
     * محصولات پرتخفیف
     */
    public static function topDiscounted(int $limit = 8): array
    {
        return self::filterSorted('p.discount_percent DESC', $limit, 'p.discount_percent > 0');
    }

    /**
     * کوئری عمومی محصولات فعال با ترتیب دلخواه
     */
    private static function filterSorted(string $orderBy, int $limit, string $extraWhere = ''): array
    {
        $db = Database::getInstance();
        $where = 'p.status = 1' . ($extraWhere ? ' AND ' . $extraWhere : '');
        return $db->fetchAll(
            "SELECT p.*, c.name AS category_name, b.name AS brand_name,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE $where
             ORDER BY $orderBy
             LIMIT " . (int)$limit
        );
    }

    /**
     * محصولات مشابه (همان دسته‌بندی)
     */
    public static function related(array $product, int $limit = 4): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.*, b.name AS brand_name,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.category_id = ? AND p.id != ? AND p.status = 1
             ORDER BY RAND()
             LIMIT " . (int)$limit,
            [$product['category_id'], $product['id']]
        );
    }

    /**
     * افزایش تعداد بازدید محصول
     */
    public static function incrementViews(int $id): void
    {
        $db = Database::getInstance();
        $db->query('UPDATE products SET views = views + 1 WHERE id = ?', [$id]);
    }

    /**
     * میانگین امتیاز و تعداد نظرات تایید شده محصول
     */
    public static function rating(int $productId): array
    {
        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT COALESCE(AVG(rating), 0) AS avg_rating, COUNT(*) AS total
             FROM comments WHERE product_id = ? AND status = 'approved'",
            [$productId]
        );
        return [
            'average' => $row ? round((float)$row['avg_rating'], 1) : 0,
            'total'   => $row ? (int)$row['total'] : 0,
        ];
    }

    /**
     * جستجوی زنده محصولات (برای باکس جستجوی هدر)
     */
    public static function liveSearch(string $q, int $limit = 6): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.id, p.title, p.price, p.discount_percent,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM products p
             WHERE p.status = 1 AND p.title LIKE ?
             ORDER BY p.views DESC
             LIMIT " . (int)$limit,
            ['%' . $q . '%']
        );
    }

    /* =====================================================
     * متدهای مدیریتی (پنل ادمین)
     * ===================================================== */

    /**
     * دریافت همه محصولات (حتی غیرفعال) برای پنل مدیریت
     */
    public static function allForAdmin(string $q = '', int $categoryId = 0): array
    {
        $db = Database::getInstance();
        $where  = ['1=1'];
        $params = [];
        if ($q !== '') {
            $where[] = 'p.title LIKE ?';
            $params[] = '%' . $q . '%';
        }
        if ($categoryId > 0) {
            $where[] = 'p.category_id = ?';
            $params[] = $categoryId;
        }
        return $db->fetchAll(
            "SELECT p.*, c.name AS category_name, b.name AS brand_name,
                    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY p.id DESC",
            $params
        );
    }

    /**
     * پیدا کردن محصول بدون شرط وضعیت (برای ویرایش ادمین)
     */
    public static function findAny(int $id): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM products WHERE id = ?', [$id]);
    }

    /**
     * افزودن محصول جدید - خروجی: شناسه محصول
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO products (title, slug, category_id, brand_id, price, discount_percent, stock,
                short_description, description, specifications, is_new, is_special, status)
             VALUES (:title, :slug, :category_id, :brand_id, :price, :discount_percent, :stock,
                :short_description, :description, :specifications, :is_new, :is_special, :status)",
            $data
        );
        return $db->lastInsertId();
    }

    /**
     * ویرایش محصول
     */
    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $data['id'] = $id;
        $db->query(
            "UPDATE products SET
                title = :title, slug = :slug, category_id = :category_id, brand_id = :brand_id,
                price = :price, discount_percent = :discount_percent, stock = :stock,
                short_description = :short_description, description = :description,
                specifications = :specifications, is_new = :is_new, is_special = :is_special, status = :status
             WHERE id = :id",
            $data
        );
    }

    /**
     * حذف محصول به همراه تصاویر آن
     */
    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        // حذف فایل‌های تصویر از دیسک
        foreach (self::images($id) as $img) {
            $path = APP_ROOT . '/' . ltrim($img['image'], '/');
            if (is_file($path)) {
                @unlink($path);
            }
        }
        $db->query('DELETE FROM product_images WHERE product_id = ?', [$id]);
        $db->query('DELETE FROM cart WHERE product_id = ?', [$id]);
        $db->query('DELETE FROM wishlist WHERE product_id = ?', [$id]);
        $db->query('DELETE FROM products WHERE id = ?', [$id]);
    }

    /**
     * افزودن تصویر به گالری محصول
     */
    public static function addImage(int $productId, string $imagePath): void
    {
        $db = Database::getInstance();
        $maxOrder = (int)$db->fetchColumn(
            'SELECT COALESCE(MAX(sort_order), 0) FROM product_images WHERE product_id = ?',
            [$productId]
        );
        $db->query(
            'INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)',
            [$productId, $imagePath, $maxOrder + 1]
        );
    }

    /**
     * حذف یک تصویر از گالری
     */
    public static function deleteImage(int $imageId): void
    {
        $db = Database::getInstance();
        $img = $db->fetch('SELECT * FROM product_images WHERE id = ?', [$imageId]);
        if ($img) {
            $path = APP_ROOT . '/' . ltrim($img['image'], '/');
            if (is_file($path)) {
                @unlink($path);
            }
            $db->query('DELETE FROM product_images WHERE id = ?', [$imageId]);
        }
    }
}
