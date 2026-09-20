<?php
/**
 * ===============================================
 * کنترلر مدیریت دسته‌بندی‌ها در پنل ادمین
 * ===============================================
 */

class CategoryController
{
    /**
     * لیست دسته‌بندی‌ها + فرم افزودن/ویرایش
     */
    public function index(): void
    {
        $categories = Category::all(false);
        $editItem   = null;

        // در حالت ویرایش، دسته‌بندی انتخابی خوانده می‌شود
        $editId = (int)input('edit', '0');
        if ($editId) {
            $editItem = Category::find($editId);
        }

        $pageTitle = 'مدیریت دسته‌بندی‌ها';

        include ADMIN_ROOT . '/views/layout/header.php';
        include ADMIN_ROOT . '/views/categories.php';
        include ADMIN_ROOT . '/views/layout/footer.php';
    }

    /**
     * ذخیره دسته‌بندی (افزودن یا ویرایش)
     */
    public function save(): void
    {
        csrf_check_or_die();

        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if (mb_strlen($name) < 2) {
            set_flash('danger', 'نام دسته‌بندی باید حداقل ۲ حرف باشد.');
            redirect(admin_url('categories'));
        }

        $data = [
            'name'       => $name,
            'slug'       => slugify($_POST['slug'] ?? $name),
            'parent_id'  => (int)($_POST['parent_id'] ?? 0),
            'icon'       => trim($_POST['icon'] ?? 'bi-grid'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => !empty($_POST['status']) ? 1 : 0,
        ];

        if ($id) {
            Category::update($id, $data);
            set_flash('success', 'دسته‌بندی ویرایش شد.');
        } else {
            Category::create($data);
            set_flash('success', 'دسته‌بندی جدید اضافه شد.');
        }

        redirect(admin_url('categories'));
    }

    /**
     * حذف دسته‌بندی
     */
    public function delete(): void
    {
        csrf_check_or_die();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Category::delete($id);
            set_flash('success', 'دسته‌بندی حذف شد.');
        }
        redirect(admin_url('categories'));
    }
}
