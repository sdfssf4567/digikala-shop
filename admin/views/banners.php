<?php
/**
 * ===============================================
 * مدیریت بنرها و اسلایدر - لیست + فرم افزودن
 * متغیر: $banners
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">مدیریت بنرها و اسلایدر</h4>

<div class="row g-4">
    <!-- ================= فرم افزودن بنر ================= -->
    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3">افزودن بنر جدید</h6>

            <form method="post" action="<?= admin_url('banner-save') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label small">عنوان بنر *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">تصویر بنر *</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <div class="small text-muted mt-1">پیشنهاد: اسلایدر ۱۲۰۰×۴۵۰ - بنر وسط ۶۰۰×۳۰۰</div>
                </div>

                <div class="mb-3">
                    <label class="form-label small">لینک (اختیاری)</label>
                    <input type="text" name="link" class="form-control ltr-input" placeholder="<?= e(url('products')) ?>">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">موقعیت</label>
                        <select name="position" class="form-select">
                            <option value="slider">اسلایدر اصلی</option>
                            <option value="middle">بنر وسط صفحه</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">ترتیب نمایش</label>
                        <input type="text" name="sort_order" class="form-control ltr-input" inputmode="numeric" value="0">
                    </div>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="banner-status" checked>
                    <label class="form-check-label small" for="banner-status">فعال</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">افزودن بنر</button>
            </form>
        </div>
    </div>

    <!-- ================= لیست بنرها ================= -->
    <div class="col-lg-8">
        <?php if (empty($banners)): ?>
            <div class="admin-card p-4 text-center text-muted">هنوز بنری ثبت نشده است.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($banners as $banner): ?>
                    <div class="col-md-6">
                        <div class="admin-card p-3 h-100">
                            <img src="<?= BASE_URL ?>/<?= e(ltrim($banner['image'], '/')) ?>"
                                 class="w-100 rounded mb-2" style="max-height:150px;object-fit:cover"
                                 alt="<?= e($banner['title']) ?>">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small fw-bold"><?= e($banner['title']) ?></div>
                                <span class="badge text-bg-<?= $banner['status'] ? 'success' : 'secondary' ?>">
                                    <?= $banner['status'] ? 'فعال' : 'غیرفعال' ?>
                                </span>
                            </div>

                            <div class="small text-muted mb-3">
                                <span class="badge text-bg-light border">
                                    <?= $banner['position'] === 'slider' ? 'اسلایدر اصلی' : 'وسط صفحه' ?>
                                </span>
                                ترتیب: <?= fa_num($banner['sort_order']) ?>
                            </div>

                            <div class="d-flex gap-2">
                                <form method="post" action="<?= admin_url('banner-toggle') ?>" class="flex-grow-1">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$banner['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                        <?= $banner['status'] ? 'غیرفعال کردن' : 'فعال کردن' ?>
                                    </button>
                                </form>
                                <form method="post" action="<?= admin_url('banner-delete') ?>" class="confirm-delete-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$banner['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
