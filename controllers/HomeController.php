<?php
/**
 * ===============================================
 * کنترلر صفحه اصلی فروشگاه
 * ===============================================
 */

class HomeController
{
    public function index(): void
    {
        $db = Database::getInstance();

        // داده‌های صفحه اصلی
        $sliderBanners    = Banner::slider();
        $middleBanners    = Banner::middle();
        $categories       = Category::mainCategories();
        $categoryCounts   = Category::productCounts();
        $specialOffers    = Product::specialOffers(8);
        $latestProducts   = Product::latest(10);
        $bestSellers      = Product::bestSellers(10);
        $topDiscounted    = Product::topDiscounted(8);
        $brands           = Brand::all();

        $pageTitle = SITE_TAGLINE;

        include APP_ROOT . '/views/layouts/header.php';
        include APP_ROOT . '/views/home.php';
        include APP_ROOT . '/views/layouts/footer.php';
    }
}
