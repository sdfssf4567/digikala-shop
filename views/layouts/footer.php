<?php
/**
 * ===============================================
 * پاورقی مشترک صفحات فروشگاه
 * ===============================================
 */
?>
<!-- ================= پاورقی سایت ================= -->
<footer class="site-footer mt-5">
    <div class="container">
        <!-- ردیف ویژگی‌های فروشگاه -->
        <div class="row g-4 features-row pb-4 border-bottom">
            <div class="col-md-3 col-6">
                <div class="feature-item">
                    <i class="bi bi-truck"></i>
                    <div>
                        <strong>امکان تحویل اکسپرس</strong>
                        <small>در تهران و کرج</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-item">
                    <i class="bi bi-cash-coin"></i>
                    <div>
                        <strong>پرداخت در محل</strong>
                        <small>در تمام نقاط ایران</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-item">
                    <i class="bi bi-arrow-repeat"></i>
                    <div>
                        <strong>۷ روز ضمانت بازگشت</strong>
                        <small>بدون قید و شرط</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <div>
                        <strong>ضمانت اصل بودن کالا</strong>
                        <small>تمام محصولات</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ردیف لینک‌ها -->
        <div class="row g-4 py-4">
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3">درباره <?= e(SITE_NAME) ?></h6>
                <p class="text-muted small lh-lg">
                    <?= e(SITE_NAME) ?> یک فروشگاه اینترنتی نمونه است که با زبان PHP خالص و معماری MVC
                    ساخته شده است. در این فروشگاه می‌توانید انواع کالا شامل موبایل، لپ‌تاپ، لوازم جانبی،
                    پوشاک و کالاهای خانه را با بهترین قیمت خریداری کنید.
                </p>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold mb-3">راهنمای خرید</h6>
                <ul class="list-unstyled footer-links small">
                    <li><a href="<?= url('products') ?>">همه محصولات</a></li>
                    <li><a href="<?= url('products', ['sort' => 'discount']) ?>">تخفیف‌های امروز</a></li>
                    <li><a href="<?= url('products', ['sort' => 'popular']) ?>">محصولات محبوب</a></li>
                    <li><a href="<?= url('products', ['sort' => 'cheap']) ?>">ارزان‌ترین‌ها</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold mb-3">حساب کاربری</h6>
                <ul class="list-unstyled footer-links small">
                    <li><a href="<?= url('profile') ?>">پروفایل من</a></li>
                    <li><a href="<?= url('orders') ?>">سفارش‌های من</a></li>
                    <li><a href="<?= url('wishlist') ?>">علاقه‌مندی‌ها</a></li>
                    <li><a href="<?= url('cart') ?>">سبد خرید</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3">اطلاعات تماس</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><i class="bi bi-telephone ms-2"></i><?= e(SITE_PHONE) ?></li>
                    <li class="mb-2"><i class="bi bi-envelope ms-2"></i><?= e(SITE_EMAIL) ?></li>
                    <li class="mb-2"><i class="bi bi-geo-alt ms-2"></i>تهران، ایران</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- کپی‌رایت -->
    <div class="footer-bottom py-3">
        <div class="container text-center small">
            © <?= fa_num(date('Y')) ?> تمام حقوق مادی و معنوی این سایت متعلق به <?= e(SITE_NAME) ?> است.
        </div>
    </div>
</footer>

<!-- دکمه بازگشت به بالا -->
<button id="back-to-top" class="btn btn-primary back-to-top" title="بازگشت به بالا">
    <i class="bi bi-chevron-up"></i>
</button>

<!-- ================= اسکریپت‌ها ================= -->
<script src="<?= asset('vendor/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
