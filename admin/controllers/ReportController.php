<?php
/**
 * ===============================================
 * کنترلر گزارش فروش در پنل ادمین
 * ===============================================
 */

class ReportController
{
    public function index(): void
    {
        $overview      = Stats::overview();
        $topProducts   = Stats::topProducts(10);
        $salesByCat    = Stats::salesByCategory();
        $ordersByStatus = Stats::ordersByStatus();
        $chartData     = Stats::salesLast14Days();

        $pageTitle = 'گزارش فروش';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/reports.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }
}
