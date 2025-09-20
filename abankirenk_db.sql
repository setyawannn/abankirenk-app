-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2025 at 08:51 PM
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
-- Database: `abankirenk_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone_number`, `password`, `role`, `created_at`) VALUES
(1, 'Prayoga Adi Setyawan', 'testadmin@gmail.com', '081333717212', '$2y$10$bwZ2B7uZlwFEF4Fn6BygUek/rjHro/x0GaMdD/XRP0XyuUdEW0qrS', 'admin', '2025-09-20 17:38:18'),
(2, 'Prayoga User', 'hikaryuzbysetyawannn@gmail.com', '081333717213', '$2y$10$4/EPzzo2AUDcrrt4eMyaSemdKec3tt4XzJPeOjFmpaGPVtvgqFGIm', 'user', '2025-09-20 18:41:28'),
(3, 'Admin Utama', 'admin@example.com', '081234567890', '$2y$10$bwZ2B7uZlwFEF4Fn6BygUek/rjHro/x0GaMdD/XRP0XyuUdEW0qrS', 'admin', '2025-09-20 18:48:59'),
(4, 'Pengguna Biasa', 'user@example.com', '089876543210', '$2y$10$bwZ2B7uZlwFEF4Fn6BygUek/rjHro/x0GaMdD/XRP0XyuUdEW0qrS', 'user', '2025-09-20 18:48:59');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
