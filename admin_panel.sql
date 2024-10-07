-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 06:58 PM
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
-- Database: `admin_panel`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `civil_status` enum('single','married','divorced','widowed') NOT NULL,
  `citizenship` varchar(50) NOT NULL,
  `achieved_status` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `user_email` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `age`, `gender`, `civil_status`, `citizenship`, `achieved_status`, `email`, `password`, `role`, `user_email`, `contact`, `birth_date`, `address`, `image`) VALUES
(3, 'Admin', NULL, 'User', 30, 'male', 'single', 'citizen', NULL, 'admin', 'admin123', 'admin', NULL, NULL, NULL, NULL, NULL),
(19, 'ADMIN', '', '', 21, 'male', 'single', 'Pinoy', 'Student', 'admin1', 'admin123', 'admin', NULL, NULL, NULL, NULL, NULL),
(20, 'Therenz', 'Andulana', 'Jaromohom', 21, 'male', 'single', 'Filipino', 'Student', 'Zuou', '$2y$10$hhng8NAa5zAbfbZhk90bd.MTcYudmJFjqvTwlxc0R0/1BTHhz9gCO', 'user', 'jaromohomrenze231@gmail.com', '09682021257', '2003-01-12', 'Umapad, Mandaue City, Cebu', NULL),
(23, 'Ray Jay', 'Estenzo', 'Lato', 21, 'female', 'single', 'Opaw', 'Student', 'Akari', '$2y$10$5zP5wm.tG2uwrZ2lj3u4CO/eNCOTTMLRd5DvTdyDt2DqHJ2pyrRkS', 'user', 'pilotbyjames@gmail.com', '09682021245', '2000-06-30', 'Compostela, Cebu', 'uploads/IMG_20220930_154417.jpg'),
(24, 'Steven', 'Auron', 'Tejero', 69, 'male', 'single', 'Pablo', 'Student', 'Stizmo', '$2y$10$cUjrwkV4iHtdhYwvx8wg4u/3iI11KSNbIWDYnZW6Ob.zs82o6xhAq', 'user', 'hotmeteorite3@gmail.com', '09323722251', '2004-04-18', 'Jubay, Liloan', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
