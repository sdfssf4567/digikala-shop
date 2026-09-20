<?php
/**
 * ===============================================
 * جزئیات سفارش کاربر - اقلام، آدرس تحویل و وضعیت
 * متغیرها: $order, $orderItems
 * ===============================================
 */
?>
<div class="container my-4">
    <nav class="breadcrumb-nav small mb-3">
        <a href="<?= url('home') ?>">دیجی‌شاپ</a> <span>/</span>
        <a href="<?= url('orders') ?>">سفارش‌های من</a> <span>/</span>
        <span class="text-muted"><?= e($order['order_number']) ?></span>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- اقلام سفارش -->
            <div class="border rounded-3 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">اقلام سفارش</h6>
                    <span class="badge text-bg-<?= order_status_class($order['status']) ?>">
                        <?= order_status_label($order['status']) ?>
                    </span>
                </div>

                <?php foreach ($orderItems as $item): ?>
                    <div class="d-flex align-items-center border-bottom py-3">
                        <img src="<?= BASE_URL ?>/<?= e(ltrim($item['image'] ?? 'assets/images/products/placeholder.svg', '/')) ?>"
                             class="cart-item-image rounded" alt="">
                        <div class="flex-grow-1 ms-3">
                            <?php if (!empty($item['product_id'])): ?>
                                <a href="<?= url('product', ['id' => $item['product_id']]) ?>"
                                   class="text-decoration-none d-block"><?= e($item['product_title']) ?></a>
                            <?php else: ?>
                                <span><?= e($item['product_title']) ?></span>
                            <?php endif; ?>
                            <div class="small text-muted mt-1">تعداد: <?= fa_num($item['quantity']) ?></div>
                        </div>
                        <div class="fw-bold"><?= fa_price($item['price'] * (int)$item['quantity']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- آدرس تحویل -->
            <div class="border rounded-3 bg-white p-4">
                <h6 class="fw-bold mb-3">اطلاعات ارسال</h6>
                <div class="row small">
                    <div class="col-md-6 mb-2"><strong>گیرنده:</strong> <?= e($order['receiver_name']) ?></div>
                    <div class="col-md-6 mb-2"><strong>موبایل:</strong> <span class="ltr-input-inline"><?= e($order['phone']) ?></span></div>
                    <div class="col-md-6 mb-2"><strong>استان:</strong> <?= e($order['province']) ?></div>
                    <div class="col-md-6 mb-2"><strong>شهر:</strong> <?= e($order['city']) ?></div>
                    <div class="col-12 mb-2"><strong>نشانی:</strong> <?= e($order['address']) ?></div>
                    <div class="col-md-6 mb-2"><strong>کد پستی:</strong> <span class="ltr-input-inline"><?= e($order['postal_code']) ?></span></div>
                    <div class="col-md-6 mb-2"><strong>روش پرداخت:</strong> <?= payment_method_label($order['payment_method']) ?></div>
                    <?php if ($order['note']): ?>
                        <div class="col-12"><strong>توضیحات:</strong> <?= e($order['note']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- خلاصه مالی -->
        <div class="col-lg-4">
            <div class="buy-box rounded-3 p-4">
                <h6 class="fw-bold mb-3">خلاصه سفارش</h6>
                <div class="d-flex justify-content-between small mb-2">
                    <span>تاریخ ثبت:</span>
                    <span><?= fa_datetime($order['created_at']) ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span>مبلغ کالاها:</span>
                    <span><?= fa_price($order['total_amount']) ?></span>
                </div>
                <?php if ((int)$order['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between small mb-2 text-success">
                        <span>تخفیف:</span>
                        <span><?= fa_price($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">مبلغ نهایی:</span>
                    <span class="fw-bold text-danger"><?= fa_price($order['final_amount']) ?></span>
                </div>

                <a href="<?= url('products') ?>" class="btn btn-outline-primary w-100 mt-4">ادامه خرید</a>
            </div>
        </div>
    </div>
</div>
