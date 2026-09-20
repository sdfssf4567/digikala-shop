<?php
/**
 * ===============================================
 * صفحه سبد خرید - تغییر تعداد و حذف با Ajax
 * ===============================================
 */
?>
<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-basket2 ms-2"></i>سبد خرید شما</h4>

    <?php if (empty($items)): ?>
        <!-- سبد خالی -->
        <div class="empty-state my-5">
            <i class="bi bi-basket2"></i>
            <h6>سبد خرید شما خالی است!</h6>
            <p class="text-muted small">می‌توانید از صفحه محصولات کالاهای مورد نظر را اضافه کنید.</p>
            <a href="<?= url('products') ?>" class="btn btn-primary btn-sm mt-2">مشاهده محصولات</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- ================= اقلام سبد ================= -->
            <div class="col-lg-8">
                <div id="cart-items-list">
                    <?php foreach ($items as $item):
                        $final = discounted_price($item['price'], $item['discount_percent']); ?>
                        <div class="cart-item d-flex align-items-center" data-cart-item="<?= (int)$item['cart_id'] ?>">
                            <!-- تصویر -->
                            <a href="<?= url('product', ['id' => $item['product_id']]) ?>" class="flex-shrink-0">
                                <img src="<?= BASE_URL ?>/<?= e(ltrim($item['image'] ?? 'assets/images/products/placeholder.svg', '/')) ?>"
                                     class="cart-item-image" alt="<?= e($item['title']) ?>">
                            </a>

                            <!-- عنوان و قیمت واحد -->
                            <div class="flex-grow-1 ms-3">
                                <a href="<?= url('product', ['id' => $item['product_id']]) ?>"
                                   class="cart-item-title d-block text-decoration-none"><?= e($item['title']) ?></a>
                                <div class="small text-muted mt-1">
                                    <?php if ((int)$item['discount_percent'] > 0): ?>
                                        <span class="old-price me-2"><?= fa_price($item['price']) ?></span>
                                    <?php endif; ?>
                                    واحد: <span class="fw-bold text-dark"><?= fa_price($final) ?></span>
                                </div>
                            </div>

                            <!-- انتخاب تعداد -->
                            <div class="qty-selector flex-shrink-0 mx-2">
                                <button type="button" class="qty-btn" data-cart-qty="plus" data-cart-id="<?= (int)$item['cart_id'] ?>">+</button>
                                <input type="text" class="qty-input cart-qty-input" value="<?= (int)$item['quantity'] ?>"
                                       data-cart-id="<?= (int)$item['cart_id'] ?>" inputmode="numeric">
                                <button type="button" class="qty-btn" data-cart-qty="minus" data-cart-id="<?= (int)$item['cart_id'] ?>">−</button>
                            </div>

                            <!-- جمع ردیف -->
                            <div class="cart-item-total flex-shrink-0 text-center mx-2 d-none d-md-block">
                                <div class="small text-muted">جمع</div>
                                <div class="fw-bold"><?= fa_price($final * (int)$item['quantity']) ?></div>
                            </div>

                            <!-- حذف -->
                            <button type="button" class="btn btn-link text-danger p-1 flex-shrink-0"
                                    data-cart-remove="<?= (int)$item['cart_id'] ?>" title="حذف از سبد">
                                <i class="bi bi-trash3 fs-5"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ================= خلاصه سفارش ================= -->
            <div class="col-lg-4">
                <div class="buy-box rounded-3 p-4 sticky-top" style="top: 130px">
                    <h6 class="fw-bold mb-3">خلاصه سفارش</h6>

                    <div class="d-flex justify-content-between small mb-2">
                        <span>مبلغ قابل پرداخت:</span>
                        <span class="fw-bold" id="cart-sum"><?= fa_price($totals['sum']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2 text-success">
                        <span>سود شما از خرید:</span>
                        <span id="cart-savings"><?= fa_price($totals['savings']) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">مبلغ نهایی:</span>
                        <span class="fw-bold fs-5 text-danger" id="cart-payable"><?= fa_price($totals['payable']) ?></span>
                    </div>

                    <a href="<?= url('checkout') ?>" class="btn btn-primary w-100 btn-lg">
                        تایید و تکمیل سفارش <i class="bi bi-arrow-left me-2"></i>
                    </a>

                    <div class="small text-muted mt-3">
                        <div><i class="bi bi-truck ms-2"></i>هزینه ارسال رایگان</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
