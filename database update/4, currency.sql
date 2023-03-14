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