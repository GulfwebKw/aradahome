ALTER TABLE `gwc_colors` ADD `details_en` TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL AFTER `title_ar`, ADD `details_ar` TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL AFTER `details_en`;
ALTER TABLE `gwc_sizes` ADD `details_en` TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL AFTER `title_ar`, ADD `details_ar` TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL AFTER `details_en`;
ALTER TABLE `gwc_settings` ADD `check_out_note_en` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `order_note_en`, ADD `check_out_note_ar` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `check_out_note_en`;