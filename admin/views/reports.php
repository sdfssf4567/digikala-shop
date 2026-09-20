<?php
/**
 * ===============================================
 * گزارش فروش پنل مدیریت
 * متغیرها: $overview, $topProducts, $salesByCat, $ordersByStatus, $chartData
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">گزارش فروش</h4>

<!-- ================= خلاصه آمار ================= -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-danger-subtle">
            <div class="stat-icon text-danger"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value"><?= fa_price($overview['total_sales']) ?></div>
                <div class="stat-label">مجموع فروش (بدون لغوشده‌ها)</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-primary-subtle">
            <div class="stat-icon text-primary"><i class="bi bi-bag-check"></i></div>
            <div>
                <div class="stat-value"><?= fa_num($overview['orders_count']) ?></div>
                <div class="stat-label">کل سفارش‌ها</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-info-subtle">
            <div class="stat-icon text-info"><i class="bi bi-truck"></i></div>
            <div>
                <div class="stat-value"><?= fa_num($overview['pending_orders']) ?></div>
                <div class="stat-label">سفارش‌های در انتظار</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-warning-subtle">
            <div class="stat-icon text-warning"><i class="bi bi-box"></i></div>
            <div>
                <div class="stat-value"><?= fa_num($overview['low_stock']) ?></div>
                <div class="stat-label">محصولات کم‌موجود</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- نمودار روند فروش -->
    <div class="col-lg-7">
        <div class="admin-card p-3 h-100">
            <h6 class="fw-bold mb-3">روند فروش ۱۴ روز اخیر (تومان)</h6>
            <canvas id="reportChart" height="130"></canvas>
        </div>
    </div>

    <!-- فروش به تفکیک وضعیت -->
    <div class="col-lg-5">
        <div class="admin-card p-3 h-100">
            <h6 class="fw-bold mb-3">سفارش‌ها به تفکیک وضعیت</h6>
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr class="small text-muted"><th>وضعیت</th><th>تعداد</th><th>مبلغ</th></tr>
                </thead>
                <tbody>
                <?php $allStatuses = ['pending', 'shipped', 'delivered', 'canceled']; ?>
                <?php foreach ($allStatuses as $st): $row = $ordersByStatus[$st] ?? null; ?>
                    <tr>
                        <td><span class="badge text-bg-<?= order_status_class($st) ?>"><?= order_status_label($st) ?></span></td>
                        <td class="small fw-bold"><?= fa_num($row['count'] ?? 0) ?></td>
                        <td class="small"><?= fa_price($row['total'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- پرفروش‌ترین محصولات -->
    <div class="col-lg-6">
        <div class="admin-card p-3 h-100">
            <h6 class="fw-bold mb-3">پرفروش‌ترین محصولات</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="small text-muted"><th>#</th><th>محصول</th><th>تعداد فروش</th><th>درآمد</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($topProducts)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3 small">هنوز فروشی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($topProducts as $i => $row): ?>
                        <tr>
                            <td class="small"><?= fa_num($i + 1) ?></td>
                            <td class="small"><?= e(str_limit($row['product_title'], 35)) ?></td>
                            <td class="small fw-bold"><?= fa_num($row['sold_qty']) ?></td>
                            <td class="small"><?= fa_price($row['revenue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- فروش به تفکیک دسته‌بندی -->
    <div class="col-lg-6">
        <div class="admin-card p-3 h-100">
            <h6 class="fw-bold mb-3">فروش به تفکیک دسته‌بندی</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="small text-muted"><th>دسته‌بندی</th><th>تعداد فروش</th><th>درآمد</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($salesByCat)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-3 small">هنوز فروشی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($salesByCat as $row): ?>
                        <tr>
                            <td class="small fw-bold"><?= e($row['category_name'] ?? 'بدون دسته') ?></td>
                            <td class="small"><?= fa_num($row['sold_qty']) ?></td>
                            <td class="small"><?= fa_price($row['revenue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- نمودار -->
<script src="<?= asset('vendor/chart.umd.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('reportChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartData['labels'], JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'فروش (تومان)',
                data: <?= json_encode($chartData['values']) ?>,
                backgroundColor: '#ef4056',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { family: 'Vazirmatn' } }, grid: { color: '#f1f1f3' } },
                x: { ticks: { font: { family: 'Vazirmatn' } }, grid: { display: false } }
            }
        }
    });
});
</script>
