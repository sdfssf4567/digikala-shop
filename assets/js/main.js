/**
 * ===============================================
 * اسکریپت اصلی فروشگاه
 * شامل: جستجوی زنده، سبد خرید Ajax، گالری تصاویر،
 * امتیازدهی ستاره‌ای، علاقه‌مندی‌ها و ابزارهای عمومی
 * ===============================================
 */

'use strict';

/* ---------------------------------------------
 * ابزار عمومی
 * --------------------------------------------- */

/** ساخت آدرس مطلق از مسیر نسبی assets */
function assetUrl(path) {
    const base = window.location.origin;
    // استخراج آدرس پایه از اولین اسکریپت یا href موجود در صفحه
    const anchor = document.querySelector('link[href*="/assets/"]');
    if (anchor) {
        const href = anchor.getAttribute('href');
        return href.substring(0, href.indexOf('/assets/')) + '/' + path;
    }
    return base + '/' + path;
}

/** نمایش پیام توست کوتاه */
function showToast(message, type = 'default') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = 'dk-toast ' + type;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3200);
}

/** درخواست POST با FormData */
async function postForm(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    return await response.json();
}

/* ---------------------------------------------
 * جستجوی زنده در هدر
 * --------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('live-search-input');
    const resultsBox  = document.getElementById('live-search-results');
    let searchTimer   = null;

    if (searchInput && resultsBox) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim();

            // بستن نتایج اگر کمتر از ۲ حرف
            if (q.length < 2) {
                resultsBox.classList.add('d-none');
                resultsBox.innerHTML = '';
                return;
            }

            clearTimeout(searchTimer);
            // تاخیر ۳۰۰ میلی‌ثانیه برای کاهش تعداد درخواست‌ها
            searchTimer = setTimeout(async () => {
                try {
                    const url = assetUrl('') + 'index.php?route=search/live&q=' + encodeURIComponent(q);
                    const res  = await fetch(url);
                    const data = await res.json();

                    if (!data.results || data.results.length === 0) {
                        resultsBox.innerHTML = '<div class="p-3 text-center text-muted small">نتیجه‌ای یافت نشد.</div>';
                        resultsBox.classList.remove('d-none');
                        return;
                    }

                    let html = data.results.map(item => `
                        <a href="${item.url}" class="live-result-item">
                            <img src="${item.image}" alt="">
                            <div class="flex-grow-1">
                                <div class="live-result-title">${item.title}</div>
                                <div class="live-result-price">${item.price}</div>
                            </div>
                        </a>
                    `).join('');

                    html += `<a href="${data.all_url}" class="live-results-more">مشاهده همه نتایج</a>`;

                    resultsBox.innerHTML = html;
                    resultsBox.classList.remove('d-none');
                } catch (e) {
                    // در صورت خطای شبکه، باکس بسته می‌شود
                    resultsBox.classList.add('d-none');
                }
            }, 300);
        });

        // بستن باکس با کلیک بیرون از آن
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-box')) {
                resultsBox.classList.add('d-none');
            }
        });
    }
});

/* ---------------------------------------------
 * سبد خرید با Ajax
 * --------------------------------------------- */

/** افزودن محصول به سبد خرید */
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('[data-add-to-cart]');
    if (!btn || btn.disabled) return;

    const productId = btn.dataset.addToCart;
    // در صفحه محصول، تعداد از باکس انتخاب تعداد خوانده می‌شود
    const qtyInput = document.getElementById('qty-input');
    const quantity = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('csrf_token', getCsrfToken());

    // حالت بارگذاری دکمه
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm ms-2"></span>';

    try {
        const data = await postForm(assetUrl('') + 'index.php?route=cart/add', formData);

        if (data.ok) {
            updateCartBadge(data.count);
            showToast(data.message, 'success');
        } else if (data.need_login) {
            showToast('برای خرید ابتدا وارد حساب کاربری شوید.', 'error');
            setTimeout(() => window.location.href = data.login_url, 1200);
        } else {
            showToast(data.error || 'خطا در افزودن به سبد خرید.', 'error');
        }
    } catch (err) {
        showToast('خطای شبکه! دوباره تلاش کنید.', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = originalHtml;
});

/** دریافت توکن CSRF از اولین فیلد مخفی صفحه */
function getCsrfToken() {
    const input = document.querySelector('input[name="csrf_token"]');
    return input ? input.value : '';
}

/** به‌روزرسانی نشان تعداد سبد در هدر و نویگیشن موبایل */
function updateCartBadge(count) {
    const badges = document.querySelectorAll('#cart-count-badge, #cart-count-badge-mobile');
    badges.forEach(badge => {
        if (count > 0) {
            badge.textContent = toPersianDigits(count);
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    });
}

/** تبدیل رقم به فارسی برای نمایش */
function toPersianDigits(num) {
    const fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return String(num).replace(/\d/g, d => fa[+d]);
}

/* ---------------------------------------------
 * تغییر تعداد و حذف اقلام در صفحه سبد خرید
 * --------------------------------------------- */
document.addEventListener('click', async function (e) {
    const qtyBtn = e.target.closest('[data-cart-qty]');
    if (!qtyBtn) return;

    const cartId = qtyBtn.dataset.cartId;
    const input  = document.querySelector(`.cart-qty-input[data-cart-id="${cartId}"]`);
    if (!input) return;

    let qty = parseInt(input.value, 10) || 1;
    qty = qtyBtn.dataset.cartQty === 'plus' ? qty + 1 : qty - 1;
    if (qty < 1) qty = 1;

    await updateCartQuantity(cartId, qty, input);
});

// ثبت تعداد با تغییر مستقیم فیلد
document.addEventListener('change', async function (e) {
    const input = e.target.closest('.cart-qty-input');
    if (!input) return;
    let qty = parseInt(input.value, 10) || 1;
    if (qty < 1) qty = 1;
    await updateCartQuantity(input.dataset.cartId, qty, input);
});

/** ارسال درخواست تغییر تعداد به سرور */
async function updateCartQuantity(cartId, qty, input) {
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', qty);
    formData.append('csrf_token', getCsrfToken());

    try {
        const data = await postForm(assetUrl('') + 'index.php?route=cart/update', formData);
        if (data.ok) {
            input.value = qty;
            updateCartBadge(data.count);
            // به‌روزرسانی مبالغ خلاصه سفارش
            const sumEl     = document.getElementById('cart-sum');
            const savingsEl = document.getElementById('cart-savings');
            const payEl     = document.getElementById('cart-payable');
            if (sumEl) sumEl.textContent = data.sum;
            if (savingsEl) savingsEl.textContent = data.savings;
            if (payEl) payEl.textContent = data.payable;
        } else {
            showToast(data.error || 'خطا در تغییر تعداد.', 'error');
        }
    } catch (err) {
        showToast('خطای شبکه!', 'error');
    }
}

/** حذف قلم از سبد */
document.addEventListener('click', async function (e) {
    const removeBtn = e.target.closest('[data-cart-remove]');
    if (!removeBtn) return;

    if (!confirm('این کالا از سبد خرید حذف شود؟')) return;

    const cartId = removeBtn.dataset.cartRemove;
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('csrf_token', getCsrfToken());

    try {
        const data = await postForm(assetUrl('') + 'index.php?route=cart/remove', formData);
        if (data.ok) {
            updateCartBadge(data.count);
            const row = document.querySelector(`[data-cart-item="${cartId}"]`);
            if (row) row.remove();

            // اگر سبد خالی شد، صفحه دوباره بارگذاری می‌شود
            if (data.empty) {
                window.location.reload();
                return;
            }

            // به‌روزرسانی مبالغ
            const sumEl     = document.getElementById('cart-sum');
            const savingsEl = document.getElementById('cart-savings');
            const payEl     = document.getElementById('cart-payable');
            if (sumEl) sumEl.textContent = data.sum;
            if (savingsEl) savingsEl.textContent = data.savings;
            if (payEl) payEl.textContent = data.payable;

            showToast('کالا از سبد حذف شد.', 'success');
        }
    } catch (err) {
        showToast('خطای شبکه!', 'error');
    }
});

/* ---------------------------------------------
 * علاقه‌مندی‌ها (کلید قلب)
 * --------------------------------------------- */
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('[data-wishlist-toggle]');
    if (!btn) return;

    const productId = btn.dataset.wishlistToggle;
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('csrf_token', getCsrfToken());

    try {
        const data = await postForm(assetUrl('') + 'index.php?route=wishlist/toggle', formData);

        if (data.ok) {
            showToast(data.message, 'success');
            // تغییر ظاهر آیکون قلب
            const icon = btn.querySelector('i');
            if (data.in_wishlist) {
                icon.className = 'bi bi-heart-fill ms-1';
            } else {
                icon.className = 'bi bi-heart ms-1';
            }
        } else if (data.need_login) {
            showToast('ابتدا وارد حساب کاربری شوید.', 'error');
            setTimeout(() => window.location.href = data.login_url, 1200);
        } else {
            showToast(data.error || 'خطا!', 'error');
        }
    } catch (err) {
        showToast('خطای شبکه!', 'error');
    }
});

/* ---------------------------------------------
 * گالری تصاویر صفحه محصول
 * --------------------------------------------- */
document.addEventListener('click', function (e) {
    const thumb = e.target.closest('[data-gallery-thumb]');
    if (!thumb) return;

    const mainImage = document.getElementById('gallery-main-image');
    if (mainImage) {
        mainImage.src = thumb.src;
    }

    // هایلایت تصویر انتخاب شده
    document.querySelectorAll('[data-gallery-thumb]').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
});

/* ---------------------------------------------
 * انتخاب تعداد در صفحه محصول
 * --------------------------------------------- */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-qty]');
    if (!btn) return;

    const input = document.getElementById('qty-input');
    if (!input) return;

    let qty  = parseInt(input.value, 10) || 1;
    const max = parseInt(input.dataset.maxStock, 10) || 99;

    if (btn.dataset.qty === 'plus' && qty < max) qty++;
    if (btn.dataset.qty === 'minus' && qty > 1) qty--;

    input.value = qty;
});

/* ---------------------------------------------
 * امتیازدهی ستاره‌ای فرم نظر
 * --------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    const ratingBox = document.querySelector('.star-rating-input');
    if (!ratingBox) return;

    const stars = ratingBox.querySelectorAll('[data-star]');
    const input = document.getElementById('rating-input');
    const text  = ratingBox.querySelector('.rating-text');
    const labels = { 1: 'خیلی بد', 2: 'بد', 3: 'متوسط', 4: 'خوب', 5: 'عالی' };

    function paint(value) {
        stars.forEach(s => {
            s.classList.toggle('active', parseInt(s.dataset.star, 10) <= value);
        });
        if (text && labels[value]) text.textContent = labels[value];
    }

    stars.forEach(star => {
        star.addEventListener('mouseenter', () => paint(parseInt(star.dataset.star, 10)));
        star.addEventListener('mouseleave', () => paint(parseInt(input.value, 10)));
        star.addEventListener('click', () => {
            input.value = star.dataset.star;
            paint(parseInt(star.dataset.star, 10));
        });
    });

    paint(5);
});

/* ---------------------------------------------
 * مرتب‌سازی نتایج صفحه محصولات
 * --------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('route', 'products');
            currentUrl.searchParams.set('sort', this.value);
            window.location.href = currentUrl.href;
        });
    }
});

/* ---------------------------------------------
 * فلش‌های ناوبری اسکرول افقی (اسلایدر محصولات)
 * --------------------------------------------- */
document.addEventListener('click', function (e) {
    const nav = e.target.closest('[data-strip-nav]');
    if (!nav) return;

    const strip = document.getElementById(nav.dataset.stripNav);
    if (!strip) return;

    // جهت: قبلی = به راست، بعدی = به چپ (در چیدمان RTL)
    const step = Math.max(strip.clientWidth * 0.7, 300);
    strip.scrollBy({
        left: nav.dataset.stripNav === 'prev' ? -step : step,
        behavior: 'smooth'
    });
});

/* فعال/غیرفعال کردن فلش‌ها بر اساس موقعیت اسکرول */
function refreshStripNavs() {
    document.querySelectorAll('[data-strip-nav]').forEach(nav => {
        const strip = document.getElementById(nav.dataset.stripNav);
        if (!strip) return;
        if (nav.dataset.stripNav === 'prev') {
            nav.disabled = strip.scrollLeft <= 2;
        } else {
            nav.disabled = strip.scrollLeft + strip.clientWidth >= strip.scrollWidth - 2;
        }
    });
}
document.addEventListener('DOMContentLoaded', () => {
    refreshStripNavs();
    document.querySelectorAll('.strip').forEach(strip => {
        strip.addEventListener('scroll', refreshStripNavs, { passive: true });
    });
    window.addEventListener('resize', refreshStripNavs);
});

/* ---------------------------------------------
 * تایمر شمارش معکوس پیشنهاد شگفت‌انگیز
 * ورودی: ثانیه باقی‌مانده در data-countdown
 * --------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    const timerEl = document.getElementById('amazing-timer');
    if (!timerEl) return;

    let remaining = parseInt(timerEl.dataset.countdown, 10) || 0;
    const cells = {
        h: timerEl.querySelector('[data-unit="h"]'),
        m: timerEl.querySelector('[data-unit="m"]'),
        s: timerEl.querySelector('[data-unit="s"]')
    };

    function paint() {
        const t = Math.max(0, remaining);
        const h = Math.floor(t / 3600);
        const m = Math.floor((t % 3600) / 60);
        const s = t % 60;
        const pad = n => String(n).padStart(2, '0');
        if (cells.h) cells.h.textContent = toPersianDigits(pad(h));
        if (cells.m) cells.m.textContent = toPersianDigits(pad(m));
        if (cells.s) cells.s.textContent = toPersianDigits(pad(s));
    }

    paint();
    setInterval(() => {
        if (remaining > 0) remaining--;
        paint();
    }, 1000);
});

/* ---------------------------------------------
 * دکمه بازگشت به بالا
 * --------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    const backBtn = document.getElementById('back-to-top');
    if (!backBtn) return;

    window.addEventListener('scroll', function () {
        backBtn.classList.toggle('show', window.scrollY > 300);
    });

    backBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
