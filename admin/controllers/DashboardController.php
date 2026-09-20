<?php
/**
 * ===============================================
 * کنترلر داشبورد مدیریت
 * نمایش آمار کلی و نمودار فروش ۱۴ روز اخیر
 * ===============================================
 */

class DashboardController
{
    public function index(): void
    {
        $overview  = Stats::overview();
        $chartData = Stats::salesLast14Days();
        $latestOrders = Stats::latestOrders(8);

        $pageTitle = 'داشبورد';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/dashboard.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }
}
