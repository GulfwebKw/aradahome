INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Bundles', 'javascript:;', 'flaticon2-open-box', '0', '2', '1', '2022-01-14 02:59:08', '2022-01-14 02:59:08');
INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Categories', 'bundles/category', 'flaticon2-list-1', '58', '1', '1', '2022-01-14 03:03:23', '2022-01-14 03:03:23'), (NULL, 'Setting', 'bundles/setting', 'flaticon-settings', '58', '2', '1', '2022-01-14 03:03:23', '2022-01-14 03:03:23');

ALTER TABLE `gwc_settings` ADD `huawei_url` VARCHAR(250) NULL AFTER `ios_url`;
ALTER TABLE `gwc_settings` ADD `invoice_qrcode` FLOAT NOT NULL DEFAULT '0' AFTER `invoice_template`;

CREATE TABLE `gwc_bundle_setting` ( `key` VARCHAR(65) NOT NULL , `value` TEXT NULL DEFAULT NULL , UNIQUE (`key`)) ENGINE = InnoDB;


INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'bundle-edit', 'admin', current_timestamp(), current_timestamp());


CREATE TABLE `gwc_bundle_categories` (
                                         `id` bigint(20) UNSIGNED NOT NULL,
                                         `name_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `name_ar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `details_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `details_ar` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `seo_keywords_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `seo_keywords_ar` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `seo_description_ar` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `seo_description_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `friendly_url` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `display_order` int(11) NOT NULL DEFAULT 0,
                                         `is_active` tinyint(1) NOT NULL DEFAULT 0,
                                         `app_views` int(11) NOT NULL DEFAULT 1,
                                         `web_views` int(11) NOT NULL DEFAULT 1,
                                         `parent_id` int(10) UNSIGNED DEFAULT 0,
                                         `is_offer` tinyint(1) NOT NULL DEFAULT 0,
                                         `title_1_en` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_1_ar` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_2_en` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_2_ar` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_3_en` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_3_ar` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_4_en` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `title_4_ar` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `offer_link` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `offer_image` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `is_full_width` tinyint(1) NOT NULL DEFAULT 0,
                                         `header_image` varchar(192) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `is_highlighted` tinyint(1) NOT NULL DEFAULT 0,
                                         `created_at` timestamp NULL DEFAULT NULL,
                                         `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `gwc_bundle_categories`
    DROP `seo_keywords_en`,
    DROP `seo_keywords_ar`,
    DROP `seo_description_ar`,
    DROP `seo_description_en`,
    DROP `friendly_url`,
    DROP `app_views`,
    DROP `web_views`,
    DROP `is_offer`,
    DROP `title_1_en`,
    DROP `title_1_ar`,
    DROP `title_2_en`,
    DROP `title_2_ar`,
    DROP `title_3_en`,
    DROP `title_3_ar`,
    DROP `title_4_en`,
    DROP `title_4_ar`,
    DROP `offer_link`,
    DROP `offer_image`,
    DROP `is_full_width`,
    DROP `header_image`,
    DROP `is_highlighted`
;

ALTER TABLE `gwc_bundle_categories`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `gwc_bundle_categories`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
      (NULL, 'bundle-category-list', 'admin', current_timestamp(), current_timestamp()),
      (NULL, 'bundle-category-create', 'admin', current_timestamp(), current_timestamp()),
      (NULL, 'bundle-category-edit', 'admin', current_timestamp(), current_timestamp()),
      (NULL, 'bundle-category-view', 'admin', current_timestamp(), current_timestamp()),
      (NULL, 'bundle-category-delete', 'admin', current_timestamp(), current_timestamp());



CREATE TABLE `gwc_products_bundle_category` (
                                                `id` int(11) NOT NULL,
                                                `product_id` int(11) DEFAULT NULL,
                                                `category_id` int(11) DEFAULT NULL,
                                                `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                                `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `gwc_products_bundle_category`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `gwc_products_bundle_category`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;





ALTER TABLE `gwc_orders_details` ADD `bundle_discount` FLOAT NULL DEFAULT NULL AFTER `seller_discount`;


