<?php
/**
 * ===============================================
 * سربرگ مشترک همه صفحات فروشگاه
 * شامل منو، جستجوی زنده، آیکون سبد خرید و حساب کاربری
 * ===============================================
 */

$pageTitle  = $pageTitle ?? SITE_TAGLINE;
$flash      = $flash ?? get_flash();
$categories = $categories ?? null;

// اگر دسته‌بندی‌ها قبلاً در کنترلر آماده نشده باشند، از دیتابیس خوانده می‌شود
if ($categories === null) {
    $categories = Category::mainCategories();
}

// تعداد اقلام سبد خرید برای نمایش در آیکون
$cartCount = is_logged_in() ? Cart::count((int)$_SESSION['user_id']) : 0;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(SITE_TAGLINE) ?> - خرید آنلاین با بهترین قیمت">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>

    <!-- بوت‌استرپ ۵ نسخه راست‌چین (به صورت محلی) -->
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap.rtl.min.css') ?>">
    <!-- آیکون‌های بوت‌استرپ -->
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap-icons.css') ?>">
    <!-- فونت وزیرمتن (محلی) -->
    <link rel="stylesheet" href="<?= asset('css/fonts.css') ?>">
    <!-- استایل اختصاصی فروشگاه -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<!-- ================= نوار بالای سایت ================= -->
<div class="top-bar text-white py-1">
    <div class="container d-flex justify-content-between align-items-center small">
        <span><i class="bi bi-truck ms-1"></i> امکان تحویل اکسپرس در تهران و کرج</span>
        <span class="d-none d-md-inline">
            <i class="bi bi-telephone ms-1"></i> <?= e(SITE_PHONE) ?>
            <span class="mx-2">|</span>
            <i class="bi bi-envelope ms-1"></i> <?= e(SITE_EMAIL) ?>
        </span>
    </div>
</div>

<!-- ================= هدر اصلی ================= -->
<header class="site-header bg-white border-bottom sticky-top">
    <div class="container py-3">
        <div class="row g-3 align-items-center">

            <!-- لوگو -->
            <div class="col-auto">
                <a href="<?= url('home') ?>" class="logo-link d-flex align-items-center text-decoration-none">
                    <span class="logo-icon"><i class="bi bi-shop"></i></span>
                    <span class="logo-text fs-4 fw-bold"><?= e(SITE_NAME) ?></span>
                </a>
            </div>

            <!-- باکس جستجوی زنده -->
            <div class="col-md col-12 order-md-2 order-3">
                <div class="search-box position-relative">
                    <form action="<?= url('products') ?>" method="get" autocomplete="off">
                        <input type="hidden" name="route" value="products">
                        <input type="text" id="live-search-input" name="q"
                               class="form-control search-input"
                               placeholder="جستجو در محصولات..."
                               value="<?= e($_GET['q'] ?? '') ?>">
                        <i class="bi bi-search search-icon"></i>
                    </form>
                    <!-- نتایج جستجوی زنده در این باکس نمایش داده می‌شود -->
                    <div id="live-search-results" class="live-results shadow d-none"></div>
                </div>
            </div>

            <!-- دکمه‌های ورود و سبد خرید -->
            <div class="col-auto order-md-3">
                <div class="d-flex align-items-center gap-2">
                    <?php if (is_logged_in()): ?>
                        <!-- منوی حساب کاربری -->
                        <div class="dropdown">
                            <button class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle ms-1"></i>
                                <span class="d-none d-sm-inline"><?= e($_SESSION['user_name'] ?? '') ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start">
                                <li><a class="dropdown-item" href="<?= url('profile') ?>"><i class="bi bi-person ms-2"></i> حساب کاربری</a></li>
                                <li><a class="dropdown-item" href="<?= url('orders') ?>"><i class="bi bi-box-seam ms-2"></i> سفارش‌های من</a></li>
                                <li><a class="dropdown-item" href="<?= url('wishlist') ?>"><i class="bi bi-heart ms-2"></i> علاقه‌مندی‌ها</a></li>
                                <?php if (is_admin()): ?>
                                    <li><a class="dropdown-item" href="<?= admin_url('dashboard') ?>"><i class="bi bi-speedometer2 ms-2"></i> پنل مدیریت</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-left ms-2"></i> خروج</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-box-arrow-in-left ms-1"></i> ورود
                        </a>
                        <a href="<?= url('register') ?>" class="btn btn-primary btn-sm d-none d-sm-inline-block">
                            <i class="bi bi-person-plus ms-1"></i> ثبت‌نام
                        </a>
                    <?php endif; ?>

                    <!-- آیکون سبد خرید با شمارنده -->
                    <a href="<?= url('cart') ?>" class="btn btn-light position-relative border" title="سبد خرید">
                        <i class="bi bi-basket2 fs-5"></i>
                        <span id="cart-count-badge"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?= $cartCount ? '' : 'd-none' ?>">
                            <?= fa_num($cartCount) ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= منوی دسته‌بندی‌ها ================= -->
    <nav class="main-menu border-top">
        <div class="container">
            <ul class="nav nav-pills flex-nowrap overflow-auto py-2 main-menu-list">
                <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap"
                           href="<?= url('products', ['cat' => $cat['slug']]) ?>">
                            <i class="bi <?= e($cat['icon'] ?: 'bi-grid') ?> ms-1"></i>
                            <?= e($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item">
                    <a class="nav-link text-nowrap text-danger fw-bold" href="<?= url('products', ['sort' => 'discount']) ?>">
                        <i class="bi bi-percent ms-1"></i> تخفیف‌ها و پیشنهادها
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<!-- توکن CSRF سراسری برای درخواست‌های Ajax (سبد خرید و علاقه‌مندی) -->
<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

<!-- ================= پیام‌های فلش ================= -->
<div class="container mt-3" id="flash-area">
    <?php foreach ($flash as $msg): ?>
        <div class="alert alert-<?= e($msg['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($msg['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
</div>
