<?php
/**
 * ===============================================
 * کنترل‌کننده اصلی پنل مدیریت (Front Controller)
 * آدرس‌دهی: admin/index.php?page=نام صفحه
 * دسترسی به این بخش فقط برای کاربران با نقش «مدیر» مجاز است
 * ===============================================
 */

require_once __DIR__ . '/../config/config.php';
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/core/helpers.php';

/* --------- کنترل دسترسی: فقط مدیر --------- */
if (!is_logged_in()) {
    redirect(url('login'));
}
if (!is_admin()) {
    set_flash('danger', 'شما به پنل مدیریت دسترسی ندارید.');
    redirect(url('home'));
}

/* --------- جدول صفحات پنل مدیریت --------- */
$pages = [
    'dashboard'     => 'DashboardController@index',
    'products'      => 'ProductController@list',
    'product-edit'  => 'ProductController@edit',
    'product-save'  => 'ProductController@save',
    'product-delete'=> 'ProductController@delete',
    'image-delete'  => 'ProductController@deleteImage',
    'categories'    => 'CategoryController@index',
    'category-save' => 'CategoryController@save',
    'category-delete' => 'CategoryController@delete',
    'orders'        => 'OrderController@list',
    'order-view'    => 'OrderController@view',
    'order-status'  => 'OrderController@changeStatus',
    'users'         => 'UserController@list',
    'user-delete'   => 'UserController@delete',
    'comments'      => 'CommentController@list',
    'comment-status'=> 'CommentController@changeStatus',
    'comment-delete'=> 'CommentController@delete',
    'banners'       => 'BannerController@index',
    'banner-save'   => 'BannerController@save',
    'banner-delete' => 'BannerController@delete',
    'banner-toggle' => 'BannerController@toggle',
    'reports'       => 'ReportController@index',
];

/* --------- تشخیص صفحه درخواستی --------- */
$page = $_GET['page'] ?? 'dashboard';

if (!isset($pages[$page])) {
    http_response_code(404);
    die('صفحه مدیریتی مورد نظر یافت نشد.');
}

/* --------- اجرای کنترلر --------- */
[$controllerName, $method] = explode('@', $pages[$page]);
$controllerFile = ADMIN_ROOT . '/controllers/' . $controllerName . '.php';

if (!is_file($controllerFile)) {
    die('خطای سیستمی: فایل کنترلر مدیریتی یافت نشد.');
}

require_once $controllerFile;

try {
    (new $controllerName())->{$method}();
} catch (Throwable $e) {
    if (DEBUG) {
        die('خطا: ' . htmlspecialchars($e->getMessage()) . '<br>فایل: ' . htmlspecialchars($e->getFile()) . ' خط ' . $e->getLine());
    }
    error_log('Admin Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    die('خطای سیستمی رخ داد. لطفاً بعداً تلاش کنید.');
}
