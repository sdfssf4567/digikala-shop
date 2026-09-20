<?php
/**
 * ===============================================
 * سربرگ مشترک صفحات پنل مدیریت
 * ===============================================
 */

$pageTitle = $pageTitle ?? 'پنل مدیریت';
$flash     = $flash ?? get_flash();
// صفحه جاری برای هایلایت منو (مستقل از اسکوپ کنترلر)
$page      = $_GET['page'] ?? 'dashboard';

// شمارنده‌های نشان‌دار منو
$pendingComments = Comment::pendingCount();
$pendingOrders   = (int)Database::getInstance()->fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | پنل مدیریت <?= e(SITE_NAME) ?></title>

    <link rel="stylesheet" href="<?= asset('vendor/bootstrap.rtl.min.css') ?>"
          onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css'">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap-icons.css') ?>"
          onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'">
    <link rel="stylesheet" href="<?= asset('css/fonts.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">

<!-- ================= نوار بالای پنل ================= -->
<nav class="admin-topbar navbar navbar-dark px-3">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebar-toggle">
            <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand mb-0" href="<?= admin_url('dashboard') ?>">
            <i class="bi bi-speedometer2 ms-2"></i>پنل مدیریت <?= e(SITE_NAME) ?>
        </a>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="<?= url('home') ?>" target="_blank" class="text-white text-decoration-none small">
            <i class="bi bi-box-arrow-up-left ms-1"></i> مشاهده سایت
        </a>
        <span class="text-white-50 small d-none d-md-inline">
            <i class="bi bi-person-circle ms-1"></i><?= e($_SESSION['user_name'] ?? 'مدیر') ?>
        </span>
        <a href="<?= url('logout') ?>" class="btn btn-sm btn-outline-light">
            <i class="bi bi-box-arrow-left ms-1"></i> خروج
        </a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- ================= منوی کنار پنل ================= -->
        <aside class="col-lg-2 col-md-3 admin-sidebar p-0" id="admin-sidebar">
            <ul class="admin-menu list-unstyled p-3 mb-0">
                <li>
                    <a href="<?= admin_url('dashboard') ?>" class="<?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 ms-2"></i> داشبورد
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('products') ?>" class="<?= str_starts_with($page ?? '', 'product') ? 'active' : '' ?>">
                        <i class="bi bi-box-seam ms-2"></i> محصولات
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('categories') ?>" class="<?= str_starts_with($page ?? '', 'categor') ? 'active' : '' ?>">
                        <i class="bi bi-grid ms-2"></i> دسته‌بندی‌ها
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('orders') ?>" class="<?= str_starts_with($page ?? '', 'order') ? 'active' : '' ?>">
                        <i class="bi bi-bag-check ms-2"></i> سفارش‌ها
                        <?php if ($pendingOrders): ?>
                            <span class="badge rounded-pill text-bg-danger"><?= fa_num($pendingOrders) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('users') ?>" class="<?= str_starts_with($page ?? '', 'user') ? 'active' : '' ?>">
                        <i class="bi bi-people ms-2"></i> کاربران
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('comments') ?>" class="<?= str_starts_with($page ?? '', 'comment') ? 'active' : '' ?>">
                        <i class="bi bi-chat-square-text ms-2"></i> نظرات
                        <?php if ($pendingComments): ?>
                            <span class="badge rounded-pill text-bg-warning"><?= fa_num($pendingComments) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('banners') ?>" class="<?= str_starts_with($page ?? '', 'banner') ? 'active' : '' ?>">
                        <i class="bi bi-image ms-2"></i> بنرها و اسلایدر
                    </a>
                </li>
                <li>
                    <a href="<?= admin_url('reports') ?>" class="<?= ($page ?? '') === 'reports' ? 'active' : '' ?>">
                        <i class="bi bi-bar-chart-line ms-2"></i> گزارش فروش
                    </a>
                </li>
            </ul>
        </aside>

        <!-- ================= محتوای صفحه ================= -->
        <main class="col-lg-10 col-md-9 admin-content p-4">

            <!-- پیام‌های فلش -->
            <?php foreach ($flash as $msg): ?>
                <div class="alert alert-<?= e($msg['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($msg['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
