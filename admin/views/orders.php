<?php
/**
 * ===============================================
 * لیست سفارش‌ها در پنل مدیریت
 * متغیرها: $orders, $status
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">مدیریت سفارش‌ها</h4>

<!-- فیلتر وضعیت -->
<ul class="nav nav-pills mb-3 admin-filter-pills">
    <li class="nav-item">
        <a class="nav-link <?= $status === '' ? 'active' : '' ?>" href="<?= admin_url('orders') ?>">همه</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" href="<?= admin_url('orders', ['status' => 'pending']) ?>">در انتظار پردازش</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'shipped' ? 'active' : '' ?>" href="<?= admin_url('orders', ['status' => 'shipped']) ?>">ارسال شده</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'delivered' ? 'active' : '' ?>" href="<?= admin_url('orders', ['status' => 'delivered']) ?>">تحویل شده</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'canceled' ? 'active' : '' ?>" href="<?= admin_url('orders', ['status' => 'canceled']) ?>">لغو شده</a>
    </li>
</ul>

<div class="admin-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="small text-muted">
                    <th>شماره سفارش</th>
                    <th>مشتری</th>
                    <th>تاریخ</th>
                    <th>مبلغ</th>
                    <th>پرداخت</th>
                    <th>وضعیت فعلی</th>
                    <th>تغییر وضعیت</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">سفارشی یافت نشد.</td></tr>
            <?php endif; ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="fw-bold ltr-input-inline small"><?= e($order['order_number']) ?></td>
                    <td class="small">
                        <div><?= e($order['customer_name'] ?? 'کاربر حذف شده') ?></div>
                        <div class="text-muted" style="font-size:.72rem"><?= e($order['customer_email'] ?? '') ?></div>
                    </td>
                    <td class="small"><?= fa_datetime($order['created_at']) ?></td>
                    <td class="fw-bold small"><?= fa_price($order['final_amount']) ?></td>
                    <td class="small"><?= payment_method_label($order['payment_method']) ?></td>
                    <td><span class="badge text-bg-<?= order_status_class($order['status']) ?>"><?= order_status_label($order['status']) ?></span></td>
                    <td>
                        <!-- فرم تغییر سریع وضعیت -->
                        <form method="post" action="<?= admin_url('order-status') ?>" class="d-flex gap-1">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                            <select name="status" class="form-select form-select-sm" style="width:auto">
                                <option value="pending"   <?= $order['status'] === 'pending'   ? 'selected' : '' ?>>در انتظار</option>
                                <option value="shipped"   <?= $order['status'] === 'shipped'   ? 'selected' : '' ?>>ارسال شد</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>تحویل شد</option>
                                <option value="canceled"  <?= $order['status'] === 'canceled'  ? 'selected' : '' ?>>لغو</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary">ثبت</button>
                        </form>
                    </td>
                    <td>
                        <a href="<?= admin_url('order-view', ['id' => $order['id']]) ?>"
                           class="btn btn-sm btn-outline-secondary" title="مشاهده">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
