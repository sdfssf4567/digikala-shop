<?php
/**
 * ===============================================
 * صفحه اصلی فروشگاه — نسخه ۲.۰ حرفه‌ای
 * هیرو اسلایدر، خدمات، دسته‌بندی‌ها، پیشنهاد شگفت‌انگیز
 * با تایمر، اسلایدرهای افقی، پرفروش‌های رتبه‌دار، برندها
 * ===============================================
 */

// ثانیه باقی‌مانده تا پایان امروز برای تایمر شگفت‌انگیز
$secondsToMidnight = 86400 - (int)((time() - strtotime('today')) );
?>
<!-- ================= هیرو: اسلایدر + بنرهای کناری ================= -->
<?php if (!empty($sliderBanners)): ?>
<section class="container mt-4 fade-up">
    <div class="row g-3">
        <div class="col-lg-8">
            <div id="mainSlider" class="carousel slide hero-slider" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($sliderBanners as $i => $banner): ?>
                        <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="<?= $i ?>"
                                class="<?= $i === 0 ? 'active' : '' ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($sliderBanners as $i => $banner): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                            <a href="<?= e($banner['link'] ?: url('products')) ?>">
                                <img src="<?= BASE_URL ?>/<?= e(ltrim($banner['image'], '/')) ?>"
                                     class="d-block w-100" alt="<?= e($banner['title']) ?>">
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
        <div class="col-lg-4 d-none d-lg-flex flex-column gap-3">
            <?php foreach (array_slice($middleBanners ?: $sliderBanners, 0, 2) as $sideBanner): ?>
                <a href="<?= e($sideBanner['link'] ?: url('products')) ?>" class="hero-side-banner">
                    <img src="<?= BASE_URL ?>/<?= e(ltrim($sideBanner['image'], '/')) ?>" alt="<?= e($sideBanner['title']) ?>">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= نوار خدمات و اعتماد ================= -->
<section class="container mt-4">
    <div class="services-strip row g-3">
        <div class="col-6 col-md-3">
            <div class="service-item">
                <i class="bi bi-truck"></i>
                <div>
                    <strong>تحویل اکسپرس</strong>
                    <small class="d-block">در تهران و کرج</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="service-item">
                <i class="bi bi-cash-coin"></i>
                <div>
                    <strong>پرداخت در محل</strong>
                    <small class="d-block">در تمام نقاط ایران</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="service-item">
                <i class="bi bi-arrow-repeat"></i>
                <div>
                    <strong>۷ روز ضمانت بازگشت</strong>
                    <small class="d-block">بدون قید و شرط</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="service-item">
                <i class="bi bi-shield-check"></i>
                <div>
                    <strong>ضمانت اصل بودن</strong>
                    <small class="d-block">تمام محصولات</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= دسته‌بندی‌های محبوب ================= -->
<section class="container mt-4">
    <div class="section-card">
        <div class="section-head justify-content-center">
            <h2 class="section-head-title"><i class="bi bi-grid-3x3-gap-fill"></i> خرید بر اساس دسته‌بندی</h2>
        </div>
        <div class="row g-3 pb-4 justify-content-center">
            <?php foreach ($categories as $cat): ?>
                <div class="col-4 col-md-3 col-lg">
                    <a href="<?= url('products', ['cat' => $cat['slug']]) ?>" class="category-circle-link">
                        <div class="category-circle">
                            <i class="bi <?= e($cat['icon'] ?: 'bi-grid') ?>"></i>
                        </div>
                        <div class="category-name"><?= e($cat['name']) ?></div>
                        <small class="category-count"><?= fa_num($categoryCounts[$cat['id']] ?? 0) ?> کالا</small>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= پیشنهاد شگفت‌انگیز ================= -->
<?php if (!empty($specialOffers)): ?>
<section class="container mt-4">
    <div class="amazing-section">
        <div class="d-flex align-items-stretch gap-2 amazing-row">
            <!-- پنل عنوان و تایمر -->
            <div class="amazing-head">
                <div class="amazing-title">
                    <i class="bi bi-lightning-charge-fill"></i>
                    پیشنهاد شگفت‌انگیز
                </div>
                <div class="amazing-timer" id="amazing-timer" data-countdown="<?= $secondsToMidnight ?>">
                    <div class="t-unit"><span data-unit="h">۰۰</span><small>ساعت</small></div>
                    <div class="t-unit"><span data-unit="m">۰۰</span><small>دقیقه</small></div>
                    <div class="t-unit"><span data-unit="s">۰۰</span><small>ثانیه</small></div>
                </div>
                <a href="<?= url('products', ['sort' => 'discount']) ?>" class="amazing-see-all" title="مشاهده همه">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>

            <!-- اسلایدر افقی محصولات -->
            <div class="flex-grow-1 position-relative">
                <div class="strip no-scrollbar" id="amazing-strip">
                    <?php foreach ($specialOffers as $p): $cardMode = 'amazing'; include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
                </div>
                <button type="button" class="strip-nav position-absolute" data-strip-nav="amazing-strip"
                        style="top:50%;transform:translateY(-50%);right:8px;width:36px;height:36px;z-index:4">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <button type="button" class="strip-nav position-absolute" data-strip-nav="amazing-strip"
                        style="top:50%;transform:translateY(-50%);left:8px;width:36px;height:36px;z-index:4">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= بنرهای میانی (فقط اگر بیش از ۲ بنر باشند) ================= -->
<?php if (!empty($middleBanners) && count($middleBanners) > 2): ?>
<section class="container mt-4">
    <div class="row g-3">
        <?php foreach (array_slice($middleBanners, 2) as $banner): ?>
            <div class="col-md-6">
                <a href="<?= e($banner['link'] ?: url('products')) ?>" class="mid-banner">
                    <img src="<?= BASE_URL ?>/<?= e(ltrim($banner['image'], '/')) ?>" alt="<?= e($banner['title']) ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ================= جدیدترین کالاها — اسلایدر افقی ================= -->
<?php if (!empty($latestProducts)): ?>
<section class="container mt-2">
    <div class="section-card has-footer">
        <div class="section-head">
            <h2 class="section-head-title"><i class="bi bi-stars"></i> جدیدترین کالاها</h2>
            <div class="strip-navs">
                <button type="button" class="strip-nav" data-strip-nav="latest-strip"><i class="bi bi-chevron-right"></i></button>
                <button type="button" class="strip-nav" data-strip-nav="latest-strip"><i class="bi bi-chevron-left"></i></button>
            </div>
        </div>
        <div class="strip no-scrollbar" id="latest-strip">
            <?php foreach ($latestProducts as $p): $cardMode = 'strip'; include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
        </div>
        <div class="section-card-footer">
            <a href="<?= url('products') ?>">مشاهده همه محصولات <i class="bi bi-arrow-left"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= پرفروش‌ترین‌ها — رتبه‌دار ================= -->
<?php if (!empty($bestSellers)): ?>
<section class="container mt-2">
    <div class="section-card has-footer">
        <div class="section-head">
            <h2 class="section-head-title"><i class="bi bi-fire"></i> پرفروش‌ترین کالاها</h2>
            <div class="strip-navs">
                <button type="button" class="strip-nav" data-strip-nav="best-strip"><i class="bi bi-chevron-right"></i></button>
                <button type="button" class="strip-nav" data-strip-nav="best-strip"><i class="bi bi-chevron-left"></i></button>
            </div>
        </div>
        <div class="strip no-scrollbar" id="best-strip">
            <?php foreach ($bestSellers as $i => $p): $cardMode = 'rank'; $rank = $i + 1; include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
        </div>
        <div class="section-card-footer">
            <a href="<?= url('products', ['sort' => 'popular']) ?>">مشاهده همه پرفروش‌ها <i class="bi bi-arrow-left"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= تخفیف‌های ویژه — گرید ================= -->
<?php if (!empty($topDiscounted)): ?>
<section class="container mt-2">
    <div class="section-card has-footer">
        <div class="section-head">
            <h2 class="section-head-title"><i class="bi bi-percent"></i> تخفیف‌های ویژه امروز</h2>
            <a href="<?= url('products', ['sort' => 'discount']) ?>" class="see-all-link text-danger fw-bold small">
                مشاهده همه <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="row g-0 grid-in-card">
            <?php foreach (array_slice($topDiscounted, 0, 8) as $p): $cardMode = 'grid'; include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= برندهای محبوب ================= -->
<?php if (!empty($brands)): ?>
<section class="container mt-2 mb-3">
    <div class="section-card">
        <div class="section-head">
            <h2 class="section-head-title"><i class="bi bi-award"></i> برندهای محبوب</h2>
        </div>
        <div class="brand-strip no-scrollbar">
            <?php foreach ($brands as $brand): ?>
                <?php
                $brandImage = $brand['logo'] ?? '';
                $brandUrl   = url('products', ['brand' => $brand['id']]);
                ?>
                <?php if ($brandImage): ?>
                    <a href="<?= $brandUrl ?>" class="brand-pill">
                        <img src="<?= BASE_URL ?>/<?= e(ltrim($brandImage, '/')) ?>" alt="<?= e($brand['name']) ?>" loading="lazy">
                    </a>
                <?php else: ?>
                    <a href="<?= $brandUrl ?>" class="brand-pill fw-bold text-muted"><?= e($brand['name']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
