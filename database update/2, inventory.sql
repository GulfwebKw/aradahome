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
