ALTER TABLE `gwc_settings` ADD `new_tag_days` INT NOT NULL DEFAULT '15' AFTER `pos_supervisor_password`, ADD `show_new_tag` BOOLEAN NOT NULL DEFAULT TRUE AFTER `new_tag_days`;

ALTER TABLE `gwc_products` ADD `newtag` BOOLEAN NOT NULL DEFAULT TRUE AFTER `customizable`;