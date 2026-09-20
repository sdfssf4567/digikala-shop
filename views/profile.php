<?php
/**
 * ===============================================
 * صفحه پروفایل کاربر - دارای سه بخش:
 * ویرایش اطلاعات | سفارش‌ها | (علاقه‌مندی‌ها صفحه جدا دارد)
 * متغیرها: $user, $activeTab, $orders (در تب سفارش‌ها)
 * ===============================================
 */

$activeTab = $activeTab ?? 'profile';
?>
<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-person-circle ms-2"></i>حساب کاربری</h4>

    <div class="row g-4">
        <!-- ================= منوی کنار پروفایل ================= -->
        <aside class="col-lg-3">
            <div class="profile-menu border rounded-3 bg-white overflow-hidden">
                <div class="p-3 border-bottom bg-light">
                    <div class="d-flex align-items-center">
                        <div class="profile-avatar"><i class="bi bi-person"></i></div>
                        <div class="ms-2">
                            <div class="fw-bold"><?= e($user['full_name']) ?></div>
                            <div class="small text-muted"><?= e($user['email']) ?></div>
                        </div>
                    </div>
                </div>
                <ul class="list-unstyled mb-0 profile-menu-list">
                    <li>
                        <a href="<?= url('profile') ?>" class="<?= $activeTab === 'profile' ? 'active' : '' ?>">
                            <i class="bi bi-person ms-2"></i> ویرایش اطلاعات
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('orders') ?>" class="<?= $activeTab === 'orders' ? 'active' : '' ?>">
                            <i class="bi bi-box-seam ms-2"></i> سفارش‌های من
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('wishlist') ?>">
                            <i class="bi bi-heart ms-2"></i> علاقه‌مندی‌ها
                        </a>
                    </li>
                    <li><a href="<?= url('logout') ?>" class="text-danger"><i class="bi bi-box-arrow-left ms-2"></i> خروج</a></li>
                </ul>
            </div>
        </aside>

        <!-- ================= محتوای اصلی ================= -->
        <div class="col-lg-9">
            <?php if ($activeTab === 'profile'): ?>
                <!-- ---------- ویرایش اطلاعات ---------- -->
                <div class="border rounded-3 bg-white p-4 mb-4">
                    <h6 class="fw-bold mb-3">اطلاعات شخصی</h6>
                    <form method="post" action="<?= url('profile/update') ?>" class="row g-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="info">

                        <div class="col-md-6">
                            <label class="form-label small">نام و نام خانوادگی</label>
                            <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">ایمیل (غیرقابل تغییر)</label>
                            <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">شماره موبایل</label>
                            <input type="text" name="phone" class="form-control ltr-input" value="<?= e($user['phone']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">تاریخ عضویت</label>
                            <input type="text" class="form-control" value="<?= fa_date($user['created_at']) ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">آدرس پیش‌فرض (برای سفارش‌ها)</label>
                            <textarea name="address" class="form-control" rows="2"><?= e($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                        </div>
                    </form>
                </div>

                <!-- ---------- تغییر رمز عبور ---------- -->
                <div class="border rounded-3 bg-white p-4">
                    <h6 class="fw-bold mb-3">تغییر رمز عبور</h6>
                    <form method="post" action="<?= url('profile/update') ?>" class="row g-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="password">

                        <div class="col-md-4">
                            <label class="form-label small">رمز عبور فعلی</label>
                            <input type="password" name="current_password" class="form-control ltr-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">رمز عبور جدید</label>
                            <input type="password" name="new_password" class="form-control ltr-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">تکرار رمز عبور جدید</label>
                            <input type="password" name="new_password_confirm" class="form-control ltr-input" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary">تغییر رمز عبور</button>
                        </div>
                    </form>
                </div>

            <?php elseif ($activeTab === 'orders'): ?>
                <!-- ---------- لیست سفارش‌ها ---------- -->
                <div class="border rounded-3 bg-white p-4">
                    <h6 class="fw-bold mb-3">سفارش‌های من</h6>

                    <?php if (empty($orders)): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-box-seam"></i>
                            <p class="text-muted small mb-0">هنوز سفارشی ثبت نکرده‌اید.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr class="small text-muted">
                                        <th>شماره سفارش</th>
                                        <th>تاریخ ثبت</th>
                                        <th>تعداد اقلام</th>
                                        <th>مبلغ</th>
                                        <th>وضعیت</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="ltr-input-inline fw-bold"><?= e($order['order_number']) ?></td>
                                        <td class="small"><?= fa_datetime($order['created_at']) ?></td>
                                        <td><?= fa_num($order['items_count']) ?> کالا</td>
                                        <td class="fw-bold"><?= fa_price($order['final_amount']) ?></td>
                                        <td><span class="badge text-bg-<?= order_status_class($order['status']) ?>"><?= order_status_label($order['status']) ?></span></td>
                                        <td>
                                            <a href="<?= url('order/view', ['id' => $order['id']]) ?>"
                                               class="btn btn-sm btn-outline-primary">جزئیات</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
