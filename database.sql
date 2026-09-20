-- =====================================================
-- دیتابیس فروشگاه اینترنتی دیجی‌شاپ
-- نسخه: 1.0.0 | سازگار با MySQL 5.7+ و MariaDB 10.3+
-- نحوه نصب: در phpMyAdmin دیتابیس digikala_shop را بسازید
-- و سپس این فایل را Import کنید.
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- ساخت دیتابیس (اگر از قبل ساخته‌اید این بخش را اجرا نکنید)
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS `digikala_shop`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `digikala_shop`;

-- -----------------------------------------------------
-- جدول کاربران
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name`   VARCHAR(100)  NOT NULL COMMENT 'نام و نام خانوادگی',
  `email`       VARCHAR(120)  NOT NULL COMMENT 'ایمیل (یکتا)',
  `phone`       VARCHAR(20)   NOT NULL COMMENT 'شماره موبایل',
  `password`    VARCHAR(255)  NOT NULL COMMENT 'رمز عبور هش شده',
  `role`        ENUM('admin','user') NOT NULL DEFAULT 'user' COMMENT 'نقش کاربر',
  `address`     TEXT          NULL COMMENT 'آدرس پیش‌فرض',
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='کاربران سایت';

-- -----------------------------------------------------
-- جدول دسته‌بندی‌ها
-- -----------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  NOT NULL COMMENT 'نام دسته‌بندی',
  `slug`        VARCHAR(120)  NOT NULL COMMENT 'نامک انگلیسی',
  `parent_id`   INT UNSIGNED  NULL DEFAULT NULL COMMENT 'دسته والد (برای زیر دسته‌ها)',
  `icon`        VARCHAR(60)   NULL DEFAULT 'bi-grid' COMMENT 'کلاس آیکون بوت‌استرپ',
  `image`       VARCHAR(255)  NULL,
  `sort_order`  INT           NOT NULL DEFAULT 0,
  `status`      TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `idx_categories_parent` (`parent_id`),
  CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`)
    REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='دسته‌بندی محصولات';

-- -----------------------------------------------------
-- جدول برندها
-- -----------------------------------------------------
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  NOT NULL COMMENT 'نام برند',
  `slug`        VARCHAR(120)  NOT NULL,
  `logo`        VARCHAR(255)  NULL COMMENT 'لوگوی برند',
  `status`      TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_brands_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='برندها';

-- -----------------------------------------------------
-- جدول محصولات
-- -----------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`             VARCHAR(255) NOT NULL COMMENT 'عنوان محصول',
  `slug`              VARCHAR(280) NOT NULL,
  `category_id`       INT UNSIGNED NULL,
  `brand_id`          INT UNSIGNED NULL,
  `price`             BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'قیمت به تومان',
  `discount_percent`  TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'درصد تخفیف',
  `stock`             INT          NOT NULL DEFAULT 0 COMMENT 'موجودی انبار',
  `short_description` VARCHAR(500) NULL,
  `description`       TEXT         NULL COMMENT 'توضیحات کامل',
  `specifications`    TEXT         NULL COMMENT 'مشخصات فنی (JSON)',
  `is_new`            TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'نشان جدید',
  `is_special`        TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'پیشنهاد شگفت‌انگیز',
  `views`             INT UNSIGNED NOT NULL DEFAULT 0,
  `status`            TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_brand` (`brand_id`),
  KEY `idx_products_status` (`status`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`)
    REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`)
    REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='محصولات فروشگاه';

-- -----------------------------------------------------
-- جدول تصاویر محصولات (گالری چند تصویری)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`  INT UNSIGNED NOT NULL,
  `image`       VARCHAR(255) NOT NULL COMMENT 'مسیر تصویر',
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='گالری تصاویر محصول';

-- -----------------------------------------------------
-- جدول سفارش‌ها
-- -----------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT UNSIGNED NULL,
  `order_number`    VARCHAR(30)  NOT NULL COMMENT 'شماره سفارش یکتا',
  `total_amount`    BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'مبلغ کل',
  `discount_amount` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'مبلغ تخفیف',
  `final_amount`    BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'مبلغ نهایی پرداخت',
  `status`          ENUM('pending','shipped','delivered','canceled') NOT NULL DEFAULT 'pending',
  `receiver_name`   VARCHAR(100) NOT NULL COMMENT 'نام گیرنده',
  `phone`           VARCHAR(20)  NOT NULL,
  `province`        VARCHAR(60)  NOT NULL,
  `city`            VARCHAR(60)  NOT NULL,
  `postal_code`     VARCHAR(10)  NOT NULL,
  `address`         TEXT         NOT NULL,
  `note`            TEXT         NULL,
  `payment_method`  ENUM('online','cod') NOT NULL DEFAULT 'online',
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='سفارش‌ها';

-- -----------------------------------------------------
-- جدول اقلام سفارش
-- -----------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`      INT UNSIGNED NOT NULL,
  `product_id`    INT UNSIGNED NULL,
  `product_title` VARCHAR(255) NOT NULL COMMENT 'عنوان محصول در زمان خرید',
  `image`         VARCHAR(255) NULL,
  `price`         BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'قیمت واحد بعد از تخفیف',
  `quantity`      INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `idx_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='اقلام سفارش';

-- -----------------------------------------------------
-- جدول سبد خرید
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED NOT NULL,
  `quantity`    INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_user_product` (`user_id`, `product_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='سبد خرید کاربران';

-- -----------------------------------------------------
-- جدول نظرات و امتیازدهی
-- -----------------------------------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`  INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED NULL,
  `title`       VARCHAR(255) NULL COMMENT 'عنوان نظر',
  `body`        TEXT         NOT NULL COMMENT 'متن نظر',
  `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'امتیاز ۱ تا ۵',
  `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comments_product` (`product_id`),
  KEY `idx_comments_status` (`status`),
  CONSTRAINT `fk_comments_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='نظرات محصولات';

-- -----------------------------------------------------
-- جدول علاقه‌مندی‌ها
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist_user_product` (`user_id`, `product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='لیست علاقه‌مندی';

-- -----------------------------------------------------
-- جدول بنرها و اسلایدر
-- -----------------------------------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(150) NOT NULL,
  `image`       VARCHAR(255) NOT NULL,
  `link`        VARCHAR(255) NULL,
  `position`    ENUM('slider','middle') NOT NULL DEFAULT 'slider',
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `status`      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='بنرهای تبلیغاتی';

-- -----------------------------------------------------
-- جدول تنظیمات
-- -----------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(80)  NOT NULL,
  `setting_value` TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='تنظیمات سایت';

-- =====================================================
-- داده‌های نمونه
-- =====================================================

-- --------- دسته‌بندی‌ها (۵ دسته اصلی + ۳ زیر دسته) ---------
INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `icon`, `sort_order`, `status`) VALUES
(1, 'موبایل و تبلت',      'mobile',        NULL, 'bi-phone',      1, 1),
(2, 'گوشی موبایل',        'mobile-phone',  1,    'bi-phone',      1, 1),
(3, 'تبلت',               'tablet',        1,    'bi-tablet',     2, 1),
(4, 'لپ‌تاپ و کامپیوتر',  'laptop',        NULL, 'bi-laptop',     2, 1),
(5, 'لوازم جانبی',        'accessories',   NULL, 'bi-headphones', 3, 1),
(6, 'صوتی و تصویری',      'audio',         5,    'bi-speaker',    1, 1),
(7, 'پوشاک',              'clothing',      NULL, 'bi-bag-check',  4, 1),
(8, 'خانه و آشپزخانه',    'home',          NULL, 'bi-house-door', 5, 1);

-- --------- برندها ---------
INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `status`) VALUES
(1,  'سامسونگ',        'samsung',  'assets/images/brands/samsung.svg',  1),
(2,  'اپل',            'apple',    'assets/images/brands/apple.svg',    1),
(3,  'شیائومی',        'xiaomi',   'assets/images/brands/xiaomi.svg',   1),
(4,  'ایسوس',          'asus',     'assets/images/brands/asus.svg',     1),
(5,  'اچ‌پی',          'hp',       'assets/images/brands/hp.svg',       1),
(6,  'ال‌جی',          'lg',       'assets/images/brands/lg.svg',       1),
(7,  'نوکیا',          'nokia',    'assets/images/brands/nokia.svg',    1),
(8,  'لاجیتک',         'logitech', 'assets/images/brands/logitech.svg', 1),
(9,  'نایک',           'nike',     'assets/images/brands/nike.svg',     1),
(10, 'آدیداس',         'adidas',   'assets/images/brands/adidas.svg',   1),
(11, 'فیلیپس',         'philips',  'assets/images/brands/philips.svg',  1),
(12, 'تفال',           'tefal',    'assets/images/brands/tefal.svg',    1),
(13, 'جی‌بی‌ال',       'jbl',      'assets/images/brands/jbl.svg',      1),
(14, 'وسترن دیجیتال',  'wd',       'assets/images/brands/wd.svg',       1),
(15, 'انکر',           'anker',    NULL,                                1);

-- --------- کاربران (۱ مدیر + ۲ کاربر) ---------
-- رمز عبور مدیر: admin123 | رمز کاربران: 12345678
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `address`) VALUES
(1, 'مدیر سایت',  'admin@shop.ir',    '09120000000', '$2y$10$epWNf0mYrILUDPYlADM19.AkAHikPwdj8paaZhuhmlUnZQDD8r6qG', 'admin', 'تهران، خیابان ولیعصر، پلاک ۱'),
(2, 'علی رضایی',  'ali@example.com',  '09121112233', '$2y$10$Bgup3lL6.QVfBmvjdSQTluP4ZHOr2O9zMmMGBSAXBaqutQcpZ0nGG', 'user',  'تهران، سعادت‌آباد، خیابان علامه شمالی'),
(3, 'سارا محمدی', 'sara@example.com', '09123334455', '$2y$10$Bgup3lL6.QVfBmvjdSQTluP4ZHOr2O9zMmMGBSAXBaqutQcpZ0nGG', 'user',  'اصفهان، خیابان چهارباغ بالا');

-- --------- محصولات (۲۴ محصول) ---------
INSERT INTO `products`
(`id`, `title`, `slug`, `category_id`, `brand_id`, `price`, `discount_percent`, `stock`, `short_description`, `description`, `specifications`, `is_new`, `is_special`, `views`, `status`) VALUES
(1, 'گوشی موبایل سامسونگ Galaxy S24 Ultra ظرفیت ۲۵۶ گیگابایت', 'samsung-galaxy-s24-ultra', 2, 1, 52900000, 8, 15,
 'گوشی موبایل سامسونگ گلکسی S24 اولترا با تراشه Snapdragon 8 Gen 3، دوربین ۲۰۰ مگاپیکسل و صفحه نمایش ۶.۸ اینچی',
 'گوشی موبایل سامسونگ Galaxy S24 Ultra پرچم‌دار سال ۲۰۲۴ شرکت سامسونگ است. این گوشی با بهره‌گیری از تراشه قدرتمند Snapdragon 8 Gen 3 و ۱۲ گیگابایت رم، عملکردی بینظیر در اجرای بازی‌ها و برنامه‌های سنگین ارائه می‌دهد. دوربین ۲۰۰ مگاپیکسلی آن با قابلیت زوم اپتیکال ۵ برابری، تصاویری با کیفیت استودیویی ثبت می‌کند. قلم S Pen داخلی نیز تجربه کاربری را برای یادداشت‌برداری و طراحی به سطح جدیدی می‌برد. باتری ۵۰۰۰ میلی‌آمپر ساعت با شارژ سریع ۴۵ وات، همراهی مطمئن در تمام روز است.',
 '{" صفحه نمایش":"6.8 اینچ Dynamic AMOLED 2X","پردازنده":"Snapdragon 8 Gen 3","رم":"12 گیگابایت","حافظه داخلی":"256 گیگابایت","دوربین اصلی":"200 مگاپیکسل","باتری":"5000 میلی‌آمپر ساعت"}', 1, 1, 842, 1),

(2, 'گوشی موبایل اپل iPhone 15 Pro Max ظرفیت ۲۵۶ گیگابایت', 'apple-iphone-15-pro-max', 2, 2, 78500000, 5, 8,
 'گوشی موبایل اپل آیفون ۱۵ پرو مکس با تراشه A17 Pro، بدنه تیتانیومی و دوربین سه‌گانه ۴۸ مگاپیکسل',
 'iPhone 15 Pro Max جدیدترین پرچم‌دار اپل با بدنه سبک تیتانیوم و تراشه A17 Pro است. صفحه نمایش Super Retina XDR با نرخ نوسازی ۱۲۰ هرتز، تصاویری روان و درخشان ارائه می‌دهد. سیستم دوربین سه‌گانه با سنسور اصلی ۴۸ مگاپیکسل و لنز تله‌فوتو ۵ برابر، امکان فیلم‌برداری سینمایی ۴K را فراهم می‌کند. پورت USB-C جایگزین Lightning شده و انتقال داده با سرعت USB 3 انجام می‌شود.',
 '{" صفحه نمایش":"6.7 اینچ Super Retina XDR","پردازنده":"Apple A17 Pro","رم":"8 گیگابایت","حافظه داخلی":"256 گیگابایت","دوربین اصلی":"48 مگاپیکسل","بدنه":"تیتانیوم"}', 1, 1, 1240, 1),

(3, 'گوشی موبایل شیائومی Redmi Note 13 Pro ظرفیت ۲۵۶ گیگابایت', 'xiaomi-redmi-note-13-pro', 2, 3, 12800000, 15, 25,
 'گوشی موبایل شیائومی ردمی نوت ۱۳ پرو با دوربین ۲۰۰ مگاپیکسل و شارژ سریع ۶۷ وات',
 'Redmi Note 13 Pro بهترین انتخاب در بازه میان‌رده است. دوربین ۲۰۰ مگاپیکسلی با OIS، صفحه نمایش AMOLED 120 هرتز و باتری ۵۱۰۰ میلی‌آمپر با شارژ سریع ۶۷ وات از ویژگی‌های برجسته این گوشی هستند. طراحی شیشه‌ای خوش‌دست و بدنه مسطح، حس پرچم‌داری را به کاربر منتقل می‌کند.',
 '{" صفحه نمایش":"6.67 اینچ AMOLED","پردازنده":"Snapdragon 7s Gen 2","رم":"8 گیگابایت","حافظه داخلی":"256 گیگابایت","دوربین اصلی":"200 مگاپیکسل","شارژ":"67 وات"}', 1, 1, 654, 1),

(4, 'گوشی موبایل سامسونگ Galaxy A55 ظرفیت ۱۲۸ گیگابایت', 'samsung-galaxy-a55', 2, 1, 18400000, 10, 20,
 'گوشی موبایل سامسونگ گلکسی A55 با بدنه فلزی، صفحه نمایش Super AMOLED و دوربین ۵۰ مگاپیکسل',
 'Galaxy A55 میان‌رده محبوب سامسونگ با بدنه فلزی و شیشه‌ای است. صفحه نمایش Super AMOLED 6.6 اینچی با نرخ نوسازی ۱۲۰ هرتز و پردازنده Exynos 1480 عملکرد روانی ارائه می‌دهد. دوربین ۵۰ مگاپیکسل با OIS و باتری ۵۰۰۰ میلی‌آمپر از نقاط قوت این گوشی هستند.',
 '{" صفحه نمایش":"6.6 اینچ Super AMOLED","پردازنده":"Exynos 1480","رم":"8 گیگابایت","حافظه داخلی":"128 گیگابایت","دوربین اصلی":"50 مگاپیکسل"}', 1, 0, 431, 1),

(5, 'گوشی موبایل نوکیا 105 (۲۰۲۳)', 'nokia-105-2023', 2, 7, 1150000, 0, 40,
 'گوشی موبایل نوکیا ۱۰۵ با باتری پرقدرت و طراحی کلاسیک',
 'گوشی نوکیا 105 انتخابی مطمئن برای تماس‌های روزانه است. باتری ۱۰۰۰ میلی‌آمپر تا چند روز شارژ نگه می‌دارد، صفحه رنگی ۱.۸ اینچی و طراحی مقاوم از ویژگی‌های این گوشی ساده و کاربردی است.',
 '{" صفحه نمایش":"1.8 اینچ رنگی","باتری":"1000 میلی‌آمپر ساعت","دو سیم‌کارت":"بله"}', 0, 0, 122, 1),

(6, 'تبلت سامسونگ Galaxy Tab S9 ظرفیت ۱۲۸ گیگابایت', 'samsung-galaxy-tab-s9', 3, 1, 39700000, 7, 10,
 'تبلت سامسونگ گلکسی تپ S9 با قلم S Pen و صفحه نمایش ۱۱ اینچی AMOLED',
 'گلکسی تپ S9 با صفحه نمایش ۱۱ اینچی Dynamic AMOLED 2X و نرخ نوسازی ۱۲۰ هرتز، بهترین گزینه برای تماشای فیلم و کارهای حرفه‌ای است. قلم S Pen همراه محصول است و تراشه Snapdragon 8 Gen 2 قدرت اجرای هر برنامه‌ای را دارد.',
 '{" صفحه نمایش":"11 اینچ Dynamic AMOLED","پردازنده":"Snapdragon 8 Gen 2","رم":"8 گیگابایت","قلم":"S Pen همراه"}', 1, 0, 298, 1),

(7, 'لپ‌تاپ ۱۳ اینچی اپل MacBook Air M3', 'apple-macbook-air-m3', 4, 2, 68900000, 5, 6,
 'لپ‌تاپ اپل مک‌بوک ایر M3 با تراشه M3، ۸ گیگابایت رم و ۲۵۶ گیگابایت SSD',
 'MacBook Air M3 با طراحی بی‌نظیر بدون فن و تراشه M3 اپل، ترکیبی از قدرت و ظرافت است. عمر باتری تا ۱۸ ساعت، نمایشگر Liquid Retina و وزن تنها ۱.۲۴ کیلوگرم، آن را به بهترین انتخاب برای کارهای روزمره، برنامه‌نویسی و طراحی تبدیل کرده است.',
 '{" پردازنده":"Apple M3","رم":"8 گیگابایت Unified","حافظه":"256 گیگابایت SSD","نمایشگر":"13.6 اینچ Liquid Retina","باتری":"تا 18 ساعت"}', 1, 1, 512, 1),

(8, 'لپ‌تاپ ۱۵ اینچی ایسوس VivoBook 15 X1504', 'asus-vivobook-15', 4, 4, 32500000, 12, 12,
 'لپ‌تاپ ایسوس ویوو بوک ۱۵ با پردازنده Core i5 نسل ۱۲ و ۱۶ گیگابایت رم',
 'VivoBook 15 لپ‌تاپی مقرون‌به‌صرفه برای کارهای اداری و دانشجویی است. پردازنده Core i5-1235U با ۱۰ هسته، رم ۱۶ گیگابایت و SSD ۵۱۲ گیگابایتی، سرعت قابل قبولی در همه کارها ارائه می‌دهد. صفحه نمایش NanoEdge با حاشیه باریک تجربه دیدنی لذت‌بخشی می‌سازد.',
 '{" پردازنده":"Intel Core i5-1235U","رم":"16 گیگابایت DDR4","حافظه":"512 گیگابایت SSD","نمایشگر":"15.6 اینچ Full HD"}', 1, 0, 342, 1),

(9, 'لپ‌تاپ ۱۵ اینچی اچ‌پی Pavilion 15', 'hp-pavilion-15', 4, 5, 41200000, 0, 9,
 'لپ‌تاپ اچ‌پی پاویلیون ۱۵ با پردازنده Ryzen 7 و گرافیک RX 6500M',
 'Pavilion 15 ترکیبی از قدرت و زیبایی است. پردازنده AMD Ryzen 7 5825U با گرافیک اختصاصی Radeon RX 6500M، توانایی اجرای بازی‌های متوسط و نرم‌افزارهای گرافیکی را دارد. بدنه تمام فلزی و کیبورد بک‌لایت از نقاط قوت آن هستند.',
 '{" پردازنده":"AMD Ryzen 7 5825U","رم":"16 گیگابایت","حافظه":"512 گیگابایت SSD","گرافیک":"Radeon RX 6500M"}', 0, 0, 187, 1),

(10, 'هدفون بی‌سیم اپل AirPods Pro 2 (USB-C)', 'apple-airpods-pro-2', 5, 2, 11900000, 20, 18,
 'هدفون بی‌سیم اپل ایرپادز پرو ۲ با حذف نویز فعال و صدای فضایی',
 'AirPods Pro نسل دوم با تراشه H2، کیفیت صدایی بی‌نظیر و حذف نویز فعال دو برابر نسل قبل ارائه می‌دهد. حالت شفافیت تطبیقی، صدای فضایی با ردیابی حرکت سر و مقاومت IP54 از ویژگی‌های آن هستند. کیس شارژ با اسپیکر Find My و حداکثر ۳۰ ساعت پخش صدا.',
 '{" حذف نویز":"فعال (ANC)","تراشه":"Apple H2","زمان پخش":"تا 30 ساعت با کیس","مقاومت":"IP54"}', 1, 1, 723, 1),

(11, 'ساعت هوشمند شیائومی Redmi Watch 4', 'xiaomi-redmi-watch-4', 5, 3, 3450000, 25, 30,
 'ساعت هوشمند شیائومی ردمی واچ ۴ با صفحه نمایش ۱.۹۷ اینچی و ۱۵۰ حالت ورزشی',
 'Redmi Watch 4 با نمایشگر بزرگ AMOLED 1.97 اینچی و بدنه آلومینیومی، ظاهری پرچم‌داری دارد. باتری تا ۲۰ روز دوام می‌آورد و بیش از ۱۵۰ حالت ورزشی، سنجش ضربان قلب و اکسیژن خون را پشتیبانی می‌کند. ضد آب 5ATM.',
 '{" نمایشگر":"1.97 اینچ AMOLED","باتری":"تا 20 روز","حالت ورزشی":"150+","ضد آب":"5ATM"}', 1, 1, 486, 1),

(12, 'پاوربانک شیائومی ۲۰۰۰۰ میلی‌آمپر ساعت Pro', 'xiaomi-powerbank-20000', 5, 3, 1890000, 10, 35,
 'پاوربانک ۲۰۰۰۰ میلی‌آمپری شیائومی با شارژ سریع ۶۵ وات',
 'پاوربانک Pro شیائومی با ظرفیت ۲۰۰۰۰ و توان خروجی ۶۵ وات، حتی لپ‌تاپ‌ها را شارژ می‌کند. دو پورت USB-A و یک پورت USB-C با قابلیت ورودی/خروجی سریع. نمایشگر دیجیتال درصد شارژ.',
 '{" ظرفیت":"20000 میلی‌آمپر ساعت","توان":"65 وات","پورت‌ها":"2x USB-A + USB-C"}', 0, 0, 311, 1),

(13, 'ماوس گیمینگ لاجیتک G102 Lightsync', 'logitech-g102', 5, 8, 1250000, 0, 50,
 'ماوس گیمینگ لاجیتک G102 با حسگر ۸۰۰۰ DPI و نورپردازی RGB',
 'G102 محبوب‌ترین ماوس گیمینگ اقتصادی است. حسگر ۸۰۰۰ DPI، ۶ دکمه قابل برنامه‌ریزی و نورپردازی Lightsync RGB با ۱۶.۸ میلیون رنگ. طراحی متقارن برای دست‌های چپ و راست.',
 '{" حسگر":"8000 DPI","دکمه":"6 عدد قابل برنامه‌ریزی","نورپردازی":"RGB Lightsync"}', 0, 0, 425, 1),

(14, 'کیبورد بی‌سیم لاجیتک K380 Multi-Device', 'logitech-k380', 5, 8, 2150000, 8, 22,
 'کیبورد بلوتوثی لاجیتک K380 با اتصال همزمان به ۳ دستگاه',
 'K380 کیبورد جمع‌وجور و کم‌صدا برای کار و خانه. اتصال همزمان به ۳ دستگاه (کامپیوتر، تبلت، گوشی) با یک دکمه. عمر باتری ۲۴ ماه با دو عدد باتری AAA.',
 '{" اتصال":"بلوتوث + دانگل","دستگاه":"تا 3 دستگاه همزمان","باتری":"24 ماه"}', 0, 0, 168, 1),

(15, 'کابل شارژ تایپ سی انکر PowerLine ۱ متری', 'anker-powerline-usbc', 5, 15, 850000, 5, 60,
 'کابل شارژ انکر با روکش مقاوم و پشتیبانی از شارژ سریع ۶۰ وات',
 'کابل PowerLine انکر با ۲۵۰۰۰ بار تست خم شدن، ماندگاری فوق‌العاده‌ای دارد. پشتیبانی از شارژ سریع ۶۰ وات و انتقال داده ۴۸۰ مگابیت. طول ۱ متر با روکش پارچه‌ای مقاوم.',
 '{" طول":"1 متر","توان":"تا 60 وات","روکش":"پارچه‌ای مقاوم"}', 0, 0, 289, 1),

(16, 'پایه نگهدارنده گوشی رومیزی قابل تنظیم', 'desk-phone-stand', 5, NULL, 420000, 0, 80,
 'پایه رومیزی آلومینیومی گوشی با زاویه تنظیم و محفظه کابل',
 'پایه رومیزی فلزی با روکش سیلیکونی ضد لغزش و زاویه قابل تنظیم. مناسب برای ویدیو کال، تماشای فیلم و شارژ گوشی. محفظه عبور کابل برای شارژ همزمان.',
 '{" جنس":"آلومینیوم","زاویه":"قابل تنظیم","سازگار":"گوشی و تبلت تا ۱۲ اینچ"}', 0, 0, 94, 1),

(17, 'کفش ورزشی مردانه نایک Air Max SC', 'nike-air-max-sc', 7, 9, 4850000, 15, 25,
 'کفش ورزشی مردانه نایک ایر مکس با کفی هوایی و رویه مشبک',
 'کفش Air Max SC نایک با کفی هوایی معروف Air Max، راحتی بی‌نظیری در دویدن و پیاده‌روی فراهم می‌کند. رویه مشبک تنفسی و زیره لاستیکی مقاوم. مناسب استفاده روزمره و ورزش‌های سبک.',
 '{" جنس رویه":"مش تنفسی","کفی":"هوایی Air Max","کاربری":"روزمره و دویدن سبک"}', 1, 1, 376, 1),

(18, 'تیشرت ورزشی مردانه آدیداس D2M', 'adidas-d2m-tshirt', 7, 10, 1350000, 0, 45,
 'تیشرت ورزشی مردانه آدیداس با پارچه خشک‌کن AEROREADY',
 'تیشرت D2M آدیداس با تکنولوژی AEROREADY عرق را سریع خشک می‌کند و بدن را خنک نگه می‌دارد. برش آزاد (Regular Fit) و جنس پلی‌استر نرم و سبک.',
 '{" جنس":"پلی‌استر بازیافتی","تکنولوژی":"AEROREADY","برش":"Regular Fit"}', 0, 0, 145, 1),

(19, 'کتری برقی فیلیپس ۱.۷ لیتر HD9350', 'philips-kettle-hd9350', 8, 11, 3200000, 10, 14,
 'کتری برقی فیلیپس ۱۷۰۰ وات با مخزن استیل و جوش سریع',
 'کتری برقی فیلیپس با توان ۱۷۰۰ وات آب را در چند دقیقه می‌جوشاند. مخزن استیل ضد زنگ ۱.۷ لیتری، فیلتر توری ضد رسوب و چراغ نشانگر روشن بودن. خاموشی خودکار در جوش آمدن.',
 '{" توان":"1700 وات","ظرفیت":"1.7 لیتر","جنس":"استیل ضد زنگ","ایمنی":"خاموشی خودکار"}', 1, 1, 233, 1),

(20, 'جاروبرقی ال‌جی VC61 بدون کیسه ۲۱۰۰ وات', 'lg-vacuum-vc61', 8, 6, 24900000, 8, 7,
 'جاروبرقی ال‌جی بدون کیسه با فیلتر HEPA و قدرت مکش ۲۱۰۰ وات',
 'جاروبرقی VC61 ال‌جی با تکنولوژی سیکلون بدون کیسه، قدرت مکش پایداری ارائه می‌دهد. فیلتر HEPA برای آلرژی‌ها، مخزن شفاف قابل شستشو و برس چرخان دو موتوره. سبک و کم‌صدا.',
 '{" توان":"2100 وات","نوع":"بدون کیسه (سیکلون)","فیلتر":"HEPA","کابل":"باز و جمع شونده خودکار"}', 0, 0, 178, 1),

(21, 'ست قابلمه تفال ۹ پارچه Titanium Unlimited', 'tefal-cookware-9pc', 8, 12, 8700000, 12, 11,
 'ست کامل ۹ پارچه قابلمه و تابه تفال با پوشش تیتانیوم',
 'ست Titanium Unlimited تفال با پوشش ضد خش تیتانیوم ماندگاری بالا دارد. کف القایی مناسب همه اجاق‌ها از جمله گازی و القایی. دسته‌های نسوز و درب‌های شیشه‌ای با دریچه بخار.',
 '{" تعداد":"9 پارچه","پوشش":"تیتانیوم ضد خش","کف":"القایی (همه اجاق‌ها)"}', 1, 0, 205, 1),

(22, 'ساعت هوشمند اپل Watch SE (نسل دوم) ۴۰ میلی‌متری', 'apple-watch-se-2', 5, 2, 15500000, 6, 9,
 'ساعت هوشمند اپل واچ SE با تراشه S8 و تشخیص تصادف',
 'Apple Watch SE نسل دوم با تراشه S8، سرعت عملکرد بالایی دارد. سنجش ضربان قلب، تشخیص سقوط و تصادف، ردیابی خواب و بیش از ده‌ها حالت ورزشی. سازگاری کامل با آیفون.',
 '{" نمایشگر":"Retina LTPO","تراشه":"Apple S8","مقاومت":"ضد آب 50 متر","حالت ورزشی":"+"}', 1, 0, 367, 1),

(23, 'اسپیکر بلوتوثی جی‌بی‌ال Go 4', 'jbl-go-4', 6, 13, 2750000, 18, 27,
 'اسپیکر قابل حمل جی‌بی‌ال Go 4 با صدای پرقدرت و ضد آب IP67',
 'Go 4 کوچک‌ترین اسپیکر خانواده JBL با صدای JBL Pro Sound است. ضد غبار و آب IP67، ۷ ساعت پخش موسیقی و حالت PartyBoost برای اتصال به اسپیکرهای دیگر.',
 '{" صدای":"JBL Pro","ضد آب":"IP67","باتری":"7 ساعت","وزت":"فقط 190 گرم"}', 1, 1, 298, 1),

(24, 'هارد اکسترنال وسترن دیجیتال My Passport ۱ ترابایت', 'wd-my-passport-1tb', 5, 14, 4900000, 0, 16,
 'هارد اکسترنال وسترن دیجیتال ۱ ترابایت USB 3.2 با نرم‌افزار رمزگذاری',
 'هارد My Passport با طراحی جمع‌وجور و ظرفیت ۱ ترابایت، بهترین گزینه برای بکاپ‌گیری است. سرعت انتقال USB 3.2 Gen 1 و نرم‌افزار رمزگذاری با AES 256 بیتی. سه سال گارانتی.',
 '{" ظرفیت":"1 ترابایت","رابط":"USB 3.2 Gen 1","امنیت":"رمزگذاری AES 256","گارانتی":"3 ساله"}', 0, 0, 264, 1);

-- --------- گالری تصاویر محصولات (۳ تصویر برای هر محصول) ---------
INSERT INTO `product_images` (`product_id`, `image`, `sort_order`) VALUES
(1,  'assets/images/products/p01-1.svg', 1), (1,  'assets/images/products/p01-2.svg', 2), (1,  'assets/images/products/p01-3.svg', 3),
(2,  'assets/images/products/p02-1.svg', 1), (2,  'assets/images/products/p02-2.svg', 2), (2,  'assets/images/products/p02-3.svg', 3),
(3,  'assets/images/products/p03-1.svg', 1), (3,  'assets/images/products/p03-2.svg', 2), (3,  'assets/images/products/p03-3.svg', 3),
(4,  'assets/images/products/p04-1.svg', 1), (4,  'assets/images/products/p04-2.svg', 2), (4,  'assets/images/products/p04-3.svg', 3),
(5,  'assets/images/products/p05-1.svg', 1), (5,  'assets/images/products/p05-2.svg', 2), (5,  'assets/images/products/p05-3.svg', 3),
(6,  'assets/images/products/p06-1.svg', 1), (6,  'assets/images/products/p06-2.svg', 2), (6,  'assets/images/products/p06-3.svg', 3),
(7,  'assets/images/products/p07-1.svg', 1), (7,  'assets/images/products/p07-2.svg', 2), (7,  'assets/images/products/p07-3.svg', 3),
(8,  'assets/images/products/p08-1.svg', 1), (8,  'assets/images/products/p08-2.svg', 2), (8,  'assets/images/products/p08-3.svg', 3),
(9,  'assets/images/products/p09-1.svg', 1), (9,  'assets/images/products/p09-2.svg', 2), (9,  'assets/images/products/p09-3.svg', 3),
(10, 'assets/images/products/p10-1.svg', 1), (10, 'assets/images/products/p10-2.svg', 2), (10, 'assets/images/products/p10-3.svg', 3),
(11, 'assets/images/products/p11-1.svg', 1), (11, 'assets/images/products/p11-2.svg', 2), (11, 'assets/images/products/p11-3.svg', 3),
(12, 'assets/images/products/p12-1.svg', 1), (12, 'assets/images/products/p12-2.svg', 2), (12, 'assets/images/products/p12-3.svg', 3),
(13, 'assets/images/products/p13-1.svg', 1), (13, 'assets/images/products/p13-2.svg', 2), (13, 'assets/images/products/p13-3.svg', 3),
(14, 'assets/images/products/p14-1.svg', 1), (14, 'assets/images/products/p14-2.svg', 2), (14, 'assets/images/products/p14-3.svg', 3),
(15, 'assets/images/products/p15-1.svg', 1), (15, 'assets/images/products/p15-2.svg', 2), (15, 'assets/images/products/p15-3.svg', 3),
(16, 'assets/images/products/p16-1.svg', 1), (16, 'assets/images/products/p16-2.svg', 2), (16, 'assets/images/products/p16-3.svg', 3),
(17, 'assets/images/products/p17-1.svg', 1), (17, 'assets/images/products/p17-2.svg', 2), (17, 'assets/images/products/p17-3.svg', 3),
(18, 'assets/images/products/p18-1.svg', 1), (18, 'assets/images/products/p18-2.svg', 2), (18, 'assets/images/products/p18-3.svg', 3),
(19, 'assets/images/products/p19-1.svg', 1), (19, 'assets/images/products/p19-2.svg', 2), (19, 'assets/images/products/p19-3.svg', 3),
(20, 'assets/images/products/p20-1.svg', 1), (20, 'assets/images/products/p20-2.svg', 2), (20, 'assets/images/products/p20-3.svg', 3),
(21, 'assets/images/products/p21-1.svg', 1), (21, 'assets/images/products/p21-2.svg', 2), (21, 'assets/images/products/p21-3.svg', 3),
(22, 'assets/images/products/p22-1.svg', 1), (22, 'assets/images/products/p22-2.svg', 2), (22, 'assets/images/products/p22-3.svg', 3),
(23, 'assets/images/products/p23-1.svg', 1), (23, 'assets/images/products/p23-2.svg', 2), (23, 'assets/images/products/p23-3.svg', 3),
(24, 'assets/images/products/p24-1.svg', 1), (24, 'assets/images/products/p24-2.svg', 2), (24, 'assets/images/products/p24-3.svg', 3);

-- --------- بنرهای اسلایدر و بنرهای وسط صفحه ---------
INSERT INTO `banners` (`title`, `image`, `link`, `position`, `sort_order`, `status`) VALUES
('جشنواره دیجی‌شاپ - تا ۵۰٪ تخفیف', 'assets/images/banners/b1.svg', 'index.php?route=products&sort=discount', 'slider', 1, 1),
('فروش ویژه پوشاک',                 'assets/images/banners/b2.svg', 'index.php?route=products&cat=clothing',  'slider', 2, 1),
('خانه هوشمند - کالاهای برقی',       'assets/images/banners/b3.svg', 'index.php?route=products&cat=home',      'slider', 3, 1),
('ارسال رایگان',                    'assets/images/banners/m1.svg', 'index.php?route=products',               'middle', 1, 1),
('پرداخت در محل',                   'assets/images/banners/m2.svg', 'index.php?route=products',               'middle', 2, 1);

-- --------- نظرات نمونه (۹ تایید شده + ۲ در انتظار) ---------
INSERT INTO `comments` (`product_id`, `user_id`, `title`, `body`, `rating`, `status`) VALUES
(1, 2, 'کیفیت فوق‌العاده', 'بعد از دو هفته استفاده، فقط می‌تونم بگم عالیه. دوربینش در شب واقعاً شگفت‌زده‌ام کرد و باتری هم راحت یک روز کامل جواب می‌ده.', 5, 'approved'),
(1, 3, 'ارزش خرید داره', 'قیمتش بالاست ولی با این تخفیف واقعاً ارزش خرید داره. قلم S Pen هم خیلی به کارم میاد.', 4, 'approved'),
(2, 2, 'بهترین انتخاب', 'از آیفون ۱۳ ارتقا دادم و تفاوت به‌ویژه در دوربین تله‌فوتو کاملاً محسوسه. بدنه تیتانیوم هم خیلی سبک شده.', 5, 'approved'),
(3, 3, 'برای قیمتش عالیه', 'انتظار بیشتر از این نداشتم. شارژ سریعش خیلی سریع پُر میشه و نمایشگر AMOLED هم رنگ‌ها رو عالی نشون می‌ده.', 4, 'approved'),
(10, 2, 'صدای خیلی خوبی داره', 'حذف نویزش واقعاً کار می‌کنه، تو مترو تقریباً هیچ صدایی شنیده نمی‌شه. جعبه هم شیک و بسته‌بندی سالم بود.', 5, 'approved'),
(17, 3, 'راحت و سبک', 'سایز ۴۱ گرفتم و دقیقاً اندازه بود. کفی هوایی پاهام رو تو پیاده‌روی‌های طولانی خسته نمی‌کنه.', 4, 'approved'),
(11, 2, 'بیشتر از انتظارم', 'برای این قیمت هیچ ساعت هوشمندی به این خوبی نیست. باتری واقعاً ۱۵ روز دووم آورد.', 5, 'approved'),
(7, 3, 'سبک و خنک', 'برای برنامه‌نویسی خریدم و بدون فن هم هیچ گرمایی حس نمی‌شه. کیفیت ساخت اپل هم که دیگه حرف نداره.', 4, 'approved'),
(19, 2, 'تند گرم میشه', 'خیلی سریع آب رو جوش میاره ولی صدای جوشیدنش کمی بلنده. در کل برای قیمتش راضی‌ام.', 3, 'approved'),
(21, 3, 'بسته‌بندی خوب', 'تازه رسید، کیفیت پوشش ضد خش معلومه که خوبه. اگه بعد از یه ماه استفاده مشکلی نبود، امتیاز کامل می‌دم.', 4, 'pending'),
(13, 2, 'دقت خوب', 'برای بازی‌های شوتر دقت و پاسخ‌دهی‌اش فوق‌العاده‌ست. نرم‌افزار G Hub هم تنظیمات کامل می‌ده.', 5, 'pending');

-- --------- علاقه‌مندی‌های نمونه ---------
INSERT INTO `wishlist` (`user_id`, `product_id`) VALUES
(2, 2), (2, 7), (3, 1), (3, 10), (3, 17);

-- --------- سبد خرید نمونه (برای کاربر علی) ---------
INSERT INTO `cart` (`user_id`, `product_id`, `quantity`) VALUES
(2, 4, 1), (2, 13, 2);

-- --------- سفارش‌های نمونه ---------
INSERT INTO `orders`
(`id`, `user_id`, `order_number`, `total_amount`, `discount_amount`, `final_amount`, `status`, `receiver_name`, `phone`, `province`, `city`, `postal_code`, `address`, `payment_method`, `created_at`) VALUES
(1, 2, 'DS-260815-4F2A91', 16580000, 2298000, 14282000, 'delivered', 'علی رضایی', '09121112233', 'تهران', 'تهران', '1998745632', 'سعادت‌آباد، خیابان علامه شمالی، پلاک ۱۲، واحد ۳', 'online', DATE_SUB(NOW(), INTERVAL 14 DAY)),
(2, 3, 'DS-260820-8B3C22', 4312500, 862500, 3450000, 'shipped', 'سارا محمدی', '09123334455', 'اصفهان', 'اصفهان', '8158812345', 'خیابان چهارباغ بالا، کوچه گلها، پلاک ۷', 'cod', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(3, 2, 'DS-260828-1D7E55', 59457500, 4274500, 55183000, 'pending', 'علی رضایی', '09121112233', 'تهران', 'تهران', '1998745632', 'سعادت‌آباد، خیابان علامه شمالی، پلاک ۱۲، واحد ۳', 'online', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(4, 3, 'DS-260901-9A1F08', 14650000, 2875000, 11775000, 'delivered', 'سارا محمدی', '09123334455', 'اصفهان', 'اصفهان', '8158812345', 'خیابان چهارباغ بالا، کوچه گلها، پلاک ۷', 'online', DATE_SUB(NOW(), INTERVAL 2 DAY));

-- --------- اقلام سفارش‌ها ---------
INSERT INTO `order_items` (`order_id`, `product_id`, `product_title`, `image`, `price`, `quantity`) VALUES
(1, 3,  'گوشی موبایل شیائومی Redmi Note 13 Pro ظرفیت ۲۵۶ گیگابایت', 'assets/images/products/p03-1.svg', 10880000, 1),
(1, 12, 'پاوربانک شیائومی ۲۰۰۰۰ میلی‌آمپر ساعت Pro', 'assets/images/products/p12-1.svg', 1701000, 2),
(2, 11, 'ساعت هوشمند شیائومی Redmi Watch 4', 'assets/images/products/p11-1.svg', 2587500, 1),
(3, 1,  'گوشی موبایل سامسونگ Galaxy S24 Ultra ظرفیت ۲۵۶ گیگابایت', 'assets/images/products/p01-1.svg', 48668000, 1),
(3, 15, 'کابل شارژ تایپ سی انکر PowerLine ۱ متری', 'assets/images/products/p15-1.svg', 807500, 2),
(3, 24, 'هارد اکسترنال وسترن دیجیتال My Passport ۱ ترابایت', 'assets/images/products/p24-1.svg', 4900000, 1),
(4, 10, 'هدفون بی‌سیم اپل AirPods Pro 2 (USB-C)', 'assets/images/products/p10-1.svg', 9520000, 1),
(4, 23, 'اسپیکر بلوتوثی جی‌بی‌ال Go 4', 'assets/images/products/p23-1.svg', 2255000, 1);

-- --------- تنظیمات سایت ---------
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'دیجی‌شاپ'),
('site_tagline', 'فروشگاه اینترنتی دیجی‌شاپ'),
('support_phone', '021-91000000'),
('support_email', 'support@digishop.ir'),
('free_shipping_threshold', '500000'),
('items_per_page', '12');

SET FOREIGN_KEY_CHECKS = 1;

-- پایان فایل نصب دیتابیس
