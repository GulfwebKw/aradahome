CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
    `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `revoked` tinyint(1) NOT NULL,
    `expires_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `oauth_auth_codes_user_id_index` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `oauth_clients` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `personal_access_client` tinyint(1) NOT NULL,
    `password_client` tinyint(1) NOT NULL,
    `revoked` tinyint(1) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `oauth_clients_user_id_index` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
    `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `revoked` tinyint(1) NOT NULL,
    `expires_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
    (1, NULL, 'BabyDoBus Personal Access Client', 'Z5wjmwgBENY4FRpQXNhwQV2whQJJNYAHOuOS9bEM', NULL, 'http://localhost', 1, 0, 0, '2020-10-05 01:37:54', '2020-10-05 01:37:54'),
    (2, NULL, 'BabyDoBus Password Grant Client', 'OhN6HbVASuC3Ryl0tIzzrA6HhH0botzfJuIwIixt', 'users', 'http://localhost', 0, 1, 0, '2020-10-05 01:37:54', '2020-10-05 01:37:54');
INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
    (1, 1, '2020-10-05 01:37:54', '2020-10-05 01:37:54');
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
   `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
   `user_id` bigint(20) UNSIGNED DEFAULT NULL,
   `client_id` bigint(20) UNSIGNED NOT NULL,
   `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `revoked` tinyint(1) NOT NULL,
   `created_at` timestamp NULL DEFAULT NULL,
   `updated_at` timestamp NULL DEFAULT NULL,
   `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;