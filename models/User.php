<?php
/**
 * ===============================================
 * مدل کاربران - ثبت‌نام، ورود، مدیریت
 * پسوردها همیشه با password_hash ذخیره می‌شوند
 * ===============================================
 */

class User
{
    /**
     * پیدا کردن کاربر با ایمیل (برای ورود)
     */
    public static function findByEmail(string $email): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM users WHERE email = ?', [$email]);
    }

    /**
     * پیدا کردن کاربر با شناسه
     */
    public static function find(int $id): ?array
    {
        return Database::getInstance()->fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /**
     * ثبت‌نام کاربر جدید - خروجی: شناسه کاربر یا خطا
     */
    public static function register(array $data): array
    {
        $db = Database::getInstance();

        // اعتبارسنجی ورودی‌ها در سمت سرور
        $errors = [];

        $fullName = trim($data['full_name'] ?? '');
        $email    = trim(mb_strtolower($data['email'] ?? ''));
        $phone    = en_num(trim($data['phone'] ?? ''));
        $password = $data['password'] ?? '';
        $confirm  = $data['password_confirm'] ?? '';

        if (mb_strlen($fullName) < 3) {
            $errors['full_name'] = 'نام و نام خانوادگی باید حداقل ۳ حرف باشد.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'ایمیل وارد شده معتبر نیست.';
        }
        if (!preg_match('/^09\d{9}$/', $phone)) {
            $errors['phone'] = 'شماره موبایل باید به شکل ۰۹xxxxxxxxx و ۱۱ رقم باشد.';
        }
        if (strlen($password) < 6) {
            $errors['password'] = 'رمز عبور باید حداقل ۶ کاراکتر باشد.';
        }
        if ($password !== $confirm) {
            $errors['password_confirm'] = 'تکرار رمز عبور با رمز عبور یکسان نیست.';
        }
        // ایمیل تکراری نباشد
        if (!$errors && self::findByEmail($email)) {
            $errors['email'] = 'این ایمیل قبلاً در سایت ثبت شده است.';
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        // هش کردن رمز عبور قبل از ذخیره (امنیت)
        $db->query(
            'INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)',
            [$fullName, $email, $phone, password_hash($password, PASSWORD_DEFAULT), 'user']
        );

        return ['ok' => true, 'id' => $db->lastInsertId()];
    }

    /**
     * ورود کاربر - بررسی ایمیل و رمز عبور
     */
    public static function login(string $email, string $password): array
    {
        $email = trim(mb_strtolower($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'ایمیل وارد شده معتبر نیست.'];
        }
        if ($password === '') {
            return ['ok' => false, 'error' => 'رمز عبور را وارد کنید.'];
        }

        $user = self::findByEmail($email);
        // password_verify - مقایسه امن با هش ذخیره شده
        if (!$user || !password_verify($password, $user['password'])) {
            return ['ok' => false, 'error' => 'ایمیل یا رمز عبور اشتباه است.'];
        }

        return ['ok' => true, 'user' => $user];
    }

    /**
     * ویرایش اطلاعات پروفایل کاربر
     */
    public static function updateProfile(int $id, array $data): array
    {
        $errors = [];

        $fullName = trim($data['full_name'] ?? '');
        $phone    = en_num(trim($data['phone'] ?? ''));
        $address  = trim($data['address'] ?? '');

        if (mb_strlen($fullName) < 3) {
            $errors['full_name'] = 'نام و نام خانوادگی باید حداقل ۳ حرف باشد.';
        }
        if (!preg_match('/^09\d{9}$/', $phone)) {
            $errors['phone'] = 'شماره موبایل باید به شکل ۰۹xxxxxxxxx و ۱۱ رقم باشد.';
        }
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $db = Database::getInstance();
        $db->query(
            'UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?',
            [$fullName, $phone, $address, $id]
        );
        return ['ok' => true];
    }

    /**
     * تغییر رمز عبور کاربر
     */
    public static function changePassword(int $id, string $current, string $new, string $confirm): array
    {
        $user = self::find($id);
        if (!$user || !password_verify($current, $user['password'])) {
            return ['ok' => false, 'error' => 'رمز عبور فعلی اشتباه است.'];
        }
        if (strlen($new) < 6) {
            return ['ok' => false, 'error' => 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد.'];
        }
        if ($new !== $confirm) {
            return ['ok' => false, 'error' => 'تکرار رمز عبور جدید با رمز عبور یکسان نیست.'];
        }
        Database::getInstance()->query(
            'UPDATE users SET password = ? WHERE id = ?',
            [password_hash($new, PASSWORD_DEFAULT), $id]
        );
        return ['ok' => true];
    }

    /* --------- متدهای پنل مدیریت --------- */

    /**
     * لیست همه کاربران با تعداد سفارش
     */
    public static function allForAdmin(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT u.*, COUNT(o.id) AS orders_count
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id
             GROUP BY u.id
             ORDER BY u.id DESC'
        );
    }

    /**
     * حذف کاربر (کاربر مدیر قابل حذف نیست)
     */
    public static function delete(int $id): bool
    {
        $user = self::find($id);
        if (!$user || $user['role'] === 'admin') {
            return false;
        }
        $db = Database::getInstance();
        $db->query('DELETE FROM cart WHERE user_id = ?', [$id]);
        $db->query('DELETE FROM wishlist WHERE user_id = ?', [$id]);
        $db->query('DELETE FROM comments WHERE user_id = ?', [$id]);
        $db->query('DELETE FROM users WHERE id = ?', [$id]);
        return true;
    }

    /**
     * تعداد کل کاربران (برای داشبورد)
     */
    public static function count(): int
    {
        return (int)Database::getInstance()->fetchColumn('SELECT COUNT(*) FROM users');
    }
}
