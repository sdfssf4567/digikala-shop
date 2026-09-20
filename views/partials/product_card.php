<?php
/**
 * ===============================================
 * کارت محصول — کامپوننت چندحالتی نسخه ۲.۰
 * حالت‌ها:
 *   پیش‌فرض     → کارت گرید با ستون بوت‌استرپ
 *   $cardMode='strip'   → آیتم اسلایدر افقی (بدون ستون)
 *   $cardMode='amazing' → آیتم اسلایدر شگفت‌انگیز
 *   $cardMode='rank'    → آیتم رتبه‌دار پرفروش‌ها
 * متغیر ورودی: $p (آرایه اطلاعات محصول) | $rank (شماره رتبه)
 * ===============================================
 */

$finalPrice   = discounted_price($p['price'], $p['discount_percent']);
$hasDiscount  = (int)$p['discount_percent'] > 0;
$isOutOfStock = (int)$p['stock'] < 1;
$productUrl   = url('product', ['id' => $p['id']]);
$image        = $p['image'] ?? 'assets/images/products/placeholder.svg';
$cardMode     = $cardMode ?? 'grid';

// قیمت با «تومان» جدا برای ظاهر تمیزتر
$priceFormatted  = fa_num(number_format((float)($hasDiscount ? $finalPrice : $p['price'])));
$oldPriceFormat  = fa_num(number_format((float)$p['price']));
?>
<?php if ($cardMode === 'grid'): ?><div class="col-6 col-md-4 col-lg-3"><?php endif; ?>

<?php if ($cardMode === 'rank'): ?>
<div class="rank-item">
    <span class="rank-num"><?= fa_num($rank ?? 1) ?></span>
<?php endif; ?>

    <div class="product-card">

        <!-- دکمه‌های شناور: علاقه‌مندی و افزودن سریع -->
        <div class="card-actions">
            <button type="button" class="card-action-btn" title="افزودن به علاقه‌مندی‌ها"
                    data-wishlist-toggle="<?= (int)$p['id'] ?>">
                <i class="bi bi-heart"></i>
            </button>
            <?php if (!$isOutOfStock): ?>
                <button type="button" class="card-action-btn" title="افزودن به سبد خرید"
                        data-add-to-cart="<?= (int)$p['id'] ?>">
                    <i class="bi bi-basket2"></i>
                </button>
            <?php endif; ?>
        </div>

        <!-- تصویر محصول -->
        <a href="<?= $productUrl ?>" class="product-image-link">
            <img src="<?= BASE_URL ?>/<?= e(ltrim($image, '/')) ?>" class="product-image"
                 alt="<?= e($p['title']) ?>" loading="lazy">
        </a>

        <!-- عنوان محصول -->
        <a href="<?= $productUrl ?>" class="product-title-link">
            <h6 class="product-title"><?= e(str_limit($p['title'], 60)) ?></h6>
        </a>

        <!-- قیمت — سبک حرفه‌ای: پیل تخفیف کنار قیمت -->
        <div class="product-price">
            <?php if ($isOutOfStock): ?>
                <span class="out-of-stock-text">ناموجود</span>
            <?php else: ?>
                <?php if ($hasDiscount): ?>
                    <span class="discount-pill"><?= fa_num($p['discount_percent']) ?>٪</span>
                    <div class="price-block">
                        <span class="old-price"><?= $oldPriceFormat ?></span>
                        <span class="new-price"><?= $priceFormatted ?><span class="currency">تومان</span></span>
                    </div>
                <?php else: ?>
                    <div class="price-block">
                        <span class="new-price"><?= $priceFormatted ?><span class="currency">تومان</span></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

<?php if ($cardMode === 'rank'): ?>
</div>
<?php endif; ?>

<?php if ($cardMode === 'grid'): ?></div><?php endif; ?>
