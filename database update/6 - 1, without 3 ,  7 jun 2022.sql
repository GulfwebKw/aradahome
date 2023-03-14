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


CREATE TABLE `gwc_inventories` (
                                   `id` INT NOT NULL AUTO_INCREMENT ,
                                   `title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `priority` TINYINT NOT NULL DEFAULT '1' ,
                                   `is_active` TINYINT(1) NOT NULL DEFAULT '1' ,
                                   `can_delete` TINYINT(1) NOT NULL DEFAULT '1' ,
                                   `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                                   `created_at` TIMESTAMP NULL ,
                                   `updated_at` TIMESTAMP NULL ,
                                   PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `gwc_inventories` (`id`, `title`, `priority`, `is_active`, `can_delete`, `deleted_at`, `created_at`, `updated_at`)
VALUES (NULL, 'default', '1', '1', '0', NULL, current_timestamp(), current_timestamp());

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
                                                                                       (NULL, 'inventory-list', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'inventory-create', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'inventory-edit', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'inventory-view', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'inventory-delete', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'inventory-restore', 'admin', current_timestamp(), current_timestamp());

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
                                                                                       (NULL, 'pos-list', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'pos-create', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'pos-edit', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'pos-view', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'pos-delete', 'admin', current_timestamp(), current_timestamp()),
                                                                                       (NULL, 'pos-restore', 'admin', current_timestamp(), current_timestamp());

INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Inventory', 'inventories', 'x', '2', '2', '1', '2022-01-14 02:59:08', '2022-01-14 02:59:08');


CREATE TABLE `gwc_products_quantity` (
                                         `id` INT NOT NULL AUTO_INCREMENT ,
                                         `product_id` INT NULL ,
                                         `attribute_id` INT NULL ,
                                         `option_id` INT NULL ,
                                         `inventory_id` INT NOT NULL ,
                                         `quantity` INT NOT NULL DEFAULT '0' ,
                                         `is_qty_deduct` FLOAT NOT NULL DEFAULT '1',
                                         `updated_at` TIMESTAMP NULL ,
                                         `created_at` TIMESTAMP NULL ,
                                         PRIMARY KEY (`id`),
                                         UNIQUE KEY `gwc_products_quantity_uniq_id` (`product_id`,`attribute_id`,`option_id`,`inventory_id`)
) ENGINE = InnoDB;


ALTER TABLE `gwc_products_option_custom_chosen` ADD `inventory_id` INT NULL AFTER `custom_option_id`;
ALTER TABLE `gwc_products_option_custom_chosen` ADD FOREIGN KEY (`inventory_id`) REFERENCES `gwc_inventories`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `gwc_products_attribute` ADD `chosen_id` INT NULL AFTER `product_id`;
ALTER TABLE `gwc_products_options` ADD `chosen_id` INT NULL AFTER `product_id`;

ALTER TABLE `gwc_orders` ADD `inventory` JSON NULL DEFAULT NULL AFTER `order_id`;
-- ALTER TABLE `gwc_orders` ADD `inventory` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL AFTER `order_id`;

ALTER TABLE `gwc_inventories` ADD `description` TEXT NULL DEFAULT NULL AFTER `title`;



INSERT INTO `gwc_products_quantity` (`product_id`, `attribute_id`, `option_id`, `inventory_id`, `quantity`, `is_qty_deduct`, `updated_at`, `created_at`)
SELECT `id` as product_id , NULL as attribute_id , NULL as option_id , '1' as inventory_id , `quantity` ,'1' as is_qty_deduct, CURRENT_TIMESTAMP() as updated_at ,CURRENT_TIMESTAMP() as created_at  FROM `gwc_products` WHERE `is_attribute` = 0 ;

INSERT INTO `gwc_products_quantity` (`product_id`, `attribute_id`, `option_id`, `inventory_id`, `quantity`, `is_qty_deduct`, `updated_at`, `created_at`)
SELECT p.`id` as product_id , pa.id as attribute_id , NULL as option_id , '1' as inventory_id , pa.`quantity` ,'1' as is_qty_deduct, CURRENT_TIMESTAMP() as updated_at ,CURRENT_TIMESTAMP() as created_at  FROM `gwc_products` as p RIGHT JOIN `gwc_products_attribute` as pa on ( pa.product_id = p.id) WHERE p.`is_attribute` = 1;

INSERT INTO `gwc_products_quantity` (`product_id`, `attribute_id`, `option_id`, `inventory_id`, `quantity`, `is_qty_deduct`, `updated_at`, `created_at`)
SELECT p.`id` as product_id , NULL as attribute_id , pa.id as option_id , '1' as inventory_id , pa.`quantity` ,'1' as is_qty_deduct, CURRENT_TIMESTAMP() as updated_at ,CURRENT_TIMESTAMP() as created_at  FROM `gwc_products` as p RIGHT JOIN `gwc_products_options` as pa on ( pa.product_id = p.id) WHERE p.`is_attribute` = 1;
UPDATE `gwc_products_option_custom_chosen` SET `inventory_id` = '1';

UPDATE `gwc_products_options` po INNER JOIN gwc_products_option_custom_chosen pocc ON po.custom_option_id = pocc.custom_option_id and po.product_id = pocc.product_id SET po.`chosen_id`=pocc.id ;
UPDATE `gwc_products_attribute` pa INNER JOIN gwc_products_option_custom_chosen pocc ON pa.custom_option_id = pocc.custom_option_id and pa.product_id = pocc.product_id SET pa.`chosen_id`=pocc.id;

CREATE TABLE `gwc_currencies` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `code` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `symbol` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `title_en` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `title_ar` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `rate` float NOT NULL,
                                  `display_order` int(10) NOT NULL,
                                  `is_active` tinyint(1) NOT NULL DEFAULT 0,
                                  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ;


INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'currency-list', 'admin', current_timestamp(), current_timestamp()), (NULL, 'currency-create', 'admin', current_timestamp(), current_timestamp()), (NULL, 'currency-edit', 'admin', current_timestamp(), current_timestamp()), (NULL, 'currency-delete', 'admin', current_timestamp(), current_timestamp());


INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Currencies', 'currencies', 'x', '11', '6', '1', '2022-03-26 02:59:08', '2022-03-26 02:59:08');


CREATE TABLE `gwc_zones_price` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `zone_id` BIGINT(11) NULL DEFAULT NULL,
                                   `from` FLOAT NOT NULL ,
                                   `to` FLOAT NOT NULL ,
                                   `price` FLOAT NOT NULL,
                                   PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ;



ALTER TABLE `gwc_settings` ADD `show_all_currencies` BOOLEAN NOT NULL DEFAULT FALSE AFTER `validate_cust_hno`;

CREATE TABLE `gwc_country_gateway` (
                                       `country_id` int(11) NOT NULL,
                                       `gateway` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `gwc_sections` ADD `slideShow` BOOLEAN NOT NULL DEFAULT TRUE AFTER `is_active`;
ALTER TABLE `gwc_settings` ADD `show_out_of_stock` BOOLEAN NOT NULL DEFAULT TRUE AFTER `og_sale_price_dates_end`;


CREATE TABLE `gwc_pos_cash_log` (
                                    `id` bigint(20) UNSIGNED NOT NULL,
                                    `pos_id` bigint(20) UNSIGNED NOT NULL,
                                    `shift_id` bigint(20) UNSIGNED NOT NULL,
                                    `amount` float NOT NULL,
                                    `type` enum('in','out') NOT NULL DEFAULT 'in',
                                    `description` text DEFAULT NULL,
                                    `refrence_id` bigint(20) UNSIGNED DEFAULT NULL,
                                    `refrence_type` varchar(255) DEFAULT NULL,
                                    `beforeCash` float DEFAULT NULL,
                                    `afterCash` float DEFAULT NULL,
                                    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                                    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `gwc_pos_work_time` (
                                     `id` bigint(20) UNSIGNED NOT NULL,
                                     `pos_id` bigint(20) UNSIGNED NOT NULL,
                                     `start` timestamp NULL DEFAULT NULL,
                                     `ended` timestamp NULL DEFAULT NULL,
                                     `startCash` float NOT NULL DEFAULT 0,
                                     `endCash` float DEFAULT NULL,
                                     `contradictionCashOfSystem` float NOT NULL DEFAULT 0,
                                     `cashPay` float DEFAULT 0,
                                     `cardPay` float DEFAULT 0,
                                     `customers` int(11) NOT NULL DEFAULT 0,
                                     `sell` int(11) DEFAULT 0,
                                     `totalSell` float DEFAULT 0,
                                     `cashRefund` float NOT NULL DEFAULT 0,
                                     `cardRefund` float NOT NULL DEFAULT 0,
                                     `refund` int(11) NOT NULL DEFAULT 0,
                                     `totalRefund` float NOT NULL DEFAULT 0,
                                     `countCash` float NOT NULL DEFAULT 0,
                                     `countCard` float NOT NULL DEFAULT 0,
                                     `contradictionCountCash` float NOT NULL DEFAULT 0,
                                     `contradictionCountCard` float NOT NULL DEFAULT 0,
                                     `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                                     `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `gwc_pos_cash_log`
    ADD PRIMARY KEY (`id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `pos_id` (`pos_id`);


ALTER TABLE `gwc_pos_work_time`
    ADD PRIMARY KEY (`id`),
  ADD KEY `pos_id` (`pos_id`);

ALTER TABLE `gwc_pos_cash_log`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


ALTER TABLE `gwc_pos_work_time`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


ALTER TABLE `gwc_pos_cash_log`
    ADD CONSTRAINT `gwc_pos_cash_log_ibfk_1` FOREIGN KEY (`shift_id`) REFERENCES `gwc_pos_work_time` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gwc_pos_cash_log_ibfk_2` FOREIGN KEY (`pos_id`) REFERENCES `gwc_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `gwc_pos_work_time`
    ADD CONSTRAINT `gwc_pos_work_time_ibfk_1` FOREIGN KEY (`pos_id`) REFERENCES `gwc_users` (`id`) ON DELETE CASCADE;


INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'POS', 'javascript:;', 'fa fa-cash-register', '0', '5', '1', '2022-01-14 03:29:08', '2022-01-14 03:29:08'), (NULL, 'Cash Log', 'pos/cash', 'flaticon2-list-1', '63', '1', '1', '2022-01-14 03:33:23', '2022-01-14 03:33:23'), (NULL, 'End Of Day', 'pos', 'flaticon-settings', '63', '2', '1', '2022-01-14 03:33:23', '2022-01-14 03:33:23');

INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Transactions', 'pos/transactions', 'flaticon2-list-1', '63', '3', '1', '2022-01-14 03:33:23', '2022-01-14 03:33:23'), (NULL, 'Orders', 'pos/orders', 'flaticon-settings', '63', '4', '1', '2022-01-14 03:33:23', '2022-01-14 03:33:23');


UPDATE `permissions` SET `name` = 'pos-cash-list' WHERE `permissions`.`name` =  'pos-list';


UPDATE `permissions` SET `name` = 'pos-days-list' WHERE `permissions`.`name` = 'pos-create';

ALTER TABLE `gwc_orders_details` ADD `pos_id` INT NULL DEFAULT NULL AFTER `customer_id`;


ALTER TABLE `gwc_country` ADD `code` VARCHAR(4) NULL DEFAULT NULL AFTER `name_ar`;
UPDATE `gwc_country` SET `code` = 'sa' WHERE `gwc_country`.`name_en` = 'Saudi Arabia' and `parent_id` = 0;
UPDATE `gwc_country` SET `code` = 'ae' WHERE `gwc_country`.`name_en` = 'United Arab Emirates' and `parent_id` = 0;
UPDATE `gwc_country` SET `code` = 'om' WHERE `gwc_country`.`name_en` = 'Oman' and `parent_id` = 0;
UPDATE `gwc_country` SET `code` = 'qa' WHERE `gwc_country`.`name_en` = 'Qatar' and `parent_id` = 0;
UPDATE `gwc_country` SET `code` = 'bh' WHERE `gwc_country`.`name_en` = 'Bahrain' and `parent_id` = 0;
UPDATE `gwc_country` SET `code` = 'kw' WHERE `gwc_country`.`name_en` = 'Kuwait' and `parent_id` = 0;


ALTER TABLE `gwc_settings` ADD `pos_note_en` TEXT NULL DEFAULT NULL AFTER `show_all_currencies`, ADD `pos_note_ar` TEXT NULL DEFAULT NULL AFTER `pos_note_en`;


ALTER TABLE `gwc_country` ADD `currency` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL AFTER `name_ar`;