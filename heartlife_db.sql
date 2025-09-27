-- SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 06:51 AM
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
-- Database: `heartlife_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bmi_logs`
--

CREATE TABLE `bmi_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `height_cm` decimal(5,2) NOT NULL,
  `bmi_value` decimal(4,2) NOT NULL,
  `log_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bmi_logs`
--

INSERT INTO `bmi_logs` (`id`, `user_id`, `weight_kg`, `height_cm`, `bmi_value`, `log_date`, `created_at`) VALUES
(11, 5, 50.00, 170.00, 17.30, '2025-09-23', '2025-09-23 11:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `checklist_items`
--

CREATE TABLE `checklist_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_text` varchar(255) NOT NULL,
  `type` enum('morning','night') NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `completion_status` enum('pending','completed','missed') NOT NULL DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `alt_text` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'inspirational',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `url`, `alt_text`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'assets\\images\\dailyImages\\1.jpg', '', 'inspirational', 1, '2025-09-23 10:16:07', '2025-09-23 10:16:28'),
(2, 'assets\\images\\dailyImages\\2.jpg', '', 'inspirational', 1, '2025-09-23 10:16:55', '2025-09-23 10:16:55'),
(3, 'assets\\images\\dailyImages\\3.jpg', '', 'inspirational', 1, '2025-09-23 10:16:55', '2025-09-23 10:16:55'),
(4, 'assets\\images\\dailyImages\\4.jpg', '', 'inspirational', 1, '2025-09-23 10:17:09', '2025-09-23 10:17:09'),
(5, 'assets\\images\\dailyImages\\5.jpg', '', 'inspirational', 1, '2025-09-23 10:17:09', '2025-09-23 10:17:09'),
(6, 'assets\\images\\dailyImages\\6.jpg', '', 'inspirational', 1, '2025-09-23 10:17:17', '2025-09-23 10:17:17');

-- --------------------------------------------------------

--
-- Table structure for table `mood_logs`
--

CREATE TABLE `mood_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mood` varchar(50) NOT NULL,
  `log_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mood_logs`
--

INSERT INTO `mood_logs` (`id`, `user_id`, `mood`, `log_date`, `notes`, `created_at`) VALUES
(8, 1, 'Nonchalant', '2025-08-11', NULL, '2025-08-11 09:23:42'),
(11, 4, 'Angry', '2025-09-17', NULL, '2025-09-18 13:29:28'),
(13, 5, 'Angry', '2025-09-17', NULL, '2025-09-23 11:14:17'),
(14, 5, 'Fear', '2025-09-18', NULL, '2025-09-23 12:04:48'),
(15, 5, 'Lovely', '2025-09-19', NULL, '2025-09-23 12:05:08'),
(16, 5, 'Joy', '2025-09-20', NULL, '2025-09-23 12:05:31'),
(17, 5, 'Lovely', '2025-09-21', NULL, '2025-09-23 12:05:56'),
(18, 5, 'Joy', '2025-09-22', NULL, '2025-09-23 12:06:17'),
(19, 5, 'Joy', '2025-09-23', NULL, '2025-09-23 12:06:39'),
(20, 5, 'Trust', '2025-09-24', NULL, '2025-09-24 15:09:54'),
(21, 5, 'Angry', '2025-09-25', NULL, '2025-09-25 03:39:31');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `status` enum('active','completed','pinned') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Aute dolor eligendi ', 'Aliquip facilis mole', 'pinned', '2025-08-11 09:12:45', '2025-08-11 09:14:33'),
(3, 1, 'Culpa obcaecati qua', 'Dolor animi harum c', 'completed', '2025-08-11 09:13:28', '2025-08-11 09:14:23'),
(4, 1, 'Voluptatem a quos o', 'Officia id officia s', 'pinned', '2025-08-11 09:13:57', '2025-08-11 09:14:13'),
(6, 5, 'Something', 'Load', 'completed', '2025-09-22 11:24:14', '2025-09-25 01:57:15'),
(7, 5, 'Exercitationem paria', 'Odio temporibus aut ', 'pinned', '2025-09-23 11:38:21', '2025-09-25 01:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE `quotes` (
  `id` int(11) NOT NULL,
  `quote` text NOT NULL,
  `author` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`id`, `quote`, `author`) VALUES
(1, 'The secret of getting ahead is getting started.', 'Mark Twain'),
(2, 'The only way to do great work is to love what you do.', 'Steve Jobs'),
(3, 'Believe you can and you\'re halfway there.', 'Theodore Roosevelt'),
(4, 'Your time is limited, don\'t waste it living someone else\'s life.', 'Steve Jobs'),
(5, 'Strive not to be a success, but rather to be of value.', 'Albert Einstein');

-- --------------------------------------------------------

--
-- Table structure for table `sleep_logs`
--

CREATE TABLE `sleep_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hours` decimal(4,2) NOT NULL,
  `log_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sleep_logs`
--

INSERT INTO `sleep_logs` (`id`, `user_id`, `hours`, `log_date`, `created_at`) VALUES
(1, 1, 7.50, '2025-08-11', '2025-08-11 08:05:20'),
(2, 5, 8.00, '2025-09-24', '2025-09-24 16:04:14'),
(3, 5, 6.00, '2025-09-25', '2025-09-25 00:56:11'),
(4, 6, 8.00, '2025-09-25', '2025-09-25 04:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `date_of_birth`, `profile_pic`, `password_hash`, `created_at`) VALUES
(1, 'zafejawag', 'fulofadoj@treisadiutor.com', NULL, '', '$2y$10$dLvfBZfZdUUmul.ivWfNa.jxn4VKtXAMCCv9RIi/LGox/YumvNt6G', '2025-08-11 08:00:04'),
(2, 'tiqinevu', 'hyhykyzyle@treisadiutor.com', NULL, '', '$2y$10$ogV5NUcRNFAoz7C/P99P9eU8Q2FWxPB/qsh2QZkrvdmDM0lfivhZ2', '2025-08-11 08:00:16'),
(3, 'xigixavowa', 'copuq@treisadiutor.com', NULL, '', '$2y$10$e2lvJFBY.aeYdTxztmVgBOU/eWOiCVKCsV3uglAAHTFvaCARmGdPO', '2025-08-11 08:01:07'),
(4, 'krivero', 'kylarivero@treisadiutor.com', NULL, '', '$2y$10$Oxke2yi.2dBVOXZbwRAU6eJaMQklO6/.OWQ4rHxsJVA8AQydjlQxa', '2025-09-18 13:16:15'),
(5, 'kylarivero', 'kyla@treisadiutor.com', NULL, 'assets/images/profile/user_5_1758625440.png', '$2y$10$DRoNB4yH626.SbCR8tX9g.8gNXmYWYAhHfJ84Sr6BnLSKWDLRkpMe', '2025-09-23 10:07:35'),
(6, 'suditasa', 'zyrigoxy@treisadiutor.com', '1973-10-17', '', '$2y$10$hBRKdzkdHWsOqEGSG58PyOU7sJb2bggZvlN/GhYdegn5YJR/Im71a', '2025-09-25 04:00:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bmi_logs`
--
ALTER TABLE `bmi_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `checklist_items`
--
ALTER TABLE `checklist_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_user_due_date` (`user_id`,`due_date`),
  ADD KEY `idx_completion_status` (`completion_status`),
  ADD KEY `idx_user_status_due` (`user_id`,`completion_status`,`due_date`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mood_logs`
--
ALTER TABLE `mood_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_day_unique` (`user_id`,`log_date`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sleep_logs`
--
ALTER TABLE `sleep_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bmi_logs`
--
ALTER TABLE `bmi_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `checklist_items`
--
ALTER TABLE `checklist_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mood_logs`
--
ALTER TABLE `mood_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sleep_logs`
--
ALTER TABLE `sleep_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bmi_logs`
--
ALTER TABLE `bmi_logs`
  ADD CONSTRAINT `bmi_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `checklist_items`
--
ALTER TABLE `checklist_items`
  ADD CONSTRAINT `checklist_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mood_logs`
--
ALTER TABLE `mood_logs`
  ADD CONSTRAINT `mood_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sleep_logs`
--
ALTER TABLE `sleep_logs`
  ADD CONSTRAINT `sleep_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
