-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 20, 2026 at 05:13 PM
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
-- Database: `exercise_lesson_alihaha`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`) VALUES
(1, 'General', 'fa-box'),
(2, 'Industrial Machinery', 'fa-industry'),
(3, 'Chemicals', 'fa-flask'),
(4, 'Testing Instrument', 'fa-microscope');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

CREATE TABLE `manufacturers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `flag` varchar(10) DEFAULT '?',
  `description` text DEFAULT NULL,
  `rating` decimal(2,0) DEFAULT 0,
  `products_count` int(11) DEFAULT 0,
  `response_rate` int(11) DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturers`
--

INSERT INTO `manufacturers` (`id`, `name`, `country`, `flag`, `description`, `rating`, `products_count`, `response_rate`, `is_verified`, `logo`, `created_at`) VALUES
(1, 'RZX Trading Co.', 'China', '🇨🇳', NULL, 0, 0, 0, 1, 'rzx-trading.png', '2026-10-07 17:07:21'),
(2, 'Seoul Beauty Ltd.', 'South Korea', '🇰🇷', NULL, 0, 0, 0, 1, 'seoul-beauty.png', '2026-10-07 17:07:21'),
(3, 'HK Toys International', 'Hong Kong', '🇭🇰', NULL, 0, 0, 0, 1, NULL, '2026-10-07 17:07:21');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `address`, `phone`, `created_at`) VALUES
(1, 1, 5435.85, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-12 16:37:36'),
(2, 1, 5435.85, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-12 16:42:52'),
(3, 1, 5667.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-12 17:34:43'),
(4, 1, 5667.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-12 17:45:45'),
(5, 1, 5456.85, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-15 09:46:14'),
(6, 1, 86.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:33:28'),
(7, 1, 86.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:35:13'),
(8, 1, 313.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:36:55'),
(9, 1, 313.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:38:15'),
(10, 1, 86.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:43:15'),
(11, 1, 525.00, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:45:38'),
(12, 1, 86.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:47:00'),
(13, 1, 86.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 15:47:53'),
(14, 1, 4828.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 16:31:08'),
(15, 1, 4828.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 16:41:58'),
(16, 1, 5185.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-16 19:16:39'),
(17, 1, 4828.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-17 09:21:36'),
(18, 1, 4828.95, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-17 09:22:31'),
(19, 2, 86.90, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-17 10:29:07'),
(20, 2, 5456.85, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-17 16:07:36'),
(21, 4, 1443.75, 'pending', 'Yangon, Yangon 07073', '+959897032297', '2026-10-20 13:49:22');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 17, 1, 500.00),
(2, 2, 22, 1, 78.00),
(3, 2, 18, 1, 4599.00),
(4, 3, 18, 1, 4599.00),
(5, 3, 17, 1, 500.00),
(6, 3, 16, 1, 299.00),
(7, 4, 18, 1, 4599.00),
(8, 4, 17, 1, 500.00),
(9, 4, 7, 1, 299.00),
(10, 5, 18, 1, 4599.00),
(11, 5, 16, 1, 299.00),
(12, 5, 19, 1, 299.00),
(13, 6, 14, 1, 78.00),
(14, 7, 22, 1, 78.00),
(15, 8, 19, 1, 299.00),
(16, 9, 19, 1, 299.00),
(17, 10, 22, 1, 78.00),
(18, 11, 17, 1, 500.00),
(19, 12, 22, 1, 78.00),
(20, 13, 22, 1, 78.00),
(21, 14, 18, 1, 4599.00),
(22, 15, 18, 1, 4599.00),
(23, 16, 15, 1, 4939.00),
(24, 17, 18, 1, 4599.00),
(25, 18, 18, 1, 4599.00),
(26, 19, 22, 1, 78.00),
(27, 20, 19, 1, 299.00),
(28, 20, 18, 1, 4599.00),
(29, 20, 13, 1, 299.00),
(30, 21, 25, 1, 78.00),
(31, 21, 26, 1, 299.00),
(32, 21, 28, 1, 299.00),
(33, 21, 27, 1, 699.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `min_order` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General',
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `price`, `min_order`, `image`, `category_id`, `supplier_id`, `description`, `category`, `seller_id`) VALUES
(2, 'Book', 299.00, 1, '1791393452_images (2).jpeg', 3, 3, NULL, 'General', 1),
(3, 'Book', 19.00, 1, '1791393915_beauty 6.jpeg', 3, 1, NULL, 'General', 1),
(4, 'book', 78.00, 49, '1791393959_beauty 3.jpeg', 2, 2, 'high quanlity', 'General', 1),
(5, 'book', 78.00, 4, '1791394004_beauty 5.jpeg', 2, 2, NULL, 'General', 1),
(6, 'book', 19.00, 1, '1791394046_beauty 2.jpeg', 4, 3, NULL, 'General', 1),
(7, 'NIKE', 299.00, 1, '1791394170_0x0.webp', 1, 3, NULL, 'General', 1),
(8, 'NIKE', 299.00, 8000, '1791395878_112-best-nike-zoomx-running-shoes-15607111-main.webp', 2, 2, NULL, 'General', 1),
(10, 'book', 4748.00, 122, '1791428081_book 13.jpeg', 1, 3, NULL, 'General', 1),
(11, 'Book', 78.00, 7, '1791428132_book 10.jpeg', 2, 1, NULL, 'General', 1),
(12, 'Book', 48.00, 74, '1791428191_book2.jpeg', 3, 2, NULL, 'General', 1),
(13, 'book', 299.00, 139, '1791463430_beauty 7.jpeg', 3, 1, NULL, 'General', 1),
(14, 'book', 78.00, 154, '1791463489_beauty 1.jpeg', 2, 2, NULL, 'General', 1),
(15, 'Camera', 4939.00, 112, '1791463938_camera4.jpeg', 2, 1, 'high quanlity', 'General', 1),
(16, 'Camera', 299.00, 15, '1791463982_camera5.webp', 2, 3, NULL, 'General', 1),
(17, 'camera', 500.00, 10000, '1791464026_camera3.webp', 4, 2, NULL, 'General', 1),
(18, 'camera', 4599.00, 390, '1791464069_camera6.jpg', 1, 2, NULL, 'General', 1),
(19, 'camera', 299.00, 90, '1791464109_camera8.webp', 1, 2, '', '1', 1),
(22, 'Skin Care', 78.00, 90, '1791464231_shopping8.jpeg', 2, 2, NULL, 'General', 1),
(25, 'New Balance', 78.00, 60, '1792263987_new balance 2.jpeg', 1, 1, 'High Quanlity', '1', 3),
(26, 'New Balance', 299.00, 90, '1792264725_new balance 1.jpeg', 2, 2, NULL, 'General', 3),
(27, 'Adidas', 699.00, 800, '1792264772_adidas5.jpeg', 4, 2, NULL, 'General', 3),
(28, 'Shoes', 299.00, 69, '1792264863_adidas3.jpeg', 4, 3, NULL, 'General', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Kyaw', 'kyaw@gmail.com', '$2y$10$Km.FHzSn0fWV.G4g0UEys.cDnWfZBhilWlqfCtCGbGxKhftZ43ViG', 'admin'),
(2, 'CLK', 'chit@gmail.com', '$2y$10$ER2p2i1Nq7hA9tVzHTM8v.ytUkasxNu1z/qD1iG9.AvliFEeWD8De', 'user'),
(3, 'Water man', 'water@gmail.com', '$2y$10$cWPhlCF9kirvjB.IPyPWROk1oUmZBq1Aw7n/XjW8RSPQLBqCicIOK', 'seller'),
(4, 'Nyi', 'nyi@gmail.com', '$2y$10$WthOhKNimMG8QBc2xTBkY.Bi2kHr8FRxLHArgVzYfz4dcNPxo4.dy', 'buyer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manufacturers`
--
ALTER TABLE `manufacturers`
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
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
