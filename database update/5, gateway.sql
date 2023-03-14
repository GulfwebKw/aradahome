CREATE TABLE `gwc_country_gateway` (
                                       `country_id` int(11) NOT NULL,
                                       `gateway` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `gwc_sections` ADD `slideShow` BOOLEAN NOT NULL DEFAULT TRUE AFTER `is_active`;
ALTER TABLE `gwc_settings` ADD `show_out_of_stock` BOOLEAN NOT NULL DEFAULT TRUE AFTER `og_sale_price_dates_end`;