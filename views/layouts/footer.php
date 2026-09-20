<?php
/**
 * ===============================================
 * پاورقی مشترک صفحات فروشگاه — نسخه ۲.۰
 * شامل: خدمات، خبرنامه، لینک‌ها، نمادهای اعتماد
 * و نویگیشن پایین موبایل
 * ===============================================
 */
$currentRoute = $_GET['route'] ?? 'home';
?>
<!-- ================= پاورقی سایت ================= -->
<footer class="site-footer">

    <div class="container">
        <!-- ردیف خدمات و ویژگی‌ها -->
        <div class="footer-features">
            <div class="footer-feature">
                <span class="ff-icon"><i class="bi bi-truck"></i></span>
                <div>
                    <strong>تحویل اکسپرس</strong>
                    <small>در تهران و کرج</small>
                </div>
            </div>
            <div class="footer-feature">
                <span class="ff-icon"><i class="bi bi-cash-coin"></i></span>
                <div>
                    <strong>پرداخت در محل</strong>
                    <small>در تمام نقاط ایران</small>
                </div>
            </div>
            <div class="footer-feature">
                <span class="ff-icon"><i class="bi bi-arrow-repeat"></i></span>
                <div>
                    <strong>۷ روز ضمانت بازگشت</strong>
                    <small>بدون قید و شرط</small>
                </div>
            </div>
            <div class="footer-feature">
                <span class="ff-icon"><i class="bi bi-shield-check"></i></span>
                <div>
                    <strong>ضمانت اصل بودن کالا</strong>
                    <small>تمام محصولات</small>
                </div>
            </div>
        </div>

        <!-- بدنه اصلی فوتر -->
        <div class="footer-main row g-4">
            <div class="col-lg-4 col-md-6">
                <a href="<?= url('home') ?>" class="logo-link d-inline-flex align-items-center mb-3">
                    <span class="logo-icon"><i class="bi bi-shop"></i></span>
                    <span class="logo-text"><?= e(SITE_NAME) ?></span>
                </a>
                <p class="footer-about">
                    <?= e(SITE_NAME) ?> فروشگاه اینترنتی با بیش از هزاران کالای متنوع در دسته‌های
                    دیجیتال، لوازم خانگی، مد و پوشاک است که با ضمانت اصل بودن کالا، بهترین قیمت
                    و ۷ روز ضمانت بازگشت، خریدی مطمئن را برای شما رقم می‌زند.
                </p>
                <div class="social-links mt-3">
                    <a href="#" title="اینستاگرام"><i class="bi bi-instagram"></i></a>
                    <a href="#" title="تلگرام"><i class="bi bi-telegram"></i></a>
                    <a href="#" title="توییتر"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" title="یوتیوب"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">راهنمای خرید</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('products') ?>">همه محصولات</a></li>
                    <li><a href="<?= url('products', ['sort' => 'discount']) ?>">تخفیف‌های امروز</a></li>
                    <li><a href="<?= url('products', ['sort' => 'popular']) ?>">محصولات محبوب</a></li>
                    <li><a href="<?= url('products', ['sort' => 'cheap']) ?>">ارزان‌ترین‌ها</a></li>
                    <li><a href="<?= url('products', ['sort' => 'expensive']) ?>">گران‌ترین‌ها</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">خدمات مشتریان</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('profile') ?>">پروفایل من</a></li>
                    <li><a href="<?= url('orders') ?>">سفارش‌های من</a></li>
                    <li><a href="<?= url('wishlist') ?>">علاقه‌مندی‌ها</a></li>
                    <li><a href="<?= url('cart') ?>">سبد خرید</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h6 class="footer-heading">از تخفیف‌ها زودتر باخبر شوید</h6>
                <p class="footer-about mb-3">ایمیل خود را وارد کنید تا جدیدترین تخفیف‌ها و پیشنهادهای ویژه را دریافت کنید.</p>
                <form class="newsletter-box d-flex mb-3" onsubmit="showToast('ایمیل شما با موفقیت ثبت شد. 🎉','success'); this.reset(); return false;">
                    <input type="email" class="form-control" placeholder="آدرس ایمیل شما" required>
                    <button type="submit" class="btn btn-primary px-3">عضویت</button>
                </form>
                <div class="d-flex gap-2">
                    <div class="trust-badge">
                        <i class="bi bi-patch-check-fill"></i>
                        نماد اعتماد
                    </div>
                    <div class="trust-badge">
                        <i class="bi bi-award"></i>
                        اتحادیه کشوری
                    </div>
                    <div class="trust-badge">
                        <i class="bi bi-shield-lock"></i>
                        پرداخت امن
                    </div>
                </div>
            </div>
        </div>

        <!-- اطلاعات تماس -->
        <div class="d-flex flex-wrap gap-4 pb-4 pt-2 small text-muted">
            <span><i class="bi bi-telephone ms-1"></i> <?= e(SITE_PHONE) ?></span>
            <span><i class="bi bi-envelope ms-1"></i> <?= e(SITE_EMAIL) ?></span>
            <span><i class="bi bi-geo-alt ms-1"></i> تهران، خیابان ولیعصر، پلاک ۱۲۳۴</span>
            <span><i class="bi bi-clock ms-1"></i> پشتیبانی ۲۴ ساعته، ۷ روز هفته</span>
        </div>
    </div>

    <!-- کپی‌رایت -->
    <div class="footer-bottom py-3">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span>© <?= fa_num(date('Y')) ?> تمام حقوق مادی و معنوی این سایت متعلق به <?= e(SITE_NAME) ?> است.</span>
            <span>طراحی و توسعه با <i class="bi bi-heart-fill text-danger"></i> برای کاربران ایرانی</span>
        </div>
    </div>
</footer>

<!-- ================= نویگیشن پایین موبایل ================= -->
<nav class="mobile-bottom-nav">
    <a href="<?= url('home') ?>" class="mbn-item <?= $currentRoute === 'home' ? 'active' : '' ?>">
        <i class="bi bi-house<?= $currentRoute === 'home' ? '-fill' : '' ?>"></i>
        خانه
    </a>
    <a href="<?= url('products') ?>" class="mbn-item <?= $currentRoute === 'products' ? 'active' : '' ?>">
        <i class="bi bi-grid"></i>
        دسته‌بندی
    </a>
    <a href="<?= url('cart') ?>" class="mbn-item <?= $currentRoute === 'cart' ? 'active' : '' ?>">
        <i class="bi bi-basket2"></i>
        سبد خرید
        <span id="cart-count-badge-mobile" class="cart-count-badge <?= $cartCount ? '' : 'd-none' ?>" style="position:absolute;top:0;left:50%;margin-left:2px"><?= fa_num($cartCount) ?></span>
    </a>
    <a href="<?= url('wishlist') ?>" class="mbn-item <?= $currentRoute === 'wishlist' ? 'active' : '' ?>">
        <i class="bi bi-heart"></i>
        علاقه‌مندی
    </a>
    <a href="<?= is_logged_in() ? url('profile') : url('login') ?>" class="mbn-item <?= in_array($currentRoute, ['profile', 'login', 'register', 'orders']) ? 'active' : '' ?>">
        <i class="bi bi-person"></i>
        <?= is_logged_in() ? 'پروفایل' : 'ورود' ?>
    </a>
</nav>

<!-- دکمه بازگشت به بالا -->
<button id="back-to-top" class="btn btn-primary back-to-top" title="بازگشت به بالا">
    <i class="bi bi-chevron-up"></i>
</button>

<!-- ================= اسکریپت‌ها ================= -->
<script src="<?= asset('vendor/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
