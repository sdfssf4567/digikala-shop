/**
 * ===============================================
 * اسکریپت پنل مدیریت
 * شامل: تایید حذف‌ها، افزودن سطر مشخصات، منوی موبایل
 * ===============================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    /* ---------- تایید حذف قبل از ارسال فرم ---------- */
    document.querySelectorAll('.confirm-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('آیا از انجام این عملیات مطمئن هستید؟ این عمل قابل بازگشت نیست.')) {
                e.preventDefault();
            }
        });
    });

    /* ---------- افزودن سطر مشخصات فنی در فرم محصول ---------- */
    const addSpecBtn = document.getElementById('add-spec-row');
    if (addSpecBtn) {
        addSpecBtn.addEventListener('click', function () {
            const container = document.getElementById('specs-rows');
            if (!container) return;

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'specs[]';
            input.className = 'form-control ltr-input-inline mb-2';
            input.placeholder = 'عنوان|مقدار';
            container.appendChild(input);
            input.focus();
        });
    }

    /* ---------- باز و بسته کردن منو در موبایل ---------- */
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        // بستن منو با کلیک بیرون از آن
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992 && !e.target.closest('#admin-sidebar') && !e.target.closest('#sidebar-toggle')) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* ---------- پیش‌نمایش تصاویر انتخاب شده (فرم محصول) ---------- */
    const imageInput = document.querySelector('input[name="images[]"]');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            if (this.files.length > 1) {
                // نمایش تعداد فایل‌های انتخاب شده
                let note = this.parentElement.querySelector('.selected-count');
                if (!note) {
                    note = document.createElement('div');
                    note.className = 'small text-success mt-1 selected-count';
                    this.parentElement.appendChild(note);
                }
                note.textContent = 'تعداد ' + this.files.length + ' تصویر انتخاب شد.';
            }
        });
    }
});
