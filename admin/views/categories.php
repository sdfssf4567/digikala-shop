<?php
/**
 * ===============================================
 * مدیریت دسته‌بندی‌ها - لیست + فرم افزودن/ویرایش
 * متغیرها: $categories, $editItem
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">مدیریت دسته‌بندی‌ها</h4>

<div class="row g-4">
    <!-- ================= فرم ================= -->
    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3"><?= $editItem ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی جدید' ?></h6>

            <form method="post" action="<?= admin_url('category-save') ?>">
                <?= csrf_field() ?>
                <?php if ($editItem): ?>
                    <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small">نام دسته‌بندی *</label>
                    <input type="text" name="name" class="form-control" required
                           value="<?= e($editItem['name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small">نامک انگلیسی (اختیاری)</label>
                    <input type="text" name="slug" class="form-control ltr-input"
                           value="<?= e($editItem['slug'] ?? '') ?>" placeholder="mobile">
                </div>
                <div class="mb-3">
                    <label class="form-label small">دسته والد</label>
                    <select name="parent_id" class="form-select">
                        <option value="0">بدون والد (دسته اصلی)</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php if ($editItem && $editItem['id'] == $cat['id']) continue; ?>
                            <option value="<?= (int)$cat['id'] ?>"
                                <?= ($editItem && $editItem['parent_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="form-label small">آیکون بوت‌استرپ</label>
                        <input type="text" name="icon" class="form-control ltr-input" placeholder="bi-phone"
                               value="<?= e($editItem['icon'] ?? 'bi-grid') ?>">
                    </div>
                    <div class="col-5">
                        <label class="form-label small">ترتیب نمایش</label>
                        <input type="text" name="sort_order" class="form-control ltr-input" inputmode="numeric"
                               value="<?= e($editItem['sort_order'] ?? '0') ?>">
                    </div>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="cat-status"
                           <?= (!$editItem || $editItem['status']) ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="cat-status">فعال</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">ذخیره</button>
                    <?php if ($editItem): ?>
                        <a href="<?= admin_url('categories') ?>" class="btn btn-outline-secondary">انصراف</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= لیست ================= -->
    <div class="col-lg-8">
        <div class="admin-card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>آیکون</th>
                            <th>نام</th>
                            <th>دسته والد</th>
                            <th>تعداد محصول</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><i class="bi <?= e($cat['icon'] ?: 'bi-grid') ?> fs-5"></i></td>
                            <td class="fw-bold small"><?= e($cat['name']) ?></td>
                            <td class="small"><?= e($cat['parent_name'] ?? '—') ?></td>
                            <td class="small"><?= fa_num($cat['product_count'] ?? 0) ?></td>
                            <td>
                                <span class="badge <?= $cat['status'] ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                    <?= $cat['status'] ? 'فعال' : 'غیرفعال' ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= admin_url('categories', ['edit' => $cat['id']]) ?>"
                                       class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="<?= admin_url('category-delete') ?>" class="confirm-delete-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
