ALTER TABLE `gwc_brands` ADD `is_discount` TINYINT(4) NOT NULL DEFAULT '0' AFTER `bgimage`, ADD `discount` TINYINT(3) NOT NULL DEFAULT '0' AFTER `is_discount`;

ALTER TABLE `gwc_transaction` ADD `pay_mode` VARCHAR(65) NULL DEFAULT NULL COMMENT 'COD = Cash On delivery , KNET = Knet PG, CC=Credit Card , MF=Myfatoora' AFTER `trackid`;
ALTER TABLE `gwc_users` ADD `created_by` INT NULL DEFAULT NULL AFTER `is_home`;