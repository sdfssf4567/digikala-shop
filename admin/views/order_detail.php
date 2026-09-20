<?php
/**
 * ===============================================
 * جزئیات سفارش در پنل مدیریت
 * متغیرها: $order, $orderItems
 * ===============================================
 */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h4 class="fw-bold mb-0">سفارش <span class="ltr-input-inline"><?= e($order['order_number']) ?></span></h4>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge text-bg-<?= order_status_class($order['status']) ?> fs-6"><?= order_status_label($order['status']) ?></span>
        <a href="<?= admin_url('orders') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right ms-1"></i> بازگشت
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- اقلام سفارش -->
    <div class="col-lg-8">
        <div class="admin-card p-4 mb-4">
            <h6 class="fw-bold mb-3">اقلام سفارش</h6>
            <?php foreach ($orderItems as $item): ?>
                <div class="d-flex align-items-center border-bottom py-3">
                    <img src="<?= BASE_URL ?>/<?= e(ltrim($item['image'] ?? 'assets/images/products/placeholder.svg', '/')) ?>"
                         class="admin-product-thumb rounded" alt="">
                    <div class="flex-grow-1 ms-3">
                        <div class="small fw-bold"><?= e($item['product_title']) ?></div>
                        <div class="text-muted" style="font-size:.75rem">تعداد: <?= fa_num($item['quantity']) ?></div>
                    </div>
                    <div class="fw-bold small"><?= fa_price($item['price'] * (int)$item['quantity']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- اطلاعات گیرنده -->
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3">اطلاعات گیرنده و آدرس</h6>
            <div class="row small">
                <div class="col-md-6 mb-2"><strong>نام مشتری:</strong> <?= e($order['customer_name'] ?? '—') ?></div>
                <div class="col-md-6 mb-2"><strong>گیرنده:</strong> <?= e($order['receiver_name']) ?></div>
                <div class="col-md-6 mb-2"><strong>موبایل گیرنده:</strong> <span class="ltr-input-inline"><?= e($order['phone']) ?></span></div>
                <div class="col-md-6 mb-2"><strong>کد پستی:</strong> <span class="ltr-input-inline"><?= e($order['postal_code']) ?></span></div>
                <div class="col-md-6 mb-2"><strong>استان:</strong> <?= e($order['province']) ?></div>
                <div class="col-md-6 mb-2"><strong>شهر:</strong> <?= e($order['city']) ?></div>
                <div class="col-12 mb-2"><strong>نشانی:</strong> <?= e($order['address']) ?></div>
                <?php if ($order['note']): ?>
                    <div class="col-12"><strong>توضیحات مشتری:</strong> <?= e($order['note']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- خلاصه مالی -->
    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3">خلاصه مالی</h6>
            <div class="d-flex justify-content-between small mb-2">
                <span>تاریخ ثبت:</span><span><?= fa_datetime($order['created_at']) ?></span>
            </div>
            <div class="d-flex justify-content-between small mb-2">
                <span>مبلغ کالاها:</span><span><?= fa_price($order['total_amount']) ?></span>
            </div>
            <?php if ((int)$order['discount_amount'] > 0): ?>
                <div class="d-flex justify-content-between small mb-2 text-success">
                    <span>سود مشتری:</span><span><?= fa_price($order['discount_amount']) ?></span>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between small mb-2">
                <span>روش پرداخت:</span><span><?= payment_method_label($order['payment_method']) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span class="fw-bold">مبلغ نهایی:</span>
                <span class="fw-bold text-danger fs-5"><?= fa_price($order['final_amount']) ?></span>
            </div>
        </div>
    </div>
</div>
