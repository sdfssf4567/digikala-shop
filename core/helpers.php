<?php
/**
 * ===============================================
 * توابع کمکی سراسری پروژه
 * شامل: امنیت، CSRF، تبدیل اعداد فارسی، تاریخ شمسی،
 * مدیریت نشست، پیام‌های فلش و توابع URL
 * ===============================================
 */

/* =========================================
 * توابع امنیتی
 * ========================================= */

/**
 * تبدیل کاراکترهای خاص HTML برای جلوگیری از حمله XSS
 * تمام خروجی‌های دیتابیس باید از این تابع عبور کنند
 */
function e(?string $text): string
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

/**
 * تولید توکن CSRF و ذخیره آن در نشست
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * ساخت فیلد مخفی CSRF برای فرم‌ها
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * اعتبارسنجی توکن CSRF (در تمام ارسال‌های POST الزامی است)
 */
function verify_csrf(): bool
{
    $sent = $_POST['csrf_token'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
}

/**
 * بررسی CSRF و در صورت نامعتبر بودن، قطع عملیات
 */
function csrf_check_or_die(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verify_csrf()) {
        http_response_code(419);
        die('خطای امنیتی: توکن CSRF نامعتبر است. لطفاً صفحه را رفرش کنید.');
    }
}

/* =========================================
 * توابع ساخت آدرس
 * ========================================= */

/**
 * ساخت آدرس صفحات فروشگاه
 * مثال: url('product', ['id' => 5]) → index.php?route=product&id=5
 */
function url(string $route = '', array $params = []): string
{
    if ($route !== '') {
        $params = array_merge(['route' => $route], $params);
    }
    return BASE_URL . '/index.php' . ($params ? '?' . http_build_query($params) : '');
}

/**
 * ساخت آدرس صفحات پنل مدیریت
 */
function admin_url(string $page = '', array $params = []): string
{
    if ($page !== '') {
        $params = array_merge(['page' => $page], $params);
    }
    return BASE_URL . '/admin/index.php' . ($params ? '?' . http_build_query($params) : '');
}

/**
 * ساخت آدرس فایل‌های استاتیک (css / js / تصاویر)
 */
function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * ریدایرکت به آدرس مورد نظر
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * ریدایرکت به صفحه ورود اگر کاربر لاگین نباشد
 */
function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect(url('login'));
    }
}

/* =========================================
 * توابع نشست و کاربر
 * ========================================= */

/**
 * آیا کاربر وارد شده است؟
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * آیا کاربر جاری مدیر سایت است؟
 */
function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * اطلاعات کاربر وارد شده (از دیتابیس)
 */
function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    static $user = null;
    if ($user === null) {
        $db = Database::getInstance();
        $user = $db->fetch('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
        if (!$user) { // کاربر حذف شده است
            session_destroy();
            return null;
        }
    }
    return $user;
}

/* =========================================
 * پیام‌های فلش (یکبار نمایش)
 * ========================================= */

/**
 * ذخیره پیام فلش در نشست
 * نوع‌ها: success / danger / warning / info
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * دریافت و پاک کردن پیام‌های فلش
 */
function get_flash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * ذخیره مقدار قبلی فرم (برای پر کردن مجدد بعد از خطا)
 */
function old(string $key, $default = '')
{
    return e($_SESSION['old'][$key] ?? $default);
}

/**
 * ذخیره مقادیر فرم برای استفاده مجدد
 */
function set_old(array $data): void
{
    $_SESSION['old'] = $data;
}

/**
 * پاک کردن مقادیر قدیمی فرم
 */
function clear_old(): void
{
    unset($_SESSION['old']);
}

/* =========================================
 * توابع اعداد و قیمت فارسی
 * ========================================= */

/**
 * تبدیل ارقام انگلیسی به فارسی
 */
function fa_num($text): string
{
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return str_replace($en, $fa, (string)$text);
}

/**
 * تبدیل ارقام فارسی به انگلیسی (برای اعتبارسنجی ورودی کاربر)
 */
function en_num(string $text): string
{
    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    return str_replace($ar, $en, str_replace($fa, $en, $text));
}

/**
 * قیمت با جداکننده هزارگان و ارقام فارسی
 */
function fa_price($number): string
{
    return fa_num(number_format((float)$number)) . ' تومان';
}

/**
 * محاسبه قیمت نهایی پس از تخفیف
 */
function discounted_price($price, $discountPercent): int
{
    $price = (int)$price;
    $discountPercent = max(0, min(100, (int)$discountPercent));
    if ($discountPercent === 0) {
        return $price;
    }
    return (int)round($price * (100 - $discountPercent) / 100);
}

/* =========================================
 * تاریخ شمسی (جلالی)
 * ========================================= */

/**
 * تبدیل تاریخ میلادی به شمسی - الگوریتم استاندارد
 */
function gregorian_to_jalali(int $gy, int $gm, int $gd): array
{
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    $jy = ($gy <= 1600) ? 0 : 979;
    $gy -= ($gy <= 1600) ? 621 : 1600;
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100)
          + intdiv($gy2 + 399, 400) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * intdiv($days, 12053);
    $days %= 12053;
    $jy += 4 * intdiv($days, 1461);
    $days %= 1461;
    $jy += intdiv($days - 1, 365);
    if ($days > 365) {
        $days = ($days - 1) % 365;
    }
    $jm = ($days < 186) ? 1 + intdiv($days, 31) : 7 + intdiv($days - 186, 30);
    $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
    return [$jy, $jm, $jd];
}

/**
 * تاریخ شمسی خوانا از روی تاریخ میلادی دیتابیس
 * مثال خروجی: ۲۹ شهریور ۱۴۰۴
 */
function fa_date(?string $mysqlDate): string
{
    if (empty($mysqlDate)) {
        return '-';
    }
    $ts = strtotime($mysqlDate);
    if ($ts === false) {
        return '-';
    }
    [$jy, $jm, $jd] = gregorian_to_jalali((int)date('Y', $ts), (int)date('n', $ts), (int)date('j', $ts));
    $months = ['', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
               'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    return fa_num($jd) . ' ' . $months[$jm] . ' ' . fa_num($jy);
}

/**
 * تاریخ و ساعت شمسی
 * مثال خروجی: ۲۹ شهریور ۱۴۰۴ ساعت ۱۴:۳۰
 */
function fa_datetime(?string $mysqlDate): string
{
    if (empty($mysqlDate)) {
        return '-';
    }
    $ts = strtotime($mysqlDate);
    if ($ts === false) {
        return '-';
    }
    return fa_date($mysqlDate) . ' ساعت ' . fa_num(date('H:i', $ts));
}

/* =========================================
 * توابع عمومی کمکی
 * ========================================= */

/**
 * برش متن طولانی با افزودن سه نقطه
 */
function str_limit(?string $text, int $limit = 100): string
{
    $text = trim((string)$text);
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '…';
}

/**
 * فقط حروف و اعداد مجاز است (برای slug و...)
 */
function slugify(string $text): string
{
    $text = trim(mb_strtolower($text));
    // جایگزینی فاصله و کاراکترهای نامجاز با خط تیره
    $text = preg_replace('/[^a-z0-9\x{0600}-\x{06FF}]+/u', '-', $text);
    return trim($text, '-') ?: 'item-' . time();
}

/**
 * دریافت و پاکسازی ورودی GET/POST
 */
function input(string $key, $default = '', int $filter = INPUT_GET)
{
    $value = filter_input($filter, $key, FILTER_DEFAULT) ?? $default;
    return trim((string)$value);
}

/**
 * برچسب وضعیت سفارش به فارسی
 */
function order_status_label(string $status): string
{
    $labels = [
        'pending'   => 'در انتظار پردازش',
        'shipped'   => 'ارسال شده',
        'delivered' => 'تحویل داده شده',
        'canceled'  => 'لغو شده',
    ];
    return $labels[$status] ?? $status;
}

/**
 * کلاس رنگ بوت‌استرپ برای وضعیت سفارش
 */
function order_status_class(string $status): string
{
    $classes = [
        'pending'   => 'warning',
        'shipped'   => 'info',
        'delivered' => 'success',
        'canceled'  => 'danger',
    ];
    return $classes[$status] ?? 'secondary';
}

/**
 * برچسب روش پرداخت به فارسی
 */
function payment_method_label(string $method): string
{
    return $method === 'cod' ? 'پرداخت در محل' : 'پرداخت آنلاین';
}

/**
 * پاسخ JSON برای درخواست‌های Ajax
 */
function json_response(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
