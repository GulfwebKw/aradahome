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