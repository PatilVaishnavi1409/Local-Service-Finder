-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2025 at 05:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sem_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `service_category` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `status` enum('pending','confirmed','completed','canceled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `provider_id`, `service_category`, `booking_date`, `booking_time`, `notes`, `email`, `status`, `created_at`) VALUES
(1, 2, 3, 'plumbing', '2025-12-27', '09:45:00', '', 'pavan@gmail.com', 'completed', '2025-12-26 11:05:48'),
(2, 1, 4, 'cleaning', '2025-12-27', '12:30:00', '', 'pranavsuryawanshi@gmail.com', 'completed', '2025-12-27 09:48:39'),
(3, 1, 5, 'electrical', '2025-12-28', '13:30:00', '', 'pranavsuryawanshi@gmail.com', 'pending', '2025-12-28 10:14:04'),
(4, 1, 3, 'plumbing', '2025-12-28', '13:15:00', '', 'pranavsuryawanshi@gmail.com', 'pending', '2025-12-28 10:19:03'),
(5, 10, 9, 'transport', '2025-12-29', '12:00:00', '', 'Vicky@gmail.com', 'pending', '2025-12-28 16:08:12'),
(6, 10, 5, 'electrical', '2025-12-30', '14:15:00', '', 'Vicky@gmail.com', 'pending', '2025-12-28 16:08:58'),
(7, 10, 7, 'health', '2025-12-29', '14:45:00', '', 'Vicky@gmail.com', 'pending', '2025-12-28 16:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`login_id`, `user_id`, `email`, `login_time`) VALUES
(1, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:10:26'),
(2, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:14:45'),
(3, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:16:09'),
(4, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:18:36'),
(5, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:18:48'),
(6, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:18:57'),
(7, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:21:08'),
(8, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:22:45'),
(9, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:23:00'),
(10, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:25:46'),
(11, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:28:28'),
(12, 1, 'pranavsuryawanshi@gmail.com', '2025-12-26 10:28:41'),
(13, 2, 'pavan@gmail.com', '2025-12-26 10:30:31'),
(14, 3, 'yash@gmail.com', '2025-12-26 11:08:20'),
(15, 3, 'yash@gmail.com', '2025-12-26 11:13:59'),
(16, 3, 'yash@gmail.com', '2025-12-26 11:16:27'),
(17, 3, 'yash@gmail.com', '2025-12-26 11:18:40'),
(18, 2, 'pavan@gmail.com', '2025-12-26 11:20:52'),
(19, 3, 'yash@gmail.com', '2025-12-27 09:21:50'),
(20, 3, 'yash@gmail.com', '2025-12-27 09:32:37'),
(21, 2, 'pavan@gmail.com', '2025-12-27 09:36:17'),
(22, 4, 'dakshita@gmail.com', '2025-12-27 09:36:57'),
(23, 1, 'pranavsuryawanshi@gmail.com', '2025-12-27 09:42:16'),
(24, 1, 'pranavsuryawanshi@gmail.com', '2025-12-27 09:53:20'),
(25, 4, 'dakshita@gmail.com', '2025-12-27 09:54:06'),
(26, 1, 'pranavsuryawanshi@gmail.com', '2025-12-27 09:54:22'),
(27, 1, 'pranavsuryawanshi@gmail.com', '2025-12-27 10:28:24'),
(28, 1, 'pranavsuryawanshi@gmail.com', '2025-12-27 10:33:25'),
(29, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 09:27:35'),
(30, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 09:36:34'),
(31, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 10:19:52'),
(32, 5, 'vedant@gmail.com', '2025-12-28 10:20:40'),
(33, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 10:35:13'),
(34, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 10:37:50'),
(35, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 10:52:42'),
(36, 10, 'Vicky@gmail.com', '2025-12-28 16:04:41'),
(37, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 16:24:43'),
(38, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 16:32:42'),
(39, 1, 'pranavsuryawanshi@gmail.com', '2025-12-28 16:42:26'),
(40, 5, 'vedant@gmail.com', '2025-12-28 16:43:09');

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `provider_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `service_category` varchar(50) NOT NULL,
  `experience_years` int(11) DEFAULT 0,
  `hourly_rate` decimal(8,2) DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`provider_id`, `user_id`, `business_name`, `service_category`, `experience_years`, `hourly_rate`, `rating`, `rating_count`, `description`, `created_at`) VALUES
(1, 3, 'abc', 'plumbing', 5, 50.00, 0.00, 0, '', '2025-12-26 10:37:51'),
(2, 4, 'abc', 'cleaning', 2, 50.00, 4.50, 2, '', '2025-12-26 11:23:01'),
(3, 5, 'Vedant', 'electrical', 5, 50.00, 3.00, 3, '', '2025-12-27 09:28:15'),
(4, 6, 'Om Enterprises', 'tutoring', 3, 100.00, 2.00, 1, '', '2025-12-28 10:37:37'),
(5, 7, 'Parth Agency', 'health', 2, 75.00, 5.00, 2, '', '2025-12-28 10:44:29'),
(6, 8, 'Harsh Agency', 'cleaning', 3, 60.00, 3.00, 2, '', '2025-12-28 10:46:04'),
(7, 9, 'Vikas Agency', 'transport', 1, 200.00, 0.00, 0, '', '2025-12-28 10:52:14'),
(8, 11, '', 'tutoring', 3, 40.00, 0.00, 0, '', '2025-12-28 16:32:27');

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `user_type` enum('customer','provider') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`user_id`, `first_name`, `last_name`, `email`, `password`, `address`, `phone`, `user_type`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'pranav', 'surya', 'pranavsuryawanshi@gmail.com', '$2y$10$s5e779VvIrTQUt74GL5JLOBunu.HCJ9Pny702K6tw9gwJELtjpShK', 'abc', '7709154425', 'customer', '2025-12-26 10:09:56', 'f62b8c5cfc3be3425d28a5881c2314fc74643152eff21f163ef6b635ea6f8206', '2025-12-28 18:33:37'),
(6, 'Om', 'Patil', 'ompatil@gmail.com', '$2y$10$IYdY2Gvh9IXGSiDIA5p.ReqB3veC10zQsKemtWI9lnQxA6MPMtwOu', 'Nimzari Naka', '7709154425', 'customer', '2025-12-28 10:37:37', NULL, NULL),
(3, 'Yash', 'bagal', 'yash@gmail.com', '$2y$10$DJTMdCFfW0VCDzfAtHrGjex3Y7GEIccTb9tXYqQatEUUXUQ2lhfpO', 'Nimzari naka', '7709154425', 'provider', '2025-12-26 10:37:51', NULL, NULL),
(4, 'Dakshita', 'Sharma', 'dakshita@gmail.com', '$2y$10$1/wjvfYDQ/eUZVibxKYpmulCu53zmjOCVTtzaH8k291PpFqH22ykS', 'Karwand naka', '7709154425', 'customer', '2025-12-26 11:23:01', NULL, NULL),
(5, 'Vedant', 'Patil', 'vedant@gmail.com', '$2y$10$kq60lesHG2sT2tsQtt9VQuKwQbeFfNyHD.uUFmfY/r0cYQsbjAw5q', 'Karwand', '7709154425', 'customer', '2025-12-27 09:28:15', NULL, NULL),
(7, 'Parth', 'Deshmukh', 'parth@gmail.com', '$2y$10$ILvJyDW99NJKymO4agMA0O/W3fFqJaIB3ES2ufbKiuQy7F44WWQIC', 'Karwand naka', '7709154425', 'provider', '2025-12-28 10:44:29', NULL, NULL),
(8, 'Harsh', 'Patil', 'harsh@gmail.com', '$2y$10$aSyyow126PKiKtCoolj21eX.sLqK/lJgUCLDmoy./RKapWL.ijymO', 'Nimzari Naka', '7709154425', 'provider', '2025-12-28 10:46:04', NULL, NULL),
(9, 'Vikas', 'More', 'Vikas@gmail.com', '$2y$10$CDiNnWw37V5Pg3VfL7Ley.bFwk6CdUg/IbXCno5/wnieOCCPW8jty', 'Bus Stand,Shirpur', '7709154425', 'customer', '2025-12-28 10:52:14', NULL, NULL),
(10, 'Vicky', 'Jain', 'Vicky@gmail.com', '$2y$10$EfP1O148hcaHQvewb7zmge2SjbfsJyJ7KqNKIEVcTycbGzt.wx8NC', 'Karwand naka', '7709154425', 'customer', '2025-12-28 16:04:15', NULL, NULL),
(11, 'Nikhil', 'Pawar', 'nikhil11@gmail.com', '$2y$10$hhxkRiSZdHHukQGThMlHxuRYPBxltHMvOdxHyeh.b2VTXe7MSEIwm', 'Karwand', '7709154234', 'customer', '2025-12-28 16:32:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('customer','provider') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
