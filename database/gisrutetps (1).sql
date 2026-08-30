-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 10, 2026 at 01:45 PM
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
-- Database: `gisrutetps`
--

-- --------------------------------------------------------

--
-- Table structure for table `angkutan`
--

DROP TABLE IF EXISTS `angkutan`;
CREATE TABLE IF NOT EXISTS `angkutan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `angkutan`
--

INSERT INTO `angkutan` (`id`, `nama`, `nomor`, `created_at`, `updated_at`) VALUES
(1, 'Kendaraan A', 'BK 1234 A', '2026-08-04 22:35:30', '2026-08-04 22:36:15');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

DROP TABLE IF EXISTS `jadwal`;
CREATE TABLE IF NOT EXISTS `jadwal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `tps_id` int DEFAULT NULL,
  `angkutan_id` int DEFAULT NULL,
  `hari` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `angkutan_id` (`angkutan_id`),
  KEY `tps_id` (`tps_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `user_id`, `tps_id`, `angkutan_id`, `hari`, `created_at`, `updated_at`) VALUES
(2, 2, 14, 1, 'Senin', '2026-08-05 01:52:21', '2026-08-05 01:52:21'),
(3, 2, 43, 1, 'Minggu', '2026-08-09 15:35:18', '2026-08-09 15:35:18'),
(4, 2, 38, 1, 'Minggu', '2026-08-09 15:35:39', '2026-08-09 15:37:57'),
(5, 2, 41, 1, 'Minggu', '2026-08-09 15:36:00', '2026-08-09 15:36:00'),
(6, 2, 40, 1, 'Minggu', '2026-08-09 15:36:32', '2026-08-09 15:36:32');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matriks_jarak`
--

DROP TABLE IF EXISTS `matriks_jarak`;
CREATE TABLE IF NOT EXISTS `matriks_jarak` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asal_tipe` enum('kantor','tps','tpa') COLLATE utf8mb4_general_ci NOT NULL,
  `asal_id` int DEFAULT NULL,
  `tujuan_tipe` enum('kantor','tps','tpa') COLLATE utf8mb4_general_ci NOT NULL,
  `tujuan_id` int DEFAULT NULL,
  `jarak_meter` int NOT NULL,
  `durasi_detik` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `asal_id` (`asal_id`),
  KEY `tujuan_id` (`tujuan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat`
--

DROP TABLE IF EXISTS `riwayat`;
CREATE TABLE IF NOT EXISTS `riwayat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` bigint UNSIGNED DEFAULT NULL,
  `latitudeawal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `longitudeawal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `odp` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `odp` (`odp`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `rute_harian`
--

DROP TABLE IF EXISTS `rute_harian`;
CREATE TABLE IF NOT EXISTS `rute_harian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `angkutan_id` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('belum_mulai','berlangsung','selesai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'belum_mulai',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `total_jarak_meter` int DEFAULT NULL,
  `total_durasi_detik` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `angkutan_id` (`angkutan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rute_harian`
--

INSERT INTO `rute_harian` (`id`, `user_id`, `angkutan_id`, `tanggal`, `status`, `waktu_mulai`, `waktu_selesai`, `total_jarak_meter`, `total_durasi_detik`, `created_at`, `updated_at`) VALUES
(3, 2, 1, '2026-08-09', 'selesai', '2026-08-09 16:14:56', '2026-08-09 16:22:26', 185787, -451, '2026-08-09 16:14:20', '2026-08-09 16:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `rute_log`
--

DROP TABLE IF EXISTS `rute_log`;
CREATE TABLE IF NOT EXISTS `rute_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rute_harian_id` int DEFAULT NULL,
  `rute_tujuan_id` int DEFAULT NULL,
  `aksi` enum('mulai_rute','tiba','selesai','skip','tidak_dikunjungi','reorder_manual','reorder_otomatis') COLLATE utf8mb4_general_ci NOT NULL,
  `alasan` text COLLATE utf8mb4_general_ci,
  `urutan_snapshot` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rute_harian_id` (`rute_harian_id`),
  KEY `rute_tujuan_id` (`rute_tujuan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rute_log`
--

INSERT INTO `rute_log` (`id`, `rute_harian_id`, `rute_tujuan_id`, `aksi`, `alasan`, `urutan_snapshot`, `created_at`, `updated_at`) VALUES
(17, 3, 13, 'mulai_rute', NULL, NULL, '2026-08-09 16:14:56', '2026-08-09 16:14:56'),
(18, 3, 13, 'skip', 'perbaikan jalan', NULL, '2026-08-09 16:15:22', '2026-08-09 16:15:22'),
(19, 3, 18, 'skip', 'y', NULL, '2026-08-09 16:16:08', '2026-08-09 16:16:08'),
(20, 3, 19, 'skip', 'y', NULL, '2026-08-09 16:20:41', '2026-08-09 16:20:41'),
(21, 3, 14, 'tiba', NULL, NULL, '2026-08-09 16:21:30', '2026-08-09 16:21:30'),
(22, 3, 14, 'selesai', NULL, NULL, '2026-08-09 16:21:42', '2026-08-09 16:21:42'),
(23, 3, 15, 'tiba', NULL, NULL, '2026-08-09 16:21:50', '2026-08-09 16:21:50'),
(24, 3, 15, 'selesai', NULL, NULL, '2026-08-09 16:21:52', '2026-08-09 16:21:52'),
(25, 3, 16, 'tiba', NULL, NULL, '2026-08-09 16:21:55', '2026-08-09 16:21:55'),
(26, 3, 16, 'selesai', NULL, NULL, '2026-08-09 16:21:57', '2026-08-09 16:21:57'),
(27, 3, 20, 'tiba', NULL, NULL, '2026-08-09 16:22:04', '2026-08-09 16:22:04'),
(28, 3, 20, 'selesai', NULL, NULL, '2026-08-09 16:22:07', '2026-08-09 16:22:07'),
(29, 3, 17, 'tiba', NULL, NULL, '2026-08-09 16:22:23', '2026-08-09 16:22:23'),
(30, 3, 17, 'selesai', NULL, NULL, '2026-08-09 16:22:26', '2026-08-09 16:22:26'),
(31, 3, NULL, 'selesai', NULL, NULL, '2026-08-09 16:22:26', '2026-08-09 16:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `rute_tujuan`
--

DROP TABLE IF EXISTS `rute_tujuan`;
CREATE TABLE IF NOT EXISTS `rute_tujuan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rute_harian_id` int DEFAULT NULL,
  `tps_id` int DEFAULT NULL,
  `tipe` enum('tps','tpa') COLLATE utf8mb4_general_ci NOT NULL,
  `urutan` int NOT NULL,
  `status` enum('menunggu','aktif','selesai','dilewati','tidak_dikunjungi') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'menunggu',
  `alasan` text COLLATE utf8mb4_general_ci,
  `jarak_dari_sebelumnya_meter` int DEFAULT NULL,
  `durasi_dari_sebelumnya_detik` int DEFAULT NULL,
  `waktu_tiba` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rute_harian_id` (`rute_harian_id`),
  KEY `tps_id` (`tps_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rute_tujuan`
--

INSERT INTO `rute_tujuan` (`id`, `rute_harian_id`, `tps_id`, `tipe`, `urutan`, `status`, `alasan`, `jarak_dari_sebelumnya_meter`, `durasi_dari_sebelumnya_detik`, `waktu_tiba`, `waktu_selesai`, `created_at`, `updated_at`) VALUES
(13, 3, 43, 'tps', 1, 'dilewati', 'perbaikan jalan', 20818, NULL, NULL, NULL, '2026-08-09 16:14:20', '2026-08-09 16:15:22'),
(14, 3, 38, 'tps', 4, 'selesai', NULL, 51991, NULL, '2026-08-09 16:21:30', '2026-08-09 16:21:42', '2026-08-09 16:14:20', '2026-08-09 16:21:42'),
(15, 3, 41, 'tps', 5, 'selesai', NULL, 2310, NULL, '2026-08-09 16:21:50', '2026-08-09 16:21:52', '2026-08-09 16:14:20', '2026-08-09 16:21:52'),
(16, 3, 40, 'tps', 6, 'selesai', NULL, 716, NULL, '2026-08-09 16:21:55', '2026-08-09 16:21:57', '2026-08-09 16:14:20', '2026-08-09 16:21:57'),
(17, 3, NULL, 'tpa', 8, 'selesai', NULL, 65396, NULL, '2026-08-09 16:22:23', '2026-08-09 16:22:26', '2026-08-09 16:14:20', '2026-08-09 16:22:26'),
(18, 3, 43, 'tps', 2, 'dilewati', 'y', 20818, NULL, NULL, NULL, '2026-08-09 16:15:22', '2026-08-09 16:16:08'),
(19, 3, 43, 'tps', 3, 'dilewati', 'y', 20818, NULL, NULL, NULL, '2026-08-09 16:16:08', '2026-08-09 16:20:41'),
(20, 3, 43, 'tps', 7, 'selesai', NULL, 65374, NULL, '2026-08-09 16:22:04', '2026-08-09 16:22:07', '2026-08-09 16:20:41', '2026-08-09 16:22:07');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tps`
--

DROP TABLE IF EXISTS `tps`;
CREATE TABLE IF NOT EXISTS `tps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'yang input',
  `nama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `longitude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `odp_ibfk_1` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tps`
--

INSERT INTO `tps` (`id`, `user_id`, `nama`, `alamat`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, 1, 'ODP-PLW-FAE/157', 'palembang', '-3.409783', '119.310804', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(3, 1, 'ODP-PLW-FC/08', 'palembang', '-3.408309', '119.307199', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(4, 1, 'ODP-PLW-FC/030', 'palembang', '-3.407436', '119.303544', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(5, 1, 'ODP-PLW-FA/042', 'palembang', '-3.40716', '119.302444', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(6, 1, 'ODP-PLW-FAE/062', 'palembang', '-3.406503', '119.300759', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(7, 1, 'ODP-PLW-FB/16', 'palembang', '-3.421382', '119.325774', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(8, 1, 'ODP-PLW-FB/20', 'palembang', '-3.424501', '119.330358', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(9, 1, 'ODP-PLW-FD/008', 'palembang', '-3.426163', '119.334381', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(10, 1, 'ODP-PLW-FB/24', 'palembang', '-3.427221', '119.336228', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(11, 1, 'ODP-PLW-FB/083', 'palembang', '-3.428449', '119.339135', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(12, 1, 'ODP-PLW-FA/064', 'palembang', '-3.417145', '119.316051', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(13, 1, 'ODP-PLW-FA/063', 'palembang', '-3.420236', '119.314421', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(14, 1, 'ODP-PLW-FA/054', 'palembang', '-3.42091', '119.314076', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(15, 1, 'ODP-PLW-FAE/131', 'palembang', '-3.423185', '119.313089', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(16, 1, 'ODP-PLW-FA/073', 'palembang', '-3.424102', '119.312649', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(17, 1, 'ODP-PLW-FD/006', 'palembang', '-3.411028', '119.319177', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(18, 1, 'ODP-PLW-FA/061', 'palembang', '-3.409097', '119.32018', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(19, 1, 'ODP-PLW-FAF/072', 'palembang', '-3.406769', '119.3214', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(20, 1, 'ODP-PLW-FA/069', 'palembang', '-3.404267', '119.322913', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(21, 1, 'ODP-PLW-FAF/037', 'palembang', '-3.402737', '119.32431', '2025-08-14 16:13:13', '2025-08-14 16:13:13'),
(22, 2, 'Tes ODP', 'Tokyo', '3.5818469276514', '98.603496551514', '2025-08-14 17:09:02', '2025-08-14 18:30:47'),
(23, 3, 'Tes 2', 'Medan', '3.5763939245175', '98.687889575958', '2025-08-14 18:33:24', '2025-08-14 18:33:24'),
(24, 1, 'ODP-PLW-FAE/157', NULL, '-3.409783', '119.310804', '2025-08-16 13:31:04', '2025-08-16 13:31:09'),
(25, 1, 'ODP-PLW-FC/08', NULL, '-3.408309', '119.307199', '2025-08-16 13:31:15', '2025-08-16 13:31:18'),
(26, 1, 'ODP-PLW-FC/030', NULL, '-3.407436', '119.303544', '2025-08-16 13:31:21', '2025-08-16 13:31:24'),
(27, 1, 'ODP-PLW-FA/042', NULL, '-3.40716', '119.302444', NULL, NULL),
(28, 1, 'ODP-PLW-FAE/062', NULL, '-3.406503', '119.300759', NULL, NULL),
(29, 1, 'ODP-PLW-FB/16', NULL, '-3.421382', '119.325774', NULL, NULL),
(30, 1, 'ODP-PLW-FB/20', NULL, '-3.424501', '119.330358', NULL, NULL),
(31, 1, 'ODP-PLW-FD/008', NULL, '-3.426163', '119.334381', NULL, NULL),
(32, 1, 'ODP-PLW-FB/24', NULL, '-3.427221', '119.336228', NULL, NULL),
(33, 1, 'ODP-PLW-FB/083', NULL, '-3.428449', '119.339135', NULL, NULL),
(34, 1, 'ODP-PLW-FA/064', '-', '3.9982100350308', '98.306179046631', NULL, '2026-08-09 15:34:23'),
(35, 1, 'ODP-PLW-FA/063', '-', '4.0113100650655', '98.298110961914', NULL, '2026-08-09 15:34:13'),
(36, 1, 'ODP-PLW-FA/054', '-', '4.0095976547574', '98.273134231567', NULL, '2026-08-09 15:33:37'),
(37, 1, 'ODP-PLW-FAE/131', '-', '4.0104939949756', '98.280880451202', NULL, '2026-08-09 15:34:03'),
(38, 1, 'ODP-PLW-FA/073', '-', '4.0234680749643', '98.304634094238', NULL, '2026-08-09 15:33:25'),
(39, 1, 'ODP-PLW-FD/006', '-', '4.0168084832685', '98.280773162842', NULL, '2026-08-09 15:33:15'),
(40, 1, 'ODP-PLW-FA/061', '-', '4.0110077178813', '98.280687332153', NULL, '2026-08-09 15:32:59'),
(41, 1, 'ODP-PLW-FAF/072', '-', '4.0124632643602', '98.286974430084', NULL, '2026-08-09 15:32:47'),
(42, 1, 'ODP-PLW-FA/069', '-', '4.0124632643602', '98.28733921051', NULL, '2026-08-09 15:32:38'),
(43, 1, 'ODP-PLW-FAF/037', '-', '3.5736767802605', '98.674478530884', NULL, '2026-08-09 15:32:24');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Fazal', 'admin', 'zetsu680@gmail.com', '2025-06-19 12:37:33', '$2y$12$CyYOaPgpU/QGm4H9VtNl1Olw0mcPiks9UffO6IfuBe09XysFVXAIG', 'admin', 'vwTWGd9lBPsjpk0VQkDxW85n9AcEBHFv3OryYevMNtQo1bCsJQWMjVbR7QBh', '2025-06-19 12:37:34', '2025-08-14 12:51:28'),
(2, 'Gojo Satoru', 'fazal', 'fazal@email.com', NULL, '$2y$12$FR3LJm5xl9VeeKnTwVw6bepnOxJzTtXB5g6BZGKTTCmdDwJ/CLKwq', 'sopir', NULL, '2025-08-14 08:28:44', '2025-08-14 12:52:22'),
(3, 'Taufik', 'taufik', 'taufik@gmail.com', NULL, '$2y$12$ABLAbIFPgwZr5yh8pMUagOg5dEH4fzHvYDoK4ByRCrSSksdFBL7OK', 'sopir', NULL, '2025-08-14 11:30:21', '2025-08-14 12:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `website`
--

DROP TABLE IF EXISTS `website`;
CREATE TABLE IF NOT EXISTS `website` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `alamat` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `koordinat_tpa` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `koordinat_kantor` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website`
--

INSERT INTO `website` (`id`, `nama`, `deskripsi`, `alamat`, `telepon`, `email`, `logo`, `koordinat_tpa`, `koordinat_kantor`, `created_at`, `updated_at`) VALUES
(1, 'GIS Rute TPS', 'Implementasi Algoritma \"Ant Colony Optimization\" dalam menentukan Rute terpendek di Dinas Lingkungan Hidup Kota Binjai ke TPA.', 'Jl. Sibolga No.5, Rambung Bar., Kec. Binjai Sel., Kota Binjai, Sumatera Utara 20735', '0800 44462', 'dlh.kotabinjai@gmail.com', 'logo.png', '[4.0140220, 98.2837680]', '[3.593147, 98.4879093]', '2026-08-04 16:26:09', '2026-08-05 00:35:30');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`angkutan_id`) REFERENCES `angkutan` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`tps_id`) REFERENCES `tps` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  ADD CONSTRAINT `jadwal_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `matriks_jarak`
--
ALTER TABLE `matriks_jarak`
  ADD CONSTRAINT `matriks_jarak_ibfk_1` FOREIGN KEY (`asal_id`) REFERENCES `tps` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  ADD CONSTRAINT `matriks_jarak_ibfk_2` FOREIGN KEY (`tujuan_id`) REFERENCES `tps` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `riwayat`
--
ALTER TABLE `riwayat`
  ADD CONSTRAINT `riwayat_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_ibfk_2` FOREIGN KEY (`odp`) REFERENCES `tps` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rute_harian`
--
ALTER TABLE `rute_harian`
  ADD CONSTRAINT `rute_harian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `rute_harian_ibfk_2` FOREIGN KEY (`angkutan_id`) REFERENCES `angkutan` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `rute_log`
--
ALTER TABLE `rute_log`
  ADD CONSTRAINT `rute_log_ibfk_1` FOREIGN KEY (`rute_harian_id`) REFERENCES `rute_harian` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `rute_log_ibfk_2` FOREIGN KEY (`rute_tujuan_id`) REFERENCES `rute_tujuan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `rute_tujuan`
--
ALTER TABLE `rute_tujuan`
  ADD CONSTRAINT `rute_tujuan_ibfk_1` FOREIGN KEY (`rute_harian_id`) REFERENCES `rute_harian` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `rute_tujuan_ibfk_2` FOREIGN KEY (`tps_id`) REFERENCES `tps` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `tps`
--
ALTER TABLE `tps`
  ADD CONSTRAINT `tps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
