-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 08:10 PM
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
-- Database: `school_journal`
--
CREATE DATABASE IF NOT EXISTS `school_journal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `school_journal`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','editor','moderator') DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `role`, `created_at`, `profile_pic`) VALUES
(1, 'AdminViana', 'rapviana2005@gmail.com', '$2y$10$vQ9WJOCTZpD5AytN.b2hk.KqAyrAv2rAvHnEazuOynTDzm/tn3BgK', 'superadmin', '2025-01-05 12:01:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `timestamp`, `admin_username`) VALUES
(1, NULL, 'Excluiu o admin \'AdminLuisSantos\' (ID: 4)', '2025-01-05 14:06:17', NULL),
(2, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-05 15:33:55', NULL),
(3, NULL, 'Alterou permissões do admin \'AdminLuisSantos\' (ID: 5) para \'superadmin\'', '2025-01-05 15:33:57', NULL),
(4, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-05 17:58:04', NULL),
(5, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-05 17:58:21', NULL),
(6, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-05 21:28:23', NULL),
(7, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-05 21:28:35', NULL),
(8, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-06 10:01:45', NULL),
(9, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-10 12:00:51', NULL),
(10, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-28 16:46:49', NULL),
(11, NULL, 'Acessou edição de permissões do admin \'AdminLuisSantos\' (ID: 5)', '2025-01-28 16:48:33', NULL),
(12, NULL, 'Excluiu o admin \'AdminLuisSantos\' (ID: 5)', '2025-01-28 16:50:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE `failed_logins` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `additional_images` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `main_image`, `additional_images`, `created_at`) VALUES
(1, 'A vida do viana', 'Um dia o jacare decidiu mudar de cidade porque ele era bla bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 'uploads/LOU_0002.jpg', '[\"uploads\\/LOU_0419.jpg\",\"uploads\\/LOU_3930.jpg\",\"uploads\\/vinhas.jpg\"]', '2025-01-05 00:41:54'),
(10, 'vdsvhudsvvuvndsun', 'fnfnj', 'uploads/durante-stalinismo.jpg', '[]', '2025-01-10 12:01:10'),
(11, 'olaolaola', 'fds', 'uploads/vecteezy_google-mail-icons_17396757.png', '[]', '2025-01-12 23:03:58'),
(13, 'aaa', 'aaa', 'uploads/Viana-avatar.png', '[]', '2025-02-03 11:13:30'),
(14, 'qeqw', 'ew', 'uploads/Viana-avatar.png', '[]', '2025-02-04 14:37:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `fk_admin_logs_username` (`admin_username`);

--
-- Indexes for table `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_admin_logs_username` FOREIGN KEY (`admin_username`) REFERENCES `admins` (`username`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
