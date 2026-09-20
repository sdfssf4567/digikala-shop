<?php
/**
 * ===============================================
 * صفحه ورود به حساب کاربری — نسخه ۲.۰
 * طراحی دوستونه با پنل معرفی گرادیانی
 * ===============================================
 */

$loginError = $_SESSION['old']['login_error'] ?? '';
?>
<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-10 col-12">
                <div class="auth-card row g-0">

                    <!-- پنل معرفی (فقط دسکتاپ) -->
                    <div class="col-md-5 d-none d-md-block">
                        <div class="auth-side h-100">
                            <i class="bi bi-shop big"></i>
                            <h4 class="fw-black">به <?= e(SITE_NAME) ?> خوش آمدید!</h4>
                            <p class="opacity-75 small lh-lg mb-0">
                                با ورود به حساب کاربری می‌توانید سفارش‌های خود را پیگیری کنید،
                                کالاهای مورد علاقه را ذخیره کنید و از تخفیف‌های ویژه زودتر باخبر شوید.
                            </p>
                            <ul class="list-unstyled small mt-2 mb-0 opacity-90">
                                <li class="mb-2"><i class="bi bi-check2-circle ms-2"></i>پیگیری لحظه‌ای سفارش‌ها</li>
                                <li class="mb-2"><i class="bi bi-check2-circle ms-2"></i>ذخیره علاقه‌مندی‌ها</li>
                                <li><i class="bi bi-check2-circle ms-2"></i>تخفیف‌های اختصاصی اعضا</li>
                            </ul>
                        </div>
                    </div>

                    <!-- فرم ورود -->
                    <div class="col-md-7">
                        <div class="p-4 p-md-5">
                            <div class="text-center mb-4">
                                <a href="<?= url('home') ?>" class="logo-link d-inline-flex align-items-center">
                                    <span class="logo-icon"><i class="bi bi-shop"></i></span>
                                    <span class="logo-text"><?= e(SITE_NAME) ?></span>
                                </a>
                                <p class="text-muted-2 small mt-2 mb-0">برای ادامه وارد حساب کاربری خود شوید</p>
                            </div>

                            <?php if ($loginError): ?>
                                <div class="alert alert-danger small border-0" style="border-radius:10px;background:var(--dk-red-soft);color:var(--dk-red)">
                                    <i class="bi bi-exclamation-circle ms-1"></i> <?= e($loginError) ?>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="<?= url('login/submit') ?>">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">ایمیل</label>
                                    <input type="email" name="email" class="form-control ltr-input"
                                           value="<?= old('email') ?>" required autofocus>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold">رمز عبور</label>
                                    <input type="password" name="password" class="form-control ltr-input" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2">ورود به حساب</button>
                            </form>

                            <div class="text-center mt-4 small">
                                حساب کاربری ندارید؟
                                <a href="<?= url('register') ?>" class="text-danger fw-bold">ثبت‌نام کنید</a>
                            </div>

                            <!-- راهنمای حساب‌های نمونه برای تست -->
                            <div class="alert alert-light border small mt-4 mb-0 text-muted-2" style="border-radius:10px">
                                <strong class="text-dark">حساب‌های نمونه برای تست:</strong><br>
                                مدیر: <span class="ltr-input-inline">admin@shop.ir / admin123</span><br>
                                کاربر: <span class="ltr-input-inline">ali@example.com / 12345678</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
