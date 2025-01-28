-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2024 at 07:26 AM
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
-- Database: `dost_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `username` varchar(55) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `username`, `item`, `date`, `action`) VALUES
(1, 'yvanmaribbay1218', 'Water', '2024-07-11 01:52:02', 'Added'),
(2, 'yvanmaribbay1218', 'Water', '2024-07-11 01:59:42', 'Added'),
(3, 'yvanmaribbay1218', 'Water', '2024-07-11 02:03:06', 'Edited'),
(4, 'yvanmaribbay1218', 'Latex Gloves', '2024-07-11 02:04:43', 'Added'),
(5, 'yvanmaribbay1218', 'Plant Extract', '2024-07-11 02:05:46', 'Added'),
(6, 'yvanmaribbay1218', 'Water', '2024-07-11 08:47:54', 'Edited'),
(7, 'yvanmaribbay1218', 'Water', '2024-07-11 08:48:31', 'Edited'),
(8, 'yvanmaribbay1218', 'Water', '2024-07-11 08:53:30', 'Edited'),
(9, 'yvanmaribbay1218', 'Water', '2024-07-11 08:53:49', 'Edited'),
(10, 'yvanmaribbay1218', 'Sample', '2024-07-13 12:27:51', 'Added'),
(11, 'yvanmaribbay1218', 'Culture Media', '2024-07-15 08:03:37', 'Added'),
(12, 'yvanmaribbay1218', 'Sample', '2024-07-30 03:05:56', 'Edited'),
(13, 'yvanmaribbay1218', 'Culture Media', '2024-08-05 02:08:13', 'Edited'),
(14, 'yvanmaribbay1218', 'subok lang', '2024-08-06 08:34:20', 'Added'),
(15, 'yvanmaribbay1218', 'subok lang', '2024-08-06 08:34:45', 'Deleted'),
(16, 'yvanmaribbay1218', NULL, '2024-08-06 08:35:08', 'Deleted'),
(17, 'yvanmaribbay1218', 'Trial ulit', '2024-08-06 08:35:36', 'Added'),
(18, 'yvanmaribbay1218', 'Trial ulit', '2024-08-06 08:35:40', 'Deleted'),
(19, 'yvan_admin', 'Acetic Acid', '2024-08-09 07:43:28', 'Added'),
(20, 'Kath', 'Drugs', '2024-08-09 13:37:15', 'Added'),
(21, 'yvan_admin', 'Drugs', '2024-08-11 13:08:46', 'Deleted'),
(22, 'Admin', 'Acetic Acid', '2024-08-12 01:50:28', 'Edited'),
(23, 'Trial', 'Acetic Acid', '2024-08-12 02:33:40', 'Edited'),
(24, 'yvan_admin', 'Sample', '2024-08-12 02:47:27', 'Added');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `batch_number` varchar(55) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `unit_measurement` varchar(50) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `minimum_stock` int(11) DEFAULT NULL,
  `used_stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `lab_id`, `item_id`, `batch_number`, `exp_date`, `unit_measurement`, `stock`, `minimum_stock`, `used_stock`, `created_at`, `updated_at`) VALUES
(68, 20, 8, 'trial', '2024-08-23', '10ml', 0, 0, 82, '2024-08-12 02:47:52', '2024-08-13 05:25:04'),
(69, 20, 8, 'Sample2', '2024-08-15', '10ml', 0, 0, 140, '2024-08-12 06:59:56', '2024-08-13 05:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `laboratory` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` enum('Added','Edited','Deleted') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`log_id`, `username`, `laboratory`, `item`, `batch_number`, `date`, `action`) VALUES
(1, 'yvanmaribbay1218', '3', '3', 'Trial Only', '2024-08-05 06:34:07', 'Edited'),
(2, 'yvanmaribbay1218', '3', '3', 'Trial Only', '2024-08-05 06:34:14', 'Deleted'),
(3, 'yvanmaribbay1218', '4', '3', 'Trial', '2024-08-05 06:34:30', 'Added'),
(4, 'yvanmaribbay1218', '4', '3', 'Trial', '2024-08-05 06:38:21', 'Edited'),
(5, 'yvanmaribbay1218', '3', '4', 'Trial pa', '2024-08-05 07:10:11', 'Added'),
(6, 'yvanmaribbay1218', '3', '4', 'Trial pa', '2024-08-05 07:10:41', 'Deleted'),
(7, 'yvanmaribbay1218', '4', '3', 'Trial', '2024-08-05 07:10:43', 'Deleted'),
(8, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-05 07:34:16', 'Edited'),
(9, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-06 05:19:34', 'Edited'),
(10, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-06 05:19:50', 'Edited'),
(11, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-06 05:19:57', 'Edited'),
(12, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-06 05:20:04', 'Edited'),
(13, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-06 08:47:29', 'Edited'),
(14, 'yvanmaribbay1218', '1', '4', 'trial', '2024-08-07 00:52:04', 'Added'),
(15, 'yvanmaribbay1218', '1', '4', 'trial', '2024-08-07 00:52:38', 'Deleted'),
(16, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-07 01:41:33', 'Edited'),
(17, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-07 01:41:44', 'Edited'),
(18, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 01:41:58', 'Edited'),
(19, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 01:48:27', 'Deleted'),
(20, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-07 01:48:30', 'Deleted'),
(21, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-07 01:48:33', 'Deleted'),
(22, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 01:48:53', 'Added'),
(23, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-07 01:49:15', 'Added'),
(24, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-07 01:49:37', 'Added'),
(25, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 01:55:05', 'Edited'),
(26, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 02:03:37', 'Edited'),
(27, 'yvanmaribbay1218', '1', '2', 'Sample 4', '2024-08-07 02:06:47', 'Added'),
(28, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 02:11:06', 'Edited'),
(29, 'yvanmaribbay1218', '4', '4', 'Sample 2', '2024-08-07 02:11:11', 'Edited'),
(30, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-07 02:11:15', 'Edited'),
(31, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 02:22:16', 'Edited'),
(32, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-07 02:37:25', 'Edited'),
(33, 'yvanmaribbay1218', '1', '2', 'Sample 4', '2024-08-08 01:47:37', 'Edited'),
(34, 'yvanmaribbay1218', '1', '2', 'Sample 4', '2024-08-08 01:48:46', 'Edited'),
(35, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-08 14:31:18', 'Edited'),
(36, 'yvanmaribbay1218', '4', '4', 'Sample 3', '2024-08-09 02:08:24', 'Edited'),
(37, 'yvanmaribbay1218', '1', '2', 'Sample 4', '2024-08-09 03:30:25', 'Edited'),
(38, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-09 04:02:51', 'Edited'),
(39, 'yvanmaribbay1218', '4', '4', 'Sample 1', '2024-08-09 04:03:53', 'Edited'),
(40, 'yvanmaribbay1218', '4', '3', 'Trial 2', '2024-08-09 05:10:45', 'Added'),
(41, 'Kath', '20', '9', '1', '2024-08-09 13:38:44', 'Added'),
(42, 'yvan_admin', '20', '9', '1', '2024-08-09 14:27:19', 'Deleted'),
(43, 'yvan_admin', '20', '8', 'trial', '2024-08-12 02:47:52', 'Added'),
(44, 'yvan_admin', '20', '8', 'trial', '2024-08-12 02:48:03', 'Edited'),
(45, 'yvan_admin', '20', '8', 'trial', '2024-08-12 02:48:28', 'Edited'),
(46, 'yvan_admin', '20', '8', 'trial', '2024-08-12 02:51:52', 'Edited'),
(47, 'yvan_admin', '20', '8', 'trial', '2024-08-12 06:10:00', 'Edited'),
(48, 'yvan_admin', '20', '8', 'trial', '2024-08-12 06:51:05', 'Edited'),
(49, 'yvan_admin', '20', '8', 'trial', '2024-08-12 06:58:02', 'Edited'),
(50, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 06:59:56', 'Added'),
(51, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:00:44', 'Edited'),
(52, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:00:50', 'Edited'),
(53, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:11:51', 'Edited'),
(54, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:11:57', 'Edited'),
(55, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:21:04', 'Edited'),
(56, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:21:11', 'Edited'),
(57, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:22:01', 'Edited'),
(58, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:27:51', 'Edited'),
(59, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:27:59', 'Edited'),
(60, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:29:01', 'Edited'),
(61, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:29:04', 'Edited'),
(62, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:29:41', 'Edited'),
(63, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:35:10', 'Edited'),
(64, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:35:13', 'Edited'),
(65, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:35:26', 'Edited'),
(66, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:35:29', 'Edited'),
(67, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:38:10', 'Edited'),
(68, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:38:13', 'Edited'),
(69, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:50:21', 'Edited'),
(70, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:51:33', 'Edited'),
(71, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:51:37', 'Edited'),
(72, 'yvan_admin', '20', '8', 'Sample2', '2024-08-12 07:56:16', 'Edited'),
(73, 'yvan_admin', '20', '8', 'trial', '2024-08-12 07:56:20', 'Edited'),
(74, 'yvan_admin', '21', '10', 'try', '2024-08-12 07:57:13', 'Added'),
(75, 'yvan_admin', '21', '10', 'try', '2024-08-12 07:57:24', 'Deleted'),
(76, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 01:12:50', 'Edited'),
(77, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 01:52:00', 'Edited'),
(78, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 01:56:57', 'Edited'),
(79, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 01:57:14', 'Edited'),
(80, 'yvan_admin', '20', '8', 'trial', '2024-08-13 01:57:17', 'Edited'),
(81, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:09:16', 'Edited'),
(82, 'yvan_admin', '20', '8', 'trial', '2024-08-13 02:09:20', 'Edited'),
(83, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:24:32', 'Edited'),
(84, 'yvan_admin', '20', '8', 'trial', '2024-08-13 02:24:36', 'Edited'),
(85, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:33:20', 'Edited'),
(86, 'yvan_admin', '20', '8', 'trial', '2024-08-13 02:33:24', 'Edited'),
(87, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:39:08', 'Edited'),
(88, 'yvan_admin', '20', '8', 'trial', '2024-08-13 02:39:12', 'Edited'),
(89, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:52:31', 'Edited'),
(90, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:52:59', 'Edited'),
(91, 'yvan_admin', '20', '8', 'trial', '2024-08-13 02:53:04', 'Edited'),
(92, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 02:54:51', 'Edited'),
(93, 'yvan_admin', '20', '8', 'trial', '2024-08-13 02:54:55', 'Edited'),
(94, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:09:41', 'Edited'),
(95, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:09:59', 'Edited'),
(96, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:10:02', 'Edited'),
(97, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:15:10', 'Edited'),
(98, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:15:14', 'Edited'),
(99, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:20:01', 'Edited'),
(100, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:24:31', 'Edited'),
(101, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:24:35', 'Edited'),
(102, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:25:41', 'Edited'),
(103, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:25:45', 'Edited'),
(104, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:26:06', 'Edited'),
(105, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:26:12', 'Edited'),
(106, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:33:08', 'Edited'),
(107, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:33:12', 'Edited'),
(108, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:43:35', 'Edited'),
(109, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:43:39', 'Edited'),
(110, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:47:29', 'Edited'),
(111, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:51:06', 'Edited'),
(112, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:51:10', 'Edited'),
(113, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:56:00', 'Edited'),
(114, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:56:35', 'Edited'),
(115, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:56:39', 'Edited'),
(116, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:57:23', 'Edited'),
(117, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 03:57:33', 'Edited'),
(118, 'yvan_admin', '20', '8', 'trial', '2024-08-13 03:57:46', 'Edited'),
(119, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 04:05:43', 'Edited'),
(120, 'yvan_admin', '20', '8', 'trial', '2024-08-13 04:05:47', 'Edited'),
(121, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 05:07:34', 'Edited'),
(122, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 05:13:43', 'Edited'),
(123, 'yvan_admin', '20', '8', 'trial', '2024-08-13 05:13:47', 'Edited'),
(124, 'yvan_admin', '20', '8', 'Sample2', '2024-08-13 05:20:21', 'Edited');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` enum('Chemical/Reagent','Glassware','Equipment','Consumable','Culture Media') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `unit_measurement` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `category`, `description`, `unit_measurement`, `created_at`, `updated_at`) VALUES
(8, 'Acetic Acid', 'Chemical/Reagent', 'It is an organic acid', '10ml', '2024-08-09 07:43:28', '2024-08-12 02:33:40'),
(10, 'Sample', 'Equipment', 'Sample', '10g', '2024-08-12 02:47:27', '2024-08-12 02:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `laboratories`
--

CREATE TABLE `laboratories` (
  `lab_id` int(11) NOT NULL,
  `lab_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laboratories`
--

INSERT INTO `laboratories` (`lab_id`, `lab_name`, `description`, `location`, `contact_info`, `created_at`, `updated_at`) VALUES
(19, 'Microbiology', 'Receives samples from patients to identify organisms that are responsible for infection including bacteria, fungi and parasites', 'N/A', 'N/A', '2024-08-09 07:39:23', '2024-08-09 07:39:23'),
(20, 'Chemical', 'Has all the necessary equipment for preparation of samples and standards for various analyses of liquid and solid samples', 'N/A', 'N/A', '2024-08-09 07:40:14', '2024-08-09 07:40:19'),
(21, 'Metrology', 'Scientific study of measurement with the theoretical and practical aspects of measurements units and standards', 'N/A', 'N/A', '2024-08-09 07:41:01', '2024-08-12 01:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `laboratory` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `total_used_stock` int(11) NOT NULL,
  `batch_details` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `user`, `laboratory`, `item`, `total_used_stock`, `batch_details`, `created_at`, `remarks`) VALUES
(32, 'yvan_admin', 'Chemical', 'Acetic Acid', 20, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-12 07:38:06', ''),
(33, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"}]', '2024-08-12 07:50:16', 'Used'),
(34, 'yvan_admin', 'Chemical', 'Acetic Acid', 20, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-12 07:56:11', ''),
(35, 'yvan_admin', 'Chemical', 'Acetic Acid', 20, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 02:09:02', ''),
(36, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 02:33:03', 'Used'),
(37, 'yvan_admin', 'Chemical', 'Acetic Acid', 5, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"}]', '2024-08-13 02:33:37', 'Broken Glass\n'),
(38, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"}]', '2024-08-13 02:52:22', 'Used'),
(39, 'yvan_admin', 'Chemical', 'Acetic Acid', 12, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"2\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 02:52:42', 'Used'),
(40, 'User', 'Laboratory', 'Item', 0, '[{\"batch_number\":\"BatchNumber\",\"used_stock\":\"UsedStock\"}]', '2024-08-13 02:54:37', 'broken'),
(41, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 03:09:25', 'broken'),
(42, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 03:09:48', 'broken'),
(43, 'yvan_admin', 'Chemical', 'Acetic Acid', 5, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"}]', '2024-08-13 03:13:28', 'trial remarks'),
(44, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 03:13:52', 'double batch remarks'),
(45, 'yvan_admin', 'Chemical', 'Acetic Acid', 5, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"}]', '2024-08-13 03:15:24', 'one batch\n'),
(46, 'yvan_admin', 'Chemical', 'Acetic Acid', 25, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"15\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 03:16:01', 'Double trial'),
(47, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 03:20:19', '15 batch\n\n'),
(48, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 03:24:55', 'Trial\n'),
(49, 'yvan_admin', 'Chemical', 'Acetic Acid', 20, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 03:25:53', 'Used'),
(50, 'yvan_admin', 'Chemical', 'Acetic Acid', 5, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"}]', '2024-08-13 03:26:22', 'Used'),
(51, 'yvan_admin', 'Chemical', 'Acetic Acid', 12, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"7\"}]', '2024-08-13 03:32:14', 'Trial lang'),
(52, 'yvan_admin', 'Chemical', 'Acetic Acid', 12, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"7\"}]', '2024-08-13 03:33:01', 'Used'),
(53, 'yvan_admin', 'Chemical', 'Acetic Acid', 20, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 03:43:23', 'Used'),
(54, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 03:47:13', 'Used'),
(55, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 03:50:49', 'Di gumagana'),
(56, 'yvan_admin', 'Chemical', 'Acetic Acid', 20, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"10\"}]', '2024-08-13 03:53:11', 'Try lang nang try'),
(57, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"}]', '2024-08-13 03:56:18', 'wow\n\n'),
(58, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 03:56:58', 'wow'),
(59, 'yvan_admin', 'Chemical', 'Acetic Acid', 5, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"}]', '2024-08-13 03:57:29', 'Used'),
(60, 'yvan_admin', 'Chemical', 'Acetic Acid', 5, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"}]', '2024-08-13 03:57:41', 'hehe'),
(61, 'yvan_admin', 'Chemical', 'Acetic Acid', 10, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"5\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 04:05:37', 'Please'),
(62, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 05:07:19', 'try'),
(63, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 05:13:39', 'raw\n'),
(64, 'yvan_admin', 'Chemical', 'Acetic Acid', 15, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"5\"}]', '2024-08-13 05:20:16', 'ayaw'),
(65, 'yvan_admin', 'Chemical', 'Acetic Acid', 2, '[{\"batch_number\":\"trial\",\"used_stock\":\"2\"}]', '2024-08-13 05:24:40', 'dalawa'),
(66, 'yvan_admin', 'Chemical', 'Acetic Acid', 13, '[{\"batch_number\":\"Sample2\",\"used_stock\":\"10\"},{\"batch_number\":\"trial\",\"used_stock\":\"3\"}]', '2024-08-13 05:25:09', 'sana ');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Lab_Manager','Microbiology Lab Manager','Chemical Lab Manager','Metrology Lab Manager','Analyst','Microbiology Analyst','Chemical Analyst','Metrology Analyst','CRO') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `profile_image`, `created_at`, `updated_at`) VALUES
(7, 'yvan_admin', '$2y$10$NwrNM8x3ESeURl8qsX4NLOjm7tBW6OQxMTquGVILwe1lLB.D.5L5m', 'Admin', 'Yvan.jpg', '2024-08-09 06:51:18', '2024-08-09 06:51:18'),
(8, 'Kath', '$2y$10$P3GHa2sC8KVAva2EnDy/sO3U3RVDqIRkrdLY7r/3t3n1Zg7FON/hO', 'Admin', 'Catherine.jpg', '2024-08-09 13:32:48', '2024-08-09 13:32:48'),
(9, 'Admin', '$2y$10$AEaQn7xNXeCfSEWScgYlXuOjXzBzGhovYvwNpM7o4XD/P5iW/4Jp6', 'Admin', 'DOST.png', '2024-08-12 01:21:24', '2024-08-12 01:21:24'),
(12, 'Trial', '$2y$10$RtlIhyk.gfcg7XU9/E87qOrLRSh8If1me7i7vFakGqEnmgF.Qugh6', 'Microbiology Lab Manager', 'Yvan.jpg', '2024-08-12 02:32:20', '2024-08-12 02:32:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `lab_id` (`lab_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `laboratories`
--
ALTER TABLE `laboratories`
  ADD PRIMARY KEY (`lab_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `laboratories`
--
ALTER TABLE `laboratories`
  MODIFY `lab_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`lab_id`) REFERENCES `laboratories` (`lab_id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
