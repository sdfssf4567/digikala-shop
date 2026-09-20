<?php
/**
 * ===============================================
 * لیست کاربران در پنل مدیریت
 * متغیر: $users
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">مدیریت کاربران</h4>

<div class="admin-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="small text-muted">
                    <th>#</th>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>موبایل</th>
                    <th>نقش</th>
                    <th>تعداد سفارش</th>
                    <th>تاریخ عضویت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="small text-muted"><?= fa_num($user['id']) ?></td>
                    <td class="small fw-bold">
                        <?= e($user['full_name']) ?>
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge text-bg-dark">مدیر</span>
                        <?php endif; ?>
                    </td>
                    <td class="small ltr-input-inline"><?= e($user['email']) ?></td>
                    <td class="small ltr-input-inline"><?= e($user['phone']) ?></td>
                    <td class="small"><?= $user['role'] === 'admin' ? 'مدیریت' : 'مشتری' ?></td>
                    <td><span class="badge text-bg-light border"><?= fa_num($user['orders_count']) ?></span></td>
                    <td class="small"><?= fa_date($user['created_at']) ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <form method="post" action="<?= admin_url('user-delete') ?>" class="confirm-delete-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف کاربر">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
