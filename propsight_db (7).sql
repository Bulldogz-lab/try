-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2026 at 10:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `propsight_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` int(10) UNSIGNED NOT NULL,
  `property_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `icon` varchar(30) NOT NULL DEFAULT 'security',
  `status` enum('available','unavailable','maintenance') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenity_id`, `property_id`, `name`, `icon`, `status`, `created_at`) VALUES
(2, 14, 'Free Wifi', 'wifi', 'available', '2026-03-22 07:34:38'),
(5, 14, 'Free Shower', 'shower', 'unavailable', '2026-03-22 07:41:19'),
(6, 15, 'Water', 'water', 'available', '2026-03-22 07:52:57'),
(8, 15, 'Rooftop', 'rooftop', 'available', '2026-03-22 08:20:56');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_dates`
--

CREATE TABLE `blocked_dates` (
  `id` int(10) UNSIGNED NOT NULL,
  `blocked_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `guests` int(3) NOT NULL DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `checkin_status` enum('pending','done') NOT NULL DEFAULT 'pending',
  `checkout_status` enum('pending','done') NOT NULL DEFAULT 'pending',
  `checkin_actual` datetime DEFAULT NULL,
  `checkout_actual` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_ref` varchar(100) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `unit_id`, `tenant_id`, `user_id`, `checkin_date`, `checkout_date`, `guests`, `total_amount`, `status`, `special_requests`, `created_at`, `updated_at`, `checkin_status`, `checkout_status`, `checkin_actual`, `checkout_actual`, `payment_method`, `paid_at`, `payment_ref`, `payment_notes`) VALUES
(1, 11, 1, 14, '2026-03-23', '2026-03-26', 2, 150000.00, 'cancelled', NULL, '2026-03-22 15:00:36', '2026-03-22 15:02:34', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 13, 1, 14, '2026-03-23', '2026-03-26', 2, 450000.00, 'completed', NULL, '2026-03-22 15:01:54', '2026-03-22 16:20:06', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 14, 1, 14, '2026-03-23', '2026-03-26', 2, 75000.00, 'cancelled', NULL, '2026-03-22 15:02:23', '2026-03-22 15:02:40', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 16, 1, 14, '2026-03-23', '2026-03-26', 2, 75000.00, 'cancelled', NULL, '2026-03-22 15:08:54', '2026-03-22 15:28:13', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 11, 1, 14, '2026-03-23', '2026-03-26', 2, 150000.00, 'completed', NULL, '2026-03-22 15:26:00', '2026-03-22 15:28:42', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 14, 1, 14, '2026-03-23', '2026-03-26', 2, 75000.00, 'cancelled', NULL, '2026-03-22 15:27:46', '2026-03-22 15:28:18', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 14, 1, 14, '2026-03-23', '2026-03-26', 2, 75000.00, 'cancelled', NULL, '2026-03-22 15:32:20', '2026-03-22 15:40:16', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 15, 1, 14, '2026-03-23', '2026-03-26', 2, 75000.00, 'cancelled', NULL, '2026-03-22 15:32:48', '2026-03-22 15:39:50', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 18, 1, 14, '2026-03-23', '2026-03-26', 2, 7666665.00, 'cancelled', NULL, '2026-03-22 15:38:43', '2026-03-22 15:39:52', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 11, 1, 14, '2026-04-23', '2026-04-26', 2, 150000.00, 'cancelled', NULL, '2026-03-22 15:39:17', '2026-03-22 15:40:01', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 17, 1, 14, '2026-03-23', '2026-03-26', 2, 0.00, 'cancelled', NULL, '2026-03-22 15:39:29', '2026-03-22 15:40:05', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 11, 2, 12, '2026-03-26', '2026-03-29', 2, 150000.00, 'confirmed', NULL, '2026-03-25 08:46:00', '2026-03-25 08:48:22', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `expense_category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `property_id`, `expense_category`, `description`, `amount`, `expense_date`, `recorded_by`, `unit_id`) VALUES
(1, 15, 'Utilities', 'Aircon', 5000.00, '2026-03-25', 11, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `financial_records`
--

CREATE TABLE `financial_records` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `revenue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `maintenance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `utilities` decimal(15,2) NOT NULL DEFAULT 0.00,
  `salaries` decimal(15,2) NOT NULL DEFAULT 0.00,
  `admin` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_reports`
--

CREATE TABLE `financial_reports` (
  `report_id` int(11) NOT NULL,
  `report_month` int(11) DEFAULT NULL,
  `report_year` int(11) DEFAULT NULL,
  `total_income` decimal(12,2) DEFAULT NULL,
  `total_expenses` decimal(12,2) DEFAULT NULL,
  `net_profit` decimal(12,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(30) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `issued_date` date NOT NULL,
  `due_date` date NOT NULL,
  `items` varchar(255) NOT NULL,
  `total` decimal(12,2) NOT NULL CHECK (`total` >= 0),
  `status` enum('Pending','Paid','Overdue') NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `issue_description` text DEFAULT NULL,
  `request_status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `request_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('paid','pending','late') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `property_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `address`, `city`, `state`, `zip`, `status`, `property_type`, `created_at`) VALUES
(14, 'Casa De Primera', 'Tulubhan Barangay Manoc-manoc, Boracay Island, Malay, Aklan 5608', 'Boracay Island, Malay', 'Aklan', '5608', 'Active', 'Residential', '2026-03-20 18:05:05'),
(15, 'Casa Camilla Beachfront', 'Station 3 Angol Barangay Manoc-manoc, Boracay Island, Malay, Aklan 5608', 'Boracay Island, Malay', 'Aklan', '5608', 'Active', 'Residential', '2026-03-22 07:38:11'),
(17, 'Roxon Residences', 'Station 3 Ambulong Barangay Manoc-manoc, Boracay Island, Malay, Aklan 5608', 'Boracay Island, Malay', 'Aklan', '5608', 'Active', 'Residential', '2026-03-22 14:01:16'),
(18, 'Ocean Garden Villas', 'Newcoast Barangay Yapak, Boracay Island, Malay, Aklan 5608', 'Boracay Island, Malay', 'Aklan', '5608', 'Active', 'Residential', '2026-03-22 14:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `tenant_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`tenant_id`, `full_name`, `phone`, `email`, `move_in_date`, `created_at`) VALUES
(1, 'John Michael Arcido', NULL, 'joda.arcido.ui@phinmaed.com', '2026-03-23', '2026-03-22 13:04:43'),
(2, 'Marlon Pogi', NULL, 'marlonvillegas86@gmail.com', '2026-03-26', '2026-03-25 08:46:00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `type` enum('Income','Expense') NOT NULL,
  `amount` decimal(12,2) NOT NULL CHECK (`amount` >= 0),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_categories`
--

CREATE TABLE `transaction_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_categories`
--

INSERT INTO `transaction_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Rent', '2026-03-24 11:19:09'),
(2, 'Deposit', '2026-03-24 11:19:09'),
(3, 'Maintenance', '2026-03-24 11:19:09'),
(4, 'Utilities', '2026-03-24 11:19:09'),
(5, 'Insurance', '2026-03-24 11:19:09'),
(6, 'Taxes', '2026-03-24 11:19:09'),
(7, 'Management Fee', '2026-03-24 11:19:09'),
(8, 'Other', '2026-03-24 11:19:09');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `unit_number` varchar(50) DEFAULT NULL,
  `unit_name` varchar(100) DEFAULT NULL,
  `unit_type` varchar(50) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `rent_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('occupied','vacant','maintenance') DEFAULT 'vacant',
  `tenant_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `property_id`, `unit_number`, `unit_name`, `unit_type`, `floor`, `rent_amount`, `status`, `tenant_name`, `description`) VALUES
(11, 15, 'Unit 10', '', 'Studio', 0, 50000.00, 'occupied', '', NULL),
(13, 14, 'Unit A18', '', 'Penthouse', 10, 150000.00, 'vacant', '', ''),
(14, 18, 'Unit A', '', 'Loft', 5, 25000.00, 'vacant', '', ''),
(15, 18, 'Unit H', '', '3 Bedroom', 4, 25000.00, 'vacant', '', ''),
(16, 17, 'Unit 5', '', '2 Bedroom', 2, 25000.00, 'vacant', '', ''),
(17, 18, '', '', '1 Bedroom', 6, 0.00, 'vacant', '', ''),
(18, 14, '', '', 'Penthouse', 10, 2555555.00, 'vacant', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `unit_amenities`
--

CREATE TABLE `unit_amenities` (
  `id` int(10) UNSIGNED NOT NULL,
  `unit_id` int(11) NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit_amenities`
--

INSERT INTO `unit_amenities` (`id`, `unit_id`, `amenity_id`) VALUES
(7, 11, 6),
(8, 11, 8),
(9, 13, 2),
(10, 18, 2);

-- --------------------------------------------------------

--
-- Table structure for table `unit_images`
--

CREATE TABLE `unit_images` (
  `image_id` int(10) UNSIGNED NOT NULL,
  `unit_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `sort_order` tinyint(3) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit_images`
--

INSERT INTO `unit_images` (`image_id`, `unit_id`, `image_path`, `sort_order`, `created_at`) VALUES
(7, 11, 'uploads/units/11/unit_69bfaa4b4e6043.93688765.jpg', 0, '2026-03-22 08:37:31'),
(9, 13, 'uploads/units/13/unit_69bff4ac7481e4.66003486.jpg', 0, '2026-03-22 13:54:52'),
(10, 14, 'uploads/units/14/unit_69bff6d0afd442.12060825.jpg', 0, '2026-03-22 14:04:00'),
(11, 15, 'uploads/units/15/unit_69bff7440c81f1.43632377.jpg', 0, '2026-03-22 14:05:56'),
(12, 16, 'uploads/units/16/unit_69bff769d92183.90596974.jpg', 0, '2026-03-22 14:06:33'),
(13, 17, 'uploads/units/17/unit_69bff795600a53.56970741.jpg', 0, '2026-03-22 14:07:17'),
(14, 18, 'uploads/units/18/unit_69bff90ce31088.28439973.jpg', 0, '2026-03-22 14:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `birthday` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female','Prefer not to say') DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `verification_status` enum('Not Verified','Verified') NOT NULL DEFAULT 'Not Verified',
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `nationality`, `birthday`, `gender`, `password`, `created_at`, `role`, `verification_status`, `login_attempts`, `last_attempt`, `is_locked`, `locked_until`, `is_blacklisted`, `is_active`, `last_login`) VALUES
(2, 'Jr', 'Marticio', 'jr@gmail.com', '09876543211', 'Filipino', '2004-09-08', 'Male', '$2y$10$SLpufN.25x34BMQG0l.Wou8NQGxCPbxv6T0L0G2kqhrfqByh/rQXa', '2026-03-18 14:34:21', 'user', 'Not Verified', 0, '2026-03-21 21:05:33', 0, NULL, 0, 1, NULL),
(4, 'Myra', 'Jonson', 'myrajonson@gmail.com', '09876543210', NULL, NULL, NULL, '$2y$10$wXIDzETTGkxbTwkIbBaKDOzxIUvFq2DSwEu2AqILaC8rWofz1pZXa', '2026-03-20 11:30:54', 'admin', 'Not Verified', 0, '2026-03-21 21:10:44', 0, NULL, 0, 1, NULL),
(6, 'Sonny', 'Wagas', 'sonny@phinmaed.com', '09324123512', NULL, NULL, NULL, '$2y$10$/Ry3y5remClD8wUdj8/zIuf3CPp8vJqh.3KTFCHrJsu5ZVpvGPazi', '2026-03-21 15:28:14', 'user', 'Not Verified', 0, NULL, 0, NULL, 1, 1, NULL),
(11, 'Marlon', 'Garcia', 'marlonvillegas00@gmail.com', '09497680942', NULL, NULL, NULL, '$2y$10$OifSfqsYgGUCM3s2BWuNCesGD30kEDy35r0gYBjVMVy6zxDmzua8W', '2026-03-21 15:45:48', 'admin', 'Not Verified', 1, '2026-03-23 22:35:44', 0, NULL, 0, 1, NULL),
(12, 'Marlon', 'Pogi', 'marlonvillegas86@gmail.com', '09497695123', NULL, NULL, NULL, '$2y$10$hlRbO5WSPVIa00gygt.uyemTYt6Tx12yb3qejqdWmPu/42hJYEZPO', '2026-03-21 15:48:32', 'user', 'Not Verified', 0, NULL, 0, NULL, 0, 1, NULL),
(13, 'Sean', 'Peniero', 'sean@gmail.com', '09235612571', NULL, NULL, NULL, '$2y$10$RKfNhMcgrvbBLghTdvbgSuyf4qUQq.8xdjaZY3cnbpNUPSqcbmTyS', '2026-03-22 05:34:17', 'user', 'Not Verified', 0, NULL, 0, NULL, 0, 1, NULL),
(14, 'John Michael', 'Arcido', 'joda.arcido.ui@phinmaed.com', '09497695099', 'Filipino', '', 'Female', '$2y$10$.oxDJrUYsLqb2y9yOipeWeAB7rKpVgX9U7.YbYIi9p4XoWWb4BYOC', '2026-03-22 11:14:33', 'user', 'Not Verified', 0, NULL, 0, NULL, 0, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`),
  ADD KEY `idx_property` (`property_id`);

--
-- Indexes for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_blocked_date` (`blocked_date`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_booking_unit` (`unit_id`),
  ADD KEY `fk_booking_tenant` (`tenant_id`),
  ADD KEY `fk_booking_user` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `financial_records`
--
ALTER TABLE `financial_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_record` (`property_id`,`month`,`year`),
  ADD KEY `idx_year_month` (`year`,`month`),
  ADD KEY `idx_property_id` (`property_id`);

--
-- Indexes for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_invoice_no` (`invoice_no`),
  ADD KEY `idx_tenant_id` (`tenant_id`),
  ADD KEY `idx_unit_id` (`unit_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_issued_date` (`issued_date`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_year_status` (`issued_date`,`status`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`tenant_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reference_no` (`reference_no`),
  ADD KEY `idx_transaction_date` (`transaction_date`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_year_type` (`transaction_date`,`type`);

--
-- Indexes for table `transaction_categories`
--
ALTER TABLE `transaction_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_category_name` (`name`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `unit_amenities`
--
ALTER TABLE `unit_amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_unit_amenity` (`unit_id`,`amenity_id`),
  ADD KEY `fk_ua_unit` (`unit_id`),
  ADD KEY `fk_ua_amenity` (`amenity_id`);

--
-- Indexes for table `unit_images`
--
ALTER TABLE `unit_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `idx_unit` (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `financial_records`
--
ALTER TABLE `financial_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_categories`
--
ALTER TABLE `transaction_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `unit_amenities`
--
ALTER TABLE `unit_amenities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `unit_images`
--
ALTER TABLE `unit_images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `amenities`
--
ALTER TABLE `amenities`
  ADD CONSTRAINT `fk_amenities_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD CONSTRAINT `fk_blocked_dates_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `financial_records`
--
ALTER TABLE `financial_records`
  ADD CONSTRAINT `financial_records_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoice_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoice_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`),
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `booking_id` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_txn_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `unit_amenities`
--
ALTER TABLE `unit_amenities`
  ADD CONSTRAINT `fk_ua_amenity` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ua_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `unit_images`
--
ALTER TABLE `unit_images`
  ADD CONSTRAINT `fk_unit_images` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
