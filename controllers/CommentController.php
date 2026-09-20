<?php
/**
 * ===============================================
 * کنترلر نظرات - ثبت نظر و امتیاز برای محصولات
 * ===============================================
 */

class CommentController
{
    /**
     * ثبت نظر جدید (POST از صفحه محصول)
     */
    public function add(): void
    {
        require_login();
        csrf_check_or_die();

        $productId = (int)($_POST['product_id'] ?? 0);
        $product   = Product::find($productId);

        if (!$product) {
            set_flash('danger', 'محصول یافت نشد.');
            redirect(url('products'));
        }

        $result = Comment::add($productId, (int)$_SESSION['user_id'], $_POST);

        if ($result['ok']) {
            set_flash('success', 'نظر شما ثبت شد و پس از تایید مدیر نمایش داده می‌شود.');
        } else {
            foreach ($result['errors'] as $error) {
                set_flash('danger', $error);
            }
        }

        redirect(url('product', ['id' => $productId]) . '#comments');
    }
}
