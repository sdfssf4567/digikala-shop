<?php
/**
 * ===============================================
 * کارت محصول - کامپوننت قابل استفاده مجدد
 * متغیر ورودی: $p (آرایه اطلاعات محصول)
 * ===============================================
 */

$finalPrice   = discounted_price($p['price'], $p['discount_percent']);
$hasDiscount  = (int)$p['discount_percent'] > 0;
$isOutOfStock = (int)$p['stock'] < 1;
$productUrl   = url('product', ['id' => $p['id']]);
$image        = $p['image'] ?? 'assets/images/products/placeholder.svg';
?>
<div class="col-6 col-md-4 col-lg-3">
    <div class="product-card h-100">

        <!-- نشان تخفیف / ناموجود -->
        <?php if ($isOutOfStock): ?>
            <span class="badge out-of-stock-badge">ناموجود</span>
        <?php elseif ($hasDiscount): ?>
            <span class="badge discount-badge"><?= fa_num($p['discount_percent']) ?>٪ تخفیف</span>
        <?php endif; ?>

        <!-- تصویر محصول -->
        <a href="<?= $productUrl ?>" class="product-image-link">
            <img src="<?= BASE_URL ?>/<?= e(ltrim($image, '/')) ?>" class="product-image"
                 alt="<?= e($p['title']) ?>" loading="lazy">
        </a>

        <!-- عنوان محصول -->
        <a href="<?= $productUrl ?>" class="product-title-link">
            <h6 class="product-title"><?= e(str_limit($p['title'], 55)) ?></h6>
        </a>

        <!-- قیمت -->
        <div class="product-price mt-auto">
            <?php if ($hasDiscount): ?>
                <span class="old-price"><?= fa_price($p['price']) ?></span>
            <?php endif; ?>
            <span class="new-price"><?= fa_price($finalPrice) ?></span>
        </div>

        <!-- دکمه افزودن به سبد -->
        <button type="button"
                class="btn btn-add-to-cart w-100 mt-2 <?= $isOutOfStock ? 'disabled' : '' ?>"
                data-add-to-cart="<?= (int)$p['id'] ?>"
                <?= $isOutOfStock ? 'disabled' : '' ?>>
            <i class="bi bi-basket2 ms-1"></i> افزودن به سبد
        </button>
    </div>
</div>
