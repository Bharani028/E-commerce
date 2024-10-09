-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2024 at 07:55 PM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(2, 'bharanisrinivasan1@gmail.com', '$2y$10$AKhOjE6NU1MS6C9mcP1fm.WxxZi.ZED1ShFmflO0D91vsmb0XFprK');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'paste', '2024-08-27 17:15:53'),
(2, 'soap', '2024-08-27 17:15:53'),
(3, 'vegetable', '2024-08-27 17:15:53'),
(4, 'energy', '2024-08-27 17:15:53'),
(5, 'shampoo', '2024-08-27 17:15:53'),
(6, 'snacks', '2024-08-27 17:15:53'),
(7, 'masala', '2024-08-27 17:15:53'),
(8, 'soft drink', '2024-08-27 17:15:53'),
(9, 'baby product', '2024-08-27 17:15:53'),
(10, 'others', '2024-08-27 17:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mobile_number` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `mobile_number`, `address`, `user_name`, `expected_delivery_date`) VALUES
(1, 3, 130.50, 'Pending', '2024-10-05 16:30:20', '88', 'wwe', NULL, '2024-10-09'),
(8, 3, 90.00, 'Pending', '2024-10-06 14:16:42', '589652', 'fdsgd fvs fd', NULL, '2024-10-09'),
(11, 3, 256.50, 'Pending', '2024-10-07 14:23:36', '5885', 'njan huaiehui', 'bharani', '2024-10-11'),
(12, 3, 769.50, 'Pending', '2024-10-07 14:24:11', '5554448485', 'fonksdn fojdujf', 'bharani', '2024-10-14'),
(13, 3, 435.00, 'Pending', '2024-10-07 14:31:46', '588859*8/8', 'hyaeh', 'bharani', '2024-10-09'),
(14, 2, 171.00, 'Delivered', '2024-10-07 16:01:15', '85', 'ueru', 'babu', '2024-10-18'),
(15, 2, 40.00, 'Delivered', '2024-10-08 08:35:22', '99856247', 'njush jish', 'babu', '2024-10-08'),
(16, 2, 55.00, 'Pending', '2024-10-08 10:11:50', '123456', 'uygug guyg', 'babu', '2024-10-12'),
(17, 2, 769.50, 'Pending', '2024-10-08 15:10:59', '898525253', 'uueudhu uhahhunfu yur', 'babu', '2024-10-12'),
(18, 2, 855.00, 'Pending', '2024-10-08 15:15:12', '96584', 'ygyg uhh', 'babu', '2024-10-12');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `product_name`) VALUES
(1, 1, 30, 1, 85.50, NULL),
(9, 8, 29, 1, 45.00, NULL),
(12, 11, 32, 3, 256.50, 'hamam'),
(13, 12, 32, 9, 769.50, 'hamam'),
(14, 13, 47, 6, 330.00, 'Aashirvaad 1KG'),
(15, 13, 42, 3, 105.00, 'Lifebuoy'),
(16, 14, 30, 2, 171.00, 'harlicks'),
(17, 15, 36, 1, 40.00, NULL),
(18, 16, 47, 1, 55.00, NULL),
(19, 17, 30, 9, 769.50, NULL),
(20, 18, 30, 10, 855.00, 'harlicks');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `created_at`) VALUES
(29, 'boost 500G', 'Description for product 11', 45.00, '../product/boost.jpg', 4, '2024-08-29 10:52:15'),
(30, 'harlicks', 'Description for product 12', 85.50, '../product/harlicks.jpg', 4, '2024-08-29 10:52:15'),
(31, 'colgate', 'Description for product 11', 45.00, '../product/colgate.jpeg', 1, '2024-08-29 10:53:39'),
(32, 'hamam', 'Description for product 12', 85.50, '../product/hamam.jpeg', 2, '2024-08-29 10:53:39'),
(34, 'Closeup', 'srgfs rsgrs', 50.00, '../product/closeup.jpeg', 1, '2024-09-30 11:22:44'),
(36, 'Dabur RED', 'hfuisheauifhi hrushfuih', 40.00, '../product/Dabur RED.jpg', 1, '2024-09-30 11:43:50'),
(41, 'dove', 'jgg j', 56.00, '../product/dove.jpeg', 2, '2024-09-30 12:17:17'),
(42, 'Lifebuoy', 'gcygs gyegs', 35.00, '../product/lifebuoy.jpeg', 2, '2024-10-01 04:56:28'),
(44, 'Sensodyne', 'Best for teeth', 53.00, '../product/sensodyne.jpg', 1, '2024-10-01 05:07:25'),
(47, 'Aashirvaad 1KG', 'ygy gyg', 55.00, '../product/aashirvaad 1KG.webp', 10, '2024-10-04 05:30:07'),
(48, 'hiuhuihd', 'fw', 12.00, '../product/dettol.jpg', 2, '2024-10-07 17:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(2, 'babu', 'bharanis22mcs008@skasc.ac.in', '$2y$10$OqKojzea.ExSxLE9NpI3JutPkj3/IpWJM7KYJsP2Z8pWsAVBodAEO', '2024-08-29 11:19:38'),
(3, 'bharani', 'bharanichess02@gmail.com', '$2y$10$vgbq9pEiHLsSgreKaWZEFObcDilaWXG0b7yGauqFhIHlscDIXpJzO', '2024-09-27 15:40:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
