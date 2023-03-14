CREATE TABLE `gwc_blog_posts` (
                                  `id` bigint(20) UNSIGNED NOT NULL,
                                  `title_en` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
                                  `title_ar` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
                                  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
                                  `details_en` text CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
                                  `details_ar` text CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
                                  `status` enum('published','hidden','draft') NOT NULL DEFAULT 'draft',
                                  `viewed` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
                                  `image` varchar(65) DEFAULT NULL,
                                  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB;
CREATE TABLE `gwc_blog_tags` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `tag_en` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL ,
    `tag_ar` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
CREATE TABLE `gwc_blog_comments` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `post_id` BIGINT UNSIGNED NOT NULL , `user_id` BIGINT UNSIGNED NULL DEFAULT NULL , `reply_id` BIGINT UNSIGNED NULL DEFAULT NULL , `verifier_id` BIGINT UNSIGNED NULL DEFAULT NULL , `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL , `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL , `comment` TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL , `is_en` BOOLEAN NOT NULL DEFAULT TRUE , `status` ENUM('published','waiting','reject') NOT NULL DEFAULT 'waiting' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `gwc_blog_categories` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `parent_id` BIGINT UNSIGNED NULL DEFAULT NULL , `slug` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL , `name_en` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL , `name_ar` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL , `is_active` BOOLEAN NOT NULL DEFAULT TRUE , `display_order` INT NULL DEFAULT '0' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `gwc_blog_post_category` ( `post_id` BIGINT UNSIGNED NOT NULL , `category_id` BIGINT UNSIGNED NOT NULL ) ENGINE = InnoDB;
CREATE TABLE `gwc_blog_post_tags` ( `post_id` BIGINT UNSIGNED NOT NULL , `tag_id` BIGINT UNSIGNED NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `gwc_blog_comments` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `gwc_blog_comments` CHANGE `user_id` `user_id` INT NULL DEFAULT NULL;

ALTER TABLE `gwc_blog_posts` ADD FOREIGN KEY (`created_by`) REFERENCES `gwc_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `gwc_blog_categories` ADD FOREIGN KEY (`parent_id`) REFERENCES `gwc_blog_categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `gwc_blog_comments` ADD FOREIGN KEY (`post_id`) REFERENCES `gwc_blog_posts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `gwc_blog_comments` ADD FOREIGN KEY (`verifier_id`) REFERENCES `gwc_users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE; ALTER TABLE `gwc_blog_comments` ADD FOREIGN KEY (`user_id`) REFERENCES `gwc_customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `gwc_blog_comments` ADD FOREIGN KEY (`reply_id`) REFERENCES `gwc_blog_comments`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `gwc_blog_post_category` ADD FOREIGN KEY (`category_id`) REFERENCES `gwc_blog_categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `gwc_blog_post_category` ADD FOREIGN KEY (`post_id`) REFERENCES `gwc_blog_posts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `gwc_blog_post_tags` ADD FOREIGN KEY (`post_id`) REFERENCES `gwc_blog_posts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `gwc_blog_post_tags` ADD FOREIGN KEY (`tag_id`) REFERENCES `gwc_blog_tags`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;



INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Weblog', 'javascript:;', 'fa fa-newspaper', '0', '3', '1', '2022-07-09 14:48:32', '2022-07-09 14:48:32');
SET @last_id_in_table1 = LAST_INSERT_ID();
INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Posts', 'blog/post', '', @last_id_in_table1 , '1', '1', '2022-07-09 14:48:32', '2022-07-09 14:48:32');
INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Comments', 'blog/comment', '', @last_id_in_table1 , '2', '1', '2022-07-09 14:48:32', '2022-07-09 14:48:32');
INSERT INTO `gwc_menus` (`id`, `name`, `link`, `icon`, `parent_id`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Categories', 'blog/category', '', @last_id_in_table1 , '3', '0', '2022-07-09 14:48:32', '2022-07-09 14:48:32');


INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'post-list', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59'), (NULL, 'post-create', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59'), (NULL, 'post-edit', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59'), (NULL, 'post-delete', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59')
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'blog-category-list', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59'), (NULL, 'blog-category-create', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59'), (NULL, 'blog-category-edit', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59'), (NULL, 'blog-category-delete', 'admin', '2022-04-07 10:32:59', '2022-04-07 10:32:59')

