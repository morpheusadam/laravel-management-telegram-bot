CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sl` int(11) NOT NULL,
  `module_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_text` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'month',
  `limit_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bulk_limit_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `subscription_module` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `team_module` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `modules` (`id`, `sl`, `module_name`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `subscription_module`, `team_module`, `status`, `deleted`) VALUES
(1, 1, 'Connect Bot', '', '0', '0', '1', '0', '1', '0'),
(9, 2, 'Group Members', '', '1', '0', '1', '0', '1', '0'),
(22, 14, 'Group Management', '', '0', '0', '1', '0', '1', '0');



CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '0 means all',
  `is_seen` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `seen_by` text COLLATE utf8mb4_unicode_ci COMMENT 'if user_id = 0 then comma seperated user_ids',
  `last_seen_at` datetime DEFAULT NULL,
  `color_class` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fas fa-bell',
  `published` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `linkable` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `custom_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `package_name` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_type` enum('subscription','team') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subscription',
  `module_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_limit` text COLLATE utf8mb4_unicode_ci,
  `bulk_limit` text COLLATE utf8mb4_unicode_ci,
  `team_access` text COLLATE utf8mb4_unicode_ci,
  `price` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `validity` int(11) DEFAULT NULL,
  `validity_extra_info` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '1,M',
  `is_default` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_agency` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_whitelabel` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `subscriber_limit` int(11) NOT NULL DEFAULT '-1',
  `user_limit` int(11) NOT NULL DEFAULT '-1',
  `product_data` text COLLATE utf8mb4_unicode_ci,
  `discount_data` text COLLATE utf8mb4_unicode_ci,
  `visible` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `highlight` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `pay_per_use` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `pay_per_use_default_pack` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `packages` (`id`, `user_id`, `package_name`, `package_type`, `module_ids`, `monthly_limit`, `bulk_limit`, `team_access`, `price`, `validity`, `validity_extra_info`, `is_default`, `is_agency`, `is_whitelabel`, `subscriber_limit`, `user_limit`, `product_data`, `discount_data`, `visible`, `highlight`, `pay_per_use`, `pay_per_use_default_pack`, `deleted`) VALUES
(1, 1, 'Basic', 'subscription', '1,9,22', '{\"1\":\"3\",\"9\":\"3000\",\"22\":\"0\"}', '{\"1\":\"1\",\"9\":\"0\",\"22\":\"0\"}', NULL, 'Trial', 7, '1,W', '1', '0', '0', -1, -1, '{\"paypal\":{\"plan_id\":\"\"}}', '{\"percent\":\"\",\"terms\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"timezone\":\"Asia\\/Dhaka\",\"status\":\"0\"}', '1', '0', '0', '0', '0');

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `payment_api_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `buyer_user_id` int(11) DEFAULT NULL,
  `call_time` datetime DEFAULT NULL,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_response` text COLLATE utf8mb4_unicode_ci,
  `error` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `buyer_user_id` (`buyer_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_alt` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `favicon` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `email_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_responder_signup_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sms_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `analytics_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `agency_landing_settings` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `open_ai_config` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `settings` (`id`, `user_id`, `app_name`, `logo`, `logo_alt`, `favicon`, `timezone`, `language`, `email_settings`, `auto_responder_signup_settings`, `sms_settings`, `upload_settings`, `analytics_code`, `agency_landing_settings`, `updated_at`, `open_ai_config`) VALUES
(1, 1, 'TeleGroupBot', '', '', '', 'Europe/Dublin', 'en', '{\"default\":\"1\",\"sender_email\":\"no-reply@telegram-group.test\",\"sender_name\":\"TeleGroupBot\"}', '{\"mailchimp\":[],\"sendinblue\":[],\"activecampaign\":[],\"mautic\":[],\"acelle\":[]}', '', '{\"bot\":{\"image\":0,\"video\":0,\"audio\":0,\"file\":0}}', '{\"fb_pixel_id\":\"\",\"google_analytics_id\":\\",\"tme_widget_id\":\"\",\"whatsapp_widget_id\":\"\"}','{\"details_feature_1_img\":\"assets/landing/images/hero/hero-image-2.png\",\"details_feature_2_img\":\"assets/landing/images/about/about-image-1.png\",\"details_feature_3_img\":\"assets/landing/images/about/about-image-2.png\",\"details_feature_4_img\":\"assets/landing/images/about/about-image-3.png\",\"details_feature_5_img\":\"assets/landing/images/about/about-image-4.png\",\"details_feature_6_img\":\"assets/landing/images/about/about-image-5.png\",\"details_feature_7_img\":\"assets/landing/images/about/about-image-6.png\",\"details_feature_8_img\":\"assets/landing/images/cta/cta-image-1.png\",\"details_feature_9_img\":\"assets/landing/images/cta/cta-image-2.png\",\"company_email\":\"admin@example.com\",\"company_address\":\"Holding #, nth Floor, City, Country\",\"company_title\":\"Telegram Group Management Bot\",\"company_short_description\":\"As Telegram continues to grow in popularity, managing a vibrant and engaged community becomes both exciting and challenging for group administrators. The constant influx of messages, images, and media can quickly lead to clutter and, in some cases, unwelcome spam. Fortunately, there is a powerful solution at your disposal.\",\"company_cover_image\":\"assets/landing/images/hero/hero-image-2.png\",\"company_keywords\":\"telegram,group management,group bot,anti spam bot\",\"company_fb_messenger\":\"https://m.me/xxxxxx\",\"company_fb_page\":\"https://facebook.com/xxxxxx\",\"company_telegram_bot\":\"https://t.me/xxxxxx_bot\",\"company_telegram_channel\":\"https://t.me/xxxxxx\",\"company_youtube_channel\":\"https://www.youtube.com/xxxxxx\",\"company_twitter_account\":\"https://twitter.com/xxxxxx\",\"company_instagram_account\":\"https://instagram.com/xxxxxx\",\"company_linkedin_channel\":\"https://linkedin.com/company/xxxxxx\",\"company_support_url\":\"support.example.com\",\"links_docs_url\":\"/docs\",\"review_1_name\":\"Sarah\",\"review_1_designation\":\"Marketing Specialist\",\"review_1_avatar\":\"/assets/images/avatar/avatar-1.png\",\"review_1_description\":\"It has transformed the way we communicate and collaborate within our marketing team. The features for content sharing, quick discussions, and polls have streamlined our processes, making sure everyone is on the same page. It\'s like having a virtual meeting room at our fingertips!\",\"review_2_name\":\"Alex\",\"review_2_designation\":\"Educator\",\"review_2_avatar\":\"/assets/images/avatar/avatar-2.png\",\"review_2_description\":\"As an educator, it has revolutionized my online teaching approach. The platform\'s tools for interactive quizzes, resource sharing, and real-time discussions have created a vibrant virtual classroom. My students are engaged like never before, and managing assignments has become a breeze.\",\"review_3_name\":\"Mark\",\"review_3_designation\":\"Event Organizer\",\"review_3_avatar\":\"/assets/images/avatar/avatar-3.png\",\"review_3_description\":\"Managing events has never been easier thanks to TelegroupBot. From sending updates and RSVP tracking to handling last-minute changes, the platform has simplified event communication. Our attendees appreciate the timely information and real-time interaction\",\"review_4_name\":\"\",\"review_4_designation\":\"\",\"review_4_avatar\":\"\",\"review_4_description\":\"\",\"review_5_name\":\"\",\"review_5_designation\":\"\",\"review_5_avatar\":\"\",\"review_5_description\":\"\",\"review_6_name\":\"\",\"review_6_designation\":\"\",\"review_6_avatar\":\"\",\"review_6_description\":\"\",\"review_7_name\":\"\",\"review_7_designation\":\"\",\"review_7_avatar\":\"\",\"review_7_description\":\"\",\"review_8_name\":\"\",\"review_8_designation\":\"\",\"review_8_avatar\":\"\",\"review_8_description\":\"\",\"review_9_name\":\"\",\"review_9_designation\":\"\",\"review_9_avatar\":\"\",\"review_9_description\":\"\",\"review_10_name\":\"\",\"review_10_designation\":\"\",\"review_10_avatar\":\"\",\"review_10_description\":\"\",\"disable_landing_page\":\"0\",\"disable_review_section\":\"0\",\"enable_dark_mode\":\"0\"}', '2023-07-30 09:28:35', '[]');



CREATE TABLE IF NOT EXISTS `settings_email_autoresponders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `settings_data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings_email_autoresponder_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_email_autoresponder_id` int(11) NOT NULL,
  `list_name` mediumtext COLLATE utf8mb4_unicode_ci,
  `list_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `string_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_folder_id` int(11) NOT NULL,
  `list_total_subscribers` int(11) NOT NULL,
  `list_total_blacklisted` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list` (`settings_email_autoresponder_id`,`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ecommerce_store_id` int(11) DEFAULT NULL COMMENT 'null means payment settings not ecommerce',
  `whatsapp_bot_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `paypal` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mollie` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sslcommerz` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_v2` text COLLATE utf8mb4_unicode_ci,
  `toyyibpay` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `xendit` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `myfatoorah` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fastspring` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypro` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `yoomoney` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `flutterwave` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `manual_payment_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `manual_payment_instruction` text COLLATE utf8mb4_unicode_ci,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `decimal_point` tinyint(4) NOT NULL,
  `thousand_comma` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `currency_position` enum('left','right') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'left',
  `updated_at` datetime NOT NULL,
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `zv0fyow7ez789lh41sb5` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `settings_sms_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_type` enum('sms','email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email',
  `user_id` int(11) NOT NULL,
  `settings_data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `sms_email_send_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_api_id` int(11) DEFAULT NULL,
  `settings_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`api_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `telegram_bots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `bot_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bot_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_bot` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_join_groups` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_read_all_group_messages` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `supports_inline_queries` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_auto_responder_settings` mediumtext COLLATE utf8mb4_unicode_ci,
  `settings_sms_id` int(11) DEFAULT NULL,
  `sms_reply_message` mediumtext COLLATE utf8mb4_unicode_ci,
  `settings_email_id` int(11) DEFAULT NULL,
  `email_reply_message` mediumtext COLLATE utf8mb4_unicode_ci,
  `email_reply_subject` mediumtext COLLATE utf8mb4_unicode_ci,
  `broadcast_sequence_settings_sms_id` int(11) DEFAULT NULL,
  `sms_broadcast_sequence_campaign_id` int(11) NOT NULL,
  `broadcast_sequence_settings_email_id` int(11) DEFAULT NULL,
  `email_broadcast_sequence_campaign_id` int(11) NOT NULL,
  `started_button_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `welcome_message` mediumtext COLLATE utf8mb4_unicode_ci,
  `chat_human_email` mediumtext COLLATE utf8mb4_unicode_ci,
  `no_match_found_reply_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `persistent_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bot_token` (`bot_token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `telegram_bot_livechat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `telegram_group_subscriber_group_subscriber_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_bot_id` int(11) DEFAULT NULL,
  `sender` enum('user','bot') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `agent_name` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_content` longtext COLLATE utf8mb4_unicode_ci,
  `conversation_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `telegram_bot_livechat_messages` (`telegram_bot_id`),
  KEY `group_subscriber_id` (`telegram_group_subscriber_group_subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `telegram_groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supergroup_subscriber_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_bot_id` int(11) NOT NULL,
  `group_name` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_bot_admin` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_subscriber_id` (`supergroup_subscriber_id`),
  KEY `telegramgroup` (`telegram_bot_id`),
  KEY `group_id_index` (`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `telegram_group_message_filterings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `telegram_group_id` bigint(20) NOT NULL,
  `delete_command` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_image` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_voice` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_document` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_sticker` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_diceroll` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_links` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_forword_image` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_forward_links` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_all_forward_message` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `delete_containing_words` text COLLATE utf8mb4_unicode_ci,
  `remove_admin_message` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `restrict_member_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_joined_group` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `user_left_group` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `new_join_restrict_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `same_message_delete_count` int(11) DEFAULT NULL,
  `same_message_restrict_count` int(11) DEFAULT NULL,
  `allowed_message_per_time_count` int(11) DEFAULT NULL,
  `same_message_restrict_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minute_allowed_message_per_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `telegram_group_message_filtering1` (`user_id`),
  KEY `telegram_group_message_filtering2` (`telegram_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `telegram_group_message_sends` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telegram_group_id` bigint(20) NOT NULL,
  `message_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `schedule_time` datetime DEFAULT NULL,
  `timezone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_message_time` datetime DEFAULT NULL,
  `delete_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `message_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `telegram_group_message_send1` (`telegram_group_id`),
  KEY `telegram_group_message_send2` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `telegram_group_subscribers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `telegram_group_id` bigint(20) NOT NULL,
  `group_chat_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_subscriber_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `mute_time` datetime DEFAULT NULL,
  `is_banned` enum('0','ban','unban') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_left` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `removed` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_subscriber_id` (`group_subscriber_id`),
  KEY ` telegram_group_subscribers1` (`telegram_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `transaction_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `buyer_user_id` int(11) NOT NULL,
  `verify_status` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_at` datetime NOT NULL,
  `payment_method` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_amount` float NOT NULL,
  `cycle_start_date` date DEFAULT NULL,
  `cycle_expired_date` date DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `package_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_source` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_txn_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_url` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `buyer_user_id` (`buyer_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `transaction_manual_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `buyer_user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `transaction_id` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_amount` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thm_user_id` (`user_id`),
  KEY `buyer_user_id` (`buyer_user_id`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `update_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files` text NOT NULL,
  `sql_query` text NOT NULL,
  `update_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `usage_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usage_month` int(11) NOT NULL,
  `usage_year` year(4) NOT NULL,
  `usage_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`,`user_id`),
  KEY `c7zsc35trvp4lcmgyi42` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `profile_pic` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_user_id` int(11) NOT NULL DEFAULT '1',
  `user_type` enum('Member','Admin','Agent','Manager','Team') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Member',
  `agent_has_whitelabel` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `agent_has_ppu` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `agent_ppu_remaining` int(11) NOT NULL DEFAULT '-1' COMMENT '0 means unlimited',
  `agent_ppu_expiry_date` date DEFAULT NULL,
  `agent_domain` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_mailgun_username` mediumtext COLLATE utf8mb4_unicode_ci,
  `agent_mailgun_password` mediumtext COLLATE utf8mb4_unicode_ci,
  `package_id` int(11) DEFAULT NULL,
  `expired_date` datetime DEFAULT NULL,
  `allowed_telegram_bot_ids` text COLLATE utf8mb4_unicode_ci,
  `allowed_whatsapp_bot_ids` text COLLATE utf8mb4_unicode_ci,
  `allowed_ecommerce_store_ids` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bot_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `enable_forum_thread` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `enable_blog_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `enable_ticketing` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `timezone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscription_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `subscription_data` text COLLATE utf8mb4_unicode_ci,
  `last_payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `api_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_affiliate` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `under_which_affiliate_user` int(11) NOT NULL,
  `total_earn` double NOT NULL,
  `payment_commission` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `payment_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentage` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fixed_amount` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_recurring` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `affiliate_commission_given` double NOT NULL,
  `paypal_subscriber_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_next_check_time` datetime DEFAULT NULL,
  `paypal_processing` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `browser_notification_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `agent_domain` (`agent_domain`),
  KEY `parent_user_id` (`parent_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `email_verified_at`, `password`, `remember_token`, `address`, `profile_pic`, `created_at`, `updated_at`, `purchase_date`, `last_login_at`, `last_login_ip`, `activation_code`, `parent_user_id`, `user_type`, `agent_has_whitelabel`, `agent_has_ppu`, `agent_ppu_remaining`, `agent_ppu_expiry_date`, `agent_domain`, `agent_mailgun_username`, `agent_mailgun_password`, `package_id`, `expired_date`, `allowed_telegram_bot_ids`, `allowed_whatsapp_bot_ids`, `allowed_ecommerce_store_ids`, `status`, `bot_status`, `enable_forum_thread`, `enable_blog_comment`, `enable_ticketing`, `deleted`, `timezone`, `language`, `vat_no`, `subscription_enabled`, `subscription_data`, `last_payment_method`, `comment`, `api_key`, `is_affiliate`, `under_which_affiliate_user`, `total_earn`, `payment_commission`, `payment_type`, `percentage`, `fixed_amount`, `is_recurring`, `affiliate_commission_given`, `paypal_subscriber_id`, `paypal_next_check_time`, `paypal_processing`, `browser_notification_enabled`) VALUES
(1, 'Admin', 'admin@gmail.com', '', '2022-07-30 10:07:10', '$2y$10$LEnPv7azu39xTMe3Vlhi7.PBAOeg6zS282ha335OxpPGMWcspKC1y', '', '', '', '2023-07-30 12:00:00', '2023-07-30 03:01:26', NULL, '2023-07-30 12:09:16', '127.0.0.1', NULL, 0, 'Admin', '0', '0', 0, NULL, NULL, NULL, NULL, 103, '2023-07-30 00:00:00', NULL, NULL, NULL, '1', '1', '1', '1', '1', '0', 'Europe/Dublin', NULL, NULL, '0', NULL, '', NULL, NULL, '0', 0, 0, '0', '', '', '', '0', 0, '', NULL, '0', '1');


CREATE TABLE IF NOT EXISTS `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL,
  `current` enum('1','0') NOT NULL DEFAULT '1',
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`),
  KEY `Current` (`current`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


ALTER TABLE `packages`
  ADD CONSTRAINT `j4imv733ji1t3jkcc5xn` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `payment_api_logs`
  ADD CONSTRAINT `j4imv733ji1t3jkcc5xp` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings`
  ADD CONSTRAINT `j4imv733ji1t3kk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_email_autoresponders`
  ADD CONSTRAINT `j4imv733ji1t30o` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_email_autoresponder_lists`
  ADD CONSTRAINT `j4imv733ji1t366` FOREIGN KEY (`settings_email_autoresponder_id`) REFERENCES `settings_email_autoresponders` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_payments`
  ADD CONSTRAINT `zv0fyow7ez789lh41sb5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_sms_emails`
  ADD CONSTRAINT `zv0fyow7ez789lh41sb0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `sms_email_send_logs`
  ADD CONSTRAINT `zv0fyow7ez789lh41sb2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `telegram_bots`
  ADD CONSTRAINT `zv0fyow7ez7812` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `telegram_bot_livechat_messages`
  ADD CONSTRAINT `telegram_bot_livechat_messages` FOREIGN KEY (`telegram_bot_id`) REFERENCES `telegram_bots` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `telegram_bot_livechat_messages2` FOREIGN KEY (`telegram_group_subscriber_group_subscriber_id`) REFERENCES `telegram_group_subscribers` (`group_subscriber_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `telegram_groups`
  ADD CONSTRAINT `telegramgroup` FOREIGN KEY (`telegram_bot_id`) REFERENCES `telegram_bots` (`id`) ON DELETE CASCADE;

ALTER TABLE `telegram_group_message_filterings`
  ADD CONSTRAINT `telegram_group_message_filtering1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `telegram_group_message_filtering2` FOREIGN KEY (`telegram_group_id`) REFERENCES `telegram_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `telegram_group_message_sends`
  ADD CONSTRAINT `telegram_group_message_send1` FOREIGN KEY (`telegram_group_id`) REFERENCES `telegram_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `telegram_group_message_send2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `telegram_group_subscribers`
  ADD CONSTRAINT ` telegram_group_subscribers1` FOREIGN KEY (`telegram_group_id`) REFERENCES `telegram_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `transaction_logs`
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi46` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi47` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `transaction_manual_logs`
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi43` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi44` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `usage_logs`
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi42` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;