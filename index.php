<?php
/**
 * ===============================================
 * فایل اصلی و نقطه ورود فروشگاه (Front Controller)
 * تمام درخواست‌های کاربران از طریق این فایل مسیریابی می‌شوند
 * آدرس‌دهی به شکل index.php?route=نام‌مسیر انجام می‌شود
 * ===============================================
 */

require_once __DIR__ . '/config/config.php';
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/core/helpers.php';

/* --------- جدول مسیرهای فروشگاه ---------
 * کلید: نام مسیر | مقدار: کنترلر@متد
 */
$routes = [
    // صفحه اصلی
    'home'            => 'HomeController@index',

    // محصولات و جستجو
    'products'        => 'ProductController@index',
    'product'         => 'ProductController@show',
    'search/live'     => 'SearchController@live',

    // سبد خرید
    'cart'            => 'CartController@index',
    'cart/add'        => 'CartController@add',
    'cart/update'     => 'CartController@update',
    'cart/remove'     => 'CartController@remove',
    'cart/count'      => 'CartController@count',

    // فرایند خرید
    'checkout'        => 'CheckoutController@index',
    'checkout/submit' => 'CheckoutController@submit',

    // حساب کاربری
    'login'           => 'AuthController@loginForm',
    'login/submit'    => 'AuthController@login',
    'register'        => 'AuthController@registerForm',
    'register/submit' => 'AuthController@register',
    'logout'          => 'AuthController@logout',
    'profile'         => 'UserController@profile',
    'profile/update'  => 'UserController@updateProfile',
    'orders'          => 'UserController@orders',
    'order/view'      => 'UserController@orderDetail',

    // علاقه‌مندی‌ها
    'wishlist'        => 'WishlistController@index',
    'wishlist/toggle' => 'WishlistController@toggle',

    // نظرات
    'comment/add'     => 'CommentController@add',
];

/* --------- تشخیص مسیر درخواستی --------- */
$route = $_GET['route'] ?? 'home';
$route = rtrim(trim($route), '/');

// اگر مسیر مجاز نبود، صفحه ۴۰۴ نمایش داده می‌شود
if (!isset($routes[$route])) {
    http_response_code(404);
    $pageTitle = 'صفحه پیدا نشد';
    $flash = get_flash();
    include APP_ROOT . '/views/layouts/header.php';
    include APP_ROOT . '/views/error.php';
    include APP_ROOT . '/views/layouts/footer.php';
    exit;
}

/* --------- اجرای کنترلر مربوطه --------- */
[$controllerName, $method] = explode('@', $routes[$route]);
$controllerFile = APP_ROOT . '/controllers/' . $controllerName . '.php';

if (!is_file($controllerFile)) {
    die('خطای سیستمی: فایل کنترلر یافت نشد.');
}

require_once $controllerFile;

try {
    $controller = new $controllerName();
    $controller->{$method}();
} catch (Throwable $e) {
    // مدیریت خطاهای پیش‌بینی نشده
    if (DEBUG) {
        die('خطا: ' . htmlspecialchars($e->getMessage()) . '<br>فایل: ' . htmlspecialchars($e->getFile()) . ' خط ' . $e->getLine());
    }
    error_log('Shop Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    $pageTitle = 'خطای سیستمی';
    $flash = get_flash();
    include APP_ROOT . '/views/layouts/header.php';
    include APP_ROOT . '/views/error.php';
    include APP_ROOT . '/views/layouts/footer.php';
}
