<?php
/**
 * ===============================================
 * صفحه اصلی فروشگاه
 * شامل: اسلایدر بنرها، دسته‌بندی‌ها، پیشنهاد شگفت‌انگیز،
 * محصولات جدید، پرفروش‌ترین‌ها و تخفیف‌های ویژه
 * ===============================================
 */
?>
<!-- ================= اسلایدر بنرهای تبلیغاتی ================= -->
<?php if (!empty($sliderBanners)): ?>
<section class="container mt-4">
    <div class="row g-3">
        <div class="col-lg-8">
            <div id="mainSlider" class="carousel slide rounded-3 overflow-hidden main-slider" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($sliderBanners as $i => $banner): ?>
                        <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="<?= $i ?>"
                                class="<?= $i === 0 ? 'active' : '' ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($sliderBanners as $i => $banner): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <a href="<?= e($banner['link'] ?: url('products')) ?>">
                                <img src="<?= BASE_URL ?>/<?= e(ltrim($banner['image'], '/')) ?>"
                                     class="d-block w-100 slider-image" alt="<?= e($banner['title']) ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block">
            <?php $sideBanner = $sliderBanners[0] ?? null; ?>
            <?php if ($sideBanner): ?>
                <a href="<?= e($sideBanner['link'] ?: url('products')) ?>">
                    <img src="<?= BASE_URL ?>/<?= e(ltrim($sideBanner['image'], '/')) ?>"
                         class="w-100 h-100 rounded-3 side-banner" alt="<?= e($sideBanner['title']) ?>">
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= دسته‌بندی‌های فروشگاه ================= -->
<section class="container mt-5">
    <h5 class="section-title">خرید بر اساس دسته‌بندی</h5>
    <div class="row g-3 justify-content-center">
        <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-3 col-lg">
                <a href="<?= url('products', ['cat' => $cat['slug']]) ?>" class="category-circle-link text-decoration-none">
                    <div class="category-circle">
                        <i class="bi <?= e($cat['icon'] ?: 'bi-grid') ?>"></i>
                    </div>
                    <div class="category-name"><?= e($cat['name']) ?></div>
                    <small class="text-muted"><?= fa_num($categoryCounts[$cat['id']] ?? 0) ?> کالا</small>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ================= بنرهای وسط صفحه ================= -->
<?php if (!empty($middleBanners)): ?>
<section class="container mt-5">
    <div class="row g-3">
        <?php foreach ($middleBanners as $banner): ?>
            <div class="col-md-6">
                <a href="<?= e($banner['link'] ?: url('products')) ?>">
                    <img src="<?= BASE_URL ?>/<?= e(ltrim($banner['image'], '/')) ?>"
                         class="w-100 rounded-3 middle-banner" alt="<?= e($banner['title']) ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ================= پیشنهاد شگفت‌انگیز ================= -->
<?php if (!empty($specialOffers)): ?>
<section class="container mt-5">
    <div class="section-header special-header">
        <h5 class="section-title mb-0"><i class="bi bi-lightning-charge-fill ms-2"></i>پیشنهاد شگفت‌انگیز</h5>
        <a href="<?= url('products', ['sort' => 'discount']) ?>" class="see-all">مشاهده همه</a>
    </div>
    <div class="row g-3">
        <?php foreach ($specialOffers as $p): include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ================= جدیدترین محصولات ================= -->
<section class="container mt-5">
    <div class="section-header">
        <h5 class="section-title mb-0"><i class="bi bi-stars ms-2"></i>جدیدترین کالاها</h5>
        <a href="<?= url('products') ?>" class="see-all">مشاهده همه</a>
    </div>
    <div class="row g-3">
        <?php foreach ($latestProducts as $p): include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
    </div>
</section>

<!-- ================= پرفروش‌ترین محصولات ================= -->
<section class="container mt-5">
    <div class="section-header">
        <h5 class="section-title mb-0"><i class="bi bi-fire ms-2"></i>پرفروش‌ترین کالاها</h5>
        <a href="<?= url('products', ['sort' => 'popular']) ?>" class="see-all">مشاهده همه</a>
    </div>
    <div class="row g-3">
        <?php foreach ($bestSellers as $p): include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
    </div>
</section>

<!-- ================= بیشترین تخفیف‌ها ================= -->
<?php if (!empty($topDiscounted)): ?>
<section class="container mt-5 mb-4">
    <div class="section-header">
        <h5 class="section-title mb-0"><i class="bi bi-percent ms-2"></i>تخفیف‌های ویژه</h5>
        <a href="<?= url('products', ['sort' => 'discount']) ?>" class="see-all">مشاهده همه</a>
    </div>
    <div class="row g-3">
        <?php foreach ($topDiscounted as $p): include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
    </div>
</section>
<?php endif; ?>
