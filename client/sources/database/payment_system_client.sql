-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 28, 2021 at 12:30 AM
-- Server version: 10.5.10-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `payment_system_client`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `username` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `role_id`, `unique_id`, `status`, `username`, `password`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, '1731be1542e23ec5fafc5aa984e07978', 1, 'enindu', '$2y$10$x0GkVo2Ltnp9yQII1eOlVOCEsxqbpPloyPyKbPFywT.JhupaxMlOq', NULL, '2021-04-03 14:36:23', '2021-04-03 14:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `section_id`, `slug`, `title`, `subtitle`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'own-account-fund-transfer-609686d2eaf96', 'Own Account Fund Transfer', 'OAFT', 'false', NULL, '2021-05-08 18:10:50', '2021-05-08 18:10:50'),
(2, 1, 'third-party-fund-transfer-609686ec21cad', 'Third Party Fund Transfer', 'TPFT', 'false', NULL, '2021-05-08 18:11:16', '2021-05-08 18:11:16'),
(3, 1, 'inter-bank-fund-transfer-60968704e6367', 'Inter Bank Fund Transfer', 'IBFT', 'false', NULL, '2021-05-08 18:11:40', '2021-05-08 18:11:40'),
(4, 1, 'electronic-payment-609797f985946', 'Electronic Payment', 'EPAY', 'false', NULL, '2021-05-09 13:36:17', '2021-05-09 13:36:17'),
(5, 2, 'toyota-6097a0f85d5be', 'Toyota', '100000000000', 'false', NULL, '2021-05-09 00:35:00', '2021-05-09 14:14:40'),
(6, 2, 'dialog-tv-prepaid-6097a12473e65', 'Dialog TV (Prepaid)', '200000000000', 'false', NULL, '2021-05-09 00:35:34', '2021-05-09 14:15:24'),
(7, 2, 'dialog-tv-postpaid-6097a1b8224e4', 'Dialog TV (Postpaid)', '200000000000', 'false', NULL, '2021-05-09 00:35:52', '2021-05-09 14:17:52'),
(8, 3, 'mobitel-6097a1fddffa6', 'Mobitel', '300000000000', 'false', NULL, '2021-05-09 14:02:52', '2021-05-09 14:19:01'),
(9, 3, 'dialog-6097a20721901', 'Dialog', '200000000000', 'false', NULL, '2021-05-09 14:03:51', '2021-05-09 14:19:11'),
(10, 3, 'hutch-6097a25fd966f', 'Hutch', '400000000000', 'false', NULL, '2021-05-09 14:04:20', '2021-05-09 14:20:39'),
(11, 5, 'commercial-bank-60afa934cf654', 'Commercial Bank', 'false', 'false', NULL, '2021-05-27 19:44:12', '2021-05-27 19:44:12'),
(12, 5, 'sampath-bank-60afa93fdab9e', 'Sampath Bank', 'false', 'false', NULL, '2021-05-27 19:44:23', '2021-05-27 19:44:23'),
(13, 5, 'bank-of-ceylon-60afa95011563', 'Bank of Ceylon', 'false', 'false', NULL, '2021-05-27 19:44:40', '2021-05-27 19:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE `contents` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `subtitle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `subtitle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `subtitle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `section_id`, `title`, `subtitle`, `description`, `file`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'false', 'false', 'false', 'a0dcf0a6dad5bc97609980d11ec53.jpg', NULL, '2021-05-11 00:22:01', '2021-05-11 00:22:01'),
(2, 4, 'false', 'false', 'false', '6f46d5048d8209e56099824acfceb.jpg', NULL, '2021-05-11 00:28:18', '2021-05-11 00:28:18'),
(3, 4, 'false', 'false', 'false', 'de7eb82655ee74f5609982575087f.jpg', NULL, '2021-05-11 00:28:31', '2021-05-11 00:28:31'),
(4, 4, 'false', 'false', 'false', '4ce1ad1b34e99230609982636b008.jpg', NULL, '2021-05-11 00:28:43', '2021-05-11 00:28:43'),
(5, 4, 'false', 'false', 'false', 'bd2e425f4e094c436099826c446a1.jpg', NULL, '2021-05-11 00:28:52', '2021-05-11 00:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'john@example.com', 'Test Message', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.', NULL, '2021-05-11 00:13:18', '2021-05-11 01:03:12');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Adminsitrator', NULL, '1995-07-21 06:29:00', '1995-07-21 06:29:00'),
(2, 'User', NULL, '1995-07-21 06:29:00', '1995-07-21 06:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Money Transfer Type', NULL, '2021-05-08 18:09:39', '2021-05-08 18:09:39'),
(2, 'Payee', NULL, '2021-05-09 00:27:07', '2021-05-09 00:27:07'),
(3, 'Service Provider', NULL, '2021-05-09 14:02:24', '2021-05-09 14:02:24'),
(4, 'Advertisement', NULL, '2021-05-11 00:21:25', '2021-05-11 00:21:25'),
(5, 'Bank', NULL, '2021-05-27 19:43:41', '2021-05-27 19:43:41');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `unique_id`, `status`, `first_name`, `last_name`, `nic`, `email`, `phone`, `account`, `device_id`, `password`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, '2d28905fea63d53a6251282a3bdd8ff8', 1, 'John', 'Doe', '000000000000', 'john@example.com', '0000000000', '000000000000', 'c1otd7dp567sipmno1eg', '$2y$10$ye77PanAJy4Cg4V23hVftOa57E1UN9TwBQixSP1/0GgJpN/bYBHRi', NULL, '2021-04-10 22:17:25', '2021-04-20 16:59:20'),
(2, 2, 'f95983ce12eb862a74e5f38cd9069fc2', 1, 'Brad', 'Doe', '000000000001', 'brad@example.com', '0000000001', '000000000001', 'c2apa15p567q5tnoo5ig', '$2y$10$Pi99kS0.8YldMfTNrELcnuxcbmg.TmQKBxpUbNhcsfx9ANR3i.kTe', NULL, '2021-05-08 00:59:08', '2021-05-08 00:59:14'),
(3, 2, 'b06fea9c89f1361234828006198589b2', 1, 'Max', 'Doe', '000000000002', 'max@example.com', '0000000002', '000000000002', 'c2d4mtdp567saoscsnsg', '$2y$10$AZDowl.rlrX2TOOsEwNUbOUEixs.f92Ymvx8GPx5Li.i5QAWZkOEC', NULL, '2021-05-11 14:46:37', '2021-05-11 14:46:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD UNIQUE KEY `device_id` (`device_id`),
  ADD UNIQUE KEY `nic` (`nic`,`email`,`phone`,`account`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
