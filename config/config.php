<?php
/**
 * ===============================================
 * فایل پیکربندی اصلی فروشگاه دیجی‌شاپ
 * تنظیمات اتصال به دیتابیس و مقادیر سراسری سایت
 * ===============================================
 */

/* --------- تنظیمات دیتابیس (MySQL) --------- */
// برای نصب روی XAMPP مقادیر پیش‌فرض معمولاً بدون تغییر کار می‌کنند
define('DB_HOST', 'localhost');      // آدرس سرور دیتابیس
define('DB_NAME', 'digikala_shop');  // نام دیتابیس
define('DB_USER', 'root');           // نام کاربری دیتابیس
define('DB_PASS', '');               // رمز عبور دیتابیس
define('DB_CHARSET', 'utf8mb4');     // انکودینگ برای پشتیبانی کامل فارسی

/* --------- حالت دیباگ ---------
 * در حالت توسعه true بگذارید تا خطاها نمایش داده شوند.
 * روی هاست اصلی حتماً false قرار دهید.
 */
define('DEBUG', false);

if (DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('log_errors', '1');
}

/* --------- تعیین منطقه زمانی ایران --------- */
date_default_timezone_set('Asia/Tehran');

/* --------- تشخیص خودکار آدرس پایه سایت ---------
 * بدون نیاز به ویرایش، در هر پوشه‌ای از htdocs کار می‌کند
 * مثال: http://localhost/digikala-shop
 */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir      = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
// اگر درخواست از داخل پوشه admin باشد، یک سطح به عقب برمی‌گردیم
if (basename($dir) === 'admin') {
    $dir = str_replace('\\', '/', dirname($dir));
}
$dir = rtrim($dir, '/');
define('BASE_URL', $protocol . '://' . $host . $dir);

/* --------- مسیر ریشه پروژه روی دیسک --------- */
define('APP_ROOT', dirname(__DIR__));
define('ADMIN_ROOT', APP_ROOT . '/admin');

/* --------- اطلاعات کلی فروشگاه (قابل ویرایش) --------- */
define('SITE_NAME',    'دیجی‌شاپ');
define('SITE_TAGLINE', 'فروشگاه اینترنتی دیجی‌شاپ');
define('SITE_PHONE',   '۰۲۱-۹۱۰۰۰۰۰۰');
define('SITE_EMAIL',   'support@digishop.ir');

/* --------- مسیرهای آپلود فایل --------- */
define('UPLOAD_PATH_PRODUCTS', APP_ROOT . '/assets/images/products');
define('UPLOAD_PATH_BANNERS',  APP_ROOT . '/assets/images/banners');
define('UPLOAD_PATH_BRANDS',   APP_ROOT . '/assets/images/brands');
define('UPLOAD_PATH_CATEGORIES', APP_ROOT . '/assets/images/categories');

/* --------- شروع نشست امن ---------
 * httponly باعث می‌شود کوکی نشست با جاوااسکریپت قابل دسترسی نباشد (امنیت بیشتر)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 7, // یک هفته
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('digishop_session');
    session_start();
}

/* --------- اتولودر خودکار کلاس‌ها ---------
 * کلاس‌های models و core بدون نیاز به require دستی بارگذاری می‌شوند
 */
spl_autoload_register(function (string $className): void {
    foreach ([APP_ROOT . '/models/', APP_ROOT . '/core/'] as $directory) {
        $file = $directory . $className . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
