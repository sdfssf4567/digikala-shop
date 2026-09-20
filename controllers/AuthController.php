<?php
/**
 * ===============================================
 * کنترلر ورود و ثبت‌نام
 * شامل اعتبارسنجی سمت سرور، CSRF و هش رمز عبور
 * ===============================================
 */

class AuthController
{
    /**
     * فرم ورود
     */
    public function loginForm(): void
    {
        // اگر قبلاً وارد شده، به صفحه اصلی برود
        if (is_logged_in()) {
            redirect(url('home'));
        }
        $pageTitle = 'ورود به حساب کاربری';
        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/login.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * پردازش فرم ورود
     */
    public function login(): void
    {
        csrf_check_or_die();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = User::login($email, $password);

        if (!$result['ok']) {
            set_flash('danger', $result['error']);
            set_old(['email' => $email]);
            redirect(url('login'));
        }

        clear_old();

        // ذخیره اطلاعات کاربر در نشست
        $user = $result['user'];
        session_regenerate_id(true); // جلوگیری از حمله نشست‌ربایی
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        set_flash('success', 'خوش آمدید ' . $user['full_name'] . '!');

        // اگر کاربر از صفحه‌ای ریدایرکت شده بود، به همان صفحه برگردد
        $target = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);
        if ($target !== '' && str_starts_with($target, '/')) {
            redirect($target);
        }

        // مدیر به پنل مدیریت هدایت می‌شود
        if ($user['role'] === 'admin') {
            redirect(admin_url('dashboard'));
        }
        redirect(url('home'));
    }

    /**
     * فرم ثبت‌نام
     */
    public function registerForm(): void
    {
        if (is_logged_in()) {
            redirect(url('home'));
        }
        $pageTitle = 'ثبت‌نام در سایت';
        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/register.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }

    /**
     * پردازش فرم ثبت‌نام
     */
    public function register(): void
    {
        csrf_check_or_die();

        $result = User::register($_POST);

        if (!$result['ok']) {
            foreach ($result['errors'] as $error) {
                set_flash('danger', $error);
            }
            set_old([
                'full_name' => $_POST['full_name'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'phone'     => $_POST['phone'] ?? '',
            ]);
            redirect(url('register'));
        }

        clear_old();

        // ورود خودکار پس از ثبت‌نام موفق
        $user = User::find($result['id']);
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        set_flash('success', 'ثبت‌نام شما با موفقیت انجام شد. خوش آمدید!');
        redirect(url('home'));
    }

    /**
     * خروج از حساب کاربری
     */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect(url('home'));
    }
}
