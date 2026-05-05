-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 05, 2026 at 02:48 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bridgeconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('shelter','food','medical','hygiene','job_support','other') NOT NULL,
  `description` text,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `available_beds` int DEFAULT '0',
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `name`, `type`, `description`, `address`, `phone`, `available_beds`, `updated_by`, `created_at`) VALUES
(1, 'Hope Shelter', 'shelter', 'Emergency overnight shelter with meals and case management support.', '123 Main St, Baltimore, MD', '555-111-2222', 12, NULL, '2026-05-05 02:30:50'),
(2, 'Safe Night Housing', 'shelter', 'Temporary shelter for adults with evening intake.', '200 Shelter Ave, Baltimore, MD', '555-222-3333', 5, NULL, '2026-05-05 02:30:50'),
(3, 'Community Food Pantry', 'food', 'Free groceries and hot meals available Monday through Friday.', '456 Oak Ave, Baltimore, MD', '555-333-4444', 0, NULL, '2026-05-05 02:30:50'),
(4, 'Fresh Start Food Drive', 'food', 'Weekly food drive with meals, water, and hygiene kits.', '800 North Ave, Baltimore, MD', '555-444-5555', 0, NULL, '2026-05-05 02:30:50'),
(5, 'Care Clinic', 'medical', 'Basic health services, checkups, and referrals.', '789 Pine Rd, Baltimore, MD', '555-555-6666', 0, NULL, '2026-05-05 02:30:50'),
(6, 'Clean Hands Hygiene Center', 'hygiene', 'Showers, hygiene kits, and laundry support.', '300 Green St, Baltimore, MD', '555-777-8888', 0, NULL, '2026-05-05 02:30:50'),
(7, 'Pathway Job Support', 'job_support', 'Resume help, job search assistance, and interview preparation.', '900 Career Blvd, Baltimore, MD', '555-999-1010', 0, NULL, '2026-05-05 02:30:50');

-- --------------------------------------------------------

--
-- Table structure for table `saved_resources`
--

CREATE TABLE `saved_resources` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `resource_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `about` text,
  `password` varchar(100) NOT NULL,
  `role` enum('person_in_need','volunteer','shelter_provider','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `gender`, `about`, `password`, `role`) VALUES
(1, 'Demo User', 'user@test.com', NULL, NULL, '1234', 'person_in_need'),
(2, 'Shelter Provider', 'provider@test.com', NULL, NULL, '1234', 'shelter_provider'),
(3, 'Volunteer Worker', 'volunteer@test.com', NULL, NULL, '1234', 'volunteer'),
(4, 'Admin User', 'admin@test.com', NULL, NULL, '1234', 'admin'),
(5, 'Xavier baines', 'xb@test.co', 'male', 'housing', '1234', 'person_in_need');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `saved_resources`
--
ALTER TABLE `saved_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `resource_id` (`resource_id`);

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
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `saved_resources`
--
ALTER TABLE `saved_resources`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `saved_resources`
--
ALTER TABLE `saved_resources`
  ADD CONSTRAINT `saved_resources_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_resources_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
