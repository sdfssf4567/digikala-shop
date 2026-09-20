<?php
/**
 * ===============================================
 * کنترلر مدیریت نظرات در پنل ادمین
 * تایید / رد / حذف نظرات کاربران
 * ===============================================
 */

class CommentController
{
    /**
     * لیست نظرات با فیلتر وضعیت
     */
    public function list(): void
    {
        $status = input('status', '');
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = '';
        }

        $comments  = Comment::allForAdmin($status);
        $pageTitle = 'مدیریت نظرات';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/comments.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * تغییر وضعیت نظر
     */
    public function changeStatus(): void
    {
        csrf_check_or_die();

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id && Comment::changeStatus($id, $status)) {
            $labels = ['approved' => 'تایید', 'rejected' => 'رد', 'pending' => 'در انتظار'];
            set_flash('success', 'نظر ' . ($labels[$status] ?? '') . ' شد.');
        } else {
            set_flash('danger', 'عملیات ناموفق بود.');
        }
        redirect(admin_url('comments'));
    }

    /**
     * حذف نظر
     */
    public function delete(): void
    {
        csrf_check_or_die();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Comment::delete($id);
            set_flash('success', 'نظر حذف شد.');
        }
        redirect(admin_url('comments'));
    }
}
