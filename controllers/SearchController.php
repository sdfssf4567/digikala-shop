<?php
/**
 * ===============================================
 * کنترلر جستجوی زنده (Ajax)
 * با تاخیر تایپ کاربر، نتایج آنی نمایش داده می‌شود
 * ===============================================
 */

class SearchController
{
    public function live(): void
    {
        $q = trim(input('q', ''));

        // حداقل ۲ حرف برای جستجو لازم است
        if (mb_strlen($q) < 2) {
            json_response(['results' => []]);
        }

        $products = Product::liveSearch($q, 6);
        $results  = [];

        foreach ($products as $p) {
            $final = discounted_price($p['price'], $p['discount_percent']);
            $results[] = [
                'id'    => (int)$p['id'],
                'title' => $p['title'],
                'image' => BASE_URL . '/' . ltrim($p['image'] ?? 'assets/images/products/placeholder.svg', '/'),
                'price' => fa_price($final),
                'url'   => url('product', ['id' => $p['id']]),
            ];
        }

        json_response([
            'results' => $results,
            'all_url' => url('products', ['q' => $q]),
        ]);
    }
}
