<?php
/**
 * ===============================================
 * مدیریت نظرات در پنل ادمین - تایید/رد/حذف
 * متغیرها: $comments, $status
 * ===============================================
 */
?>
<h4 class="fw-bold mb-4">مدیریت نظرات</h4>

<!-- فیلتر وضعیت -->
<ul class="nav nav-pills mb-3 admin-filter-pills">
    <li class="nav-item">
        <a class="nav-link <?= $status === '' ? 'active' : '' ?>" href="<?= admin_url('comments') ?>">همه</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" href="<?= admin_url('comments', ['status' => 'pending']) ?>">
            در انتظار تایید
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'approved' ? 'active' : '' ?>" href="<?= admin_url('comments', ['status' => 'approved']) ?>">تایید شده</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'rejected' ? 'active' : '' ?>" href="<?= admin_url('comments', ['status' => 'rejected']) ?>">رد شده</a>
    </li>
</ul>

<?php if (empty($comments)): ?>
    <div class="admin-card p-4 text-center text-muted">نظری یافت نشد.</div>
<?php else: ?>
    <?php foreach ($comments as $comment): ?>
        <div class="admin-card p-3 mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <strong class="small"><?= e($comment['user_name'] ?? 'کاربر حذف شده') ?></strong>
                        <span class="text-warning small">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi <?= $i <= (int)$comment['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="badge text-bg-<?= $comment['status'] === 'approved' ? 'success' : ($comment['status'] === 'pending' ? 'warning' : 'secondary') ?>">
                            <?= $comment['status'] === 'approved' ? 'تایید شده' : ($comment['status'] === 'pending' ? 'در انتظار' : 'رد شده') ?>
                        </span>
                        <span class="text-muted" style="font-size:.72rem"><?= fa_datetime($comment['created_at']) ?></span>
                    </div>

                    <?php if ($comment['title']): ?>
                        <div class="fw-bold small mb-1"><?= e($comment['title']) ?></div>
                    <?php endif; ?>
                    <p class="small mb-2 lh-lg"><?= e($comment['body']) ?></p>

                    <div class="text-muted small">
                        <i class="bi bi-box ms-1"></i>
                        محصول:
                        <?php if ($comment['product_id']): ?>
                            <a href="<?= url('product', ['id' => $comment['product_id']]) ?>" target="_blank">
                                <?= e(str_limit($comment['product_title'] ?? 'محصول حذف شده', 50)) ?>
                            </a>
                        <?php else: ?>
                            محصول حذف شده
                        <?php endif; ?>
                    </div>
                </div>

                <!-- دکمه‌های عملیات -->
                <div class="d-flex flex-column gap-2 flex-shrink-0">
                    <?php if ($comment['status'] !== 'approved'): ?>
                        <form method="post" action="<?= admin_url('comment-status') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$comment['id'] ?>">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-check2 ms-1"></i> تایید
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if ($comment['status'] !== 'rejected'): ?>
                        <form method="post" action="<?= admin_url('comment-status') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$comment['id'] ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-warning w-100">
                                <i class="bi bi-x ms-1"></i> رد
                            </button>
                        </form>
                    <?php endif; ?>
                    <form method="post" action="<?= admin_url('comment-delete') ?>" class="confirm-delete-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$comment['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi bi-trash3 ms-1"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
