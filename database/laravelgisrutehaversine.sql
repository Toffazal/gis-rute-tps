-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 16, 2025 at 06:56 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravelgisrutehaversine`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `odp`
--

DROP TABLE IF EXISTS `odp`;
CREATE TABLE IF NOT EXISTS `odp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` bigint UNSIGNED DEFAULT NULL COMMENT 'yang input',
  `kode` varchar(50) DEFAULT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `odp_ibfk_1` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `odp`
--

INSERT INTO `odp` (`id`, `user`, `kode`, `nama`, `alamat`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, 1, 'KODE001', 'ODP-PLW-FAE/157', 'palembang', '-3.409783', '119.310804', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(3, 1, 'KODE002', 'ODP-PLW-FC/08', 'palembang', '-3.408309', '119.307199', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(4, 1, 'KODE003', 'ODP-PLW-FC/030', 'palembang', '-3.407436', '119.303544', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(5, 1, 'KODE004', 'ODP-PLW-FA/042', 'palembang', '-3.40716', '119.302444', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(6, 1, 'KODE005', 'ODP-PLW-FAE/062', 'palembang', '-3.406503', '119.300759', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(7, 1, 'KODE006', 'ODP-PLW-FB/16', 'palembang', '-3.421382', '119.325774', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(8, 1, 'KODE007', 'ODP-PLW-FB/20', 'palembang', '-3.424501', '119.330358', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(9, 1, 'KODE008', 'ODP-PLW-FD/008', 'palembang', '-3.426163', '119.334381', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(10, 1, 'KODE009', 'ODP-PLW-FB/24', 'palembang', '-3.427221', '119.336228', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(11, 1, 'KODE010', 'ODP-PLW-FB/083', 'palembang', '-3.428449', '119.339135', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(12, 1, 'KODE011', 'ODP-PLW-FA/064', 'palembang', '-3.417145', '119.316051', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(13, 1, 'KODE012', 'ODP-PLW-FA/063', 'palembang', '-3.420236', '119.314421', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(14, 1, 'KODE013', 'ODP-PLW-FA/054', 'palembang', '-3.42091', '119.314076', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(15, 1, 'KODE014', 'ODP-PLW-FAE/131', 'palembang', '-3.423185', '119.313089', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(16, 1, 'KODE015', 'ODP-PLW-FA/073', 'palembang', '-3.424102', '119.312649', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(17, 1, 'KODE016', 'ODP-PLW-FD/006', 'palembang', '-3.411028', '119.319177', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(18, 1, 'KODE017', 'ODP-PLW-FA/061', 'palembang', '-3.409097', '119.32018', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(19, 1, 'KODE018', 'ODP-PLW-FAF/072', 'palembang', '-3.406769', '119.3214', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(20, 1, 'KODE019', 'ODP-PLW-FA/069', 'palembang', '-3.404267', '119.322913', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(21, 1, 'KODE020', 'ODP-PLW-FAF/037', 'palembang', '-3.402737', '119.32431', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(22, 2, 'B_7716', 'Tes ODP', 'Tokyo', '3.5818469276514', '98.603496551514', '2025-08-14 17:09:02', '2025-08-14 18:30:47'),
(23, 3, 'B_771623', 'Tes 2', 'Medan', '3.5763939245175', '98.687889575958', '2025-08-14 18:33:24', '2025-08-14 18:33:24'),
(24, 1, NULL, 'ODP-PLW-FAE/157', NULL, '-3.409783', '119.310804', '2025-08-16 13:31:04', '2025-08-16 13:31:09'),
(25, 1, NULL, 'ODP-PLW-FC/08', NULL, '-3.408309', '119.307199', '2025-08-16 13:31:15', '2025-08-16 13:31:18'),
(26, 1, NULL, 'ODP-PLW-FC/030', NULL, '-3.407436', '119.303544', '2025-08-16 13:31:21', '2025-08-16 13:31:24'),
(27, 1, NULL, 'ODP-PLW-FA/042', NULL, '-3.40716', '119.302444', NULL, NULL),
(28, 1, NULL, 'ODP-PLW-FAE/062', NULL, '-3.406503', '119.300759', NULL, NULL),
(29, 1, NULL, 'ODP-PLW-FB/16', NULL, '-3.421382', '119.325774', NULL, NULL),
(30, 1, NULL, 'ODP-PLW-FB/20', NULL, '-3.424501', '119.330358', NULL, NULL),
(31, 1, NULL, 'ODP-PLW-FD/008', NULL, '-3.426163', '119.334381', NULL, NULL),
(32, 1, NULL, 'ODP-PLW-FB/24', NULL, '-3.427221', '119.336228', NULL, NULL),
(33, 1, NULL, 'ODP-PLW-FB/083', NULL, '-3.428449', '119.339135', NULL, NULL),
(34, 1, NULL, 'ODP-PLW-FA/064', NULL, '-3.417145', '119.316051', NULL, NULL),
(35, 1, NULL, 'ODP-PLW-FA/063', NULL, '-3.420236', '119.314421', NULL, NULL),
(36, 1, NULL, 'ODP-PLW-FA/054', NULL, '-3.42091', '119.314076', NULL, NULL),
(37, 1, NULL, 'ODP-PLW-FAE/131', NULL, '-3.423185', '119.313089', NULL, NULL),
(38, 1, NULL, 'ODP-PLW-FA/073', NULL, '-3.424102', '119.312649', NULL, NULL),
(39, 1, NULL, 'ODP-PLW-FD/006', NULL, '-3.411028', '119.319177', NULL, NULL),
(40, 1, NULL, 'ODP-PLW-FA/061', NULL, '-3.409097', '119.32018', NULL, NULL),
(41, 1, NULL, 'ODP-PLW-FAF/072', NULL, '-3.406769', '119.3214', NULL, NULL),
(42, 1, NULL, 'ODP-PLW-FA/069', NULL, '-3.404267', '119.322913', NULL, NULL),
(43, 1, NULL, 'ODP-PLW-FAF/037', NULL, '-3.402737', '119.32431', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat`
--

DROP TABLE IF EXISTS `riwayat`;
CREATE TABLE IF NOT EXISTS `riwayat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` bigint UNSIGNED DEFAULT NULL,
  `latitudeawal` varchar(50) NOT NULL,
  `longitudeawal` varchar(50) NOT NULL,
  `odp` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `odp` (`odp`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `riwayat`
--

INSERT INTO `riwayat` (`id`, `user`, `latitudeawal`, `longitudeawal`, `odp`, `created_at`, `updated_at`) VALUES
(1, 2, '34346436', '434378543334', 5, '2025-08-14 16:48:58', '2025-08-14 16:48:59'),
(2, 2, '3.5782656', '98.6873856', 22, '2025-08-14 18:25:09', '2025-08-14 18:25:09'),
(3, 2, '3.5782656', '98.6873856', 22, '2025-08-14 18:25:33', '2025-08-14 18:25:33'),
(4, 3, '3.5782656', '98.6873856', 22, '2025-08-14 18:31:16', '2025-08-14 18:31:16'),
(5, 3, '3.5782656', '98.6873856', 23, '2025-08-14 19:36:26', '2025-08-14 19:36:26'),
(6, 1, '3.588096', '98.6742784', 23, '2025-08-16 13:45:23', '2025-08-16 13:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Fazal', 'admin', 'zetsu680@gmail.com', '2025-06-19 12:37:33', '$2y$12$CyYOaPgpU/QGm4H9VtNl1Olw0mcPiks9UffO6IfuBe09XysFVXAIG', 'user', NULL, '2025-06-19 12:37:34', '2025-08-14 12:51:28'),
(2, 'Gojo Satoru', 'fazal', 'fazal@email.com', NULL, '$2y$12$FR3LJm5xl9VeeKnTwVw6bepnOxJzTtXB5g6BZGKTTCmdDwJ/CLKwq', 'user', NULL, '2025-08-14 08:28:44', '2025-08-14 12:52:22'),
(3, 'Taufik', 'taufik', 'taufik@gmail.com', NULL, '$2y$12$ABLAbIFPgwZr5yh8pMUagOg5dEH4fzHvYDoK4ByRCrSSksdFBL7OK', 'user', NULL, '2025-08-14 11:30:21', '2025-08-14 12:53:32');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `odp`
--
ALTER TABLE `odp`
  ADD CONSTRAINT `odp_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `riwayat`
--
ALTER TABLE `riwayat`
  ADD CONSTRAINT `riwayat_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_ibfk_2` FOREIGN KEY (`odp`) REFERENCES `odp` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
