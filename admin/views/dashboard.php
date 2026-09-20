<?php
/**
 * ===============================================
 * داشبورد مدیریت - آمار کلی + نمودار فروش + آخرین سفارش‌ها
 * متغیرها: $overview, $chartData, $latestOrders
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">داشبورد</h4>

<!-- ================= کارت‌های آماری ================= -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-danger-subtle">
            <div class="stat-icon text-danger"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value"><?= fa_price($overview['total_sales']) ?></div>
                <div class="stat-label">مجموع فروش</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-primary-subtle">
            <div class="stat-icon text-primary"><i class="bi bi-bag-check"></i></div>
            <div>
                <div class="stat-value"><?= fa_num($overview['orders_count']) ?></div>
                <div class="stat-label">تعداد سفارش‌ها</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-success-subtle">
            <div class="stat-icon text-success"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-value"><?= fa_num($overview['users_count']) ?></div>
                <div class="stat-label">کاربران ثبت‌نام شده</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-warning-subtle">
            <div class="stat-icon text-warning"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="stat-value"><?= fa_num($overview['products_count']) ?></div>
                <div class="stat-label">تعداد محصولات</div>
            </div>
        </div>
    </div>
</div>

<!-- هشدارهای مدیریتی -->
<?php if ($overview['pending_orders'] || $overview['pending_comments'] || $overview['low_stock']): ?>
<div class="alert alert-warning d-flex flex-wrap gap-4 align-items-center">
    <?php if ($overview['pending_orders']): ?>
        <span><i class="bi bi-exclamation-circle ms-1"></i><?= fa_num($overview['pending_orders']) ?> سفارش در انتظار پردازش</span>
    <?php endif; ?>
    <?php if ($overview['pending_comments']): ?>
        <span><i class="bi bi-chat-dots ms-1"></i><?= fa_num($overview['pending_comments']) ?> نظر در انتظار تایید</span>
    <?php endif; ?>
    <?php if ($overview['low_stock']): ?>
        <span><i class="bi bi-box ms-1"></i><?= fa_num($overview['low_stock']) ?> محصول کم‌موجود (۳ یا کمتر)</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- ================= نمودار فروش ================= -->
    <div class="col-lg-8">
        <div class="admin-card p-3">
            <h6 class="fw-bold mb-3">نمودار فروش ۱۴ روز اخیر (تومان)</h6>
            <canvas id="salesChart" height="110"></canvas>
        </div>
    </div>

    <!-- ================= آخرین سفارش‌ها ================= -->
    <div class="col-lg-4">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">آخرین سفارش‌ها</h6>
                <a href="<?= admin_url('orders') ?>" class="small">همه</a>
            </div>
            <?php if (empty($latestOrders)): ?>
                <p class="text-muted small mb-0">سفارشی ثبت نشده است.</p>
            <?php else: ?>
                <?php foreach ($latestOrders as $order): ?>
                    <a href="<?= admin_url('order-view', ['id' => $order['id']]) ?>"
                       class="d-flex justify-content-between align-items-center border-bottom py-2 text-decoration-none text-dark latest-order-row">
                        <div>
                            <div class="small fw-bold ltr-input-inline"><?= e($order['order_number']) ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= e($order['customer_name'] ?? 'کاربر حذف شده') ?></div>
                        </div>
                        <div class="text-end">
                            <div class="small fw-bold"><?= fa_price($order['final_amount']) ?></div>
                            <span class="badge text-bg-<?= order_status_class($order['status']) ?>" style="font-size:.65rem">
                                <?= order_status_label($order['status']) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ================= اسکریپت نمودار (Chart.js) ================= -->
<script src="<?= asset('vendor/chart.umd.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartData['labels'], JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'فروش (تومان)',
                data: <?= json_encode($chartData['values']) ?>,
                borderColor: '#ef4056',
                backgroundColor: 'rgba(239, 64, 86, 0.12)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#ef4056'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { font: { family: 'Vazirmatn' } },
                    grid: { color: '#f1f1f3' }
                },
                x: {
                    ticks: { font: { family: 'Vazirmatn' } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
