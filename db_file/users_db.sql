-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 02:11 AM
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
-- Database: `users_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_access`
--

CREATE TABLE `tbl_access` (
  `oAccessid` int(10) UNSIGNED NOT NULL,
  `oUserid` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `oModuleid` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `oMain` int(1) UNSIGNED DEFAULT 0,
  `oAdd` int(1) UNSIGNED DEFAULT 0,
  `oEdit` int(1) UNSIGNED DEFAULT 0,
  `oView` int(1) UNSIGNED DEFAULT 0,
  `oSave` int(1) UNSIGNED DEFAULT 0,
  `oPost` int(1) UNSIGNED DEFAULT 0,
  `oCancel` int(1) UNSIGNED DEFAULT 0,
  `oPrint` int(1) UNSIGNED DEFAULT 0,
  `oDiscount` int(1) UNSIGNED DEFAULT 0,
  `oSend` int(1) UNSIGNED DEFAULT 0,
  `oSalesassistant` int(1) UNSIGNED DEFAULT 0,
  `oSupervisor` int(1) UNSIGNED DEFAULT 0,
  `oManager` int(1) UNSIGNED DEFAULT 0,
  `oAudit` int(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_access`
--

INSERT INTO `tbl_access` (`oAccessid`, `oUserid`, `oModuleid`, `oMain`, `oAdd`, `oEdit`, `oView`, `oSave`, `oPost`, `oCancel`, `oPrint`, `oDiscount`, `oSend`, `oSalesassistant`, `oSupervisor`, `oManager`, `oAudit`) VALUES
(1, 16, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 16, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 16, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 16, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(5, 16, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(6, 15, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(7, 19, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(8, 19, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(9, 16, 16, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(10, 23, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(11, 24, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 25, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(13, 28, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(14, 29, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(15, 30, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(16, 31, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(17, 32, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 33, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 19, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(20, 34, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(21, 35, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(22, 36, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(23, 36, 16, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item`
--

CREATE TABLE `tbl_item` (
  `item_id` int(11) NOT NULL,
  `item_locator` varchar(150) NOT NULL,
  `item_code` varchar(125) NOT NULL,
  `item_barcode` varchar(125) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_desc` varchar(125) NOT NULL,
  `item_no` int(11) NOT NULL,
  `item_uom` varchar(55) NOT NULL,
  `item_first_loc` tinyint(1) NOT NULL DEFAULT 0,
  `item_sec_loc` tinyint(1) NOT NULL DEFAULT 0,
  `item_stock` int(125) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item`
--

INSERT INTO `tbl_item` (`item_id`, `item_locator`, `item_code`, `item_barcode`, `created_at`, `item_desc`, `item_no`, `item_uom`, `item_first_loc`, `item_sec_loc`, `item_stock`) VALUES
(1, ' 5C30A6 ', '1301-02', '1301-02', '2026-11-23 00:53:37', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 0, 0, 0),
(2, ' 5C31A7 ', '1301-04', '1301-04', '2026-11-23 00:53:37', 'GI PIPE EAGLES S-40(LG)1-1/2#32MM x6M 180/BNDL', 8093, 'LENGTH', 0, 0, 28),
(3, '5C32A8', '1301-04', '1301-04', '2026-11-23 03:07:02', 'DESERT EAGLE S-40(LG)1-1/2#32MM x6M 180', 7777, 'LENGTH', 0, 0, 17);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_details`
--

CREATE TABLE `tbl_item_details` (
  `item_id` int(11) NOT NULL,
  `item_locator` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` varchar(50) NOT NULL,
  `item_code` varchar(125) NOT NULL,
  `item_barcode` varchar(125) NOT NULL,
  `item_desc` varchar(125) NOT NULL,
  `item_no` int(11) NOT NULL,
  `item_uom` varchar(50) NOT NULL,
  `item_first_loc` tinyint(1) NOT NULL DEFAULT 0,
  `item_sec_loc` tinyint(1) NOT NULL DEFAULT 0,
  `item_stock` int(125) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_details`
--

INSERT INTO `tbl_item_details` (`item_id`, `item_locator`, `created_at`, `added_by`, `item_code`, `item_barcode`, `item_desc`, `item_no`, `item_uom`, `item_first_loc`, `item_sec_loc`, `item_stock`, `remarks`) VALUES
(23, ' 5C30A6 ', '2026-09-15 07:20:54', 'Meycko', '1301-02', '1301-02', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 1, 0, 0, 'test4'),
(26, ' 5C30A6 ', '2026-09-15 07:49:03', 'Meycko', '1301-02', '1301-02', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 0, 1, 0, 'test3'),
(27, ' 5C30A6 ', '2026-09-15 07:50:21', 'Meycko', '1301-02', '1301-02', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 1, 0, 0, 'test2'),
(31, '5C32A8', '2026-11-23 03:33:16', 'Meycko', '1301-04', '1301-04', 'DESERT EAGLE S-40(LG)1-1/2#32MM x6M 180', 7777, 'LENGTH', 1, 0, 17, 'test5'),
(32, '5C30A6', '2026-11-23 03:36:20', 'Meycko', '1301-04', '1301-04', 'GI PIPE EAGLES S-40(LG)1-1/2#32MM x6M 180/BNDL', 8093, 'LENGTH', 0, 1, 28, 'test6'),
(33, '5C30A6', '2026-11-23 03:43:40', 'Meycko', '1301-02', '1301-02', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 1, 0, 0, 'Test7'),
(34, '5C32A8', '2026-11-23 05:13:08', 'ivan', '1301-04', '1301-04', 'DESERT EAGLE S-40(LG)1-1/2#32MM x6M 180', 7777, 'LENGTH', 0, 1, 17, 'ivan test'),
(35, '5C30A6', '2026-11-23 05:13:45', 'ivan', '1301-02', '1301-02', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 1, 0, 0, 'test2 ivan'),
(36, '5C30A6', '2026-11-23 08:14:51', 'Meycko', '1301-02', '1301-02', 'GI PIPE EAGLES S-40(LG)1-1/2#40MM x6M 150/BNDL', 8091, 'LENGTH', 1, 0, 0, 'OOP TEST'),
(37, '5C32A8', '2026-11-23 08:28:43', 'ivan', '1301-04', '1301-04', 'DESERT EAGLE S-40(LG)1-1/2#32MM x6M 180', 7777, 'LENGTH', 0, 1, 17, 'OOP Test2'),
(38, '5C30A6', '2026-11-23 08:58:31', 'Juditho', '1301-04', '1301-04', 'DESERT EAGLE S-40(LG)1-1/2#32MM x6M 180', 7777, 'LENGTH', 1, 0, 17, 'OOPAW TEST');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_module`
--

CREATE TABLE `tbl_module` (
  `oModuleid` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `oModulename` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `oTableid` int(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_module`
--

INSERT INTO `tbl_module` (`oModuleid`, `oModulename`, `oTableid`) VALUES
(1, 'MY ACCOUNT', 1),
(2, 'ADMINISTRATIVE TOOLS', 2),
(3, 'USER ACCOUNTS', 3),
(4, 'STORE DASHBOARD - MAIN', 4),
(5, 'SALES PERFORMANCE', 5),
(6, 'OPPORTUNITY LOSS', 6),
(7, 'STOCK TRANSFER OUT', 7),
(8, 'STOCK TRANSFER IN', 8),
(9, 'SALES TRANSACTION COUNT', 9),
(10, 'DELIVERY TRANSACTION COUNT', 10),
(11, 'CYCLE TIME', 11),
(12, 'POS CASHIER PERFORMANCE', 12),
(13, 'CUSTOMER RETURNED', 13),
(14, 'FOOT TRAFFIC', 14),
(15, 'ADD PERSONNEL', 15),
(16, 'ITEM MASTERLIST', 16);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `oUserid` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(250) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `status` int(11) NOT NULL,
  `post_code` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`oUserid`, `name`, `username`, `email`, `password`, `role`, `status`, `post_code`) VALUES
(16, 'Meycko', 'admin', 'admin@gmail.com', '$2y$10$yCALtoLU4td7Vc6LoVKxDOPUb95mFYcFJMnvqT2di2xwgATXmBaW2', 'admin', 1, '6542'),
(19, 'ivan', 'ivan', 'ivan@gmail.com', '$2y$10$gPkS8S6BSMAMa6HoLMoSZ.dQN9KjNMA4YHmqOHC/ivqQU5l0jpbOK', 'user', 1, '1022'),
(31, 'Argie Dignos', 'Argie', 'argie@gmail.com', '$2y$10$6iL1hKtb24R3G6OGfWnrfeepQPmGBUi2EZI5MC4d2wh4Tw9L/BdHi', 'user', 1, '7645'),
(33, 'test', 'test', 'test@gmail.com', '$2y$10$lm6L2l5cgLBXC8u0Nb09iOCVAN5l1yYFNJK0AA1ev2W68JigFC/Hy', 'user', 1, '8220'),
(36, 'Juditho', 'Jud', 'juditho@gmail.com', '$2y$10$ClGoAwE7R.pIPK4jceDyROClJ2i28fXPRrF3fQVXQBzOBfsXoCGWS', 'user', 1, '2576');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_access`
--
ALTER TABLE `tbl_access`
  ADD PRIMARY KEY (`oAccessid`),
  ADD UNIQUE KEY `unique_user_module` (`oUserid`,`oModuleid`);

--
-- Indexes for table `tbl_item`
--
ALTER TABLE `tbl_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `tbl_item_details`
--
ALTER TABLE `tbl_item_details`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `tbl_module`
--
ALTER TABLE `tbl_module`
  ADD KEY `oTableid` (`oTableid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`oUserid`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD UNIQUE KEY `username_unique` (`username`),
  ADD UNIQUE KEY `post_code` (`post_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_access`
--
ALTER TABLE `tbl_access`
  MODIFY `oAccessid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_item`
--
ALTER TABLE `tbl_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_item_details`
--
ALTER TABLE `tbl_item_details`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tbl_module`
--
ALTER TABLE `tbl_module`
  MODIFY `oTableid` int(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `oUserid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
