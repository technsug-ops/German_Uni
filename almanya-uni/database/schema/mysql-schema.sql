/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `api_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` enum('free','partner','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `rate_limit_per_minute` int unsigned NOT NULL DEFAULT '60',
  `allowed_endpoints` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_clients_slug_unique` (`slug`),
  KEY `api_clients_is_active_plan_index` (`is_active`,`plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `api_usage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_usage_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_client_id` bigint unsigned DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint unsigned NOT NULL,
  `duration_ms` int unsigned DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `api_usage_logs_api_client_id_created_at_index` (`api_client_id`,`created_at`),
  KEY `api_usage_logs_path_created_at_index` (`path`,`created_at`),
  KEY `api_usage_logs_created_at_index` (`created_at`),
  CONSTRAINT `api_usage_logs_api_client_id_foreign` FOREIGN KEY (`api_client_id`) REFERENCES `api_clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_trackers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application_trackers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `steps_completed` json DEFAULT NULL,
  `steps_data` json DEFAULT NULL,
  `target_intake` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_university_id` bigint unsigned DEFAULT NULL,
  `target_degree` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `email_reminders` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_trackers_user_id_unique` (`user_id`),
  KEY `application_trackers_target_university_id_foreign` (`target_university_id`),
  CONSTRAINT `application_trackers_target_university_id_foreign` FOREIGN KEY (`target_university_id`) REFERENCES `universities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `application_trackers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blocked_account_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blocked_account_providers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('fintech','traditional_bank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fintech',
  `backend_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setup_fee_eur` decimal(8,2) DEFAULT NULL,
  `monthly_fee_eur` decimal(8,2) DEFAULT NULL,
  `yearly_fee_eur` decimal(8,2) DEFAULT NULL,
  `activation_days_min` smallint unsigned DEFAULT NULL,
  `activation_days_max` smallint unsigned DEFAULT NULL,
  `combo_insurance` tinyint(1) NOT NULL DEFAULT '0',
  `insurance_provider_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_monthly_eur` decimal(8,2) DEFAULT NULL,
  `monthly_withdrawal_limit_eur` int unsigned DEFAULT NULL,
  `required_yearly_deposit_eur` int unsigned DEFAULT NULL,
  `has_mobile_app` tinyint(1) NOT NULL DEFAULT '0',
  `bafin_licensed` tinyint(1) NOT NULL DEFAULT '0',
  `supported_languages` json DEFAULT NULL,
  `description_tr` longtext COLLATE utf8mb4_unicode_ci,
  `description_en` longtext COLLATE utf8mb4_unicode_ci,
  `description_de` longtext COLLATE utf8mb4_unicode_ci,
  `description_long` longtext COLLATE utf8mb4_unicode_ci,
  `pros` json DEFAULT NULL,
  `cons` json DEFAULT NULL,
  `features` json DEFAULT NULL,
  `visa_recognition_note` text COLLATE utf8mb4_unicode_ci,
  `turkish_students_note` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `last_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blocked_account_providers_slug_unique` (`slug`),
  KEY `blocked_account_providers_is_published_sort_order_index` (`is_published`,`sort_order`),
  KEY `blocked_account_providers_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `wikidata_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` bigint unsigned DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `population` int unsigned DEFAULT NULL,
  `stw_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stw_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stw_capacity` smallint unsigned DEFAULT NULL,
  `stw_waiting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avg_rent_min` smallint unsigned DEFAULT NULL,
  `avg_rent_max` smallint unsigned DEFAULT NULL,
  `private_chain_slugs` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `content_blocks` json DEFAULT NULL,
  `content_blocks_en` json DEFAULT NULL,
  `content_blocks_de` json DEFAULT NULL,
  `last_enriched_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_slug_unique` (`slug`),
  UNIQUE KEY `cities_wikidata_id_unique` (`wikidata_id`),
  KEY `cities_wikidata_id_index` (`wikidata_id`),
  KEY `cities_slug_index` (`slug`),
  KEY `cities_state_id_index` (`state_id`),
  FULLTEXT KEY `ft_cities_search` (`name_de`,`name_tr`,`name_en`),
  CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `city_cost_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `city_cost_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `city_id` bigint unsigned NOT NULL,
  `tier` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rent_wg` smallint unsigned NOT NULL,
  `rent_studio` smallint unsigned NOT NULL,
  `rent_apartment` smallint unsigned NOT NULL,
  `food` smallint unsigned NOT NULL,
  `transport` smallint unsigned NOT NULL,
  `utilities` smallint unsigned NOT NULL,
  `health_insurance` smallint unsigned NOT NULL,
  `entertainment` smallint unsigned NOT NULL,
  `misc` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `city_cost_data_city_id_unique` (`city_id`),
  KEY `city_cost_data_tier_index` (`tier`),
  CONSTRAINT `city_cost_data_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `content_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_brief_id` bigint unsigned NOT NULL,
  `asset_type` enum('blog','youtube_long','youtube_short','tiktok','instagram','twitter','linkedin','pinterest','podcast','newsletter','visual_brief') COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `source_asset_id` bigint unsigned DEFAULT NULL,
  `spec` json DEFAULT NULL,
  `body_md` mediumtext COLLATE utf8mb4_unicode_ci,
  `body_html` mediumtext COLLATE utf8mb4_unicode_ci,
  `media` json DEFAULT NULL,
  `video_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_by` enum('manual','ai_gemini','ai_claude','ai_openai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `prompt_used` mediumtext COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','ready','scheduled','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `published_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_assets_brief_type_unique` (`content_brief_id`,`asset_type`),
  KEY `content_assets_asset_type_index` (`asset_type`),
  KEY `content_assets_status_index` (`status`),
  KEY `content_assets_source_asset_id_foreign` (`source_asset_id`),
  KEY `content_assets_language_index` (`language`),
  CONSTRAINT `content_assets_content_brief_id_foreign` FOREIGN KEY (`content_brief_id`) REFERENCES `content_briefs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `content_assets_source_asset_id_foreign` FOREIGN KEY (`source_asset_id`) REFERENCES `content_assets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `content_briefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_briefs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `audience` enum('aday_ogrenci','veli','mevcut_ogrenci','phd_adayi','genel') COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_keyword` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_keywords` json DEFAULT NULL,
  `pain_point` text COLLATE utf8mb4_unicode_ci,
  `source_questions` json DEFAULT NULL,
  `target_word_count` smallint unsigned NOT NULL DEFAULT '1500',
  `brand_tone` enum('formal','casual','instructive','inspirational') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'instructive',
  `status` enum('draft','in_progress','ready','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `author_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_briefs_slug_unique` (`slug`),
  KEY `content_briefs_author_id_foreign` (`author_id`),
  KEY `content_briefs_audience_index` (`audience`),
  KEY `content_briefs_topic_index` (`topic`),
  KEY `content_briefs_status_index` (`status`),
  CONSTRAINT `content_briefs_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contributions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'experience',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `target_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `upvote_count` int unsigned NOT NULL DEFAULT '0',
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contributions_status_target_type_index` (`status`,`target_type`),
  KEY `contributions_user_id_index` (`user_id`),
  CONSTRAINT `contributions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_tr` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL,
  `attendee_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendee_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_reviews_user_id_foreign` (`user_id`),
  KEY `event_reviews_approved_by_foreign` (`approved_by`),
  KEY `event_reviews_event_id_status_created_at_index` (`event_id`,`status`,`created_at`),
  KEY `event_reviews_status_index` (`status`),
  CONSTRAINT `event_reviews_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_reviews_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_rsvps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_rsvps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `attendee_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendee_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'going',
  `note` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_user_unique` (`event_id`,`user_id`),
  UNIQUE KEY `event_email_unique` (`event_id`,`attendee_email`),
  KEY `event_rsvps_user_id_foreign` (`user_id`),
  KEY `event_rsvps_event_id_status_index` (`event_id`,`status`),
  KEY `event_rsvps_status_index` (`status`),
  CONSTRAINT `event_rsvps_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_rsvps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'webinar',
  `category_id` bigint unsigned DEFAULT NULL,
  `title_tr` text COLLATE utf8mb4_unicode_ci,
  `title_de` text COLLATE utf8mb4_unicode_ci,
  `title_en` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_md_tr` longtext COLLATE utf8mb4_unicode_ci,
  `description_md_en` longtext COLLATE utf8mb4_unicode_ci,
  `description_md_de` longtext COLLATE utf8mb4_unicode_ci,
  `host` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `host_user_id` bigint unsigned DEFAULT NULL,
  `sponsor` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sponsor_logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reward` text COLLATE utf8mb4_unicode_ci,
  `target_audience` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_minutes` smallint unsigned DEFAULT NULL,
  `presentation_language` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT 'tr',
  `tags` json DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `timezone` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Europe/Berlin',
  `recurrence_rule` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_event_id` bigint unsigned DEFAULT NULL,
  `mode` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `online_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_attendees` smallint unsigned DEFAULT NULL,
  `registered_count` smallint unsigned NOT NULL DEFAULT '0',
  `avg_rating` decimal(3,2) DEFAULT NULL,
  `reviews_count` int unsigned NOT NULL DEFAULT '0',
  `maybe_count` int unsigned NOT NULL DEFAULT '0',
  `registration_required` tinyint(1) NOT NULL DEFAULT '1',
  `price_eur` decimal(8,2) NOT NULL DEFAULT '0.00',
  `banner_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_color` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  KEY `events_is_active_is_featured_starts_at_index` (`is_active`,`is_featured`,`starts_at`),
  KEY `events_type_index` (`type`),
  KEY `events_starts_at_index` (`starts_at`),
  KEY `events_is_featured_index` (`is_featured`),
  KEY `events_is_active_index` (`is_active`),
  KEY `events_category_id_foreign` (`category_id`),
  KEY `events_host_user_id_foreign` (`host_user_id`),
  KEY `events_parent_event_id_index` (`parent_event_id`),
  CONSTRAINT `events_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_host_user_id_foreign` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_parent_event_id_foreign` FOREIGN KEY (`parent_event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faq_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq_topics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pool_size` int unsigned NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faq_topics_slug_unique` (`slug`),
  KEY `faq_topics_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `translation_group_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faq_topic_id` bigint unsigned NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer_md` longtext COLLATE utf8mb4_unicode_ci,
  `answer_html` longtext COLLATE utf8mb4_unicode_ci,
  `intent` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer_minutes` smallint unsigned NOT NULL DEFAULT '0',
  `has_answer` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faqs_slug_locale_unique` (`slug`,`locale`),
  KEY `faqs_faq_topic_id_is_published_sort_order_index` (`faq_topic_id`,`is_published`,`sort_order`),
  KEY `faqs_has_answer_is_published_index` (`has_answer`,`is_published`),
  KEY `faqs_locale_index` (`locale`),
  KEY `faqs_translation_group_id_index` (`translation_group_id`),
  CONSTRAINT `faqs_faq_topic_id_foreign` FOREIGN KEY (`faq_topic_id`) REFERENCES `faq_topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `favoriteable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `favoriteable_id` bigint unsigned NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fav_user_item_unique` (`user_id`,`favoriteable_type`,`favoriteable_id`),
  KEY `favorites_favoriteable_type_favoriteable_id_index` (`favoriteable_type`,`favoriteable_id`),
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_hash` char(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fb_status` (`status`),
  KEY `idx_fb_type` (`type`),
  KEY `idx_fb_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fields_of_study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fields_of_study` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_blocks` json DEFAULT NULL,
  `content_blocks_en` json DEFAULT NULL,
  `content_blocks_de` json DEFAULT NULL,
  `last_enriched_at` timestamp NULL DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fields_of_study_slug_unique` (`slug`),
  KEY `fields_of_study_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `housing_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `housing_providers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tr` longtext COLLATE utf8mb4_unicode_ci,
  `description_en` longtext COLLATE utf8mb4_unicode_ci,
  `description_de` longtext COLLATE utf8mb4_unicode_ci,
  `price_min` smallint unsigned DEFAULT NULL,
  `price_max` smallint unsigned DEFAULT NULL,
  `cities` json DEFAULT NULL,
  `features` json DEFAULT NULL,
  `total_capacity` smallint unsigned DEFAULT NULL,
  `waiting_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `housing_providers_slug_unique` (`slug`),
  KEY `housing_providers_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `housing_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `housing_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_tr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_de` text COLLATE utf8mb4_unicode_ci,
  `subject_de` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_de` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_tr_explanation` text COLLATE utf8mb4_unicode_ci,
  `placeholders` json DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `housing_templates_slug_unique` (`slug`),
  KEY `housing_templates_is_active_index` (`is_active`),
  KEY `housing_templates_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `housing_tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `housing_tips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `city_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `upvote_count` smallint unsigned NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `housing_tips_user_id_foreign` (`user_id`),
  KEY `housing_tips_city_id_is_approved_index` (`city_id`,`is_approved`),
  KEY `housing_tips_category_is_approved_index` (`category`,`is_approved`),
  KEY `housing_tips_category_index` (`category`),
  KEY `housing_tips_is_approved_index` (`is_approved`),
  CONSTRAINT `housing_tips_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `housing_tips_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_postings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_postings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `university_id` bigint unsigned DEFAULT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `field_of_study_id` bigint unsigned DEFAULT NULL,
  `position_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employment_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed_term',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `salary_band` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_min_eur` int unsigned DEFAULT NULL,
  `salary_max_eur` int unsigned DEFAULT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `requirements` longtext COLLATE utf8mb4_unicode_ci,
  `posted_at` date DEFAULT NULL,
  `deadline_at` date DEFAULT NULL,
  `application_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_remote` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_postings_university_id_foreign` (`university_id`),
  KEY `job_postings_city_id_foreign` (`city_id`),
  CONSTRAINT `job_postings_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_postings_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `legal_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `legal_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'privacy, terms, cookies, impressum, disclaimer',
  `titles` json NOT NULL COMMENT 'Per-locale title: {"tr":"...","en":"...","de":"..."}',
  `descriptions` json DEFAULT NULL COMMENT 'Per-locale meta description',
  `bodies` json NOT NULL COMMENT 'Per-locale markdown body',
  `effective_date` date DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `legal_pages_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mentor_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mentor_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mentor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `scheduled_at` timestamp NOT NULL,
  `duration_minutes` smallint unsigned NOT NULL DEFAULT '30',
  `jitsi_room_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `external_provider` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'cal_com | calendly | in_app',
  `external_booking_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `preferred_language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `status` enum('pending','confirmed','completed','cancelled','no_show') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `rating` tinyint unsigned DEFAULT NULL COMMENT '1-5 stars',
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mentor_sessions_jitsi_room_id_unique` (`jitsi_room_id`),
  KEY `mentor_sessions_mentor_id_scheduled_at_index` (`mentor_id`,`scheduled_at`),
  KEY `mentor_sessions_user_id_scheduled_at_index` (`user_id`,`scheduled_at`),
  KEY `mentor_sessions_status_scheduled_at_index` (`status`,`scheduled_at`),
  CONSTRAINT `mentor_sessions_mentor_id_foreign` FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentor_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mentors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mentors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `headline` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_role` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_company` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `university` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_of_study` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `graduation_year` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calendly_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topics` json DEFAULT NULL,
  `languages` json DEFAULT NULL,
  `availability` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate_eur` decimal(8,2) NOT NULL DEFAULT '0.00',
  `session_duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sessions_count` int unsigned NOT NULL DEFAULT '0',
  `rating_avg` decimal(3,2) DEFAULT NULL,
  `rating_count` smallint unsigned NOT NULL DEFAULT '0',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mentors_slug_unique` (`slug`),
  KEY `mentors_user_id_foreign` (`user_id`),
  KEY `mentors_is_featured_index` (`is_featured`),
  KEY `mentors_is_active_index` (`is_active`),
  CONSTRAINT `mentors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `menu_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique identifier (route name or external slug)',
  `link_type` enum('route','url') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'route',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'For external/static URLs like /forum/',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_tr` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label_de` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tr` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_de` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` enum('kesfet','araclar','firsatlar','icerik','standalone') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kesfet',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `protect_route` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'If true, middleware blocks the URL when disabled',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_pages_key_unique` (`key`),
  KEY `menu_pages_group_is_enabled_sort_order_index` (`group`,`is_enabled`,`sort_order`),
  KEY `menu_pages_is_enabled_index` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `page_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_views` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `path` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referrer` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_host` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_bot` tinyint(1) NOT NULL DEFAULT '0',
  `ip_hash` char(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_ms` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_session` (`session_id`),
  KEY `idx_pv_created` (`created_at`),
  KEY `idx_pv_path` (`path`),
  KEY `idx_pv_referrer_host` (`referrer_host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `popups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `popups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Stable cookie key — change to force re-show after edits',
  `theme` enum('gradient','minimal','banner_top','banner_bottom','side_card','fullscreen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gradient',
  `media_type` enum('text','image','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text' COMMENT 'text | image | video — controls which media field is rendered',
  `position` enum('center','top','bottom','bottom_right','bottom_left') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'center',
  `title_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_tr` text COLLATE utf8mb4_unicode_ci,
  `body_en` text COLLATE utf8mb4_unicode_ci,
  `body_de` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'YouTube/Vimeo embed URL or direct .mp4 path',
  `video_autoplay` tinyint(1) NOT NULL DEFAULT '0',
  `video_muted` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Autoplay zorunlu mute gerektirir — modern tarayıcı kuralı',
  `emoji` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Single emoji decoration',
  `accent_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hex color override (e.g. #F97316)',
  `cta_label_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_label_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_label_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_external` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'opens in new tab',
  `secondary_label_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_label_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_label_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_pages` json DEFAULT NULL COMMENT 'Array of route names or URL patterns; null = all',
  `exclude_pages` json DEFAULT NULL,
  `locales` json DEFAULT NULL COMMENT 'Show only on these locales; null = all',
  `trigger` enum('page_load','scroll_50','time_5s','time_15s','exit_intent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'time_5s',
  `delay_ms` int unsigned NOT NULL DEFAULT '5000' COMMENT 'Override for time-based triggers',
  `dismiss_days` int unsigned NOT NULL DEFAULT '7' COMMENT 'Days until popup can re-appear after dismiss',
  `show_dismiss_button` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `priority` smallint unsigned NOT NULL DEFAULT '5' COMMENT 'Lower = shown first when multiple match',
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `click_count` int unsigned NOT NULL DEFAULT '0',
  `dismiss_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `popups_key_unique` (`key`),
  KEY `popups_is_active_priority_index` (`is_active`,`priority`),
  KEY `popups_starts_at_ends_at_index` (`starts_at`,`ends_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comments_user_id_foreign` (`user_id`),
  KEY `post_comments_parent_id_foreign` (`parent_id`),
  KEY `post_comments_approved_by_foreign` (`approved_by`),
  KEY `post_comments_post_id_status_created_at_index` (`post_id`,`status`,`created_at`),
  KEY `post_comments_status_index` (`status`),
  CONSTRAINT `post_comments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `post_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `post_engagements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_engagements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scroll_depth` tinyint unsigned NOT NULL DEFAULT '0',
  `seconds` smallint unsigned NOT NULL DEFAULT '0',
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_engagements_post_id_session_id_unique` (`post_id`,`session_id`),
  KEY `post_engagements_session_id_index` (`session_id`),
  CONSTRAINT `post_engagements_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `translation_group_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `co_author_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(280) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_md` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_html` longtext COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_image_caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_duration_seconds` int unsigned DEFAULT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_images` json DEFAULT NULL,
  `reading_minutes` smallint unsigned NOT NULL DEFAULT '1',
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `unhelpful_count` int unsigned NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_locale_unique` (`slug`,`locale`),
  KEY `posts_user_id_foreign` (`user_id`),
  KEY `posts_is_published_published_at_index` (`is_published`,`published_at`),
  KEY `posts_category_id_index` (`category_id`),
  KEY `posts_locale_index` (`locale`),
  KEY `posts_translation_group_id_index` (`translation_group_id`),
  KEY `posts_co_author_id_foreign` (`co_author_id`),
  CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_co_author_id_foreign` FOREIGN KEY (`co_author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `premium_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `premium_interests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tier_interest` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_page` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `wants_beta` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User opted into early-access beta tester program',
  `beta_invited_at` timestamp NULL DEFAULT NULL COMMENT 'Admin clicked "invite to beta" — recipient got the welcome mail',
  `confirmation_sent_at` timestamp NULL DEFAULT NULL COMMENT 'Tracks whether the "thanks for interest" mail went out',
  `contacted` tinyint(1) NOT NULL DEFAULT '0',
  `contacted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `premium_interests_email_tier_interest_index` (`email`,`tier_interest`),
  KEY `premium_interests_contacted_index` (`contacted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `premium_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `premium_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tier` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ends_at` timestamp NULL DEFAULT NULL,
  `payment_provider` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_eur` decimal(8,2) DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `premium_subscriptions_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `premium_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `professions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `berufenet_id` int unsigned NOT NULL,
  `kldb_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cluster` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cluster_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_of_study_id` bigint unsigned DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_de` text COLLATE utf8mb4_unicode_ci,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `description_en` longtext COLLATE utf8mb4_unicode_ci,
  `steckbrief` text COLLATE utf8mb4_unicode_ci,
  `info_fields` json DEFAULT NULL,
  `info_fields_tr` json DEFAULT NULL,
  `info_fields_en` json DEFAULT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `professions_berufenet_id_unique` (`berufenet_id`),
  UNIQUE KEY `professions_slug_unique` (`slug`),
  KEY `professions_field_of_study_id_index` (`field_of_study_id`),
  KEY `professions_kldb_code_index` (`kldb_code`),
  KEY `professions_cluster_index` (`cluster`),
  KEY `professions_type_index` (`type`),
  KEY `professions_is_active_index` (`is_active`),
  FULLTEXT KEY `ft_professions_search` (`name_de`,`name_tr`,`description_tr`,`description_de`),
  CONSTRAINT `professions_field_of_study_id_foreign` FOREIGN KEY (`field_of_study_id`) REFERENCES `fields_of_study` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `partner_university_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `university_id` bigint unsigned NOT NULL,
  `field_of_study_id` bigint unsigned DEFAULT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree_specification` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_semesters` tinyint unsigned DEFAULT NULL,
  `start_semester` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_form` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_mode` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nc_value` decimal(4,2) DEFAULT NULL,
  `admission_summary` text COLLATE utf8mb4_unicode_ci,
  `subjects` json DEFAULT NULL,
  `study_fields_raw` json DEFAULT NULL,
  `application_deadline_summer` date DEFAULT NULL,
  `application_deadline_winter` date DEFAULT NULL,
  `tuition_fee_eur` mediumint unsigned DEFAULT NULL,
  `application_fee_eur` mediumint unsigned DEFAULT NULL,
  `cost_per_semester_eur` mediumint unsigned DEFAULT NULL,
  `financial_support` text COLLATE utf8mb4_unicode_ci,
  `support_info` text COLLATE utf8mb4_unicode_ci,
  `source_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hochschulkompass',
  `source_id` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification_requirements_tr` text COLLATE utf8mb4_unicode_ci,
  `language_requirements_tr` text COLLATE utf8mb4_unicode_ci,
  `language_level_de` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_level_en` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `required_documents_tr` text COLLATE utf8mb4_unicode_ci,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_university_id_slug_unique` (`university_id`,`slug`),
  KEY `programs_university_id_degree_index` (`university_id`,`degree`),
  KEY `programs_field_of_study_id_degree_index` (`field_of_study_id`,`degree`),
  KEY `programs_language_index` (`language`),
  KEY `programs_is_active_index` (`is_active`),
  KEY `programs_degree_index` (`degree`),
  KEY `programs_partner_id_index` (`partner_id`),
  KEY `programs_is_online_index` (`is_online`),
  FULLTEXT KEY `ft_programs_search` (`name_de`,`description_tr`,`description_en`),
  CONSTRAINT `programs_field_of_study_id_foreign` FOREIGN KEY (`field_of_study_id`) REFERENCES `fields_of_study` (`id`) ON DELETE SET NULL,
  CONSTRAINT `programs_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_deadlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_deadlines` (
  `sap_objid` bigint unsigned NOT NULL,
  `general_de` text COLLATE utf8mb4_unicode_ci,
  `general_en` text COLLATE utf8mb4_unicode_ci,
  `countries_json` json DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sap_objid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_intention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_intention` (
  `scholarship_id` bigint unsigned NOT NULL,
  `intention_id` int unsigned NOT NULL,
  PRIMARY KEY (`scholarship_id`,`intention_id`),
  KEY `scholarship_intention_intention_id_index` (`intention_id`),
  CONSTRAINT `scholarship_intention_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_intentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_intentions` (
  `id` int unsigned NOT NULL,
  `name_de` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_origin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_origin` (
  `scholarship_id` bigint unsigned NOT NULL,
  `origin_id` int unsigned NOT NULL,
  PRIMARY KEY (`scholarship_id`,`origin_id`),
  KEY `scholarship_origin_origin_id_index` (`origin_id`),
  CONSTRAINT `scholarship_origin_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_origins_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_origins_lookup` (
  `id` int unsigned NOT NULL,
  `name_de` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_es` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortname` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_status` (
  `scholarship_id` bigint unsigned NOT NULL,
  `status_id` int unsigned NOT NULL,
  PRIMARY KEY (`scholarship_id`,`status_id`),
  KEY `scholarship_status_status_id_index` (`status_id`),
  CONSTRAINT `scholarship_status_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_statuses` (
  `id` int unsigned NOT NULL,
  `name_de` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_es` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortierung` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_subject` (
  `scholarship_id` bigint unsigned NOT NULL,
  `subject_code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`scholarship_id`,`subject_code`),
  KEY `scholarship_subject_subject_code_index` (`subject_code`),
  CONSTRAINT `scholarship_subject_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarship_subject_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_subject_groups` (
  `code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_de` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_es` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scholarships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sap_objid` bigint unsigned NOT NULL,
  `daad_id` int unsigned DEFAULT NULL,
  `sap_progid` bigint unsigned DEFAULT NULL,
  `sap_target_system` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_de` text COLLATE utf8mb4_unicode_ci,
  `name_en` text COLLATE utf8mb4_unicode_ci,
  `langname_de` text COLLATE utf8mb4_unicode_ci,
  `langname_en` text COLLATE utf8mb4_unicode_ci,
  `programmname_de` text COLLATE utf8mb4_unicode_ci,
  `programmname_en` text COLLATE utf8mb4_unicode_ci,
  `programmtyp_id` int unsigned DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `introduction_json` json DEFAULT NULL,
  `q_de_json` json DEFAULT NULL,
  `q_en_json` json DEFAULT NULL,
  `is_daad` tinyint(1) NOT NULL DEFAULT '0',
  `is_move` tinyint(1) NOT NULL DEFAULT '0',
  `sorting` int DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `removed_at` timestamp NULL DEFAULT NULL,
  `detail_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scholarships_sap_objid_unique` (`sap_objid`),
  UNIQUE KEY `scholarships_slug_unique` (`slug`),
  KEY `scholarships_is_daad_index` (`is_daad`),
  KEY `scholarships_removed_at_index` (`removed_at`),
  KEY `scholarships_programmtyp_id_index` (`programmtyp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scrape_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrape_runs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scrape_source_id` bigint unsigned NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finished_at` timestamp NULL DEFAULT NULL,
  `duration_ms` int unsigned DEFAULT NULL,
  `status` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_requests` int unsigned NOT NULL DEFAULT '0',
  `items_found` int unsigned NOT NULL DEFAULT '0',
  `items_new` int unsigned NOT NULL DEFAULT '0',
  `items_updated` int unsigned NOT NULL DEFAULT '0',
  `error` text COLLATE utf8mb4_unicode_ci,
  `meta` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrape_runs_scrape_source_id_started_at_index` (`scrape_source_id`,`started_at`),
  CONSTRAINT `scrape_runs_scrape_source_id_foreign` FOREIGN KEY (`scrape_source_id`) REFERENCES `scrape_sources` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scrape_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrape_sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `university_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adapter` enum('generic_html','playwright','sitemap','custom_php') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generic_html',
  `config` json DEFAULT NULL,
  `throttle_ms` int unsigned NOT NULL DEFAULT '3000',
  `respect_robots` tinyint(1) NOT NULL DEFAULT '1',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `etag` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_modified_header` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_run_at` timestamp NULL DEFAULT NULL,
  `last_found_count` int unsigned DEFAULT NULL,
  `last_status` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrape_sources_university_id_foreign` (`university_id`),
  KEY `scrape_sources_is_enabled_last_run_at_index` (`is_enabled`,`last_run_at`),
  CONSTRAINT `scrape_sources_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scraped_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraped_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scrape_source_id` bigint unsigned NOT NULL,
  `university_id` bigint unsigned NOT NULL,
  `program_id` bigint unsigned DEFAULT NULL,
  `external_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `degree` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_semesters` tinyint unsigned DEFAULT NULL,
  `admission_mode` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_form` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deadline_raw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuition_raw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `semester_fee_raw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ects_credits` smallint unsigned DEFAULT NULL,
  `nc_value` decimal(3,2) DEFAULT NULL,
  `tuition_fee_eur` decimal(10,2) DEFAULT NULL,
  `description_de` text COLLATE utf8mb4_unicode_ci,
  `raw` json DEFAULT NULL,
  `content_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_status` enum('pending','approved','rejected','auto_approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `first_seen_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scraped_programs_source_key_unique` (`scrape_source_id`,`external_key`),
  KEY `scraped_programs_university_id_foreign` (`university_id`),
  KEY `scraped_programs_program_id_foreign` (`program_id`),
  KEY `scraped_programs_reviewed_by_foreign` (`reviewed_by`),
  KEY `scraped_programs_review_status_last_seen_at_index` (`review_status`,`last_seen_at`),
  KEY `scraped_programs_content_hash_index` (`content_hash`),
  CONSTRAINT `scraped_programs_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scraped_programs_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scraped_programs_scrape_source_id_foreign` FOREIGN KEY (`scrape_source_id`) REFERENCES `scrape_sources` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scraped_programs_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `search_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_queries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `query_raw` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `results_count` int unsigned NOT NULL DEFAULT '0',
  `breakdown` json DEFAULT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `took_ms` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search_queries_query_index` (`query`),
  KEY `search_queries_session_id_index` (`session_id`),
  KEY `search_queries_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seo_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_audits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sample_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_length` int unsigned NOT NULL DEFAULT '0',
  `h1_count` smallint unsigned NOT NULL DEFAULT '0',
  `h2_count` smallint unsigned NOT NULL DEFAULT '0',
  `image_count` smallint unsigned NOT NULL DEFAULT '0',
  `internal_link_count` smallint unsigned NOT NULL DEFAULT '0',
  `keywords_found` json DEFAULT NULL,
  `keywords_missing` json DEFAULT NULL,
  `high_value_gaps` json DEFAULT NULL,
  `opportunity_score` tinyint unsigned NOT NULL DEFAULT '0',
  `ai_suggestions` text COLLATE utf8mb4_unicode_ci,
  `ai_meta` json DEFAULT NULL,
  `last_audited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_audits_template_opportunity_score_index` (`template`,`opportunity_score`),
  KEY `seo_audits_template_index` (`template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `social_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_links_platform_unique` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `wikidata_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capital` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `population` bigint unsigned DEFAULT NULL,
  `image_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_blocks` json DEFAULT NULL,
  `content_blocks_en` json DEFAULT NULL,
  `content_blocks_de` json DEFAULT NULL,
  `last_enriched_at` timestamp NULL DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `states_slug_unique` (`slug`),
  UNIQUE KEY `states_wikidata_id_unique` (`wikidata_id`),
  KEY `states_wikidata_id_index` (`wikidata_id`),
  KEY `states_slug_index` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_dorms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_dorms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `city_id` bigint unsigned DEFAULT NULL,
  `city_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waitlist_avg` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rent_min_eur` smallint unsigned DEFAULT NULL,
  `rent_max_eur` smallint unsigned DEFAULT NULL,
  `amenities` json DEFAULT NULL,
  `notes_tr` text COLLATE utf8mb4_unicode_ci,
  `notes_en` text COLLATE utf8mb4_unicode_ci,
  `notes_de` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_dorms_city_id_is_active_index` (`city_id`,`is_active`),
  KEY `student_dorms_sort_order_index` (`sort_order`),
  CONSTRAINT `student_dorms_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `studienkollegs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studienkollegs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('staatlich','privat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staatlich',
  `city_id` bigint unsigned DEFAULT NULL,
  `city_name_cache` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` bigint unsigned DEFAULT NULL,
  `university_id` bigint unsigned DEFAULT NULL,
  `tracks` json DEFAULT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `established_year` int DEFAULT NULL,
  `capacity_per_year` int DEFAULT NULL,
  `semester_fee_eur` int DEFAULT NULL,
  `entrance_exam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` json DEFAULT NULL,
  `admission_requirements` json DEFAULT NULL,
  `notes` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `studienkollegs_slug_unique` (`slug`),
  KEY `studienkollegs_state_id_foreign` (`state_id`),
  KEY `studienkollegs_university_id_foreign` (`university_id`),
  KEY `studienkollegs_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `studienkollegs_city_id_index` (`city_id`),
  KEY `studienkollegs_type_index` (`type`),
  CONSTRAINT `studienkollegs_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `studienkollegs_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `studienkollegs_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `source` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unknown',
  `referrer_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirm_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unsubscribe_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `unsubscribe_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bounce_count` smallint unsigned NOT NULL DEFAULT '0',
  `bounced_at` timestamp NULL DEFAULT NULL,
  `complaint_at` timestamp NULL DEFAULT NULL,
  `last_sent_at` timestamp NULL DEFAULT NULL,
  `last_open_at` timestamp NULL DEFAULT NULL,
  `last_click_at` timestamp NULL DEFAULT NULL,
  `open_count` int unsigned NOT NULL DEFAULT '0',
  `click_count` int unsigned NOT NULL DEFAULT '0',
  `webhook_meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`),
  UNIQUE KEY `subscribers_confirm_token_unique` (`confirm_token`),
  UNIQUE KEY `subscribers_unsubscribe_token_unique` (`unsubscribe_token`),
  KEY `subscribers_language_index` (`language`),
  KEY `subscribers_source_index` (`source`),
  KEY `subscribers_confirmed_at_unsubscribed_at_index` (`confirmed_at`,`unsubscribed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trust_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trust_badges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'trustpilot | google_reviews | capterra | g2 | facebook | youtube | featured_in_x',
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Human-readable label, e.g. "Trustpilot"',
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SVG/PNG hosted URL — or local path',
  `profile_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Public profile/review page link',
  `rating` decimal(3,1) DEFAULT NULL COMMENT 'e.g. 4.7 (out of 5)',
  `review_count` int unsigned DEFAULT NULL,
  `badge_html` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional inline embed snippet from the platform',
  `slot` enum('footer','hero','about') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'footer',
  `sort_order` smallint unsigned NOT NULL DEFAULT '10',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hide until we actually register at the platform',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trust_badges_platform_unique` (`platform`),
  KEY `trust_badges_is_active_slot_sort_order_index` (`is_active`,`slot`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `universities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `wikidata_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hs_nummer` smallint unsigned DEFAULT NULL,
  `partner_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_tr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_de` text COLLATE utf8mb4_unicode_ci,
  `city_id` bigint unsigned DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('public','private','applied_sciences','art','religion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `hochschultyp` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `traegerschaft` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promotion_recht` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `habilitation_recht` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrk_member` tinyint(1) DEFAULT NULL,
  `is_uni_assist_member` tinyint(1) DEFAULT NULL,
  `uni_assist_id` smallint unsigned DEFAULT NULL,
  `founded_year` smallint DEFAULT NULL,
  `qs_world_rank` smallint unsigned DEFAULT NULL COMMENT 'QS World University Rankings position',
  `qs_academic_reputation` decimal(5,2) DEFAULT NULL,
  `qs_employer_reputation` decimal(5,2) DEFAULT NULL,
  `qs_citations_per_faculty` decimal(5,2) DEFAULT NULL,
  `qs_faculty_student_ratio` decimal(5,2) DEFAULT NULL,
  `qs_international_faculty` decimal(5,2) DEFAULT NULL,
  `qs_international_students` decimal(5,2) DEFAULT NULL,
  `qs_international_research` decimal(5,2) DEFAULT NULL,
  `qs_employment_outcomes` decimal(5,2) DEFAULT NULL,
  `qs_sustainability` decimal(5,2) DEFAULT NULL,
  `qs_overall_score` decimal(5,2) DEFAULT NULL,
  `the_world_rank` smallint unsigned DEFAULT NULL COMMENT 'Times Higher Education World Rankings position',
  `arwu_world_rank` smallint unsigned DEFAULT NULL COMMENT 'Shanghai ARWU world rank',
  `rankings_synced_at` timestamp NULL DEFAULT NULL,
  `community_mention_score` int unsigned NOT NULL DEFAULT '0' COMMENT 'Telegram + Forum mention count (computed periodically)',
  `community_mention_updated_at` timestamp NULL DEFAULT NULL,
  `student_count` int unsigned DEFAULT NULL,
  `wikipedia_url_tr` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wikipedia_url_en` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wikipedia_url_de` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_source` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_official` tinyint(1) NOT NULL DEFAULT '0',
  `content_blocks` json DEFAULT NULL,
  `content_blocks_en` json DEFAULT NULL,
  `content_blocks_de` json DEFAULT NULL,
  `last_enriched_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `universities_slug_unique` (`slug`),
  UNIQUE KEY `universities_wikidata_id_unique` (`wikidata_id`),
  UNIQUE KEY `universities_partner_id_unique` (`partner_id`),
  UNIQUE KEY `universities_hs_nummer_unique` (`hs_nummer`),
  KEY `universities_wikidata_id_index` (`wikidata_id`),
  KEY `universities_slug_index` (`slug`),
  KEY `universities_city_id_index` (`city_id`),
  KEY `universities_type_index` (`type`),
  KEY `universities_data_source_index` (`data_source`),
  KEY `universities_hs_nummer_index` (`hs_nummer`),
  KEY `universities_hochschultyp_index` (`hochschultyp`),
  KEY `universities_traegerschaft_index` (`traegerschaft`),
  KEY `universities_is_official_is_active_index` (`is_official`,`is_active`),
  KEY `universities_qs_world_rank_index` (`qs_world_rank`),
  KEY `universities_the_world_rank_index` (`the_world_rank`),
  KEY `universities_community_mention_score_index` (`community_mention_score`),
  KEY `universities_arwu_world_rank_index` (`arwu_world_rank`),
  FULLTEXT KEY `ft_universities_search` (`name_de`,`name_en`,`name_tr`,`short_name`),
  CONSTRAINT `universities_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `university_review_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `university_review_votes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vote` enum('helpful','unhelpful','report') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_vote` (`review_id`,`user_id`,`vote`),
  UNIQUE KEY `unique_session_vote` (`review_id`,`session_token`,`vote`),
  KEY `university_review_votes_user_id_foreign` (`user_id`),
  KEY `university_review_votes_review_id_vote_index` (`review_id`,`vote`),
  CONSTRAINT `university_review_votes_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `university_reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `university_review_votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `university_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `university_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `university_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_program` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_status` enum('current_student','alumni','admitted','applicant') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_year` int DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `status` enum('pending','approved','rejected','spam') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `moderation_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moderated_by` bigint unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `unhelpful_count` int unsigned NOT NULL DEFAULT '0',
  `reported_count` int unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email_per_uni` (`university_id`,`author_email`),
  UNIQUE KEY `university_reviews_verification_token_unique` (`verification_token`),
  KEY `university_reviews_user_id_foreign` (`user_id`),
  KEY `university_reviews_moderated_by_foreign` (`moderated_by`),
  KEY `university_reviews_university_id_status_index` (`university_id`,`status`),
  KEY `university_reviews_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `university_reviews_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `university_reviews_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `university_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `viewable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `viewable_id` bigint unsigned NOT NULL,
  `label` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_viewable_type_viewable_id_index` (`viewable_type`,`viewable_id`),
  KEY `user_activities_user_id_viewed_at_index` (`user_id`,`viewed_at`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_quiz_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_quiz_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `quiz_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answers` json NOT NULL,
  `result` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_quiz_results_user_id_quiz_type_created_at_index` (`user_id`,`quiz_type`,`created_at`),
  KEY `user_quiz_results_quiz_type_index` (`quiz_type`),
  CONSTRAINT `user_quiz_results_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_label` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_label_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_label_de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_links` json DEFAULT NULL,
  `high_school_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `german_level` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `english_level` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_field_id` bigint unsigned DEFAULT NULL,
  `target_degree` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_semester` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_budget_eur` mediumint unsigned DEFAULT NULL,
  `preferred_state_id` bigint unsigned DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `bio_en` text COLLATE utf8mb4_unicode_ci,
  `bio_de` text COLLATE utf8mb4_unicode_ci,
  `expertise` json DEFAULT NULL,
  `education` json DEFAULT NULL,
  `member_of` json DEFAULT NULL,
  `languages_spoken` json DEFAULT NULL,
  `awards` json DEFAULT NULL,
  `featured_in` json DEFAULT NULL,
  `years_experience` smallint unsigned DEFAULT NULL,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_editor` tinyint(1) NOT NULL DEFAULT '0',
  `is_author` tinyint(1) NOT NULL DEFAULT '0',
  `is_contributor` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_slug_unique` (`slug`),
  KEY `users_is_admin_index` (`is_admin`),
  KEY `users_target_field_id_foreign` (`target_field_id`),
  KEY `users_preferred_state_id_foreign` (`preferred_state_id`),
  CONSTRAINT `users_preferred_state_id_foreign` FOREIGN KEY (`preferred_state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_target_field_id_foreign` FOREIGN KEY (`target_field_id`) REFERENCES `fields_of_study` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `webhook_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `webhook_subscription_id` bigint unsigned NOT NULL,
  `event` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `status_code` smallint unsigned DEFAULT NULL,
  `response_body` text COLLATE utf8mb4_unicode_ci,
  `duration_ms` int unsigned DEFAULT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '1',
  `succeeded` tinyint(1) NOT NULL DEFAULT '0',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `webhook_deliveries_webhook_subscription_id_created_at_index` (`webhook_subscription_id`,`created_at`),
  KEY `webhook_deliveries_event_created_at_index` (`event`,`created_at`),
  CONSTRAINT `webhook_deliveries_webhook_subscription_id_foreign` FOREIGN KEY (`webhook_subscription_id`) REFERENCES `webhook_subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `webhook_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_client_id` bigint unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `events` json NOT NULL,
  `secret` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `failure_count` int unsigned NOT NULL DEFAULT '0',
  `last_success_at` timestamp NULL DEFAULT NULL,
  `last_failure_at` timestamp NULL DEFAULT NULL,
  `last_failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhook_subscriptions_api_client_id_foreign` (`api_client_id`),
  KEY `webhook_subscriptions_is_active_index` (`is_active`),
  CONSTRAINT `webhook_subscriptions_api_client_id_foreign` FOREIGN KEY (`api_client_id`) REFERENCES `api_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_05_11_224005_create_states_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_05_11_224008_create_cities_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_05_11_224009_create_universities_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_05_11_232003_create_personal_access_tokens_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_05_12_061233_create_categories_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_05_12_061234_create_posts_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_05_12_064047_create_faq_topics_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_05_12_064048_create_faqs_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_05_13_145552_create_city_cost_data_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_05_13_202616_create_fields_of_study_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_05_13_202618_create_programs_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_05_13_211939_add_external_id_to_universities',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_05_13_211939_extend_programs_table_for_partner_data',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_05_13_220652_add_is_admin_to_users',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_05_13_234039_create_professions_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_05_14_012511_extend_users_profile_fields',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_05_14_012512_create_favorites_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_05_14_012513_create_user_activities_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_05_14_012514_create_user_quiz_results_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_05_14_023947_create_student_dorms_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_05_14_023948_create_housing_tips_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_05_14_023950_create_housing_templates_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_05_15_013036_add_hrk_fields_to_universities',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_05_15_013700_widen_short_name_in_universities',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_05_15_120000_add_author_fields_to_users',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_05_15_140000_add_media_to_posts',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_05_16_100000_create_subscribers_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_05_17_004023_create_api_clients_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_05_17_005652_create_api_usage_logs_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_05_17_010113_create_webhook_subscriptions_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_05_17_020816_add_is_official_to_universities',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_05_17_022501_create_scrape_tables',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_05_17_110959_add_detail_fields_to_scraped_programs',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_05_17_224754_add_daad_enrichment_to_programs',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_05_18_054938_create_content_tables',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_05_18_055132_extend_content_asset_types',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_05_18_193730_add_media_to_content_assets',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_05_18_213922_create_seo_audits_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_05_19_160108_create_daad_scholarships_tables',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_05_20_120000_add_feedback_counts_to_posts',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_05_20_130000_seed_city_populations',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_05_20_140000_seed_state_populations_and_regions',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_05_21_100000_create_events_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_05_21_120000_extend_events_with_categories',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_05_21_120100_widen_event_type_column',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_05_21_130000_create_mentors_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_05_21_140000_create_housing_providers_and_extend_cities',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_05_21_140100_seed_housing_providers_and_city_stw',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_05_21_160000_add_parent_to_categories_and_merge_duplicates',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_05_21_170000_create_post_engagements_table',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_05_21_180000_create_search_queries_table',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_05_21_190000_create_contributions_and_contributor_flag',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_05_21_200000_add_is_editor_to_users',42);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_05_21_210000_create_social_links_table',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_05_23_220059_create_blocked_account_providers_table',44);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_05_23_230046_create_menu_pages_table',45);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_05_24_005215_add_qs_world_rank_to_universities',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_05_24_010427_add_community_mention_score_to_universities',47);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_05_24_024421_add_qs_indicator_breakdown_to_universities',48);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_05_24_031925_add_qs_indicators_part2_to_universities',49);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_05_24_082430_add_arwu_world_rank_to_universities',50);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_05_24_162403_add_description_en_to_professions',51);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_05_24_164723_add_locale_to_posts_and_faqs',52);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_05_24_184531_enlarge_faqs_question_to_text',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_05_24_205615_create_application_trackers_table',54);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_05_25_075315_add_en_columns_to_providers_and_events',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_05_25_082906_enlarge_event_titles',56);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_05_25_103601_create_premium_subscriptions_and_interests',57);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_05_25_140000_create_studienkollegs_table',58);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_05_25_150000_create_university_reviews_table',59);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_05_27_141830_create_legal_pages_table',60);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_05_28_125253_add_locale_columns_to_menu_pages',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_05_28_180000_add_info_fields_translations_to_professions',62);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_05_28_200000_add_slug_to_users',63);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_05_28_220000_create_post_comments_table',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_05_28_230000_standardize_menu_page_descriptions',65);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_05_28_240000_add_host_user_id_to_events',66);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_05_28_250000_create_event_rsvps_table',67);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_05_29_000000_merge_yapra_users_into_yaprak',68);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_05_29_010000_distribute_posts_to_category_authors',69);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_05_29_020000_add_international_authors_and_redistribute',70);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_05_29_030000_add_co_author_to_posts',71);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_05_29_040000_halil_persona_authority_redistribution',72);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_05_29_050000_add_presentation_language_to_events',73);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_05_29_060000_rewrite_founders_letter',74);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_05_29_070000_restore_admin_account',75);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2026_05_29_080000_standardize_menu_page_labels',76);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2026_05_29_100000_create_application_trackers_table',77);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2026_05_30_000000_add_recurrence_to_events',77);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2026_05_30_010000_create_event_reviews_table',78);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2026_05_30_020000_create_job_postings_table',79);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2026_05_30_030000_add_language_to_content_assets',80);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2026_05_30_040000_add_name_tr_to_scholarship_taxonomy',81);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2026_05_30_050000_add_eeat_fields_to_users',82);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2026_05_30_060000_set_explicit_menupage_translations',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2026_05_30_070000_add_locale_columns_to_users',84);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2026_05_30_090000_add_engagement_columns_to_subscribers',85);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2026_05_30_110000_create_popups_table',86);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2026_05_30_110100_add_media_to_popups',87);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2026_05_30_120000_create_trust_badges_table',88);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2026_05_30_140000_create_mentor_sessions_table',89);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2026_05_30_150000_add_beta_columns_to_premium_interests',90);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2026_05_30_160000_decode_html_entities_in_post_excerpts',91);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2026_05_30_080000_seed_team_locale_translations',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2026_05_30_100000_seed_uniassist_post_translations',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2026_05_30_130000_seed_5_howto_briefs',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2026_05_30_170000_clear_irrelevant_state_field_images',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2026_05_30_180000_decode_html_entities_in_post_content',93);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2026_05_30_190000_decode_entities_in_post_html_text_nodes',94);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2026_05_30_200000_null_wikipedia_uni_image_urls',95);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2026_05_31_000000_seed_wikidata_university_images',96);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2026_05_31_120000_create_settings_table',96);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2026_05_31_140000_fix_translated_post_meta_leak',97);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2026_05_31_160000_convert_blog_posts_to_du',98);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2026_05_31_180000_localize_taxonomy_names',99);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2026_05_31_182000_apply_blog_retranslation',100);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2026_05_31_190000_apply_blog_completion',101);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2026_05_31_200000_add_localized_content_blocks',102);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2026_05_31_201000_apply_city_blocks_pilot',103);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2026_06_01_160000_add_fulltext_search_indexes',104);
