-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2025 at 01:06 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `staybyte1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `admin_id` varchar(20) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `first_name`, `last_name`, `admin_id`, `role`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'admin1', '$2y$10$WLWRffRhyVkkrFU5NAm9VOROHfM31h0sWZrLPuuIUV9hcpj850d.y', 'admin1@example.com', 'Admin', 'User', 'ADM001', 'super_admin', '5555555555', NULL, '2025-07-30 10:28:13', '2025-07-30 10:55:42');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','active','completed','cancelled') DEFAULT 'pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_amount`, `status`, `booking_date`) VALUES
(1, 2, 1, '2024-01-15', '2024-01-20', '750.00', 'active', '2025-07-30 10:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `enquiry_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `category` enum('room','meal','general') DEFAULT 'general',
  `status` enum('open','in-progress','resolved','closed') DEFAULT 'open',
  `response` text DEFAULT NULL,
  `responded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `meal_id` int(11) NOT NULL,
  `meal_title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `meal_type` enum('Veg','Non-Veg') DEFAULT 'Veg',
  `items_included` text DEFAULT NULL,
  `meal_address` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `meal_provider_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `meal_title`, `description`, `price`, `rating`, `meal_type`, `items_included`, `meal_address`, `image_url`, `status`, `meal_provider_id`, `created_at`) VALUES
(1, 'Vegetarian Thali', 'Traditional Indian vegetarian meal', '12.99', '4.70', 'Veg', 'Dal, Rice, Roti, Sabzi, Salad, Dessert', 'Main Dining Hall', 'uploads/meals/meal_1753896798_8148.jpg', 'Active', NULL, '2025-07-30 10:28:12'),
(2, 'Non-Veg Platter', 'Delicious non-vegetarian meal with chicken and mutton dishes', '15.99', '4.50', 'Non-Veg', 'Chicken Curry, Mutton Curry, Rice, Roti, Salad', 'Main Dining Hall', 'uploads/meals/meal2.jpg', 'Active', NULL, '2025-07-30 10:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `meals_images`
--

CREATE TABLE `meals_images` (
  `image_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_provider_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `room_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `room_space` varchar(255) DEFAULT NULL,
  `bed_size` varchar(50) DEFAULT NULL,
  `guest_capacity` int(11) DEFAULT 2,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_provider_id`, `title`, `description`, `room_image`, `price`, `rating`, `room_space`, `bed_size`, `guest_capacity`, `status`, `created_at`) VALUES
(1, NULL, 'Deluxe Room', 'Spacious room with city view', 'uploads/rooms/room1.jpg', '150.00', '4.50', '500', '0', 2, 'Active', '2025-07-30 10:28:12'),
(2, NULL, 'Standard Room', 'Comfortable room for single occupancy', 'uploads/rooms/room2.jpg', '100.00', '4.20', '100', '0', 1, 'Active', '2025-07-30 10:28:12'),
(3, NULL, 'Suite', 'Luxury suite with separate living area', 'uploads/rooms/room3.jpg', '250.00', '4.80', NULL, 'King Size', 4, 'Active', '2025-07-30 10:28:12'),
(4, NULL, 'iybv', 'kjb', 'uploads/rooms/room_1753871852_4135.jpg', '200.00', '5.00', '0', '2', 3, '', '2025-07-30 10:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `rooms_images`
--

CREATE TABLE `rooms_images` (
  `image_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms_images`
--

INSERT INTO `rooms_images` (`image_id`, `room_id`, `image_url`, `created_at`) VALUES
(12, 1, 'uploads/rooms/room_1753879123_9649_2.jpeg', '2025-08-01 11:15:05'),
(13, 1, 'uploads/rooms/room_1753879123_7867_3.jpeg', '2025-08-01 11:15:05'),
(14, 1, 'uploads/rooms/room_1754046905_9483_0.jpg', '2025-08-01 11:15:05'),
(15, 1, 'uploads/rooms/room_1754046905_4727_1.jpg', '2025-08-01 11:15:05'),
(16, 1, 'uploads/rooms/room_1754046905_4942_2.jpg', '2025-08-01 11:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `room_amenities`
--

CREATE TABLE `room_amenities` (
  `amenities_id` int(11) NOT NULL,
  `facility_name` varchar(100) NOT NULL,
  `room_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_amenities`
--

INSERT INTO `room_amenities` (`amenities_id`, `facility_name`, `room_id`) VALUES
(16, 'tv', 2),
(17, 'ac', 2),
(18, 'sound bar', 2),
(22, 'TV', 1),
(23, 'ac', 1),
(24, 'washing machine', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `role_id` int(11) DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `profile_pic`, `username`, `password`, `first_name`, `last_name`, `birth_date`, `email`, `phone`, `country`, `role_id`, `created_at`) VALUES
(0, '0', 'student2', '$2y$10$00JL6baOt8lpxwhmVtE/oe/u8yStdkU256WcrNZR1PV/FquuBXjFa', 'student2', 'sdf', '2002-01-08', 'student22@gmail.com', '11223344455', 'India', 2, '2025-08-08 09:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `users1`
--

CREATE TABLE `users1` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users1`
--

INSERT INTO `users1` (`user_id`, `username`, `first_name`, `last_name`, `email`, `password`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'admin', '', '', 'admin@staybyte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-07-30 10:28:12', '2025-07-30 10:28:12'),
(2, 'student1', '', '', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '2025-07-30 10:28:12', '2025-07-30 10:28:12'),
(3, 'staff1', '', '', 'staff1@staybyte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, '2025-07-30 10:28:12', '2025-07-30 10:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`role_id`, `role_name`, `role_description`, `created_at`) VALUES
(1, 'admin', 'Administrator with full access', '2025-07-30 10:28:12'),
(2, 'student', 'Student user with booking access', '2025-07-30 10:28:12'),
(3, 'staff', 'Hotel staff member', '2025-07-30 10:28:12'),
(4, 'provider', 'Service provider with booking management', '2025-07-30 10:28:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`enquiry_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `responded_by` (`responded_by`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`meal_id`);

--
-- Indexes for table `meals_images`
--
ALTER TABLE `meals_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `fk_room_provider` (`room_provider_id`);

--
-- Indexes for table `rooms_images`
--
ALTER TABLE `rooms_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_amenities`
--
ALTER TABLE `room_amenities`
  ADD PRIMARY KEY (`amenities_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users1`
--
ALTER TABLE `users1`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `enquiry_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meals_images`
--
ALTER TABLE `meals_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms_images`
--
ALTER TABLE `rooms_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `room_amenities`
--
ALTER TABLE `room_amenities`
  MODIFY `amenities_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users1`
--
ALTER TABLE `users1`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users1` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD CONSTRAINT `enquiries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users1` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enquiries_ibfk_2` FOREIGN KEY (`responded_by`) REFERENCES `users1` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `meals_images`
--
ALTER TABLE `meals_images`
  ADD CONSTRAINT `meals_images_ibfk_1` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_room_provider` FOREIGN KEY (`room_provider_id`) REFERENCES `users1` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms_images`
--
ALTER TABLE `rooms_images`
  ADD CONSTRAINT `rooms_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_amenities`
--
ALTER TABLE `room_amenities`
  ADD CONSTRAINT `room_amenities_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
