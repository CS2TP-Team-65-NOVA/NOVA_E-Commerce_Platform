-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 23, 2026 at 11:57 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs2team65_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `basket`
--

CREATE TABLE `basket` (
  `basket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` char(36) DEFAULT NULL,
  `status` enum('active','converted','abandoned') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `basket_items`
--

CREATE TABLE `basket_items` (
  `basket_item_id` int(11) NOT NULL,
  `basket_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category`, `description`, `created_at`) VALUES
(1, 'Exclusive Perfumes', 'High-end limited edition Nova fragrances.', '2026-03-21 10:50:20'),
(2, 'Women - Citrus Perfumes', 'Fresh, zesty citrus scents for women.', '2026-03-21 10:50:20'),
(3, 'Women - Floral Perfumes', 'Soft and elegant floral fragrances for women.', '2026-03-21 10:50:20'),
(4, 'Women - Spicy Perfumes', 'Warm and bold spicy notes for women.', '2026-03-21 10:50:20'),
(5, 'Men - Citrus Perfumes', 'Energetic citrus fragrances for men.', '2026-03-21 10:50:20'),
(6, 'Men - Oriental Perfumes', 'Rich oriental scents for men.', '2026-03-21 10:50:20'),
(7, 'Men - Spicy Perfumes', 'Intense and spicy fragrances for men.', '2026-03-21 10:50:20'),
(8, 'Sale', 'Discounted and promotional Nova fragrances.', '2026-03-21 10:50:20'),
(9, 'Gift Box', 'Curated perfume gift sets and boxes.', '2026-03-21 10:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `size_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_label` varchar(50) DEFAULT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `low_stock_alert_status` tinyint(1) DEFAULT 0,
  `status` enum('in_stock','low_stock','out_of_stock') DEFAULT 'in_stock',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`size_id`, `product_id`, `size_label`, `stock_qty`, `low_stock_alert_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(2, 2, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(3, 3, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(4, 4, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(5, 5, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(6, 6, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(7, 7, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(8, 8, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(9, 9, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(10, 10, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(11, 11, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(12, 12, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(13, 13, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(14, 14, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(15, 15, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(16, 16, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(17, 17, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(18, 18, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(19, 19, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(20, 20, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(21, 21, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(22, 22, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(23, 23, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(24, 24, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(25, 25, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(26, 26, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(27, 27, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(28, 28, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(29, 29, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(30, 30, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(31, 31, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(32, 32, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(33, 33, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(34, 34, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(35, 35, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(36, 36, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(37, 37, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(38, 38, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(39, 39, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(40, 40, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(41, 41, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(42, 42, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(43, 43, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(44, 44, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(45, 45, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(46, 46, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(47, 47, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(48, 48, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(49, 49, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(50, 50, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(51, 51, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(52, 52, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(53, 53, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(54, 54, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(55, 55, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(56, 56, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(57, 57, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(58, 58, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(59, 59, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(60, 60, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(61, 61, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(62, 62, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(63, 63, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(64, 64, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(65, 65, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(66, 66, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(67, 67, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(68, 68, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(69, 69, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(70, 70, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(71, 71, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(72, 72, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(73, 73, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(74, 74, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(75, 75, '50.00 ml', 15, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(128, 1, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(129, 2, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(130, 3, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(131, 4, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(132, 5, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(133, 6, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(134, 7, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(135, 8, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(136, 9, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(137, 10, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(138, 11, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(139, 12, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(140, 13, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(141, 14, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(142, 15, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(143, 16, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(144, 17, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(145, 18, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(146, 19, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(147, 20, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(148, 21, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(149, 22, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(150, 23, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(151, 24, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(152, 25, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(153, 26, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(154, 27, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(155, 28, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(156, 29, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(157, 30, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(158, 31, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(159, 32, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(160, 33, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(161, 34, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(162, 35, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(163, 36, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(164, 37, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(165, 38, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(166, 39, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(167, 40, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(168, 41, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(169, 42, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(170, 43, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(171, 44, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(172, 45, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(173, 46, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(174, 47, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(175, 48, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(176, 49, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(177, 50, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(178, 51, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(179, 52, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(180, 53, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(181, 54, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(182, 55, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(183, 56, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(184, 57, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(185, 58, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(186, 59, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(187, 60, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(188, 61, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(189, 62, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(190, 63, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(191, 64, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(192, 65, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(193, 66, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(194, 67, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(195, 68, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(196, 69, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(197, 70, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(198, 71, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(199, 72, '100.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(200, 73, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(201, 74, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(202, 75, '100.00 ml', 20, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(255, 1, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(256, 2, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(257, 3, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(258, 4, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(259, 5, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(260, 6, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(261, 7, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(262, 8, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(263, 9, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(264, 10, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(265, 11, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(266, 12, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(267, 13, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(268, 14, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(269, 15, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(270, 16, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(271, 17, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(272, 18, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(273, 19, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(274, 20, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(275, 21, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(276, 22, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(277, 23, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(278, 24, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(279, 25, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(280, 26, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(281, 27, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(282, 28, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(283, 29, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(284, 30, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(285, 31, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(286, 32, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(287, 33, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(288, 34, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(289, 35, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(290, 36, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(291, 37, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(292, 38, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(293, 39, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(294, 40, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(295, 41, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(296, 42, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(297, 43, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(298, 44, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(299, 45, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(300, 46, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(301, 47, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(302, 48, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(303, 49, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(304, 50, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(305, 51, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(306, 52, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(307, 53, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(308, 54, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(309, 55, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(310, 56, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(311, 57, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(312, 58, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(313, 59, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(314, 60, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(315, 61, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(316, 62, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(317, 63, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(318, 64, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(319, 65, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(320, 66, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(321, 67, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(322, 68, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(323, 69, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(324, 70, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(325, 71, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(326, 72, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(327, 73, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(328, 74, '150.00 ml', 10, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(329, 75, '150.00 ml', 3, 1, 'low_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(382, 1, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(383, 2, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(384, 3, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(385, 4, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(386, 5, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(387, 6, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(388, 7, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(389, 8, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(390, 9, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(391, 10, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(392, 11, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(393, 12, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(394, 13, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(395, 14, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(396, 15, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(397, 16, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(398, 17, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(399, 18, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(400, 19, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(401, 20, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(402, 21, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(403, 22, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(404, 23, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(405, 24, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(406, 25, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(407, 26, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(408, 27, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(409, 28, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(410, 29, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(411, 30, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(412, 31, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(413, 32, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(414, 33, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(415, 34, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(416, 35, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(417, 36, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(418, 37, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(419, 38, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(420, 39, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(421, 40, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(422, 41, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(423, 42, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(424, 43, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(425, 44, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(426, 45, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(427, 46, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(428, 47, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(429, 48, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(430, 49, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(431, 50, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(432, 51, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(433, 52, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(434, 53, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(435, 54, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(436, 55, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(437, 56, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(438, 57, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(439, 58, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(440, 59, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(441, 60, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(442, 61, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(443, 62, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(444, 63, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(445, 64, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(446, 65, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(447, 66, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(448, 67, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(449, 68, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(450, 69, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(451, 70, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(452, 71, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(453, 72, '200.00 ml', 0, 0, 'out_of_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(454, 73, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(455, 74, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(456, 75, '200.00 ml', 5, 0, 'in_stock', '2026-03-21 10:50:20', '2026-03-21 10:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `change_type` enum('sale','restock','return','manual') NOT NULL,
  `quantity_changed` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `reference_type` enum('order','stock','return','manual') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT current_timestamp(),
  `order_number` varchar(30) DEFAULT NULL,
  `payment_status` enum('success','pending','refunded') NOT NULL,
  `delivery_status` enum('processing','shipped','delivered','returned') NOT NULL,
  `currency` char(3) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `shipping_amount` decimal(10,2) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `order_number`, `payment_status`, `delivery_status`, `currency`, `total_amount`, `shipping_amount`, `shipping_address`, `discount_amount`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-03-21 12:24:53', 'ORD-5A9889', 'success', 'processing', 'GBP', 129.00, NULL, '4-6 Hamstead rd, Birmingham, B191DB', NULL, NULL, '2026-03-21 12:24:53', '2026-03-21 12:24:53'),
(2, 2, '2026-03-22 11:19:52', 'ORD-85BB53', 'success', 'processing', 'GBP', 66.00, NULL, '4-6 Hamstead rd, Birmingham, B191DB', NULL, NULL, '2026-03-22 11:19:52', '2026-03-22 11:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_items_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `line_total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_items_id`, `order_id`, `size_id`, `quantity`, `price`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 128, 1, 129.00, 129.00, '2026-03-21 12:24:53', '2026-03-21 12:24:53'),
(2, 2, NULL, 1, 66.00, 66.00, '2026-03-22 11:19:52', '2026-03-22 11:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_method` varchar(64) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_status` enum('success','pending','refunded') NOT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `top_notes` text DEFAULT NULL,
  `heart_notes` text DEFAULT NULL,
  `base_notes` text DEFAULT NULL,
  `launch_info` varchar(255) DEFAULT NULL,
  `scent_story` text DEFAULT NULL,
  `design_story` text DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `image_3` varchar(255) DEFAULT NULL,
  `image_4` varchar(255) DEFAULT NULL,
  `image_5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `brand_id`, `description`, `top_notes`, `heart_notes`, `base_notes`, `launch_info`, `scent_story`, `design_story`, `name`, `price`, `image`, `image_2`, `image_3`, `image_4`, `image_5`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'A rich blend of oud, rose and amber for special evenings.', 'Bright citrus, aromatic spices and a hint of saffron.', 'Regal rose, smooth oud and warm incense.', 'Amber, sandalwood and soft musk.', 'Created as a hero scent in the NOVA Exclusive line.', 'Nova Royal Essence opens with luminous citrus and aromatic spices before settling into a regal blend of oud, rose and amber for an evening statement.', 'The bottle is finished in deep ink blue glass with clean Nova branding, created to feel weighty and special in the hand and to stand out on a dresser.', 'Nova Royal Essence', 129.00, 'royal_essence.jpeg', 'exclusive_royal_essence_2.jpg', 'exclusive_royal_essence_3.jpg', 'exclusive_royal_essence_4.jpg', 'exclusive_royal_essence_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:09:04'),
(2, 1, NULL, 'Dark woods and smoky oud wrapped in vanilla.', 'Smoky woods lifted with dark citrus.', 'Oud, black vanilla and dried fruits.', 'Leather, amber woods and lingering musk.', 'Night edition developed for the NOVA Exclusive collection.', 'Nova Midnight Oud wraps smoky woods and dark vanilla around rich oud for a mysterious, late night signature that feels smooth rather than heavy.', 'A tall, midnight toned bottle with subtle gloss highlights and a minimal label mirrors the dark, shadowy character of the fragrance.', 'Nova Midnight Oud', 139.00, 'midnight_oud.jpeg', 'exclusive_midnight_oud_2.jpg', 'exclusive_midnight_oud_3.jpg', 'exclusive_midnight_oud_4.jpg', 'exclusive_midnight_oud_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:07:50'),
(3, 1, NULL, 'White musk and iris with a clean, luxurious trail.', 'Cool iris, sheer aldehydes and soft citrus.', 'Powdery musk, violet and light woods.', 'Cashmere woods, white musk and a touch of vanilla.', 'Signature musk introduced with the first NOVA Exclusive drop.', 'Nova Imperial Musk blends soft iris, powdery musk and delicate woods to create a clean skin like scent that feels quietly luxurious all day.', 'Frosted glass, rounded shoulders and a simple metal collar give the bottle a light, airy look that matches the silky texture of the perfume.', 'Nova Imperial Musk', 135.00, 'imperal musk.jpeg', 'exclusive_imperial_musk_2.jpg', 'exclusive_imperial_musk_3.jpg', 'exclusive_imperial_musk_4.jpg', 'exclusive_imperial_musk_5.jpg', '2026-03-21 10:50:20', '2026-03-23 09:51:49'),
(4, 1, NULL, 'Warm amber, vanilla and tonka bean with long-lasting depth.', 'Warm spices, plum and golden resins.', 'Velvety amber, tonka and gentle florals.', 'Vanilla, benzoin and soft woods.', 'Limited Velvet chapter in the NOVA Exclusive line.', 'Nova Velvet Amber combines warm resins, vanilla and tonka with a touch of spice to create a cocooning trail that feels like cashmere on the skin.', 'The bottle is coated in a warm neutral gradient with a satin finish, echoing the soft, comforting feel of the fragrance itself.', 'Nova Velvet Amber', 132.00, 'exclusive_velvet_amber.jpg', 'exclusive_velvet_amber_2.jpg', 'exclusive_velvet_amber_3.jpg', 'exclusive_velvet_amber_4.jpg', 'exclusive_velvet_amber_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(5, 1, NULL, 'Intense leather and spice with a modern twist.', 'Aromatic lavender, cardamom and grapefruit.', 'Oud, leather and dark woods.', 'Patchouli, amber and smoky musk.', 'Intense night release from the NOVA Exclusive series.', 'Nova Noir Intense contrasts bright aromatic notes with dense woods, leather and spice to create a bold, confident scent that lingers long into the night.', 'A sleek black lacquered bottle with sharp edges and a heavy cap is designed to feel modern, powerful and unmistakably Nova.', 'Nova Noir Intense', 145.00, 'noir_intense.jpeg', 'exclusive_noir_intense_2.jpg', 'exclusive_noir_intense_3.jpg', 'exclusive_noir_intense_4.jpg', 'exclusive_noir_intense_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:11:44'),
(6, 2, NULL, 'Sparkling bergamot and lemon over soft white flowers.', 'Sparkling bergamot, lemon zest and mandarin.', 'Orange blossom, white petals and green leaves.', 'Soft musk and pale woods.', 'Fresh floral citrus created for the Aurora capsule collection.', 'Aurora Citrus Bloom opens with sparkling bergamot and lemon before easing into soft white flowers and clean musk for a fresh but feminine aura.', 'The clear bottle shows a pale citrus toned juice and is topped with a rounded cap, giving a light, sunrise inspired look on the shelf.', 'Aurora Citrus Bloom', 69.00, 'aurora_citrus_bloom.png', 'w_citrus_aurora_citrus_bloom_2.jpg', 'w_citrus_aurora_citrus_bloom_3.jpg', 'w_citrus_aurora_citrus_bloom_4.jpg', 'w_citrus_aurora_citrus_bloom_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:40:33'),
(7, 2, NULL, 'Grapefruit, mandarin and sea breeze notes.', 'Juicy grapefruit, sweet mandarin and sea breeze accord.', 'Solar florals and soft coconut water.', 'Driftwood, white musk and ambergris style notes.', 'Seasonal Summer Zest edition for sun-ready days.', 'Summer Zest Nova blends juicy grapefruit, mandarin and sea breeze accords to capture the feeling of sun on skin and days by the water.', 'Horizontal shoulders and a bright accent label are paired with a compact bottle that slips easily into a beach bag or weekend case.', 'Summer Zest Nova', 72.00, 'summer_zest_nova.png', 'w_citrus_summer_zest_2.jpg', 'w_citrus_summer_zest_3.jpg', 'w_citrus_summer_zest_4.jpg', 'w_citrus_summer_zest_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:40:04'),
(8, 2, NULL, 'Fresh lime and neroli with a light musky base.', 'Tart lime, crushed leaves and neroli.', 'Creamy gardenia, jasmine and muguet.', 'Sheer woods and powdery musk.', 'Everyday garden citrus launched in the core women’s line.', 'Lime Gardenia Mist combines tart lime with soft gardenia petals to create a crisp, uplifting everyday scent with a gentle floral veil.', 'The slim, slightly curved bottle and light green details are inspired by dew on garden leaves in early morning light.', 'Lime Gardenia Mist', 65.00, 'w_citrus_lime_gardenia_mist.jpg', 'w_citrus_lime_gardenia_mist_2.jpg', 'w_citrus_lime_gardenia_mist_3.jpg', 'w_citrus_lime_gardenia_mist_4.jpg', 'w_citrus_lime_gardenia_mist_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(9, 2, NULL, 'Sweet orange and peach with a soft floral heart.', 'Clementine, peach skin and sparkling lemon.', 'Soft peony, rose and freesia.', 'Smooth musk and blond woods.', 'Playful citrus floral added to the Clementine story range.', 'Clementine Petals opens with sweet orange and peach before revealing a bouquet of soft florals, giving a playful yet polished impression.', 'A softly faceted bottle catches the light, while a delicate blush label reflects the juicy fruit tones of the fragrance.', 'Clementine Petals', 71.00, 'w_citrus_clementine_petals.jpg', 'w_citrus_clementine_petals_2.jpg', 'w_citrus_clementine_petals_3.jpg', 'w_citrus_clementine_petals_4.jpg', 'w_citrus_clementine_petals_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(10, 2, NULL, 'Lemon, verbena and green tea for an everyday fresh scent.', 'Lemon verbena, lime and green tea.', 'Herbal nuances and white florals.', 'Light woods and cotton musk.', 'Clean, easy citrus introduced as a daily NOVA favourite.', 'Verbena Citrus Sheen mixes lemon verbena, green tea and light woods to create a clean, refreshing scent ideal for busy days and simple outfits.', 'Minimal lines and a fine mist sprayer are paired with a fresh lime coloured strip, keeping the design as clean as the fragrance smells.', 'Verbena Citrus Sheen', 68.00, 'red_pepper_intenso.png', 'w_citrus_verbena_sheen_2.jpg', 'w_citrus_verbena_sheen_3.jpg', 'w_citrus_verbena_sheen_4.jpg', 'w_citrus_verbena_sheen_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:36:48'),
(11, 2, NULL, 'Sparkling citrus fusion with mandarin and neroli.', 'Bright mandarin, neroli and petitgrain.', 'Orange blossom and jasmine petals.', 'Soft musk and gentle vanilla.', 'Crystal clear citrus launched in the Nova Daylight story.', 'Mandarin Crystal offers a bright mandarin and neroli opening that melts into soft musk, giving a sparkling start and a gentle, skin like dry down.', 'A compact glass block with subtle bevels and a clear cap lets the radiance of the orange toned juice shine through.', 'Mandarin Crystal', 70.00, 'mandranin_crystal.jpg', 'w_citrus_mandarin_crystal_2.jpg', 'w_citrus_mandarin_crystal_3.jpg', 'w_citrus_mandarin_crystal_4.jpg', 'w_citrus_mandarin_crystal_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:42:09'),
(12, 2, NULL, 'Fresh lime zest with a soft jasmine undertone.', 'Fresh lime, bergamot and cool mint.', 'Jasmine, green leaves and a touch of lily.', 'Cedarwood and white amber.', 'Breezy citrus floral introduced for warm-weather wear.', 'Lime Breeze Flora balances tart lime with a whisper of jasmine, delivering a cool, breezy citrus scent that never feels sharp or heavy.', 'The bottle carries a light gradient from clear to soft green, echoing the movement of a breeze across a citrus grove.', 'Lime Breeze Flora', 67.00, 'w_citrus_lime_breeze_flora.jpg', 'w_citrus_lime_breeze_flora_2.jpg', 'w_citrus_lime_breeze_flora_3.jpg', 'w_citrus_lime_breeze_flora_4.jpg', 'w_citrus_lime_breeze_flora_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(13, 2, NULL, 'Citrus-green apple blend with modern musky notes.', 'Crisp green apple, lemon and pear.', 'Apple blossom, peony and musk.', 'Clean woods and soft amber.', 'Youthful citrus mist created for the Nova Weekend range.', 'Citrus Apple Mist layers crisp green apple over citrus and white musk, creating a youthful, energising trail for day to day wear.', 'Rounded glass and a fresh apple green band make the bottle feel fun and approachable while still very Nova.', 'Citrus Apple Mist', 73.00, 'citrus_apple_mix.jpg', 'w_citrus_apple_mist_2.jpg', 'w_citrus_apple_mist_3.jpg', 'w_citrus_apple_mist_4.jpg', 'w_citrus_apple_mist_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:41:13'),
(14, 2, NULL, 'Lemon verbena and soft herbal layers.', 'Verbena leaf, lime zest and crushed herbs.', 'Green florals and aromatic tea.', 'Transparent woods and fresh musk.', 'Cooling verbena water launched as an all-day splash.', 'Verbena Dew is a cool, green citrus fragrance built around verbena, herbs and light woods for a relaxed, effortless freshness.', 'The tall, slim bottle with a soft matte label is inspired by simple apothecary lines and everyday ease.', 'Verbena Dew', 69.00, 'w_citrus_verbena_dew.jpg', 'w_citrus_verbena_dew_2.jpg', 'w_citrus_verbena_dew_3.jpg', 'w_citrus_verbena_dew_4.jpg', 'w_citrus_verbena_dew_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(15, 2, NULL, 'Grapefruit blossom and lily accord.', 'Grapefruit zest, pomelo and lemon.', 'White blossoms and rose petals.', 'Pale woods and skin-soft musk.', 'Modern citrus floral created for the Grapefruit Blossom theme.', 'Grapefruit Blossom Nova blends zesty grapefruit with delicate petals to create a crisp, slightly bitter citrus floral that feels sophisticated.', 'A clear cylindrical bottle with a subtle blush band mirrors the contrast between bright fruit and soft blossom inside.', 'Grapefruit Blossom Nova', 72.00, 'w_citrus_grapefruit_blossom.jpg', 'w_citrus_grapefruit_blossom_2.jpg', 'w_citrus_grapefruit_blossom_3.jpg', 'w_citrus_grapefruit_blossom_4.jpg', 'w_citrus_grapefruit_blossom_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(16, 3, NULL, 'Classic rose bouquet with soft musk and amber.', 'Sparkling citrus, pink pepper and lychee.', 'Velvety rose, peony and violet.', 'Soft amber, musk and delicate woods.', 'Star rose centrepiece of the NOVA Floral chapter.', 'Rose Étoile captures a modern rose with sparkling top notes, a velvety floral heart and a soft amber musk base for timeless romance.', 'The bottle features gentle curves and a rose gold collar, designed to echo the glow of evening light on rose petals.', 'Rose Étoile', 79.00, 'rose_etolite.png', 'w_floral_rose_etoile_2.jpg', 'w_floral_rose_etoile_3.jpg', 'w_floral_rose_etoile_4.jpg', 'w_floral_rose_etoile_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:45:20'),
(17, 3, NULL, 'Jasmine, tuberose and ylang-ylang in a creamy blend.', 'Creamy ylang, jasmine petals and orange blossom.', 'Tuberose, jasmine absolute and heliotrope.', 'Vanilla, sandalwood and musk.', 'Velvet white floral introduced as an evening signature.', 'Jasmine Velvet Nova weaves creamy jasmine, tuberose and ylang into a rich floral cloud that feels indulgent yet wearable.', 'A slightly opaque bottle with a satin finish and a sculpted cap reflects the velvety character of the fragrance.', 'Jasmine Velvet Nova', 82.00, 'jasmine_velvet.png', 'w_floral_jasmine_velvet_2.jpg', 'w_floral_jasmine_velvet_3.jpg', 'w_floral_jasmine_velvet_4.jpg', 'w_floral_jasmine_velvet_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:44:14'),
(18, 3, NULL, 'Lily of the valley and peony with a powdery trail.', 'Pink berries, pear and mandarin.', 'Airy peony, muguet and rose.', 'Soft woods and powdery musk.', 'Whisper-light bouquet created for daytime wear.', 'Peony Whisper layers airy peony, muguet and soft woods to create a delicate, powdery floral trail perfect for daytime.', 'The bottle is compact with rounded edges, designed to look like a smooth pebble shaped by water and time.', 'Peony Whisper', 76.00, 'peony_whispher.png', 'w_floral_peony_whisper_2.jpg', 'w_floral_peony_whisper_3.jpg', 'w_floral_peony_whisper_4.jpg', 'w_floral_peony_whisper_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:44:43'),
(19, 3, NULL, 'Iris, violet and soft woods for an elegant daytime scent.', 'Iris leaf, bergamot and soft aldehydes.', 'Iris, violet and orris butter.', 'Cashmere woods, musk and a hint of amber.', 'Silken iris floral designed for polished occasions.', 'Iris Silk Bouquet highlights cool iris and violet over light woods and musk, giving a polished, understated scent ideal for the office and evenings.', 'Frosted glass and a slim profile give the bottle a fabric like softness and a quietly luxurious feel.', 'Iris Silk Bouquet', 85.00, 'w_floral_iris_silk.jpg', 'w_floral_iris_silk_2.jpg', 'w_floral_iris_silk_3.jpg', 'w_floral_iris_silk_4.jpg', 'w_floral_iris_silk_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(20, 3, NULL, 'Orange blossom and white rose with a hint of honey.', 'Neroli, orange blossom and honeyed citrus.', 'White rose, jasmine and floral lace accord.', 'Soft amber, musk and light woods.', 'Romantic lace inspired floral in the Blossom story.', 'Blossom Lace combines orange blossom, white rose and a gentle honey note for a glowing, feminine floral impression.', 'The bottle carries a lace inspired print on its label, paired with a pale golden juice that catches the light beautifully.', 'Blossom Lace', 78.00, 'w_floral_blossom_lace.jpg', 'w_floral_blossom_lace_2.jpg', 'w_floral_blossom_lace_3.jpg', 'w_floral_blossom_lace_4.jpg', 'w_floral_blossom_lace_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(21, 3, NULL, 'Soft rose and vanilla with powdery undertones.', 'Rosewater, mandarin and freesia.', 'Soft rose, peony and creamy vanilla.', 'Cashmere woods and white musk.', 'Comforting petal mist launched for cosy everyday use.', 'Rose Petal Mist wraps soft rose and vanilla in airy musk, creating a comforting floral fragrance that feels like a favourite scarf.', 'A slightly domed cap and soft blush details complete a bottle design focused on comfort and easy elegance.', 'Rose Petal Mist', 82.00, 'w_floral_rose_petal_mist.jpg', 'w_floral_rose_petal_mist_2.jpg', 'w_floral_rose_petal_mist_3.jpg', 'w_floral_rose_petal_mist_4.jpg', 'w_floral_rose_petal_mist_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(22, 3, NULL, 'Orchid, jasmine, and musk in a graceful bouquet.', 'Crisp pear, citrus and dewy greens.', 'Orchid accord, jasmine and lily.', 'Smooth woods and sheer amber.', 'Elegant orchid floral introduced to the Nova Classics line.', 'Orchid Serenade blends orchid, jasmine and gentle woods into a smooth, sophisticated floral that moves easily from day to night.', 'A clear bottle with a deep violet accent stripe nods to the richness of orchid petals while staying modern and clean.', 'Orchid Serenade', 88.00, 'orchid_serenade.png', 'w_floral_orchid_serenade_2.jpg', 'w_floral_orchid_serenade_3.jpg', 'w_floral_orchid_serenade_4.jpg', 'w_floral_orchid_serenade_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:43:06'),
(23, 3, NULL, 'Gardenia, lotus, and creamy woods.', 'Gardenia leaf, bergamot and watery greens.', 'Creamy gardenia, lotus and jasmine.', 'Skin musk and pale woods.', 'Silk-smooth white floral brought to life in the Silk range.', 'Gardenia Silk pairs creamy gardenia with lotus and soft woods for a floral scent that is plush yet not overpowering.', 'Fine vertical facets along the bottle create a silk like sheen when they catch the light.', 'Gardenia Silk', 85.00, 'w_floral_gardenia_silk.jpg', 'w_floral_gardenia_silk_2.jpg', 'w_floral_gardenia_silk_3.jpg', 'w_floral_gardenia_silk_4.jpg', 'w_floral_gardenia_silk_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(24, 3, NULL, 'Magnolia, lily and white woods.', 'Magnolia bud, lemon and leafy greens.', 'Magnolia flower, lily and white petals.', 'Soft woods and tranquil musk.', 'Serene magnolia fragrance launched for calm moments.', 'Magnolia Essence focuses on magnolia and lily, supported by gentle woods, for a fresh floral impression with a serene, calm mood.', 'The tall, slim bottle with a minimal white label is designed to feel as pure and simple as the scent itself.', 'Magnolia Essence', 80.00, 'w_floral_magnolia_essence.jpg', 'w_floral_magnolia_essence_2.jpg', 'w_floral_magnolia_essence_3.jpg', 'w_floral_magnolia_essence_4.jpg', 'w_floral_magnolia_essence_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(25, 3, NULL, 'Blooming bouquet with peony and pink rose.', 'Red berries, citrus and pink pepper.', 'Peony, pink rose and soft fruits.', 'Musk, amber and creamy woods.', 'Joyful bloom scent introduced to the Pink Harmony series.', 'Pink Bloom Harmony mixes peony, pink rose and soft fruits to create a joyful floral fragrance with a bright, modern personality.', 'The bottle is tinted with a faint pink wash and finished with a glossy cap for a youthful but chic look.', 'Pink Bloom Harmony', 86.00, 'w_floral_pink_bloom_harmony.jpg', 'w_floral_pink_bloom_harmony_2.jpg', 'w_floral_pink_bloom_harmony_3.jpg', 'w_floral_pink_bloom_harmony_4.jpg', 'w_floral_pink_bloom_harmony_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(26, 4, NULL, 'Pink pepper, vanilla and sandalwood for a cozy warmth.', 'Pink pepper, mandarin and spicy citrus.', 'Warm vanilla, jasmine and sandalwood.', 'Amber, tonka and creamy woods.', 'Crimson spice aura launched as a cosy evening scent.', 'Crimson Spice opens with pink pepper over smooth vanilla and sandalwood, giving a warm, enveloping scent that still feels polished.', 'The bottle is accented with a rich crimson band that hints at the inner heat of the fragrance.', 'Crimson Spice', 88.00, 'crimson_spice.jpeg', 'w_spicy_crimson_spice_2.jpg', 'w_spicy_crimson_spice_3.jpg', 'w_spicy_crimson_spice_4.jpg', 'w_spicy_crimson_spice_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:13:35'),
(27, 4, NULL, 'Cinnamon and clove with a sweet amber base.', 'Cinnamon, clove and cardamom.', 'Milky woods, chai accord and soft florals.', 'Vanilla, sandalwood and amber.', 'Tea-inspired fragrance in the NOVA Chai Stories line.', 'Amber Chai blends cinnamon, clove and milky woods to evoke the comfort of a favourite spiced drink on a cool day.', 'A softly rounded bottle and warm caramel toned label are used to mirror the cosy comfort of the scent.', 'Amber Chai', 90.00, 'w_spicy_amber_chai.jpg', 'w_spicy_amber_chai_2.jpg', 'w_spicy_amber_chai_3.jpg', 'w_spicy_amber_chai_4.jpg', 'w_spicy_amber_chai_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(28, 4, NULL, 'Cardamom, rose and tonka bean with a modern twist.', 'Rose pepper, bergamot and cardamom.', 'Rose, saffron and tonka.', 'Patchouli, amber and soft woods.', 'Spiced rose entry to the modern floral-spice family.', 'Spiced Rose Nova combines rose, cardamom and tonka to create a floral that blooms with subtle warmth and depth.', 'The clear bottle showcases a soft rose toned juice, framed by a simple black collar for contrast.', 'Spiced Rose Nova', 92.00, 'w_spicy_spiced_rose.jpg', 'w_spicy_spiced_rose_2.jpg', 'w_spicy_spiced_rose_3.jpg', 'w_spicy_spiced_rose_4.jpg', 'w_spicy_spiced_rose_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(29, 4, NULL, 'Ginger and mandarin over creamy vanilla.', 'Fresh ginger, mandarin and lemon.', 'Orange blossom, jasmine and spicy vanilla.', 'Amber, musk and soft woods.', 'Lively ginger floral launched for cool evenings.', 'Ginger Bloom contrasts sparkling ginger with mandarin and creamy vanilla for a lively opening that dries down to a gentle sweetness.', 'Fine etching on the bottle shoulders adds movement, inspired by swirling steam from hot ginger tea.', 'Ginger Bloom', 87.00, 'w_spicy_ginger_bloom.jpg', 'w_spicy_ginger_bloom_2.jpg', 'w_spicy_ginger_bloom_3.jpg', 'w_spicy_ginger_bloom_4.jpg', 'w_spicy_ginger_bloom_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(30, 4, NULL, 'Peppery opening with smooth cashmere woods.', 'Pink pepper, plum and citrus.', 'Cashmere woods, floral notes and amber.', 'Sandalwood, vanilla and musk.', 'Glowing ember scent introduced for the Velvet capsule.', 'Velvet Ember layers peppery notes over cashmere woods for a close to the skin scent that glows softly for hours.', 'A smoke tinted bottle paired with a fabric textured label emphasises the idea of embers under velvet.', 'Velvet Ember', 89.00, 'velvet_amber.jpeg', 'w_spicy_velvet_ember_2.jpg', 'w_spicy_velvet_ember_3.jpg', 'w_spicy_velvet_ember_4.jpg', 'w_spicy_velvet_ember_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:12:30'),
(31, 4, NULL, 'Warm cinnamon and patchouli with smooth musk.', 'Warm cinnamon, rose pepper and bergamot.', 'Rose, patchouli and spices.', 'Amber, tonka and woods.', 'Modern boho rose launched in the Nova Spice line.', 'Cinnamon Rose pairs warm spices with rose and patchouli, creating a romantic, slightly bohemian fragrance.', 'Classic floral curves are balanced with a deep red accent, giving the bottle a nostalgic yet modern feel.', 'Cinnamon Rose', 91.00, 'w_spicy_cinnamon_rose.jpg', 'w_spicy_cinnamon_rose_2.jpg', 'w_spicy_cinnamon_rose_3.jpg', 'w_spicy_cinnamon_rose_4.jpg', 'w_spicy_cinnamon_rose_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(32, 4, NULL, 'Peppery vanilla and saffron blend.', 'Saffron threads, citrus and aromatic spice.', 'Floral notes, spice and amber.', 'Vanilla, woods and musk.', 'Saffron-rich composition introduced as the Heat edition.', 'Saffron Heat uses saffron, spice and vanilla to create an addictive, gently exotic trail that works day or night.', 'The bottle carries a golden saffron coloured band and a clean silhouette that keeps the focus on the glowing juice inside.', 'Saffron Heat', 95.00, 'safron_royale_nova_square.jpeg', 'w_spicy_saffron_heat_2.jpg', 'w_spicy_saffron_heat_3.jpg', 'w_spicy_saffron_heat_4.jpg', 'w_spicy_saffron_heat_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:11:04'),
(33, 4, NULL, 'Sweet spice and amber with a creamy base.', 'Soft citrus, pink pepper and spice.', 'Amber, floral veil and warm resins.', 'Vanilla, tonka and smoke-tinged woods.', 'Sheer amber spice veil created for layering and solo wear.', 'Amber Spice Veil wraps soft spices and amber in a sheer, diffused cloud, giving warmth without weight.', 'A semi transparent label and subtle gradient glass are used to echo the veil like character of the scent.', 'Amber Spice Veil', 89.00, 'w_spicy_amber_spice_veil.jpg', 'w_spicy_amber_spice_veil_2.jpg', 'w_spicy_amber_spice_veil_3.jpg', 'w_spicy_amber_spice_veil_4.jpg', 'w_spicy_amber_spice_veil_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(34, 4, NULL, 'Ginger, pink pepper and soft florals.', 'Ginger root, mandarin and bright pepper.', 'Spiced florals, amber and woods.', 'Vanilla, benzoin and musk.', 'Energetic ginger flame launched as a bold statement scent.', 'Ginger Flame brings together ginger, pink pepper and florals to create a lively, energetic fragrance with a warm base.', 'An angled cap and bright accent stripe across the bottle emphasise the dynamic nature of the fragrance.', 'Ginger Flame', 92.00, 'w_spicy_ginger_flame.jpg', 'w_spicy_ginger_flame_2.jpg', 'w_spicy_ginger_flame_3.jpg', 'w_spicy_ginger_flame_4.jpg', 'w_spicy_ginger_flame_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(35, 4, NULL, 'Nutmeg and tonka bean with a modern edge.', 'Nutmeg, cinnamon and citrus rind.', 'Spiced woods, florals and tonka.', 'Smoky woods, vanilla and amber.', 'Comforting ember fragrance added to the winter story.', 'Nutmeg Ember combines nutmeg, woods and tonka into a slightly smoky, comforting perfume ideal for cooler weather.', 'The deep amber toned bottle with a squared profile is inspired by glowing embers and carved wood.', 'Nutmeg Ember', 90.00, 'w_spicy_nutmeg_ember.jpg', 'w_spicy_nutmeg_ember_2.jpg', 'w_spicy_nutmeg_ember_3.jpg', 'w_spicy_nutmeg_ember_4.jpg', 'w_spicy_nutmeg_ember_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(36, 5, NULL, 'Lemon, bergamot and marine notes with a woody base.', 'Lemon, bergamot and marine droplets.', 'Aromatic herbs, sea breeze and florals.', 'Cedarwood, amber and musk.', 'Marine citrus pillar scent of the Nova men’s line.', 'Nova Marine Citrus opens with lemon, bergamot and marine notes, drying down to woods and musk for a clean, aquatic masculine scent.', 'A tall, clear bottle with a blue gradient suggests sea spray rising against a glass shoreline.', 'Nova Marine Citrus', 74.00, 'marine_citrus_nova.png', 'm_citrus_marine_citrus_2.jpg', 'm_citrus_marine_citrus_3.jpg', 'm_citrus_marine_citrus_4.jpg', 'm_citrus_marine_citrus_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:34:56'),
(37, 5, NULL, 'Sharp grapefruit and vetiver with a clean dry-down.', 'Grapefruit, bitter orange and black pepper.', 'Vetiver, aromatic herbs and geranium.', 'Dry woods and smoky vetiver.', 'Office-sharp vetiver citrus launched for daily wear.', 'Graphite Citrus Vetiver sharpens grapefruit with dry vetiver and woods, giving a crisp, focused scent ideal for the office.', 'The bottle has a graphite coloured cap and base, reflecting the clean lines and sober character of the fragrance.', 'Graphite Citrus Vetiver', 79.00, 'graphite_citrus_vetiver_nova.png', 'm_citrus_vetiver_2.jpg', 'm_citrus_vetiver_3.jpg', 'm_citrus_vetiver_4.jpg', 'm_citrus_vetiver_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:34:26'),
(38, 5, NULL, 'Mandarin and mint with light cedarwood.', 'Mandarin, mint and lemon zest.', 'Aromatic herbs, cedar and watery notes.', 'Cedarwood, amber and musk.', 'High-energy Rush edition designed for sport-inspired days.', 'Citrus Rush Nova mixes mandarin, mint and cedarwood to deliver a fast, energetic freshness that keeps up with an active day.', 'The ergonomic bottle sits easily in the hand, with bold Nova branding that hints at movement and speed.', 'Citrus Rush Nova', 72.00, 'citrus_rush_nova.png', 'm_citrus_rush_2.jpg', 'm_citrus_rush_3.jpg', 'm_citrus_rush_4.jpg', 'm_citrus_rush_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:33:46'),
(39, 5, NULL, 'Lime, ginger and musk for a sporty feel.', 'Lime, ginger and frosted citrus.', 'Spicy accord, green notes and musk.', 'Woody amber and soft woods.', 'Sport-driven lime fragrance released with the Drive series.', 'Sport Lime Drive blends lime, ginger and musk, creating a sporty, invigorating fragrance that works from gym to street.', 'A textured grip detail along the side of the bottle is designed for quick grab and go use.', 'Sport Lime Drive', 73.00, 'm_citrus_sport_lime.jpg', 'm_citrus_sport_lime_2.jpg', 'm_citrus_sport_lime_3.jpg', 'm_citrus_sport_lime_4.jpg', 'm_citrus_sport_lime_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(40, 5, NULL, 'Orange zest and rosemary with aromatic notes.', 'Orange zest, bergamot and rosemary.', 'Aromatic herbs, lavender and geranium.', 'Cedarwood, moss and musk.', 'Smart-casual aromatic citrus introduced to the barbershop line.', 'Citrus Aromatique pairs orange zest with rosemary and aromatic herbs for a smart casual scent that feels both fresh and grown up.', 'The bottle combines a clear body with a brushed metal collar, echoing classic barbershop style with a modern twist.', 'Citrus Aromatique', 77.00, 'm_citrus_aromatique.jpg', 'm_citrus_aromatique_2.jpg', 'm_citrus_aromatique_3.jpg', 'm_citrus_aromatique_4.jpg', 'm_citrus_aromatique_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(41, 5, NULL, 'Bright citrus burst with bergamot and mint.', 'Bergamot, lemon and fresh lavender.', 'Clary sage, herbal notes and florals.', 'Clean woods and ambergris style notes.', 'Flash of bergamot launched as a confident signature.', 'Bergamot Flash delivers a burst of bergamot and lavender over clean woods for a bright, confident signature.', 'Faceted glass and a fine spray atomiser help create a light, diffused mist that suits the sparkling character of the scent.', 'Bergamot Flash', 78.00, 'bergamont_flash_nova.png', 'm_citrus_bergamot_flash_2.jpg', 'm_citrus_bergamot_flash_3.jpg', 'm_citrus_bergamot_flash_4.jpg', 'm_citrus_bergamot_flash_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:31:47'),
(42, 5, NULL, 'Cool lemon and cedar with fresh aromatics.', 'Icy lemon, grapefruit and aromatic greens.', 'Cedar, ginger and herbal notes.', 'Dry woods, musk and amber.', 'Frosted citrus-woody scent introduced for crisp days.', 'Cedar Lemon Frost contrasts cool lemon and cedarwood, producing a crisp, slightly icy scent that feels neat and precise.', 'A frosted bottle shoulder and cool grey detailing underline the chilled impression of the fragrance.', 'Cedar Lemon Frost', 76.00, 'cedar_lemon_frost.png', 'm_citrus_cedar_lemon_frost_2.jpg', 'm_citrus_cedar_lemon_frost_3.jpg', 'm_citrus_cedar_lemon_frost_4.jpg', 'm_citrus_cedar_lemon_frost_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:32:26'),
(43, 5, NULL, 'Orange zest and smoky vetiver.', 'Orange zest, mandarin and spice.', 'Vetiver, cedar and smoky woods.', 'Patchouli, amber and musk.', 'Urban citrus-vetiver launched with the Pulse capsule.', 'Orange Vetiver Pulse builds on orange zest with smoky vetiver, giving a citrus scent with a deeper, more serious backbone.', 'The thick glass base and linear silhouette of the bottle are inspired by city architecture at sunset.', 'Orange Vetiver Pulse', 81.00, 'm_citrus_orange_vetiver_pulse.jpg', 'm_citrus_orange_vetiver_pulse_2.jpg', 'm_citrus_orange_vetiver_pulse_3.jpg', 'm_citrus_orange_vetiver_pulse_4.jpg', 'm_citrus_orange_vetiver_pulse_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(44, 5, NULL, 'Lime twist with ginger and musk.', 'Lime, ginger and sparkling citrus.', 'Aromatic notes, herbs and light florals.', 'Soft woods and musk.', 'Fusion of lime and ginger released as a modern fresh scent.', 'Lime Fusion Nova fuses lime, ginger and soft musk, offering an easy wearing freshness with a modern twist.', 'A slim rectangular bottle with a lime coloured centre band keeps the look simple yet instantly recognisable.', 'Lime Fusion Nova', 79.00, 'm_citrus_lime_fusion.jpg', 'm_citrus_lime_fusion_2.jpg', 'm_citrus_lime_fusion_3.jpg', 'm_citrus_lime_fusion_4.jpg', 'm_citrus_lime_fusion_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(45, 5, NULL, 'Mandarin and marine freshness for daily wear.', 'Mandarin, marine accord and citrus peel.', 'Aquatic florals, herbs and woods.', 'Driftwood, musk and amber.', 'Tide-inspired citrus launched for weekends and holidays.', 'Mandarin Tide marries mandarin, marine notes and light woods to create a relaxed, laid back scent suited to weekends and holidays.', 'The bottle features a gentle blue wash rising from the base, echoing the pull of the tide on the shore.', 'Mandarin Tide', 75.00, 'm_citrus_mandarin_tide.jpg', 'm_citrus_mandarin_tide_2.jpg', 'm_citrus_mandarin_tide_3.jpg', 'm_citrus_mandarin_tide_4.jpg', 'm_citrus_mandarin_tide_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(46, 6, NULL, 'Oud, saffron and rose in an oriental accord.', 'Saffron, spicy citrus and aromatic notes.', 'Rich oud, rose and resins.', 'Amber, woods and musk.', 'Regal oud saffron edition in the Nova oriental chapter.', 'Oud Saffron Royale combines oud, saffron and rose into a rich, opulent composition that feels dressed up and confident.', 'A dark bottle with gold detailing and a weighty cap is designed to reflect the luxurious character of the fragrance.', 'Oud Saffron Royale', 99.00, 'safron_royale.jpeg', 'm_oriental_oud_saffron_2.jpg', 'm_oriental_oud_saffron_3.jpg', 'm_oriental_oud_saffron_4.jpg', 'm_oriental_oud_saffron_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:10:16'),
(47, 6, NULL, 'Incense, labdanum and warm resins.', 'Incense smoke, black pepper and citrus.', 'Resins, incense and dark woods.', 'Amber, patchouli and smoky musk.', 'Shadowy incense scent launched for evening wear.', 'Nova Incense Shadow layers incense, resins and woods to create a smoky, atmospheric scent that sits close to the skin.', 'The bottle uses smoked glass and a soft matte label to echo swirling incense in a darkened room.', 'Nova Incense Shadow', 95.00, 'oritenal_noir_code_nova.png', 'm_oriental_incense_shadow_2.jpg', 'm_oriental_incense_shadow_3.jpg', 'm_oriental_incense_shadow_4.jpg', 'm_oriental_incense_shadow_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:31:02'),
(48, 6, NULL, 'Vanilla, amber and dark woods for evenings.', 'Ambered citrus, spices and dried fruit.', 'Vanilla, amber and smoky woods.', 'Benzoin, patchouli and musk.', 'Nightfall oriental introduced as a smooth evening signature.', 'Amber Nightfall builds amber, vanilla and deep woods into a smooth, sensual evening fragrance with impressive staying power.', 'A gradient from deep amber to near black runs up the bottle, inspired by the sky at the end of the day.', 'Amber Nightfall', 97.00, 'amber_nightfall_nova.png', 'm_oriental_amber_nightfall_2.jpg', 'm_oriental_amber_nightfall_3.jpg', 'm_oriental_amber_nightfall_4.jpg', 'm_oriental_amber_nightfall_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:29:47'),
(49, 6, NULL, 'Patchouli, cocoa and spicy notes with depth.', 'Bitter orange, cocoa peel and spice.', 'Patchouli, cocoa and resins.', 'Dark woods, amber and musk.', 'Coded oriental noir launched for modern nightlife.', 'Oriental Noir Code is a complex blend of patchouli, cocoa and spices, giving a modern oriental with a slightly mysterious edge.', 'Sharp angles and a glossy black finish give the bottle a coded, almost secretive feel.', 'Oriental Noir Code', 102.00, 'm_oriental_noir_code.jpg', 'm_oriental_noir_code_2.jpg', 'm_oriental_noir_code_3.jpg', 'm_oriental_noir_code_4.jpg', 'm_oriental_noir_code_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(50, 6, NULL, 'Tonka bean, benzoin and smoky woods.', 'Smoky incense, ambered citrus and spice.', 'Amber, tonka and woods.', 'Smoked woods, vanilla and resin.', 'Softly smoked amber introduced to the Nova core line.', 'Smoked Amber Nova wraps amber and tonka in smoky woods, offering a cosy yet sophisticated take on the amber family.', 'The bottle’s warm brown tone and subtle smoke print recall firelight fading at the end of the evening.', 'Smoked Amber Nova', 98.00, 'm_oriental_smoked_amber.jpg', 'm_oriental_smoked_amber_2.jpg', 'm_oriental_smoked_amber_3.jpg', 'm_oriental_smoked_amber_4.jpg', 'm_oriental_smoked_amber_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(51, 6, NULL, 'Deep oud and vanilla contrasted with spice.', 'Spiced citrus, saffron and cardamom.', 'Oud, leather and warm spices.', 'Vanilla, amber and deep woods.', 'Commanding oud ember statement in the King series.', 'Oud Ember King brings together intense oud, warm spices and vanilla for a commanding scent with serious presence.', 'The heavy square bottle and bold metallic collar underline the idea of a powerful, crown like fragrance.', 'Oud Ember King', 109.00, 'm_oriental_oud_ember_king.jpg', 'm_oriental_oud_ember_king_2.jpg', 'm_oriental_oud_ember_king_3.jpg', 'm_oriental_oud_ember_king_4.jpg', 'm_oriental_oud_ember_king_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(52, 6, NULL, 'Dark amber, incense and sweet resin.', 'Resinous citrus, spices and incense.', 'Amber resins, labdanum and balsams.', 'Vanilla, woods and musk.', 'Opulent resin fragrance launched as Royale edition.', 'Amber Resin Royale focuses on dark amber, incense and sweet resins to create a rich, enveloping trail.', 'Faceted sides and deep golden juice give the bottle a jewel like appearance under the light.', 'Amber Resin Royale', 112.00, 'm_oriental_amber_resin_royale.jpg', 'm_oriental_amber_resin_royale_2.jpg', 'm_oriental_amber_resin_royale_3.jpg', 'm_oriental_amber_resin_royale_4.jpg', 'm_oriental_amber_resin_royale_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(53, 6, NULL, 'Warm tobacco and cocoa with smoky woods.', 'Tobacco leaf, cocoa and spicy citrus.', 'Tobacco, woods and tonka.', 'Vanilla, amber and smoky woods.', 'Warm tobacco oriental added to the Nova classics.', 'Tobacco Noir Nova blends tobacco leaf, cocoa and woods, giving a warm, slightly smoky scent that feels both classic and modern.', 'The bottle combines a tobacco brown tone with clean Nova lines for a refined but approachable look.', 'Tobacco Noir Nova', 115.00, 'tobacco_noir_nova.png', 'm_oriental_tobacco_noir_2.jpg', 'm_oriental_tobacco_noir_3.jpg', 'm_oriental_tobacco_noir_4.jpg', 'm_oriental_tobacco_noir_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:29:00'),
(54, 6, NULL, 'Saffron, leather and deep cedar.', 'Saffron, cardamom and citrus zest.', 'Leather, cedar and smoky woods.', 'Amber, patchouli and musk.', 'Urban leather blaze launched for bold nights out.', 'Saffron Leather Blaze layers saffron, leather and cedar into a striking, urban oriental that stands out in a crowd.', 'A matte finish and a strap inspired label nod to leather goods and crafted detail.', 'Saffron Leather Blaze', 118.00, 'm_oriental_saffron_leather_blaze.jpg', 'm_oriental_saffron_leather_blaze_2.jpg', 'm_oriental_saffron_leather_blaze_3.jpg', 'm_oriental_saffron_leather_blaze_4.jpg', 'm_oriental_saffron_leather_blaze_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(55, 6, NULL, 'Spiced amber and benzoin with warm depth.', 'Spiced citrus, pepper and resins.', 'Amber accord, benzoin and woods.', 'Labdanum, vanilla and smoky woods.', 'Forge-inspired amber created for depth and intensity.', 'Amber Forge mixes spiced amber, benzoin and woods to create a glowing, resinous scent with real depth.', 'The bottle features industrial style typography and a solid base, suggesting molten metal cooling into form.', 'Amber Forge', 113.00, 'm_oriental_amber_forge.jpg', 'm_oriental_amber_forge_2.jpg', 'm_oriental_amber_forge_3.jpg', 'm_oriental_amber_forge_4.jpg', 'm_oriental_amber_forge_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(56, 7, NULL, 'Black pepper and cedar with a masculine finish.', 'Black pepper, mandarin and cedar leaf.', 'Cedar, dry woods and spice.', 'Vetiver, patchouli and musk.', 'Core spicy-woody signature of the Nova men’s line.', 'Black Pepper Woods opens with a blast of black pepper before settling into cedar and dry woods for a bold, masculine signature.', 'The bottle wears a strong black and silver palette, echoing its straightforward, no nonsense character.', 'Black Pepper Woods', 86.00, 'black_pepper_woods.png', 'm_spicy_black_pepper_woods_2.jpg', 'm_spicy_black_pepper_woods_3.jpg', 'm_spicy_black_pepper_woods_4.jpg', 'm_spicy_black_pepper_woods_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:37:25'),
(57, 7, NULL, 'Nutmeg, cardamom and leather accord.', 'Cardamom, nutmeg and bergamot.', 'Soft leather, spices and florals.', 'Amber, woods and musk.', 'Dressed-up leather scent launched for evening wear.', 'Cardamom Leather combines cardamom, nutmeg and supple leather for a smooth, dressed up evening scent.', 'A deep brown wrap around label and metal cap borrow cues from classic leather accessories.', 'Cardamom Leather', 89.00, 'cardamom_leather.png', 'm_spicy_cardamom_leather_2.jpg', 'm_spicy_cardamom_leather_3.jpg', 'm_spicy_cardamom_leather_4.jpg', 'm_spicy_cardamom_leather_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:39:34'),
(58, 7, NULL, 'Warm spices over smoky vetiver.', 'Spicy citrus, pink pepper and ginger.', 'Vetiver, herbs and woods.', 'Patchouli, amber and musk.', 'Dynamic vetiver spice introduced to the Nova men range.', 'Spiced Vetiver Nova teams warm spices with earthy vetiver, giving a grounded yet energetic fragrance.', 'Fine vertical ridges along the bottle are inspired by the texture of vetiver roots.', 'Spiced Vetiver Nova', 91.00, 'spiced_vetiver_nova.png', 'm_spicy_vetiver_2.jpg', 'm_spicy_vetiver_3.jpg', 'm_spicy_vetiver_4.jpg', 'm_spicy_vetiver_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:38:06'),
(59, 7, NULL, 'Chili, saffron and dark woods for a bold trail.', 'Chili, saffron and pepper.', 'Dark woods, spices and resins.', 'Amber, leather nuance and smoky woods.', 'Fiery Ember Man edition created for bold personalities.', 'Crimson Ember Man mixes chili, saffron and dark woods, creating a fiery, attention grabbing scent for bold wearers.', 'The bottle’s crimson centre band and strong shoulders communicate intensity from the first glance.', 'Crimson Ember Man', 93.00, 'm_spicy_crimson_ember.jpg', 'm_spicy_crimson_ember_2.jpg', 'm_spicy_crimson_ember_3.jpg', 'm_spicy_crimson_ember_4.jpg', 'm_spicy_crimson_ember_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(60, 7, NULL, 'Coriander, ginger and amber for evening wear.', 'Fresh ginger, coriander and citrus.', 'Amber, spices and soft woods.', 'Warm woods, vanilla and musk.', 'Casual ember trail designed for relaxed evenings.', 'Ginger Ember Trail blends ginger, coriander and amber for a warm, approachable spicy scent with a casual edge.', 'A smoked gradient and simple black cap keep the design relaxed and wearable.', 'Ginger Ember Trail', 88.00, 'm_spicy_ginger_ember_trail.jpg', 'm_spicy_ginger_ember_trail_2.jpg', 'm_spicy_ginger_ember_trail_3.jpg', 'm_spicy_ginger_ember_trail_4.jpg', 'm_spicy_ginger_ember_trail_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(61, 7, NULL, 'Cedarwood, pepper and smoky spices.', 'Black pepper, cedar leaf and citrus.', 'Cedarwood, smoky spices and herbs.', 'Vetiver, amber and musk.', 'Storm-inspired spicy woods launched for outdoors lovers.', 'Cedar Spice Storm layers cedar, pepper and smoky spices for a rugged, outdoors leaning fragrance.', 'Angular shoulders and a textured side strip hint at storm clouds and rough weather.', 'Cedar Spice Storm', 92.00, 'cedar_spice_storm.png', 'm_spicy_cedar_spice_storm_2.jpg', 'm_spicy_cedar_spice_storm_3.jpg', 'm_spicy_cedar_spice_storm_4.jpg', 'm_spicy_cedar_spice_storm_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:38:50'),
(62, 7, NULL, 'Bold chili and amber with masculine depth.', 'Chili accord, citrus and saffron.', 'Spiced amber, florals and woods.', 'Vanilla, benzoin and musk.', 'Daring chili ember launched as a standout statement scent.', 'Chili Ember Nova brings chili heat into a modern amber accord for a daring but surprisingly smooth signature.', 'The bottle features a narrow vertical window of red tint, suggesting a flame contained within glass.', 'Chili Ember Nova', 95.00, 'm_spicy_chili_ember_nova.jpg', 'm_spicy_chili_ember_nova_2.jpg', 'm_spicy_chili_ember_nova_3.jpg', 'm_spicy_chili_ember_nova_4.jpg', 'm_spicy_chili_ember_nova_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(63, 7, NULL, 'Nutmeg, patchouli and dark woods.', 'Nutmeg, citrus and smoky spice.', 'Patchouli, woods and resins.', 'Amber, tonka and musk.', 'Shadowy nutmeg fragrance created for late evenings.', 'Nutmeg Shadow uses nutmeg, patchouli and woods to create a deep, quietly powerful scent with a shadowy edge.', 'A darkened glass body with minimal branding supports the idea of understated strength.', 'Nutmeg Shadow', 94.00, 'm_spicy_nutmeg_shadow.jpg', 'm_spicy_nutmeg_shadow_2.jpg', 'm_spicy_nutmeg_shadow_3.jpg', 'm_spicy_nutmeg_shadow_4.jpg', 'm_spicy_nutmeg_shadow_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(64, 7, NULL, 'Ginger and resin for warm evening scent.', 'Ginger, citrus and aromatic spice.', 'Resins, amber and woods.', 'Vanilla, benzoin and smoky woods.', 'Flame-like resin scent launched in the Ember collection.', 'Ginger Resin Flame brings resin, ginger and amber together, creating a long lasting warmth that glows on skin.', 'The bottle design plays with a warm orange amber tone and a refined, minimal label.', 'Ginger Resin Flame', 91.00, 'm_spicy_ginger_resin_flame.jpg', 'm_spicy_ginger_resin_flame_2.jpg', 'm_spicy_ginger_resin_flame_3.jpg', 'm_spicy_ginger_resin_flame_4.jpg', 'm_spicy_ginger_resin_flame_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(65, 7, NULL, 'Black pepper and earthy tones.', 'Black pepper, citrus and resin.', 'Pepper, woods and spices.', 'Amber, patchouli and musk.', 'Intense pepper fragrance introduced as a power scent.', 'Black Pepper Intenso focuses on black pepper, woods and subtle resins for a striking, high impact fragrance.', 'The glossy black bottle with bold white type is made to look as direct and intense as the scent smells.', 'Black Pepper Intenso', 96.00, 'm_spicy_black_pepper_intenso.jpg', 'm_spicy_black_pepper_intenso_2.jpg', 'm_spicy_black_pepper_intenso_3.jpg', 'm_spicy_black_pepper_intenso_4.jpg', 'm_spicy_black_pepper_intenso_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(66, 8, NULL, 'Fresh citrus-floral blend offered at a special price.', 'Citrus peel, mandarin and grapefruit.', 'Soft florals and fresh greens.', 'Light woods and clean musk.', 'Value Daylight edition released as part of the sale line.', 'Nova Daylight Sale Edition is a bright citrus floral blend designed as an easy, uplifting everyday choice at a great price.', 'The bottle keeps the classic Nova look but with a light, sunny label treatment to signal the Daylight edition.', 'Nova Daylight Sale Edition', 49.00, 'sale_daylight.jpg', 'sale_daylight_2.jpg', 'sale_daylight_3.jpg', 'sale_daylight_4.jpg', 'sale_daylight_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(67, 8, NULL, 'Soft vanilla musk, perfect everyday scent.', 'Airy aldehydes, citrus and musk.', 'Soft florals and vanilla.', 'Musk, sandalwood and cashmere.', 'Soft Aura value scent created for gentle everyday wear.', 'Soft Aura Sale Edition offers soft vanilla musk and gentle florals in a simple, wearable composition that suits almost any occasion.', 'A minimal, clean bottle with subtle silver details reflects the airy softness of the scent.', 'Soft Aura Sale Edition', 45.00, 'sale_soft_aura.jpg', 'sale_soft_aura_2.jpg', 'sale_soft_aura_3.jpg', 'sale_soft_aura_4.jpg', 'sale_soft_aura_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(68, 8, NULL, 'Sporty citrus and woods in a value bottle.', 'Sporty citrus, lime and bergamot.', 'Woods, herbs and spices.', 'Cedar, amber and musk.', 'Sprint edition launched as a sport-fresh option in the sale line.', 'Nova Sprint Sale Edition mixes sporty citrus notes with woods for a casual fragrance that works from day to weekend.', 'The bottle design uses bold Nova branding and a compact shape for easy, on the go use.', 'Nova Sprint Sale Edition', 42.00, 'sale_sprint.jpg', 'sale_sprint_2.jpg', 'sale_sprint_3.jpg', 'sale_sprint_4.jpg', 'sale_sprint_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(69, 8, NULL, 'Sweet berry and floral notes discounted.', 'Juicy berries, red fruits and citrus.', 'Floral notes, rose and peony.', 'Soft musk and light woods.', 'Playful berry bloom special release at promotional value.', 'Berry Bloom Sale combines juicy berries with floral tones, creating a cheerful, playful scent offered at special value.', 'A clear bottle with a soft berry coloured label hints at the fruity character inside.', 'Berry Bloom Sale', 39.00, 'sale_berry_bloom.jpg', 'sale_berry_bloom_2.jpg', 'sale_berry_bloom_3.jpg', 'sale_berry_bloom_4.jpg', 'sale_berry_bloom_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(70, 8, NULL, 'Warm amber and sandalwood clearance edition.', 'Ambered citrus, soft spices and woods.', 'Amber, sandalwood and warm resins.', 'Tonka, musk and vanilla.', 'Drift edition added as a comforting amber in the sale range.', 'Amber Drift Sale brings warm amber and sandalwood together in a simple, comforting fragrance with a budget friendly twist.', 'The familiar Nova silhouette is paired with a warm amber panel to mark out the Drift edition.', 'Amber Drift Sale', 44.00, 'sale_amber_drift.jpg', 'sale_amber_drift_2.jpg', 'sale_amber_drift_3.jpg', 'sale_amber_drift_4.jpg', 'sale_amber_drift_5.jpg', '2026-03-21 10:50:20', '2026-03-21 10:50:20'),
(71, 9, NULL, 'Set of four mini citrus perfumes.', 'Assorted citrus peels from the three minis.', 'Floral and herbal nuances from each blend.', 'Soft woods and musk across the set.', 'Discovery box launched as an introduction to NOVA citrus scents.', 'Citrus Discovery Gift Box contains three mini citrus fragrances, each exploring a different facet from sharp lime to juicy mandarin.', 'The set is presented in a sturdy Nova gift box with citrus inspired artwork and snug interior fit for each mini bottle.', 'Citrus Discovery Gift Box', 59.00, 'citrus_discovery_gift_box.jpeg', 'gift_citrus_discovery_box_2.jpg', 'gift_citrus_discovery_box_3.jpg', 'gift_citrus_discovery_box_4.jpg', 'gift_citrus_discovery_box_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:15:20');
INSERT INTO `products` (`product_id`, `category_id`, `brand_id`, `description`, `top_notes`, `heart_notes`, `base_notes`, `launch_info`, `scent_story`, `design_story`, `name`, `price`, `image`, `image_2`, `image_3`, `image_4`, `image_5`, `created_at`, `updated_at`) VALUES
(72, 9, NULL, 'Mixed floral minis in a keepsake box.', 'Light citrus touches from the mini collection.', 'Blend of rose, white florals and petals.', 'Amber and musk from the combined drydowns.', 'Elegant floral gift box created for easy gifting.', 'Floral Elegance Gift Box brings together a trio of floral minis ranging from soft rose to luminous white petals.', 'Delicate ribbon artwork and a pale floral palette on the box make this an effortless ready wrapped gift.', 'Floral Elegance Gift Box', 62.00, 'floral_elegance_gift_box.jpeg', 'gift_floral_elegance_box_2.jpg', 'gift_floral_elegance_box_3.jpg', 'gift_floral_elegance_box_4.jpg', 'gift_floral_elegance_box_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:19:36'),
(73, 9, NULL, 'His & Hers oriental duo set.', 'Spiced citrus facets from both orientals.', 'Amber, cocoa and woods.', 'Rich resins, vanilla and musk.', 'Duo set launched to showcase NOVA oriental favourites.', 'Oriental Duo Gift Set pairs two richer, oriental leaning scents, ideal for evening wear or special occasions.', 'The duo is housed in a slimline Nova case with separate wells for each bottle, making it easy to travel or gift.', 'Oriental Duo Gift Set', 69.00, 'oriental_duo_gift_set.jpeg', 'gift_oriental_duo_2.jpg', 'gift_oriental_duo_3.jpg', 'gift_oriental_duo_4.jpg', 'gift_oriental_duo_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:17:15'),
(74, 9, NULL, 'Three best-selling unisex minis.', 'Mixed citrus and aromatic notes from four signatures.', 'Floral, woody and musk accords.', 'Amber, woods and soft resins.', 'Signature box released as a curated NOVA mini collection.', 'Nova Signature Collection Box features four of the brand’s best selling unisex favourites in miniature form.', 'A clean, monochrome box interior and simple outer sleeve keep the focus on the iconic mini bottles inside.', 'Nova Signature Collection Box', 75.00, 'nova_signatur ... ction_box.jpeg', 'gift_signature_box_2.jpg', 'gift_signature_box_3.jpg', 'gift_signature_box_4.jpg', 'gift_signature_box_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:16:38'),
(75, 9, NULL, 'Holiday limited edition selection of spicy scents.', 'Assorted spices, citrus and herbs from the trail set.', 'Warm florals, woods and resins.', 'Amber, vanilla and musk across the chest.', 'Spice Trail chest launched as a journey through NOVA spices.', 'Spice Trail Gift Chest offers a curated selection of spicy and warm scents designed to feel like a journey through different spice markets.', 'The box uses map inspired graphics and rich colour blocking to underline the idea of travel and discovery.', 'Spice Trail Gift Chest', 72.00, 'spice_gift_chest.jpeg', 'gift_spice_trail_chest_2.jpg', 'gift_spice_trail_chest_3.jpg', 'gift_spice_trail_chest_4.jpg', 'gift_spice_trail_chest_5.jpg', '2026-03-21 10:50:20', '2026-03-23 10:19:03'),
(77, 8, NULL, 'fvf', NULL, NULL, NULL, NULL, NULL, NULL, 'ysl', 66.00, 'product_69bfc1851fe880.58650227.jpg', NULL, NULL, NULL, NULL, '2026-03-22 10:16:37', '2026-03-22 10:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_versions`
--

CREATE TABLE `product_versions` (
  `size_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sku` varchar(64) DEFAULT NULL,
  `size_ml` decimal(10,2) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `discount_percentage` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `product_versions`
--

INSERT INTO `product_versions` (`size_id`, `product_id`, `sku`, `size_ml`, `price`, `stock_qty`, `created_at`, `updated_at`, `discount_percentage`) VALUES
(1, 1, 'P1_50', 50.00, 90, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(2, 2, 'P2_50', 50.00, 97, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(3, 3, 'P3_50', 50.00, 95, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(4, 4, 'P4_50', 50.00, 92, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(5, 5, 'P5_50', 50.00, 102, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(6, 6, 'P6_50', 50.00, 48, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(7, 7, 'P7_50', 50.00, 50, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(8, 8, 'P8_50', 50.00, 46, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(9, 9, 'P9_50', 50.00, 50, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(10, 10, 'P10_50', 50.00, 48, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(11, 11, 'P11_50', 50.00, 49, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(12, 12, 'P12_50', 50.00, 47, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(13, 13, 'P13_50', 50.00, 51, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(14, 14, 'P14_50', 50.00, 48, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(15, 15, 'P15_50', 50.00, 50, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(16, 16, 'P16_50', 50.00, 55, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(17, 17, 'P17_50', 50.00, 57, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(18, 18, 'P18_50', 50.00, 53, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(19, 19, 'P19_50', 50.00, 60, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(20, 20, 'P20_50', 50.00, 55, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(21, 21, 'P21_50', 50.00, 57, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(22, 22, 'P22_50', 50.00, 62, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(23, 23, 'P23_50', 50.00, 60, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(24, 24, 'P24_50', 50.00, 56, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(25, 25, 'P25_50', 50.00, 60, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(26, 26, 'P26_50', 50.00, 62, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(27, 27, 'P27_50', 50.00, 63, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(28, 28, 'P28_50', 50.00, 64, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(29, 29, 'P29_50', 50.00, 61, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(30, 30, 'P30_50', 50.00, 62, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(31, 31, 'P31_50', 50.00, 64, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(32, 32, 'P32_50', 50.00, 67, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(33, 33, 'P33_50', 50.00, 62, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(34, 34, 'P34_50', 50.00, 64, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(35, 35, 'P35_50', 50.00, 63, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(36, 36, 'P36_50', 50.00, 52, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(37, 37, 'P37_50', 50.00, 55, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(38, 38, 'P38_50', 50.00, 50, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(39, 39, 'P39_50', 50.00, 51, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(40, 40, 'P40_50', 50.00, 54, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(41, 41, 'P41_50', 50.00, 55, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(42, 42, 'P42_50', 50.00, 53, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(43, 43, 'P43_50', 50.00, 57, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(44, 44, 'P44_50', 50.00, 55, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(45, 45, 'P45_50', 50.00, 53, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(46, 46, 'P46_50', 50.00, 69, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(47, 47, 'P47_50', 50.00, 67, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(48, 48, 'P48_50', 50.00, 68, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(49, 49, 'P49_50', 50.00, 71, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(50, 50, 'P50_50', 50.00, 69, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(51, 51, 'P51_50', 50.00, 76, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(52, 52, 'P52_50', 50.00, 78, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(53, 53, 'P53_50', 50.00, 81, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(54, 54, 'P54_50', 50.00, 83, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(55, 55, 'P55_50', 50.00, 79, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(56, 56, 'P56_50', 50.00, 60, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(57, 57, 'P57_50', 50.00, 62, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(58, 58, 'P58_50', 50.00, 64, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(59, 59, 'P59_50', 50.00, 65, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(60, 60, 'P60_50', 50.00, 62, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(61, 61, 'P61_50', 50.00, 64, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(62, 62, 'P62_50', 50.00, 67, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(63, 63, 'P63_50', 50.00, 66, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(64, 64, 'P64_50', 50.00, 64, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(65, 65, 'P65_50', 50.00, 67, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(66, 66, 'P66_50', 50.00, 34, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(67, 67, 'P67_50', 50.00, 32, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(68, 68, 'P68_50', 50.00, 29, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(69, 69, 'P69_50', 50.00, 27, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(70, 70, 'P70_50', 50.00, 31, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(71, 71, 'P71_50', 50.00, 41, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(72, 72, 'P72_50', 50.00, 43, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(73, 73, 'P73_50', 50.00, 48, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(74, 74, 'P74_50', 50.00, 53, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(75, 75, 'P75_50', 50.00, 50, 15, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(128, 1, 'P1_100', 100.00, 129, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(129, 2, 'P2_100', 100.00, 139, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(130, 3, 'P3_100', 100.00, 135, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(131, 4, 'P4_100', 100.00, 132, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(132, 5, 'P5_100', 100.00, 145, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(133, 6, 'P6_100', 100.00, 69, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(134, 7, 'P7_100', 100.00, 72, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(135, 8, 'P8_100', 100.00, 65, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(136, 9, 'P9_100', 100.00, 71, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(137, 10, 'P10_100', 100.00, 68, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(138, 11, 'P11_100', 100.00, 70, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(139, 12, 'P12_100', 100.00, 67, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(140, 13, 'P13_100', 100.00, 73, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(141, 14, 'P14_100', 100.00, 69, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(142, 15, 'P15_100', 100.00, 72, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(143, 16, 'P16_100', 100.00, 79, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(144, 17, 'P17_100', 100.00, 82, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(145, 18, 'P18_100', 100.00, 76, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(146, 19, 'P19_100', 100.00, 85, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(147, 20, 'P20_100', 100.00, 78, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(148, 21, 'P21_100', 100.00, 82, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(149, 22, 'P22_100', 100.00, 88, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(150, 23, 'P23_100', 100.00, 85, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(151, 24, 'P24_100', 100.00, 80, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(152, 25, 'P25_100', 100.00, 86, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(153, 26, 'P26_100', 100.00, 88, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(154, 27, 'P27_100', 100.00, 90, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(155, 28, 'P28_100', 100.00, 92, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(156, 29, 'P29_100', 100.00, 87, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(157, 30, 'P30_100', 100.00, 89, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(158, 31, 'P31_100', 100.00, 91, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(159, 32, 'P32_100', 100.00, 95, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(160, 33, 'P33_100', 100.00, 89, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(161, 34, 'P34_100', 100.00, 92, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(162, 35, 'P35_100', 100.00, 90, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(163, 36, 'P36_100', 100.00, 74, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(164, 37, 'P37_100', 100.00, 79, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(165, 38, 'P38_100', 100.00, 72, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(166, 39, 'P39_100', 100.00, 73, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(167, 40, 'P40_100', 100.00, 77, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(168, 41, 'P41_100', 100.00, 78, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(169, 42, 'P42_100', 100.00, 76, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(170, 43, 'P43_100', 100.00, 81, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(171, 44, 'P44_100', 100.00, 79, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(172, 45, 'P45_100', 100.00, 75, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(173, 46, 'P46_100', 100.00, 99, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(174, 47, 'P47_100', 100.00, 95, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(175, 48, 'P48_100', 100.00, 97, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(176, 49, 'P49_100', 100.00, 102, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(177, 50, 'P50_100', 100.00, 98, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(178, 51, 'P51_100', 100.00, 109, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(179, 52, 'P52_100', 100.00, 112, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(180, 53, 'P53_100', 100.00, 115, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(181, 54, 'P54_100', 100.00, 118, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(182, 55, 'P55_100', 100.00, 113, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(183, 56, 'P56_100', 100.00, 86, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(184, 57, 'P57_100', 100.00, 89, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(185, 58, 'P58_100', 100.00, 91, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(186, 59, 'P59_100', 100.00, 93, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(187, 60, 'P60_100', 100.00, 88, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(188, 61, 'P61_100', 100.00, 92, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(189, 62, 'P62_100', 100.00, 95, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(190, 63, 'P63_100', 100.00, 94, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(191, 64, 'P64_100', 100.00, 91, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(192, 65, 'P65_100', 100.00, 96, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(193, 66, 'P66_100', 100.00, 49, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(194, 67, 'P67_100', 100.00, 45, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(195, 68, 'P68_100', 100.00, 42, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(196, 69, 'P69_100', 100.00, 39, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(197, 70, 'P70_100', 100.00, 44, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(198, 71, 'P71_100', 100.00, 59, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(199, 72, 'P72_100', 100.00, 62, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(200, 73, 'P73_100', 100.00, 69, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(201, 74, 'P74_100', 100.00, 75, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(202, 75, 'P75_100', 100.00, 72, 20, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(255, 1, 'P1_150', 150.00, 168, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(256, 2, 'P2_150', 150.00, 181, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(257, 3, 'P3_150', 150.00, 176, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(258, 4, 'P4_150', 150.00, 172, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(259, 5, 'P5_150', 150.00, 189, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(260, 6, 'P6_150', 150.00, 90, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(261, 7, 'P7_150', 150.00, 94, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(262, 8, 'P8_150', 150.00, 85, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(263, 9, 'P9_150', 150.00, 92, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(264, 10, 'P10_150', 150.00, 88, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(265, 11, 'P11_150', 150.00, 91, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(266, 12, 'P12_150', 150.00, 87, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(267, 13, 'P13_150', 150.00, 95, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(268, 14, 'P14_150', 150.00, 90, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(269, 15, 'P15_150', 150.00, 94, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(270, 16, 'P16_150', 150.00, 103, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(271, 17, 'P17_150', 150.00, 107, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(272, 18, 'P18_150', 150.00, 99, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(273, 19, 'P19_150', 150.00, 111, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(274, 20, 'P20_150', 150.00, 101, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(275, 21, 'P21_150', 150.00, 107, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(276, 22, 'P22_150', 150.00, 114, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(277, 23, 'P23_150', 150.00, 111, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(278, 24, 'P24_150', 150.00, 104, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(279, 25, 'P25_150', 150.00, 112, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(280, 26, 'P26_150', 150.00, 114, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(281, 27, 'P27_150', 150.00, 117, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(282, 28, 'P28_150', 150.00, 120, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(283, 29, 'P29_150', 150.00, 113, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(284, 30, 'P30_150', 150.00, 116, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(285, 31, 'P31_150', 150.00, 118, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(286, 32, 'P32_150', 150.00, 124, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(287, 33, 'P33_150', 150.00, 116, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(288, 34, 'P34_150', 150.00, 120, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(289, 35, 'P35_150', 150.00, 117, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(290, 36, 'P36_150', 150.00, 96, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(291, 37, 'P37_150', 150.00, 103, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(292, 38, 'P38_150', 150.00, 94, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(293, 39, 'P39_150', 150.00, 95, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(294, 40, 'P40_150', 150.00, 100, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(295, 41, 'P41_150', 150.00, 101, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(296, 42, 'P42_150', 150.00, 99, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(297, 43, 'P43_150', 150.00, 105, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(298, 44, 'P44_150', 150.00, 103, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(299, 45, 'P45_150', 150.00, 98, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(300, 46, 'P46_150', 150.00, 129, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(301, 47, 'P47_150', 150.00, 124, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(302, 48, 'P48_150', 150.00, 126, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(303, 49, 'P49_150', 150.00, 133, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(304, 50, 'P50_150', 150.00, 127, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(305, 51, 'P51_150', 150.00, 142, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(306, 52, 'P52_150', 150.00, 146, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(307, 53, 'P53_150', 150.00, 150, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(308, 54, 'P54_150', 150.00, 153, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(309, 55, 'P55_150', 150.00, 147, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(310, 56, 'P56_150', 150.00, 112, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(311, 57, 'P57_150', 150.00, 116, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(312, 58, 'P58_150', 150.00, 118, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(313, 59, 'P59_150', 150.00, 121, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(314, 60, 'P60_150', 150.00, 114, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(315, 61, 'P61_150', 150.00, 120, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(316, 62, 'P62_150', 150.00, 124, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(317, 63, 'P63_150', 150.00, 122, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(318, 64, 'P64_150', 150.00, 118, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(319, 65, 'P65_150', 150.00, 125, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(320, 66, 'P66_150', 150.00, 64, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(321, 67, 'P67_150', 150.00, 59, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(322, 68, 'P68_150', 150.00, 55, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(323, 69, 'P69_150', 150.00, 51, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(324, 70, 'P70_150', 150.00, 57, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(325, 71, 'P71_150', 150.00, 77, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(326, 72, 'P72_150', 150.00, 81, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(327, 73, 'P73_150', 150.00, 90, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(328, 74, 'P74_150', 150.00, 98, 10, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(329, 75, 'P75_150', 150.00, 94, 3, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(382, 1, 'P1_200', 200.00, 206, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(383, 2, 'P2_200', 200.00, 222, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(384, 3, 'P3_200', 200.00, 216, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(385, 4, 'P4_200', 200.00, 211, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(386, 5, 'P5_200', 200.00, 232, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(387, 6, 'P6_200', 200.00, 110, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(388, 7, 'P7_200', 200.00, 115, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(389, 8, 'P8_200', 200.00, 104, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(390, 9, 'P9_200', 200.00, 114, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(391, 10, 'P10_200', 200.00, 109, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(392, 11, 'P11_200', 200.00, 112, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(393, 12, 'P12_200', 200.00, 107, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(394, 13, 'P13_200', 200.00, 117, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(395, 14, 'P14_200', 200.00, 110, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(396, 15, 'P15_200', 200.00, 115, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(397, 16, 'P16_200', 200.00, 126, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(398, 17, 'P17_200', 200.00, 131, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(399, 18, 'P18_200', 200.00, 122, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(400, 19, 'P19_200', 200.00, 136, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(401, 20, 'P20_200', 200.00, 125, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(402, 21, 'P21_200', 200.00, 131, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(403, 22, 'P22_200', 200.00, 141, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(404, 23, 'P23_200', 200.00, 136, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(405, 24, 'P24_200', 200.00, 128, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(406, 25, 'P25_200', 200.00, 138, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(407, 26, 'P26_200', 200.00, 141, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(408, 27, 'P27_200', 200.00, 144, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(409, 28, 'P28_200', 200.00, 147, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(410, 29, 'P29_200', 200.00, 139, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(411, 30, 'P30_200', 200.00, 142, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(412, 31, 'P31_200', 200.00, 146, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(413, 32, 'P32_200', 200.00, 152, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(414, 33, 'P33_200', 200.00, 142, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(415, 34, 'P34_200', 200.00, 147, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(416, 35, 'P35_200', 200.00, 144, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(417, 36, 'P36_200', 200.00, 118, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(418, 37, 'P37_200', 200.00, 126, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(419, 38, 'P38_200', 200.00, 115, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(420, 39, 'P39_200', 200.00, 117, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(421, 40, 'P40_200', 200.00, 123, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(422, 41, 'P41_200', 200.00, 125, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(423, 42, 'P42_200', 200.00, 122, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(424, 43, 'P43_200', 200.00, 130, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(425, 44, 'P44_200', 200.00, 126, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(426, 45, 'P45_200', 200.00, 120, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(427, 46, 'P46_200', 200.00, 158, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(428, 47, 'P47_200', 200.00, 152, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(429, 48, 'P48_200', 200.00, 155, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(430, 49, 'P49_200', 200.00, 163, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(431, 50, 'P50_200', 200.00, 157, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(432, 51, 'P51_200', 200.00, 174, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(433, 52, 'P52_200', 200.00, 179, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(434, 53, 'P53_200', 200.00, 184, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(435, 54, 'P54_200', 200.00, 189, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(436, 55, 'P55_200', 200.00, 181, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(437, 56, 'P56_200', 200.00, 138, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(438, 57, 'P57_200', 200.00, 142, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(439, 58, 'P58_200', 200.00, 146, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(440, 59, 'P59_200', 200.00, 149, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(441, 60, 'P60_200', 200.00, 141, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(442, 61, 'P61_200', 200.00, 147, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(443, 62, 'P62_200', 200.00, 152, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(444, 63, 'P63_200', 200.00, 150, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(445, 64, 'P64_200', 200.00, 146, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(446, 65, 'P65_200', 200.00, 154, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(447, 66, 'P66_200', 200.00, 78, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(448, 67, 'P67_200', 200.00, 72, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(449, 68, 'P68_200', 200.00, 67, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(450, 69, 'P69_200', 200.00, 62, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(451, 70, 'P70_200', 200.00, 70, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(452, 71, 'P71_200', 200.00, 94, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(453, 72, 'P72_200', 200.00, 99, 0, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(454, 73, 'P73_200', 200.00, 110, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(455, 74, 'P74_200', 200.00, 120, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00),
(456, 75, 'P75_200', 200.00, 115, 5, '2026-03-21 10:50:20', '2026-03-21 10:50:20', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `promotion_name` varchar(100) NOT NULL,
  `promo_code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','scheduled','expired') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(84) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone_number`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Site Admin', 'admin@nova.com', '$2b$12$aIKpppoORlMZIFAbh09CUe4S1VSpgB52.STGDlRzyo.G8q.FyW3tC', '0000000000', 'admin', '2026-03-21 10:50:21', '2026-03-21 10:50:21'),
(2, 'testuser', 'test@example.com', '$2y$10$42kdSajLnj3ovkaNr1cKoOE8BsHZYELIL/zshYQUQ02hrp9.zMw2a', NULL, 'customer', '2026-03-21 11:56:20', '2026-03-21 11:56:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`basket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `basket_items`
--
ALTER TABLE `basket_items`
  ADD PRIMARY KEY (`basket_item_id`),
  ADD UNIQUE KEY `basket_id` (`basket_id`,`size_id`),
  ADD KEY `fk_basket_items_size` (`size_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_items_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `product_versions`
--
ALTER TABLE `product_versions`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `fk_versions_product` (`product_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`user_id`),
  ADD KEY `fk_review_users` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `basket`
--
ALTER TABLE `basket`
  MODIFY `basket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `basket_items`
--
ALTER TABLE `basket_items`
  MODIFY `basket_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `product_versions`
--
ALTER TABLE `product_versions`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `basket_items`
--
ALTER TABLE `basket_items`
  ADD CONSTRAINT `fk_basket_items_basket` FOREIGN KEY (`basket_id`) REFERENCES `basket` (`basket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_basket_items_size` FOREIGN KEY (`size_id`) REFERENCES `product_versions` (`size_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`size_id`) REFERENCES `inventory` (`size_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `inventory` (`size_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_versions`
--
ALTER TABLE `product_versions`
  ADD CONSTRAINT `fk_versions_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
