<?php
/**
 * ===============================================
 * صفحه علاقه‌مندی‌های کاربر
 * متغیر: $products (محصولات علاقه‌مندی)
 * ===============================================
 */
?>
<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-heart ms-2"></i>علاقه‌مندی‌های من</h4>

    <?php if (empty($products)): ?>
        <div class="empty-state my-5">
            <i class="bi bi-heart"></i>
            <h6>لیست علاقه‌مندی شما خالی است!</h6>
            <p class="text-muted small">با کلیک روی آیکون قلب در صفحه محصولات، آن‌ها را به این لیست اضافه کنید.</p>
            <a href="<?= url('products') ?>" class="btn btn-primary btn-sm mt-2">مشاهده محصولات</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($products as $p): include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
        </div>
    <?php endif; ?>
</div>
