<?php
/**
 * ===============================================
 * صفحه خطا (۴۰۴ و خطای سیستمی)
 * ===============================================
 */
?>
<div class="container my-5">
    <div class="empty-state py-5">
        <i class="bi bi-exclamation-triangle text-warning"></i>
        <h5>صفحه مورد نظر پیدا نشد!</h5>
        <p class="text-muted small">
            ممکن است آدرس را اشتباه وارد کرده باشید یا این صفحه حذف شده باشد.
        </p>
        <div class="mt-3">
            <a href="<?= url('home') ?>" class="btn btn-primary btn-sm">صفحه اصلی</a>
            <a href="<?= url('products') ?>" class="btn btn-outline-primary btn-sm">مشاهده محصولات</a>
        </div>
    </div>
</div>
