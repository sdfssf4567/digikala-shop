<?php
/**
 * ===============================================
 * لیست محصولات در پنل مدیریت
 * متغیرها: $products, $categories, $q, $categoryId
 * ===============================================
 */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h4 class="fw-bold mb-0">مدیریت محصولات</h4>
    <a href="<?= admin_url('product-edit') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg ms-1"></i> افزودن محصول جدید
    </a>
</div>

<!-- فیلتر جستجو -->
<form class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" class="form-control" placeholder="جستجوی نام محصول..."
               value="<?= e($q) ?>">
    </div>
    <div class="col-md-4">
        <select name="category" class="form-select">
            <option value="0">همه دسته‌بندی‌ها</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100">فیلتر</button>
    </div>
</form>

<div class="admin-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="small text-muted">
                    <th>تصویر</th>
                    <th>عنوان محصول</th>
                    <th>دسته‌بندی</th>
                    <th>قیمت</th>
                    <th>موجودی</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">محصولی یافت نشد.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $p):
                $final = discounted_price($p['price'], $p['discount_percent']); ?>
                <tr>
                    <td>
                        <img src="<?= BASE_URL ?>/<?= e(ltrim($p['image'] ?? 'assets/images/products/placeholder.svg', '/')) ?>"
                             class="admin-product-thumb rounded" alt="">
                    </td>
                    <td>
                        <div class="fw-bold small"><?= e(str_limit($p['title'], 45)) ?></div>
                        <div class="text-muted" style="font-size:.72rem"><?= e($p['brand_name'] ?? '-') ?></div>
                    </td>
                    <td class="small"><?= e($p['category_name'] ?? '-') ?></td>
                    <td class="small">
                        <?php if ((int)$p['discount_percent'] > 0): ?>
                            <span class="old-price d-block" style="font-size:.75rem"><?= fa_price($p['price']) ?></span>
                        <?php endif; ?>
                        <span class="fw-bold"><?= fa_price($final) ?></span>
                        <?php if ((int)$p['discount_percent'] > 0): ?>
                            <span class="badge discount-badge mt-1"><?= fa_num($p['discount_percent']) ?>٪</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= $p['stock'] > 3 ? 'text-bg-success' : ($p['stock'] > 0 ? 'text-bg-warning' : 'text-bg-danger') ?>">
                            <?= fa_num($p['stock']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $p['status'] ? 'text-bg-success' : 'text-bg-secondary' ?>">
                            <?= $p['status'] ? 'فعال' : 'غیرفعال' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= admin_url('product-edit', ['id' => $p['id']]) ?>"
                               class="btn btn-sm btn-outline-primary" title="ویرایش">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="post" action="<?= admin_url('product-delete') ?>"
                                  class="confirm-delete-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
