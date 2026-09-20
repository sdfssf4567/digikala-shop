<?php
/**
 * ===============================================
 * سربرگ مشترک همه صفحات فروشگاه — نسخه ۲.۰
 * هدر حرفه‌ای: لوگو، جستجوی زنده، حساب کاربری، سبد خرید
 * ===============================================
 */

$pageTitle  = $pageTitle ?? SITE_TAGLINE;
$flash      = $flash ?? get_flash();
$categories = $categories ?? null;
$currentRoute = $_GET['route'] ?? 'home';

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
    <meta name="description" content="<?= e(SITE_TAGLINE) ?> - خرید آنلاین با بهترین قیمت و ضمانت اصل بودن کالا">
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

<!-- ================= نوار اطلاع‌رسانی ================= -->
<div class="top-bar text-white py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-truck ms-1"></i> امکان تحویل اکسپرس در تهران و کرج</span>
        <span class="d-none d-md-inline">
            <i class="bi bi-telephone ms-1"></i> <?= e(SITE_PHONE) ?>
            <span class="mx-2 opacity-50">|</span>
            <i class="bi bi-envelope ms-1"></i> <?= e(SITE_EMAIL) ?>
        </span>
    </div>
</div>

<!-- ================= هدر اصلی ================= -->
<header class="site-header bg-white sticky-top">
    <div class="container py-3">
        <div class="d-flex align-items-center gap-3">

            <!-- لوگو -->
            <a href="<?= url('home') ?>" class="logo-link d-flex align-items-center flex-shrink-0">
                <span class="logo-icon"><i class="bi bi-shop"></i></span>
                <span class="logo-text d-none d-sm-inline"><?= e(SITE_NAME) ?></span>
            </a>

            <!-- باکس جستجوی زنده -->
            <div class="flex-grow-1 search-box position-relative">
                <form action="<?= url('products') ?>" method="get" autocomplete="off">
                    <input type="hidden" name="route" value="products">
                    <input type="text" id="live-search-input" name="q"
                           class="form-control search-input"
                           placeholder="جستجو در بین هزاران کالا..."
                           value="<?= e($_GET['q'] ?? '') ?>">
                    <i class="bi bi-search search-icon"></i>
                </form>
                <!-- نتایج جستجوی زنده در این باکس نمایش داده می‌شود -->
                <div id="live-search-results" class="live-results d-none"></div>
            </div>

            <!-- دکمه‌های حساب و سبد خرید -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <?php if (is_logged_in()): ?>
                    <!-- منوی حساب کاربری -->
                    <div class="dropdown">
                        <button class="account-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span class="account-label"><?= e($_SESSION['user_name'] ?? 'حساب کاربری') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start shadow-sm rounded-3" style="min-width:220px">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small"><?= e($_SESSION['user_name'] ?? '') ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?= e($_SESSION['user_email'] ?? '') ?></div>
                            </li>
                            <li><a class="dropdown-item py-2" href="<?= url('profile') ?>"><i class="bi bi-person ms-2"></i> حساب کاربری</a></li>
                            <li><a class="dropdown-item py-2" href="<?= url('orders') ?>"><i class="bi bi-box-seam ms-2"></i> سفارش‌های من</a></li>
                            <li><a class="dropdown-item py-2" href="<?= url('wishlist') ?>"><i class="bi bi-heart ms-2"></i> علاقه‌مندی‌ها</a></li>
                            <?php if (is_admin()): ?>
                                <li><a class="dropdown-item py-2" href="<?= admin_url('dashboard') ?>"><i class="bi bi-speedometer2 ms-2"></i> پنل مدیریت</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-left ms-2"></i> خروج از حساب</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="account-btn">
                        <i class="bi bi-person"></i>
                        <span class="account-label">ورود | ثبت‌نام</span>
                    </a>
                <?php endif; ?>

                <span class="d-none d-md-block" style="width:1px;height:26px;background:#e3e5ec"></span>

                <!-- آیکون سبد خرید با شمارنده -->
                <a href="<?= url('cart') ?>" class="cart-btn" title="سبد خرید">
                    <i class="bi bi-basket2"></i>
                    <span id="cart-count-badge" class="cart-count-badge <?= $cartCount ? '' : 'd-none' ?>">
                        <?= fa_num($cartCount) ?>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- ================= منوی دسته‌بندی‌ها ================= -->
    <nav class="main-menu border-top">
        <div class="container">
            <ul class="nav nav-pills flex-nowrap overflow-auto py-2 main-menu-list">
                <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap <?= ($currentRoute === 'products' && ($_GET['cat'] ?? '') === $cat['slug']) ? 'active' : '' ?>"
                           href="<?= url('products', ['cat' => $cat['slug']]) ?>">
                            <i class="bi <?= e($cat['icon'] ?: 'bi-grid') ?> ms-1"></i>
                            <?= e($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item">
                    <a class="nav-link text-nowrap text-danger fw-bold" href="<?= url('products', ['sort' => 'discount']) ?>">
                        <i class="bi bi-lightning-charge-fill ms-1"></i> تخفیف‌ها و پیشنهادها
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
        <div class="alert alert-<?= e($msg['type']) ?> alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:12px">
            <?= e($msg['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
</div>
