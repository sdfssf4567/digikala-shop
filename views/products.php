<?php
/**
 * ===============================================
 * صفحه لیست محصولات - فیلتر دسته/برند/قیمت + مرتب‌سازی + صفحه‌بندی
 * ===============================================
 */

// ساخت رشته کوئری برای حفظ فیلترها در لینک‌های صفحه‌بندی
$buildQuery = function (array $overrides = []) {
    $params = $_GET;
    foreach ($overrides as $key => $value) {
        if ($value === null) {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    unset($params['route']);
    return '?' . http_build_query(array_merge(['route' => 'products'], $params));
};

$selectedBrandIds = array_map('intval', (array)($filters['brand_ids'] ?? []));
?>
<div class="container my-4">

    <!-- مسیر راهنما -->
    <nav class="breadcrumb-nav small mb-3">
        <a href="<?= url('home') ?>">دیجی‌شاپ</a>
        <?php if ($currentCategory): ?>
            <span>/</span>
            <span class="text-muted"><?= e($currentCategory['name']) ?></span>
        <?php elseif ($filters['q']): ?>
            <span>/</span>
            <span class="text-muted">نتایج جستجو</span>
        <?php else: ?>
            <span>/</span>
            <span class="text-muted">همه محصولات</span>
        <?php endif; ?>
    </nav>

    <div class="row g-4">
        <!-- ================= ستون فیلترها ================= -->
        <aside class="col-lg-3">
            <form method="get" action="<?= url('products') ?>" id="filter-form">
                <input type="hidden" name="route" value="products">
                <?php if ($filters['q']): ?>
                    <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                <?php endif; ?>

                <div class="filter-card">
                    <h6 class="filter-title"><i class="bi bi-funnel-fill"></i> فیلترها</h6>

                    <!-- دسته‌بندی‌ها -->
                    <div class="filter-group">
                        <div class="filter-group-title">دسته‌بندی</div>
                        <a href="<?= $buildQuery(['cat' => null, 'page' => null]) ?>"
                           class="filter-cat <?= !$currentCategory ? 'active' : '' ?>">همه دسته‌ها</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?= $buildQuery(['cat' => $cat['slug'], 'page' => null]) ?>"
                               class="filter-cat <?= ($currentCategory && $currentCategory['id'] == $cat['id']) ? 'active' : '' ?>">
                                <i class="bi <?= e($cat['icon'] ?: 'bi-grid') ?>"></i> <?= e($cat['name']) ?>
                                <span class="count"><?= fa_num($categoryCounts[$cat['id']] ?? 0) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- برندها -->
                    <div class="filter-group">
                        <div class="filter-group-title">برند</div>
                        <?php foreach ($brands as $brand): ?>
                            <div class="form-check">
                                <input class="form-check-input brand-checkbox" type="checkbox" name="brands[]"
                                       value="<?= (int)$brand['id'] ?>" id="brand-<?= $brand['id'] ?>"
                                       <?= in_array((int)$brand['id'], $selectedBrandIds, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="brand-<?= $brand['id'] ?>"><?= e($brand['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- محدوده قیمت -->
                    <div class="filter-group">
                        <div class="filter-group-title">محدوده قیمت (تومان)</div>
                        <div class="d-flex gap-2 mb-2">
                            <input type="text" class="form-control form-control-sm" name="min_price"
                                   placeholder="از" value="<?= e($filters['min_price']) ?>" inputmode="numeric">
                            <input type="text" class="form-control form-control-sm" name="max_price"
                                   placeholder="تا" value="<?= e($filters['max_price']) ?>" inputmode="numeric">
                        </div>
                    </div>

                    <!-- فقط کالاهای موجود -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="available" value="1" id="available-check"
                               <?= !empty($filters['only_available']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="available-check">فقط کالاهای موجود</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">اعمال فیلتر</button>
                    <a href="<?= url('products') ?>" class="btn btn-link btn-sm w-100 mt-1 text-muted">حذف فیلترها</a>
                </div>
            </form>
        </aside>

        <!-- ================= نتایج ================= -->
        <div class="col-lg-9">
            <!-- نوار بالای نتایج: تعداد + مرتب‌سازی -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 results-bar gap-2">
                <span class="small text-muted-2"><i class="bi bi-box-seam ms-1"></i> <?= fa_num($result['total']) ?> کالا یافت شد</span>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted-2">مرتب‌سازی:</label>
                    <select id="sort-select" class="form-select form-select-sm">
                        <option value="newest"  <?= $sort === 'newest'  ? 'selected' : '' ?>>جدیدترین</option>
                        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>محبوب‌ترین</option>
                        <option value="cheap"   <?= $sort === 'cheap'   ? 'selected' : '' ?>>ارزان‌ترین</option>
                        <option value="expensive" <?= $sort === 'expensive' ? 'selected' : '' ?>>گران‌ترین</option>
                        <option value="discount"  <?= $sort === 'discount'  ? 'selected' : '' ?>>بیشترین تخفیف</option>
                    </select>
                </div>
            </div>

            <!-- چیپ‌های فیلتر فعال -->
            <?php if ($currentCategory || $filters['q']): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($currentCategory): ?>
                        <a href="<?= $buildQuery(['cat' => null, 'page' => null]) ?>" class="active-chip">
                            <i class="bi bi-tag-fill text-danger"></i> <?= e($currentCategory['name']) ?> <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($filters['q']): ?>
                        <a href="<?= $buildQuery(['q' => null, 'page' => null]) ?>" class="active-chip">
                            <i class="bi bi-search text-danger"></i> « <?= e($filters['q']) ?> » <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($result['items'])): ?>
                <!-- حالت بدون نتیجه -->
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <h6>کالایی با این مشخصات پیدا نشد!</h6>
                    <p class="text-muted small">فیلترها را تغییر دهید یا عبارت دیگری جستجو کنید.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($result['items'] as $p): include APP_ROOT . '/views/partials/product_card.php'; endforeach; ?>
                </div>

                <!-- ================= صفحه‌بندی ================= -->
                <?php if ($result['pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $result['page'] <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $buildQuery(['page' => $result['page'] - 1]) ?>">قبلی</a>
                            </li>
                            <?php for ($i = 1; $i <= $result['pages']; $i++): ?>
                                <li class="page-item <?= $i === (int)$result['page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $buildQuery(['page' => $i]) ?>"><?= fa_num($i) ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $result['page'] >= $result['pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $buildQuery(['page' => $result['page'] + 1]) ?>">بعدی</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
