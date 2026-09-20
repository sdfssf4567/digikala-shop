<?php
/**
 * ===============================================
 * صفحه ورود به حساب کاربری
 * ===============================================
 */

$loginError = $_SESSION['old']['login_error'] ?? '';
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
                    <p class="text-muted small mt-2 mb-0">ورود به حساب کاربری</p>
                </div>

                <form method="post" action="<?= url('login/submit') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small">ایمیل</label>
                        <input type="email" name="email" class="form-control ltr-input"
                               value="<?= old('email') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">رمز عبور</label>
                        <input type="password" name="password" class="form-control ltr-input" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">ورود به حساب</button>
                </form>

                <div class="text-center mt-3 small">
                    حساب کاربری ندارید؟
                    <a href="<?= url('register') ?>" class="text-primary fw-bold">ثبت‌نام کنید</a>
                </div>

                <!-- راهنمای حساب‌های نمونه برای تست -->
                <div class="alert alert-light border small mt-4 mb-0 text-muted">
                    <strong>حساب‌های نمونه برای تست:</strong><br>
                    مدیر: <span class="ltr-input-inline">admin@shop.ir / admin123</span><br>
                    کاربر: <span class="ltr-input-inline">ali@example.com / 12345678</span>
                </div>
            </div>
        </div>
    </div>
</div>
