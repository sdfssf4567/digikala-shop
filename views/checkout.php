<?php
/**
 * ===============================================
 * صفحه تسویه حساب (مرحله دوم و سوم خرید)
 * اطلاعات ارسال + روش پرداخت
 * ===============================================
 */

// بازیابی خطاهای فرم از نشست
$errors = $_SESSION['old']['errors'] ?? [];
unset($_SESSION['old']['errors']);
?>
<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-credit-card ms-2"></i>تکمیل و پرداخت سفارش</h4>

    <!-- نشانگر مراحل خرید -->
    <div class="checkout-steps mb-4">
        <div class="step">سبد خرید</div>
        <div class="step active">اطلاعات ارسال</div>
        <div class="step">پرداخت و ثبت</div>
    </div>

    <form method="post" action="<?= url('checkout/submit') ?>" class="row g-4 needs-validation">
        <?= csrf_field() ?>

        <!-- ================= فرم اطلاعات ارسال ================= -->
        <div class="col-lg-8">
            <div class="checkout-card p-4 rounded-3 bg-white border">
                <h6 class="fw-bold mb-3"><i class="bi bi-truck ms-2"></i>اطلاعات گیرنده و آدرس تحویل</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small">نام و نام خانوادگی گیرنده *</label>
                        <input type="text" name="receiver_name" class="form-control <?= isset($errors['receiver_name']) ? 'is-invalid' : '' ?>"
                               value="<?= old('receiver_name') ?>" required>
                        <?php if (isset($errors['receiver_name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['receiver_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">شماره موبایل گیرنده *</label>
                        <input type="text" name="phone" class="form-control ltr-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                               placeholder="09xxxxxxxxx" value="<?= old('phone') ?>" required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">استان *</label>
                        <input type="text" name="province" class="form-control <?= isset($errors['province']) ? 'is-invalid' : '' ?>"
                               value="<?= old('province', 'تهران') ?>" required>
                        <?php if (isset($errors['province'])): ?>
                            <div class="invalid-feedback"><?= e($errors['province']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">شهر *</label>
                        <input type="text" name="city" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>"
                               value="<?= old('city', 'تهران') ?>" required>
                        <?php if (isset($errors['city'])): ?>
                            <div class="invalid-feedback"><?= e($errors['city']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">کد پستی (۱۰ رقم) *</label>
                        <input type="text" name="postal_code" class="form-control ltr-input <?= isset($errors['postal_code']) ? 'is-invalid' : '' ?>"
                               value="<?= old('postal_code') ?>" required>
                        <?php if (isset($errors['postal_code'])): ?>
                            <div class="invalid-feedback"><?= e($errors['postal_code']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">نشانی کامل پستی *</label>
                        <textarea name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                                  rows="2" required><?= old('address') ?></textarea>
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= e($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">توضیحات سفارش (اختیاری)</label>
                        <textarea name="note" class="form-control" rows="2"><?= old('note') ?></textarea>
                    </div>
                </div>

                <!-- روش پرداخت -->
                <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-wallet2 ms-2"></i>روش پرداخت</h6>
                <div class="payment-methods">
                    <div class="form-check payment-option">
                        <input class="form-check-input" type="radio" name="payment_method" value="online" id="pay-online" checked>
                        <label class="form-check-label" for="pay-online">
                            <strong><i class="bi bi-credit-card-2-front ms-2"></i>پرداخت آنلاین</strong>
                            <div class="small text-muted">پرداخت امن از طریق درگاه اینترنتی (نمایشی)</div>
                        </label>
                    </div>
                    <div class="form-check payment-option">
                        <input class="form-check-input" type="radio" name="payment_method" value="cod" id="pay-cod">
                        <label class="form-check-label" for="pay-cod">
                            <strong><i class="bi bi-cash ms-2"></i>پرداخت در محل</strong>
                            <div class="small text-muted">پرداخت هنگام تحویل کالا به مامور پست</div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= خلاصه سفارش ================= -->
        <div class="col-lg-4">
            <div class="buy-box rounded-3 p-4">
                <h6 class="fw-bold mb-3">خلاصه سفارش</h6>

                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-truncate me-2"><?= e(str_limit($item['title'], 30)) ?> × <?= fa_num($item['quantity']) ?></span>
                        <span class="flex-shrink-0"><?= fa_price(discounted_price($item['price'], $item['discount_percent']) * (int)$item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>

                <hr>
                <div class="d-flex justify-content-between mb-2 small">
                    <span>سود شما از خرید:</span>
                    <span class="text-success"><?= fa_price($totals['savings']) ?></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span>هزینه ارسال:</span>
                    <span class="text-success">رایگان</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">مبلغ قابل پرداخت:</span>
                    <span class="fw-bold fs-5 text-danger"><?= fa_price($totals['payable']) ?></span>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    <i class="bi bi-check2-circle me-2"></i>ثبت نهایی سفارش
                </button>
            </div>
        </div>
    </form>
</div>
