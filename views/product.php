<?php
/**
 * ===============================================
 * صفحه جزئیات محصول
 * شامل: گالری تصاویر، قیمت، مشخصات، نظرات و امتیازدهی
 * ===============================================
 */

$finalPrice  = discounted_price($product['price'], $product['discount_percent']);
$hasDiscount = (int)$product['discount_percent'] > 0;
$outOfStock  = (int)$product['stock'] < 1;

// تصاویر گالری - اگر محصول تصویر نداشت از تصویر پیش‌فرض استفاده می‌شود
if (empty($images)) {
    $images = [['image' => 'assets/images/products/placeholder.svg']];
}
?>
<div class="container my-4">

    <!-- مسیر راهنما -->
    <nav class="breadcrumb-nav small mb-3">
        <a href="<?= url('home') ?>">دیجی‌شاپ</a>
        <span>/</span>
        <?php if (!empty($product['category_slug'])): ?>
            <a href="<?= url('products', ['cat' => $product['category_slug']]) ?>"><?= e($product['category_name']) ?></a>
        <?php endif; ?>
        <span>/</span>
        <span class="text-muted"><?= e(str_limit($product['title'], 40)) ?></span>
    </nav>

    <div class="row g-4">
        <!-- ================= گالری تصاویر ================= -->
        <div class="col-md-5">
            <div class="product-gallery">
                <div class="gallery-main rounded-3 border p-3 bg-white text-center">
                    <?php if ($hasDiscount): ?>
                        <span class="badge discount-badge"><?= fa_num($product['discount_percent']) ?>٪ تخفیف</span>
                    <?php endif; ?>
                    <img id="gallery-main-image" src="<?= BASE_URL ?>/<?= e(ltrim($images[0]['image'], '/')) ?>"
                         class="img-fluid gallery-main-img" alt="<?= e($product['title']) ?>">
                </div>

                <?php if (count($images) > 1): ?>
                    <div class="d-flex gap-2 mt-3 justify-content-center flex-wrap">
                        <?php foreach ($images as $i => $img): ?>
                            <img src="<?= BASE_URL ?>/<?= e(ltrim($img['image'], '/')) ?>"
                                 class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                                 data-gallery-thumb alt="تصویر <?= fa_num($i + 1) ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ================= اطلاعات اصلی محصول ================= -->
        <div class="col-md-4">
            <h4 class="product-page-title"><?= e($product['title']) ?></h4>

            <div class="d-flex align-items-center flex-wrap gap-2 mt-3 small">
                <!-- امتیاز کاربران -->
                <span class="rating-pill">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi <?= $i <= round($rating['average']) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                    <?php endfor; ?>
                    <?= fa_num($rating['average']) ?>
                </span>
                <span class="text-muted-2">(<?= fa_num($rating['total']) ?> امتیاز)</span>
                <span class="text-muted-2"><i class="bi bi-eye ms-1"></i><?= fa_num($product['views']) ?> بازدید</span>
            </div>

            <ul class="list-unstyled product-quick-info mt-3 small">
                <li><i class="bi bi-tag ms-2"></i>دسته‌بندی: <a href="<?= url('products', ['cat' => $product['category_slug'] ?? '']) ?>"><?= e($product['category_name'] ?: '-') ?></a></li>
                <li><i class="bi bi-award ms-2"></i>برند: <?= e($product['brand_name'] ?: '-') ?></li>
                <li><i class="bi bi-box-seam ms-2"></i>موجودی انبار: <?= $outOfStock ? '<span class="text-danger">ناموجود</span>' : fa_num($product['stock']) . ' عدد' ?></li>
            </ul>

            <p class="text-muted small lh-lg"><?= e($product['short_description']) ?></p>
        </div>

        <!-- ================= باکس خرید ================= -->
        <div class="col-md-3">
            <div class="buy-box p-3">
                <!-- فروشنده -->
                <div class="seller-row">
                    <i class="bi bi-shop"></i>
                    فروشنده: <strong class="text-dark"><?= e(SITE_NAME) ?></strong>
                </div>

                <!-- قیمت -->
                <div class="buy-box-price">
                    <?php if ($hasDiscount): ?>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="discount-pill"><?= fa_num($product['discount_percent']) ?>٪ تخفیف</span>
                            <span class="old-price m-0"><?= fa_price($product['price']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="new-price"><?= fa_price($finalPrice) ?></div>
                </div>

                <!-- انتخاب تعداد -->
                <div class="d-flex align-items-center justify-content-between my-3">
                    <span class="small">تعداد:</span>
                    <div class="qty-selector">
                        <button type="button" class="qty-btn" data-qty="plus">+</button>
                        <input type="text" id="qty-input" class="qty-input" value="1" inputmode="numeric"
                               data-max-stock="<?= (int)$product['stock'] ?>">
                        <button type="button" class="qty-btn" data-qty="minus">−</button>
                    </div>
                </div>

                <!-- دکمه افزودن به سبد -->
                <button type="button" class="btn btn-primary w-100 btn-lg add-to-cart-detail"
                        data-add-to-cart="<?= (int)$product['id'] ?>" <?= $outOfStock ? 'disabled' : '' ?>>
                    <?php if ($outOfStock): ?>
                        ناموجود
                    <?php else: ?>
                        <i class="bi bi-basket2 ms-2"></i> افزودن به سبد خرید
                    <?php endif; ?>
                </button>

                <!-- دکمه علاقه‌مندی -->
                <button type="button" class="btn btn-outline-danger w-100 mt-2" data-wishlist-toggle="<?= (int)$product['id'] ?>">
                    <i class="bi <?= !empty($inWishlist) && $inWishlist ? 'bi-heart-fill' : 'bi-heart' ?> ms-1"></i>
                    <?= !empty($inWishlist) && $inWishlist ? 'حذف از علاقه‌مندی‌ها' : 'افزودن به علاقه‌مندی‌ها' ?>
                </button>

                <div class="small text-muted mt-3">
                    <div class="mb-1"><i class="bi bi-shield-check ms-2"></i>ضمانت اصل بودن کالا</div>
                    <div class="mb-1"><i class="bi bi-truck ms-2"></i>ارسال سریع به سراسر ایران</div>
                    <div><i class="bi bi-arrow-repeat ms-2"></i>۷ روز ضمانت بازگشت</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= تب‌های معرفی، مشخصات و نظرات ================= -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs product-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc">معرفی محصول</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-specs">مشخصات فنی</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-comments" id="comments-tab-btn">
                        نظرات (<?= fa_num(count($comments)) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content tab-panel-card p-4">
                <!-- معرفی محصول -->
                <div class="tab-pane fade show active" id="tab-desc">
                    <p class="lh-lg text-body" style="white-space: pre-line"><?= e($product['description'] ?: $product['short_description']) ?></p>
                </div>

                <!-- مشخصات فنی -->
                <div class="tab-pane fade" id="tab-specs">
                    <?php if (!empty($specs)): ?>
                        <table class="table table-striped specs-table">
                            <tbody>
                            <?php foreach ($specs as $specName => $specValue): ?>
                                <tr>
                                    <th class="w-25"><?= e($specName) ?></th>
                                    <td><?= e($specValue) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted mb-0">مشخصات فنی برای این محصول ثبت نشده است.</p>
                    <?php endif; ?>
                </div>

                <!-- نظرات کاربران -->
                <div class="tab-pane fade" id="tab-comments">
                    <div id="comments">
                        <!-- فرم ثبت نظر -->
                        <?php if (is_logged_in()): ?>
                            <form method="post" action="<?= url('comment/add') ?>" class="comment-form mb-4">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

                                <h6 class="fw-bold mb-3">ثبت نظر و امتیاز</h6>

                                <!-- انتخاب ستاره امتیاز -->
                                <div class="star-rating-input mb-3" data-rating="5">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <i class="bi bi-star-fill" data-star="<?= $i ?>" title="<?= fa_num($i) ?> ستاره"></i>
                                    <?php endfor; ?>
                                    <input type="hidden" name="rating" value="5" id="rating-input">
                                    <span class="ms-2 small text-muted rating-text">عالی</span>
                                </div>

                                <input type="text" name="title" class="form-control mb-2" placeholder="عنوان نظر (اختیاری)">
                                <textarea name="body" class="form-control mb-2" rows="3"
                                          placeholder="تجربه خود از این کالا را بنویسید... (حداقل ۱۰ حرف)" required></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">ثبت نظر</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-light border small">
                                برای ثبت نظر ابتدا
                                <a href="<?= url('login') ?>">وارد حساب کاربری</a> شوید.
                            </div>
                        <?php endif; ?>

                        <!-- لیست نظرات تایید شده -->
                        <h6 class="fw-bold mb-3"><?= fa_num(count($comments)) ?> نظر ثبت شده</h6>
                        <?php if (empty($comments)): ?>
                            <p class="text-muted small">هنوز نظری برای این کالا ثبت نشده است. اولین نفر باشید!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="comment-avatar"><i class="bi bi-person-fill"></i></span>
                                            <div>
                                                <strong class="small d-block"><?= e($comment['user_name'] ?? 'کاربر') ?></strong>
                                                <span class="text-muted" style="font-size:.72rem"><?= fa_datetime($comment['created_at']) ?></span>
                                            </div>
                                        </div>
                                        <span class="text-warning small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi <?= $i <= (int)$comment['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <?php if ($comment['title']): ?>
                                        <div class="fw-bold small mt-1"><?= e($comment['title']) ?></div>
                                    <?php endif; ?>
                                    <p class="small text-muted mt-1 mb-0 lh-lg"><?= e($comment['body']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= محصولات مشابه ================= -->
    <?php if (!empty($related)): ?>
    <div class="mt-4">
        <div class="section-card has-footer">
            <div class="section-head">
                <h2 class="section-head-title"><i class="bi bi-collection"></i> کالاهای مشابه</h2>
            </div>
            <div class="row g-0 grid-in-card">
                <?php foreach ($related as $p): $cardMode = 'grid'; include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
