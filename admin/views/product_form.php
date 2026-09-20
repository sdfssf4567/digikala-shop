<?php
/**
 * ===============================================
 * فرم افزودن / ویرایش محصول (با آپلود چند تصویر)
 * متغیرها: $product, $images, $categories, $brands, $specs
 * ===============================================
 */

$isEdit = !empty($product);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><?= $isEdit ? 'ویرایش محصول' : 'افزودن محصول جدید' ?></h4>
    <a href="<?= admin_url('products') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right ms-1"></i> بازگشت به لیست
    </a>
</div>

<form method="post" action="<?= admin_url('product-save') ?>" enctype="multipart/form-data" class="row g-4">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
    <?php endif; ?>

    <!-- ================= اطلاعات اصلی ================= -->
    <div class="col-lg-8">
        <div class="admin-card p-4 mb-4">
            <h6 class="fw-bold mb-3">اطلاعات اصلی</h6>
            <div class="mb-3">
                <label class="form-label small">عنوان محصول *</label>
                <input type="text" name="title" class="form-control" required
                       value="<?= e($product['title'] ?? '') ?>">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small">دسته‌بندی *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">انتخاب کنید...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"
                                <?= ($isEdit && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">برند</label>
                    <select name="brand_id" class="form-select">
                        <option value="0">بدون برند</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= (int)$brand['id'] ?>"
                                <?= ($isEdit && $product['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                                <?= e($brand['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label small">قیمت (تومان) *</label>
                    <input type="text" name="price" class="form-control ltr-input" required
                           inputmode="numeric" value="<?= e($product['price'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">درصد تخفیف (۰ تا ۱۰۰)</label>
                    <input type="text" name="discount_percent" class="form-control ltr-input"
                           inputmode="numeric" value="<?= e($product['discount_percent'] ?? '0') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">موجودی انبار</label>
                    <input type="text" name="stock" class="form-control ltr-input"
                           inputmode="numeric" value="<?= e($product['stock'] ?? '10') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small">توضیح کوتاه</label>
                <input type="text" name="short_description" class="form-control"
                       value="<?= e($product['short_description'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label small">توضیحات کامل</label>
                <textarea name="description" class="form-control" rows="6"><?= e($product['description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- ================= مشخصات فنی ================= -->
        <div class="admin-card p-4 mb-4">
            <h6 class="fw-bold mb-3">مشخصات فنی</h6>
            <p class="small text-muted mb-2">هر سطر به فرمت «عنوان|مقدار» - مثال: <span class="ltr-input-inline">رنگ|مشکی</span></p>
            <div id="specs-rows">
                <?php foreach (($specs ?: ['']) as $specRow): ?>
                    <input type="text" name="specs[]" class="form-control ltr-input-inline mb-2"
                           value="<?= e($specRow) ?>" placeholder="عنوان|مقدار">
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="add-spec-row">
                <i class="bi bi-plus-lg ms-1"></i> افزودن سطر
            </button>
        </div>
    </div>

    <!-- ================= ستون کنار: وضعیت و تصاویر ================= -->
    <div class="col-lg-4">
        <!-- وضعیت نمایش -->
        <div class="admin-card p-4 mb-4">
            <h6 class="fw-bold mb-3">وضعیت و نمایش</h6>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="status" value="1" id="p-status"
                       <?= (!$isEdit || $product['status']) ? 'checked' : '' ?>>
                <label class="form-check-label small" for="p-status">فعال (قابل مشاهده در سایت)</label>
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_new" value="1" id="p-new"
                       <?= ($isEdit && $product['is_new']) ? 'checked' : '' ?>>
                <label class="form-check-label small" for="p-new">نشان «جدید»</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_special" value="1" id="p-special"
                       <?= ($isEdit && $product['is_special']) ? 'checked' : '' ?>>
                <label class="form-check-label small" for="p-special">پیشنهاد شگفت‌انگیز</label>
            </div>
        </div>

        <!-- آپلود تصاویر -->
        <div class="admin-card p-4 mb-4">
            <h6 class="fw-bold mb-3">تصاویر محصول</h6>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
            <div class="small text-muted mt-2">می‌توانید چند تصویر همزمان انتخاب کنید (JPG، PNG، WebP - حداکثر ۵ مگابایت)</div>

            <!-- گالری تصاویر فعلی -->
            <?php if ($isEdit && !empty($images)): ?>
                <hr>
                <div class="small fw-bold mb-2">تصاویر فعلی (<?= fa_num(count($images)) ?>):</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($images as $img): ?>
                        <div class="admin-image-box">
                            <img src="<?= BASE_URL ?>/<?= e(ltrim($img['image'], '/')) ?>" alt="">
                            <form method="post" action="<?= admin_url('image-delete') ?>" class="confirm-delete-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger admin-image-delete" title="حذف تصویر">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">
            <i class="bi bi-check2 ms-1"></i> ذخیره محصول
        </button>
    </div>
</form>
