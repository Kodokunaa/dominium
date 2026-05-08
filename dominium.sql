-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql212.infinityfree.com
-- Generation Time: May 07, 2026 at 10:00 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dominium`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `metric_type` enum('listings_count','bookings_count','users_count','favorites_count') NOT NULL,
  `metric_value` int(11) NOT NULL,
  `recorded_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `analytics`
--

INSERT INTO `analytics` (`id`, `metric_type`, `metric_value`, `recorded_date`, `created_at`) VALUES
(1, 'users_count', 1, '2026-05-07', '2026-05-07 14:20:11'),
(2, 'listings_count', 0, '2026-05-07', '2026-05-07 14:20:11'),
(3, 'bookings_count', 0, '2026-05-07', '2026-05-07 14:20:11'),
(4, 'favorites_count', 0, '2026-05-07', '2026-05-07 14:20:11');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(150) NOT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_id` varchar(100) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `card_last4` varchar(4) DEFAULT NULL,
  `card_brand` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `listing_id`, `user_id`, `guest_name`, `guest_email`, `checkin`, `checkout`, `price`, `status`, `booked_at`, `payment_id`, `payment_amount`, `payment_status`, `payment_method`, `card_last4`, `card_brand`) VALUES
(1, 1, 4, 'Castillo Renter', 'castillo.paulgerson@gmail.com', '2026-05-08', '2026-05-09', '2000.00', 'approved', '2026-05-07 08:28:33', 'ch_sim_69fca191b43d0', '2000.00', 'succeeded', 'stripe', '4242', 'visa'),
(2, 2, 1, 'VXE Admin', 'admin@dominium.local', '2026-05-30', '2026-06-20', '105000.00', 'approved', '2026-05-07 08:34:17', 'ch_sim_69fca2e9e983d', '105000.00', 'succeeded', 'stripe', '4242', 'visa'),
(3, 3, 5, 'Skkm Renter', 'skkm2026.avp@gmail.com', '2026-05-16', '2026-05-28', '36000.00', 'approved', '2026-05-07 08:39:57', 'ch_sim_69fca43d02b2c', '36000.00', 'succeeded', 'stripe', '4242', 'visa'),
(4, 4, 3, 'Kodokunaa Renter', 'amaionigiri04@gmail.com', '2026-05-16', '2026-05-23', '70000.00', 'cancelled', '2026-05-07 20:10:06', 'ch_sim_69fcc76e67b9e', '70000.00', 'succeeded', 'stripe', '4242', 'visa'),
(5, 4, 3, 'Kodokunaa Renter', 'amaionigiri04@gmail.com', '2026-05-16', '2026-05-30', '140000.00', 'cancelled', '2026-05-08 02:24:39', 'ch_sim_69fd1f378ed67', '140000.00', 'succeeded', 'stripe', '4242', 'visa'),
(6, 4, 7, 'zing zing', 'zingqxcz@gmail.com', '2026-05-09', '2026-05-10', '10000.00', 'approved', '2026-05-08 02:44:13', 'ch_sim_69fd23cd9f83c', '10000.00', 'succeeded', 'stripe', '4242', 'visa');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `listing_id`, `created_at`) VALUES
(2, 4, 1, '2026-05-07 14:28:05'),
(3, 3, 1, '2026-05-07 14:29:04'),
(4, 5, 3, '2026-05-07 14:38:36'),
(5, 5, 2, '2026-05-07 14:38:44'),
(6, 5, 1, '2026-05-07 14:38:45'),
(7, 1, 3, '2026-05-07 16:02:46'),
(8, 1, 2, '2026-05-07 16:02:48'),
(9, 1, 1, '2026-05-07 16:02:49'),
(10, 1, 4, '2026-05-07 16:06:56'),
(11, 1, 6, '2026-05-08 00:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `province` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `thumbnail` varchar(512) NOT NULL,
  `description` text NOT NULL,
  `bedrooms` int(11) NOT NULL DEFAULT 1,
  `guests` int(11) NOT NULL DEFAULT 1,
  `category` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `user_id`, `title`, `province`, `city`, `price`, `thumbnail`, `description`, `bedrooms`, `guests`, `category`, `is_active`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 2, 'Bahay ni Admin', 'Oriental Mindoro', 'City of Calapan', '2000.00', 'uploads/listings/46f0201f1ce8d33348cb396763891b7f.avif', 'Bodega talaga ito ni Rhendel Agosto sa Sapul', 1, 2, 'Condominium', 1, 1, '2026-05-07 14:26:55', '2026-05-07 14:27:30'),
(2, 3, 'Bahay ni Paul', 'Rizal', 'City of Antipolo', '8000.00', 'uploads/listings/0fbae9b4e2409584aa7f37f5aa28b5f5.avif', 'Di mo kami katulad, masipag kasi kami.', 3, 5, 'Apartment', 1, 1, '2026-05-07 14:32:57', '2026-05-08 01:46:55'),
(3, 2, 'Bahay ni Kapitan Bernie', 'Davao Del Norte', 'San Isidro', '3000.00', 'uploads/listings/b27deeeae94f389476f2b7722dbbf102.avif', 'May gera dito sometimes', 3, 6, 'Resort', 1, 1, '2026-05-07 14:37:33', '2026-05-07 14:37:52'),
(4, 2, 'Condo ni Sir Ron', 'Oriental Mindoro', 'City of Calapan', '10000.00', 'uploads/listings/141b3d970007d0343768e824aba567fd.avif', 'Binebenta na po namin ito', 2, 3, 'Condominium', 1, 1, '2026-05-07 16:05:11', '2026-05-07 16:06:36'),
(5, 3, 'Bahay ng aso ni Paul', 'Batangas', 'City of Lipa', '4000.00', 'uploads/listings/cd59cb009b3066d8ef2b86beacc1449a.avif', 'Rentahan niyo na lang kasi', 5, 15, 'Villa', 0, 0, '2026-05-07 16:46:00', '2026-05-07 16:46:00'),
(6, 7, 'All Blue', 'Agusan Del Norte', 'City of Cabadbaran', '100000.00', 'uploads/listings/e143729c1d4cf0ba20805dc1e2a9e502.jpg', 'comfortable environment with the finest dining area you will get in this whole country', 1, 4, 'Apartment', 1, 1, '2026-05-07 23:58:03', '2026-05-08 00:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `listing_images`
--

CREATE TABLE `listing_images` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `path` varchar(512) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listing_images`
--

INSERT INTO `listing_images` (`id`, `listing_id`, `path`, `sort_order`) VALUES
(1, 1, 'uploads/listings/384c74cf7043ed08bf8659179289302c.avif', 0),
(2, 1, 'uploads/listings/edc4c4db3196af20cd1d07b925e7f64d.avif', 1),
(5, 3, 'uploads/listings/421cf6d7ee5400936a9bd5a431849069.avif', 0),
(6, 3, 'uploads/listings/08f01dab316823eab07b1ef8db5d5c49.avif', 1),
(7, 4, 'uploads/listings/6c3976e57b084809e2d2c774c9553cc8.jpg', 0),
(8, 4, 'uploads/listings/7fc4efcae8fe866853decb31c25ad20a.avif', 1),
(9, 5, 'uploads/listings/b037f81e2217d1b2b9b5403d4d3f064b.avif', 0),
(10, 5, 'uploads/listings/57c99a7d4891e185de970f1432f38351.avif', 1),
(14, 6, 'uploads/listings/58a552003e2633136cde643ae1092738.webp', 0),
(15, 2, 'uploads/listings/ed691e884261c3a37af9e835c36cd134.avif', 0),
(16, 2, 'uploads/listings/50780077fb9a991b325e3fd69830b436.avif', 1);

-- --------------------------------------------------------

--
-- Table structure for table `price_alerts`
--

CREATE TABLE `price_alerts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `price_change_type` enum('decrease','increase') NOT NULL,
  `price_change_amount` decimal(10,2) NOT NULL,
  `price_change_percentage` decimal(5,2) NOT NULL,
  `is_notified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `price_alerts`
--

INSERT INTO `price_alerts` (`id`, `user_id`, `listing_id`, `original_price`, `new_price`, `price_change_type`, `price_change_amount`, `price_change_percentage`, `is_notified`, `created_at`, `notified_at`) VALUES
(1, 1, 2, '5000.00', '8000.00', 'increase', '3000.00', '60.00', 0, '2026-05-08 01:46:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `name` varchar(255) GENERATED ALWAYS AS (concat(coalesce(`first_name`,''),' ',coalesce(`last_name`,''))) STORED,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(50) DEFAULT NULL,
  `auth_provider` enum('email','google') DEFAULT 'email',
  `role` enum('renter','lister','admin') DEFAULT 'renter',
  `is_approved` tinyint(1) DEFAULT 0,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name_changed_at` timestamp NULL DEFAULT NULL,
  `is_banned` tinyint(1) DEFAULT 0,
  `banned_until` timestamp NULL DEFAULT NULL,
  `ban_reason` text DEFAULT NULL,
  `lister_application_pending` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `google_id`, `auth_provider`, `role`, `is_approved`, `avatar`, `created_at`, `updated_at`, `name_changed_at`, `is_banned`, `banned_until`, `ban_reason`, `lister_application_pending`) VALUES
(1, 'VXE', 'Admin', 'admin@dominium.local', '$2y$10$omgl2fkJlRcYWCtENV1Pn.JYZYuskfhU8Mu8PwegfGOAFoeGKopDm', NULL, 'email', 'admin', 1, NULL, '2023-02-28 16:00:00', '2023-02-28 16:00:00', NULL, 0, NULL, NULL, 0),
(2, 'Raikou', 'Lister', 'zcybe04@gmail.com', '$2y$10$TQQYSqhoJdS1zzNnufYoc.iOcVOut.ib81KPqhYSISUMoe1ym1ICa', '110279575950924431185', 'google', 'lister', 1, NULL, '2026-05-07 14:22:10', '2026-05-07 20:11:29', NULL, 0, NULL, NULL, 0),
(3, 'Kodokunaa', 'Renter', 'amaionigiri04@gmail.com', '$2y$10$/.NhdTBgNHYu/yBZaN55lemuGsn6W76N3P3Vhxe4KESNz8PAOupVu', NULL, 'email', 'lister', 1, NULL, '2026-05-07 14:22:44', '2026-05-07 17:13:51', NULL, 0, NULL, NULL, 0),
(4, 'Castillo', 'Renter', 'castillo.paulgerson@gmail.com', '$2y$10$efbiquc08eEfP.dvRnqbLeqoIiJE5bRWDUK4LpsysrilNT4YJNPie', '108995073365827556440', 'google', 'renter', 1, NULL, '2026-05-07 14:25:07', '2026-05-07 19:42:05', NULL, 0, NULL, NULL, 0),
(5, 'Skkm', 'Renter', 'skkm2026.avp@gmail.com', '$2y$10$NyshB3F1eWR/QJlTk8HVtOJUA0gr7n1RlVOcvDVPPuoKizYom7qIi', '112688072870689873528', 'google', 'renter', 1, NULL, '2026-05-07 14:35:32', '2026-05-07 19:42:17', NULL, 0, NULL, NULL, 0),
(6, 'Chord', 'Renter', 'chxrdify@gmail.com', '$2y$10$7qsRV2MOaS5CbbOwN.hGD.zLjMb.lVYNhhMBBn5d3/BO6iAFfNtke', NULL, 'email', 'renter', 0, NULL, '2026-05-07 16:41:41', '2026-05-07 16:41:52', NULL, 0, NULL, NULL, 1),
(7, 'zing', 'zing', 'zingqxcz@gmail.com', '$2y$10$ynlU1DQZBQ7hFFtwAKbyHOVfzsfVObYkVwe4nZVBKGHbt4Tom2ABu', NULL, 'email', 'lister', 1, NULL, '2026-05-07 23:01:24', '2026-05-07 23:49:30', NULL, 0, NULL, NULL, 0),
(8, 'Sza', 'Admin', 'szaaaa@gmail.com', '$2y$10$0sbEiCDRFPSj8PfXeoRaTefby.x35Bq7Ueyu1YcQ99KS89TskKkJG', NULL, 'email', 'renter', 0, NULL, '2026-05-08 00:07:51', '2026-05-08 00:07:51', NULL, 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` enum('view','favorite','unfavorite','search','book') NOT NULL,
  `listing_id` int(11) DEFAULT NULL,
  `activity_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`id`, `user_id`, `activity_type`, `listing_id`, `activity_data`, `created_at`) VALUES
(1, 4, 'favorite', 1, NULL, '2026-05-07 08:27:55'),
(2, 4, 'unfavorite', 1, NULL, '2026-05-07 08:28:00'),
(3, 4, 'favorite', 1, NULL, '2026-05-07 08:28:05'),
(4, 3, 'favorite', 1, NULL, '2026-05-07 08:29:04'),
(5, 5, 'favorite', 3, NULL, '2026-05-07 08:38:36'),
(6, 5, 'favorite', 2, NULL, '2026-05-07 08:38:44'),
(7, 5, 'favorite', 1, NULL, '2026-05-07 08:38:45'),
(8, 1, 'favorite', 3, NULL, '2026-05-07 19:02:46'),
(9, 1, 'favorite', 2, NULL, '2026-05-07 19:02:47'),
(10, 1, 'favorite', 1, NULL, '2026-05-07 19:02:48'),
(11, 1, 'favorite', 4, NULL, '2026-05-07 19:06:55'),
(12, 1, 'favorite', 6, NULL, '2026-05-08 03:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) DEFAULT 1,
  `price_alerts_enabled` tinyint(1) DEFAULT 1,
  `push_notifications` tinyint(1) DEFAULT 0,
  `min_price_alert` decimal(10,2) DEFAULT NULL,
  `max_price_alert` decimal(10,2) DEFAULT NULL,
  `preferred_categories` text DEFAULT NULL,
  `preferred_locations` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `email_notifications`, `price_alerts_enabled`, `push_notifications`, `min_price_alert`, `max_price_alert`, `preferred_categories`, `preferred_locations`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 0, NULL, NULL, NULL, NULL, '2026-05-07 14:21:28', '2026-05-07 14:21:28'),
(2, 4, 1, 1, 0, NULL, NULL, NULL, NULL, '2026-05-07 14:27:57', '2026-05-07 14:27:57'),
(3, 3, 1, 1, 0, NULL, NULL, NULL, NULL, '2026-05-07 14:29:10', '2026-05-07 14:29:10'),
(4, 7, 1, 1, 0, NULL, NULL, NULL, NULL, '2026-05-07 23:03:53', '2026-05-07 23:03:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_metric_date` (`metric_type`,`recorded_date`),
  ADD KEY `metric_type` (`metric_type`),
  ADD KEY `recorded_date` (`recorded_date`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `checkin` (`checkin`),
  ADD KEY `checkout` (`checkout`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_bookings_user_listing` (`user_id`,`listing_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_listing` (`user_id`,`listing_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `idx_favorites_user_date` (`user_id`,`created_at`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `is_approved` (`is_approved`),
  ADD KEY `category` (`category`),
  ADD KEY `price` (`price`),
  ADD KEY `idx_listings_search` (`is_active`,`is_approved`,`created_at`);
ALTER TABLE `listings` ADD FULLTEXT KEY `search` (`title`,`description`,`city`,`province`);

--
-- Indexes for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `price_alerts`
--
ALTER TABLE `price_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `is_notified` (`is_notified`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD KEY `role` (`role`),
  ADD KEY `is_approved` (`is_approved`),
  ADD KEY `is_banned` (`is_banned`),
  ADD KEY `banned_until` (`banned_until`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `email_notifications` (`email_notifications`),
  ADD KEY `price_alerts_enabled` (`price_alerts_enabled`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `price_alerts`
--
ALTER TABLE `price_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `fk_listings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD CONSTRAINT `fk_listing_images_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `price_alerts`
--
ALTER TABLE `price_alerts`
  ADD CONSTRAINT `fk_price_alerts_listing` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_price_alerts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `fk_user_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
