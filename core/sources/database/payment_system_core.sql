-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 28, 2021 at 12:31 AM
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
-- Database: `payment_system_core`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime(3) DEFAULT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  `deleted_at` datetime(3) DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `created_at`, `updated_at`, `deleted_at`, `first_name`, `last_name`, `nic`, `email`, `phone`, `account`, `device_id`, `balance`) VALUES
(1, '2021-04-10 22:15:27.000', '2021-05-28 00:30:14.041', NULL, 'John', 'Doe', '000000000000', 'john@example.com', '0000000000', '000000000000', 'c1otd7dp567sipmno1eg', 600),
(2, '2021-05-08 00:13:45.000', '2021-05-11 21:22:30.328', NULL, 'Brad', 'Doe', '000000000001', 'brad@example.com', '0000000001', '000000000001', 'c2apa15p567q5tnoo5ig', 1200),
(3, '2021-05-09 13:41:20.000', '2021-05-11 21:23:14.910', NULL, 'Toyota', 'Corporate', '100000000000', 'toyota@example.com', '1000000000', '100000000000', NULL, 1200),
(4, '2021-05-09 13:43:44.000', '2021-05-27 19:34:57.082', NULL, 'Dialog', 'Corporate', '200000000000', 'dialog@example.com', '2000000000', '200000000000', NULL, 1800),
(5, '2021-05-09 14:18:05.000', '2021-05-10 01:07:01.564', NULL, 'Mobitel', 'Corporate', '300000000000', 'mobitel@example.com', '3000000000', '300000000000', NULL, 1400),
(6, '2021-05-09 14:19:20.000', '2021-05-09 14:19:20.000', NULL, 'Hutch', 'Corporate', '400000000000', 'hutch@example.com', '4000000000', '400000000000', NULL, 1000),
(7, '2021-05-11 14:42:26.000', '2021-05-11 21:23:23.691', NULL, 'Max', 'Doe', '000000000002', 'max@example.com', '0000000002', '000000000002', 'c2d4mtdp567saoscsnsg', 400);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime(3) DEFAULT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  `deleted_at` datetime(3) DEFAULT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `receiver_account` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `created_at`, `updated_at`, `deleted_at`, `unique_id`, `sender_id`, `receiver_account`, `type`, `amount`, `description`) VALUES
(1, '2021-05-09 19:45:43.868', '2021-05-09 19:45:43.868', NULL, 'c2but3tp567l82g2c6p0', 1, '000000000001', 'TPFT', 100, NULL),
(2, '2021-05-09 19:46:25.264', '2021-05-09 19:46:25.264', NULL, 'c2butedp567l82g2c6pg', 1, '200000000000', 'EPAY', 100, 'Dialog TV (Prepaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(3, '2021-05-09 19:46:47.108', '2021-05-09 19:46:47.108', NULL, 'c2butjtp567l82g2c6q0', 1, '300000000000', 'EPAY', 100, 'Mobitel - 0000000000'),
(4, '2021-05-09 19:48:08.772', '2021-05-09 19:48:08.772', NULL, 'c2buu85p567l82g2c6qg', 1, '000000000001', 'IBFT', 100, NULL),
(5, '2021-05-10 00:55:17.924', '2021-05-10 00:55:17.924', NULL, 'c2c3e7dp567nlnt1m1dg', 2, '000000000000', 'TPFT', 100, NULL),
(6, '2021-05-10 01:06:29.923', '2021-05-10 01:06:29.923', NULL, 'c2c3jfdp567giothmvr0', 2, '000000000000', 'IBFT', 100, NULL),
(7, '2021-05-10 01:06:50.321', '2021-05-10 01:06:50.321', NULL, 'c2c3jklp567giothmvrg', 2, '200000000000', 'EPAY', 100, 'Dialog TV (Postpaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(8, '2021-05-10 01:07:01.564', '2021-05-10 01:07:01.564', NULL, 'c2c3jndp567giothmvs0', 2, '300000000000', 'EPAY', 100, 'Mobitel - 0000000000'),
(9, '2021-05-11 15:24:13.649', '2021-05-11 15:24:13.649', NULL, 'c2d58hdp567saoscsnt0', 7, '000000000000', 'TPFT', 100, NULL),
(10, '2021-05-11 15:33:08.416', '2021-05-11 15:33:08.416', NULL, 'c2d5cn5p567saoscsntg', 7, '200000000000', 'EPAY', 100, 'Dialog TV (Postpaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(11, '2021-05-11 21:22:30.328', '2021-05-11 21:22:30.328', NULL, 'c2dagflp567qrissn6rg', 7, '000000000001', 'TPFT', 100, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(12, '2021-05-11 21:23:02.708', '2021-05-11 21:23:02.708', NULL, 'c2dagnlp567qrissn6s0', 7, '000000000001', 'IBFT', 100, NULL),
(13, '2021-05-11 21:23:14.911', '2021-05-11 21:23:14.911', NULL, 'c2dagqlp567qrissn6sg', 7, '100000000000', 'EPAY', 100, 'Toyota - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(14, '2021-05-11 21:23:23.692', '2021-05-11 21:23:23.692', NULL, 'c2dagstp567qrissn6t0', 7, '200000000000', 'EPAY', 100, 'Dialog - 0000000000'),
(15, '2021-05-27 19:34:01.279', '2021-05-27 19:34:01.279', NULL, 'c2nqdkdp567p36cmtab0', 1, '200000000000', 'EPAY', 100, 'Dialog TV (Postpaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(16, '2021-05-27 19:34:22.958', '2021-05-27 19:34:22.958', NULL, 'c2nqdplp567p36cmtabg', 1, '200000000000', 'EPAY', 100, 'Dialog TV (Postpaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(17, '2021-05-27 19:34:43.704', '2021-05-27 19:34:43.704', NULL, 'c2nqdutp567p36cmtac0', 1, '200000000000', 'EPAY', 100, 'Dialog TV (Postpaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(18, '2021-05-27 19:34:57.082', '2021-05-27 19:34:57.082', NULL, 'c2nqe2dp567p36cmtacg', 1, '200000000000', 'EPAY', 100, 'Dialog TV (Postpaid) - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
(19, '2021-05-28 00:30:14.042', '2021-05-28 00:30:14.042', NULL, 'c2nuoflp567p36cmtad0', 1, '123456789123', 'IBFT', 100, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `account` (`account`),
  ADD KEY `idx_accounts_deleted_at` (`deleted_at`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `idx_transactions_deleted_at` (`deleted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
