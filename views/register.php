<?php
/**
 * ===============================================
 * صفحه ثبت‌نام کاربر جدید
 * ===============================================
 */
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-12">
            <div class="auth-card p-4 rounded-3 bg-white border">
                <div class="text-center mb-4">
                    <a href="<?= url('home') ?>" class="logo-link text-decoration-none">
                        <span class="logo-icon"><i class="bi bi-shop"></i></span>
                        <span class="fs-3 fw-bold"><?= e(SITE_NAME) ?></span>
                    </a>
                    <p class="text-muted small mt-2 mb-0">ساخت حساب کاربری جدید</p>
                </div>

                <form method="post" action="<?= url('register/submit') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small">نام و نام خانوادگی</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?= old('full_name') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">ایمیل</label>
                        <input type="email" name="email" class="form-control ltr-input"
                               value="<?= old('email') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">شماره موبایل</label>
                        <input type="text" name="phone" class="form-control ltr-input"
                               placeholder="09xxxxxxxxx" value="<?= old('phone') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">رمز عبور (حداقل ۶ کاراکتر)</label>
                        <input type="password" name="password" class="form-control ltr-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">تکرار رمز عبور</label>
                        <input type="password" name="password_confirm" class="form-control ltr-input" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">ثبت‌نام</button>
                </form>

                <div class="text-center mt-3 small">
                    قبلاً ثبت‌نام کرده‌اید؟
                    <a href="<?= url('login') ?>" class="text-primary fw-bold">وارد شوید</a>
                </div>
            </div>
        </div>
    </div>
</div>
