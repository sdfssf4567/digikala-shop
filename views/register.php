<?php
/**
 * ===============================================
 * صفحه ثبت‌نام کاربر جدید — نسخه ۲.۰
 * طراحی دوستونه هماهنگ با صفحه ورود
 * ===============================================
 */
?>
<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-10 col-12">
                <div class="auth-card row g-0">

                    <!-- پنل معرفی (فقط دسکتاپ) -->
                    <div class="col-md-5 d-none d-md-block">
                        <div class="auth-side h-100">
                            <i class="bi bi-person-plus big"></i>
                            <h4 class="fw-black">عضو خانواده <?= e(SITE_NAME) ?> شوید!</h4>
                            <p class="opacity-75 small lh-lg mb-0">
                                ثبت‌نام کمتر از یک دقیقه طول می‌کشد و به‌سرعت می‌توانید
                                اولین خرید خود را با بهترین قیمت تجربه کنید.
                            </p>
                            <ul class="list-unstyled small mt-2 mb-0 opacity-90">
                                <li class="mb-2"><i class="bi bi-check2-circle ms-2"></i>ثبت‌نام رایگان</li>
                                <li class="mb-2"><i class="bi bi-check2-circle ms-2"></i>خرید سریع و آسان</li>
                                <li><i class="bi bi-check2-circle ms-2"></i>اطلاع از تخفیف‌های ویژه</li>
                            </ul>
                        </div>
                    </div>

                    <!-- فرم ثبت‌نام -->
                    <div class="col-md-7">
                        <div class="p-4 p-md-5">
                            <div class="text-center mb-4">
                                <a href="<?= url('home') ?>" class="logo-link d-inline-flex align-items-center">
                                    <span class="logo-icon"><i class="bi bi-shop"></i></span>
                                    <span class="logo-text"><?= e(SITE_NAME) ?></span>
                                </a>
                                <p class="text-muted-2 small mt-2 mb-0">ساخت حساب کاربری جدید</p>
                            </div>

                            <form method="post" action="<?= url('register/submit') ?>">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">نام و نام خانوادگی</label>
                                    <input type="text" name="full_name" class="form-control"
                                           value="<?= old('full_name') ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">ایمیل</label>
                                            <input type="email" name="email" class="form-control ltr-input"
                                                   value="<?= old('email') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">شماره موبایل</label>
                                            <input type="text" name="phone" class="form-control ltr-input"
                                                   placeholder="09xxxxxxxxx" value="<?= old('phone') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">رمز عبور (حداقل ۶ کاراکتر)</label>
                                            <input type="password" name="password" class="form-control ltr-input" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">تکرار رمز عبور</label>
                                            <input type="password" name="password_confirm" class="form-control ltr-input" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2">ثبت‌نام</button>
                            </form>

                            <div class="text-center mt-4 small">
                                قبلاً ثبت‌نام کرده‌اید؟
                                <a href="<?= url('login') ?>" class="text-danger fw-bold">وارد شوید</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
