-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Apr 20, 2026 at 07:46 AM
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
-- Database: `event_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `package_tier` varchar(50) NOT NULL,
  `event_date` date NOT NULL,
  `guest_count` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `checkin_time` varchar(20) DEFAULT '10:00 AM',
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partially_paid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `event_id`, `client_id`, `package_tier`, `event_date`, `guest_count`, `full_name`, `email`, `phone`, `checkin_time`, `total_amount`, `status`, `payment_status`, `created_at`) VALUES
(1, 2, 3, 'premium', '2026-03-30', 5000, 'Roz Chaudhary', 'benduliaroz@gmail.com', '9832453533', '10:00 AM', 90000.00, 'completed', 'unpaid', '2026-03-29 09:46:51'),
(2, 1, 3, 'premium', '2026-03-31', 7787, 'Roz Chaudhary', 'benduliaroz@gmail.com', '9876543456', '10:00 AM', 0.20, 'cancelled', 'unpaid', '2026-03-30 10:38:07'),
(3, 1, 3, 'premium', '2026-03-31', 150, 'Roz Chaudhary', 'benduliaroz@gmail.com', '93999999999', '10:00 AM', 0.20, 'cancelled', 'unpaid', '2026-03-30 15:27:39'),
(4, 2, 3, 'premium', '2026-03-31', 150, 'Roz Chaudhary', 'benduliaroz@gmail.com', '93999999999', '10:00 AM', 90000.00, 'cancelled', 'unpaid', '2026-03-30 15:59:03'),
(5, 1, 3, 'premium', '2026-03-31', 150, 'Hello ', 'benduliaroz@gmail.com', '93999999999', '10:00 AM', 20000.00, 'cancelled', 'unpaid', '2026-03-30 16:50:21'),
(6, 2, 3, 'standard', '2026-04-02', 66, 'Hello ', 'benduliaroz@gmail.com', '93999999999', '10:00 AM', 40000.00, 'cancelled', 'unpaid', '2026-03-31 01:25:41'),
(7, 1, 3, 'premium', '2026-04-01', 66, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '98765432165', '10:00 AM', 20000.00, 'confirmed', 'unpaid', '2026-03-31 01:32:44'),
(8, 1, 3, 'premium', '2026-04-03', 44, 'fgdbfng', 'benduliaroz@gmail.com', '93999999999', '10:00 AM', 20000.00, 'confirmed', 'unpaid', '2026-03-31 01:53:36'),
(9, 2, 3, 'basic', '2026-04-02', 150, 'Zebra', 'hello@gmail.com', '93999999999', '10:00 AM', 30000.00, 'cancelled', 'unpaid', '2026-04-01 15:30:08'),
(10, 1, 3, 'premium', '2026-04-01', 150, 'Zebra', 'hello@gmail.com', '93999999999', '10:00 AM', 20000.00, 'confirmed', 'unpaid', '2026-04-01 16:29:48'),
(11, 2, 3, 'premium', '2026-04-02', 150, 'Zebra', 'hello@gmail.com', '93999999999', '10:00 AM', 90000.00, 'cancelled', 'unpaid', '2026-04-01 16:48:27'),
(12, 2, 3, 'standard', '2026-04-02', 150, 'Zebra', 'hello@gmail.com', '93999999999', '09:00', 40000.00, 'cancelled', 'unpaid', '2026-04-01 17:08:51'),
(13, 3, 3, 'premium', '2026-04-02', 150, 'Zebra', 'hello@gmail.com', '93999999999', '11:00', 1000.00, 'cancelled', 'unpaid', '2026-04-01 17:13:11'),
(14, 3, 3, 'premium', '2026-04-02', 150, 'Zebra', 'hello@gmail.com', '93999999999', '10:00', 1000.00, 'cancelled', 'unpaid', '2026-04-02 06:14:55'),
(15, 3, 3, 'premium', '2026-04-03', 66, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '98765432165', '10:00', 1000.00, 'confirmed', 'unpaid', '2026-04-02 06:18:16'),
(16, 3, 3, 'standard', '2026-04-03', 66, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '98765432165', '10:00', 200.00, 'confirmed', 'unpaid', '2026-04-02 06:57:26'),
(17, 3, 3, 'premium', '2026-04-03', 66, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '98765432165', '10:00', 1000.00, 'confirmed', 'unpaid', '2026-04-02 06:58:58'),
(18, 3, 3, 'premium', '2026-04-03', 66, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '98765432165', '10:00', 1000.00, 'confirmed', 'unpaid', '2026-04-02 07:02:29'),
(20, 4, 3, 'premium', '2026-04-03', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 100000.00, 'confirmed', 'unpaid', '2026-04-03 15:57:38'),
(21, 11, 3, 'premium', '2026-04-05', 159, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 3.00, 'cancelled', 'unpaid', '2026-04-04 07:54:19'),
(23, 8, 3, 'premium', '2026-04-05', 150, 'zebra', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 10000.00, 'confirmed', 'unpaid', '2026-04-04 10:29:10'),
(25, 7, 3, 'premium', '2026-04-09', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 10000.00, 'cancelled', 'unpaid', '2026-04-04 10:33:54'),
(29, 10, 3, 'premium', '2026-04-05', 123, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 10000.00, 'cancelled', 'unpaid', '2026-04-04 12:34:53'),
(30, 20, 3, 'premium', '2026-04-06', 159, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '9123456783', '10:00', 126272.00, 'pending', 'unpaid', '2026-04-05 03:56:21'),
(31, 22, 3, 'premium', '2026-04-06', 150, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '9123456783', '10:00', 1728298.00, 'pending', 'partially_paid', '2026-04-05 03:57:08'),
(32, 23, 3, 'basic', '2026-04-06', 150, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '9123456783', '09:00', 2000.00, 'pending', 'partially_paid', '2026-04-05 04:17:41'),
(33, 24, 3, 'standard', '2026-04-06', 150, 'ROZZ Bendulia', 'benduliaroz@gmail.com', '9123456783', '10:00', 1000.00, 'pending', 'partially_paid', '2026-04-05 04:20:23'),
(34, 11, 3, 'premium', '2026-04-06', 1500, 'Zebra', 'benduliaroz@gmail.com', '9123456783', '10:00', 3.00, 'pending', 'unpaid', '2026-04-05 04:33:52'),
(35, 25, 3, 'premium', '2026-04-06', 150, 'Zebra', 'benduliaroz@gmail.com', '9123456783', '10:00', 1000.00, 'cancelled', 'partially_paid', '2026-04-05 04:36:30'),
(36, 27, 3, 'basic', '2026-04-06', 200, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 100.00, 'pending', 'unpaid', '2026-04-05 06:53:04'),
(37, 28, 3, 'basic', '2026-04-06', 149, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 26262.00, 'pending', 'partially_paid', '2026-04-05 06:56:19'),
(38, 29, 3, 'premium', '2026-04-06', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876545646', '10:00', 29200.00, 'pending', 'unpaid', '2026-04-05 07:09:39'),
(39, 30, 3, 'premium', '2026-04-06', 159, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876545646', '10:00', 49499.00, 'pending', 'partially_paid', '2026-04-05 07:11:07'),
(40, 31, 3, 'standard', '2026-04-06', 159, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 282982.00, 'cancelled', 'partially_paid', '2026-04-05 07:13:28'),
(41, 32, 3, 'standard', '2026-04-06', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876545646', '10:00', 27282.00, 'confirmed', 'paid', '2026-04-05 07:29:55'),
(42, 34, 3, 'standard', '2026-04-06', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 1900.00, 'confirmed', 'paid', '2026-04-05 07:46:41'),
(43, 35, 3, 'premium', '2026-04-06', 12929, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 2500.00, 'confirmed', 'paid', '2026-04-05 07:55:55'),
(44, 36, 3, 'premium', '2026-04-06', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876545646', '10:00', 2500.00, 'confirmed', 'paid', '2026-04-05 08:00:24'),
(45, 37, 3, 'premium', '2026-04-07', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876545646', '10:00', 2500.00, 'confirmed', 'paid', '2026-04-05 08:18:57'),
(46, 38, 3, 'premium', '2026-04-06', 11, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 2500.00, 'confirmed', 'paid', '2026-04-05 08:55:47'),
(47, 39, 3, 'premium', '2026-04-07', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 1000.00, 'confirmed', 'paid', '2026-04-05 09:15:26'),
(49, 43, 3, 'basic', '2026-04-07', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9123456789', '10:00', 1000.00, 'pending', 'partially_paid', '2026-04-06 08:25:53'),
(50, 44, 3, 'standard', '2026-04-08', 150, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876543333', '10:00', 2500.00, 'confirmed', 'paid', '2026-04-06 08:33:05'),
(51, 45, 3, 'premium', '2026-04-08', 10, 'Roz Chaudhary', 'chaudharyroz68@gmail.com', '9876545646', '10:00', 2500.00, 'cancelled', 'partially_paid', '2026-04-06 08:35:08'),
(53, 47, 3, 'standard', '2026-04-10', 100, 'wow', 'chaudharyroz68@gmail.com', '9876545646', '11:00', 200.00, 'pending', 'partially_paid', '2026-04-06 13:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'draft',
  `image_path` varchar(255) DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `venue_name` varchar(255) DEFAULT NULL,
  `venue_location` varchar(255) DEFAULT NULL,
  `bookings_count` int(11) DEFAULT 0,
  `packages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`packages`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `title`, `description`, `category`, `status`, `image_path`, `event_date`, `venue_name`, `venue_location`, `bookings_count`, `packages`, `created_at`) VALUES
(1, 2, 'Random Wedding', 'I want to marry my girl.', 'Weddings', 'active', NULL, NULL, 'Royal Palace', 'Baneshwor,Kathmandu', 0, '{\"basic\":{\"description\":\"\",\"price_range\":\"\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"\",\"price_range\":\"\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"Event must has food\",\"price_range\":\"Rs.20,000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-03-30 08:33:59'),
(2, 2, 'Mahango Food', 'Mahango Food Mahango Food Mahango Food Mahango FoodMahango FoodMahango FoodMahango Food', 'Other Events and Programs', 'active', '/EventManagementSystem/public/assets/images/events/event_69caaacd06a0a_1774889677.png', NULL, 'Royal Palace', 'Baneshwor,Kathmandu', 0, '{\"basic\":{\"description\":\"Nice\",\"price_range\":\"30000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"},{\"title\":\"hhhh\",\"description\":\"hhfrhhdhhe\"}]},\"standard\":{\"description\":\"baddd\",\"price_range\":\"40000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"Event must has food\",\"price_range\":\"90000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-03-30 09:41:57'),
(3, 1, 'Concert', 'Rock the music.', 'Other Events and Programs', 'active', '/EventManagementSystem/public/assets/images/events/event_69cd51f70557c_1775063543.png', NULL, '', '', 0, '{\"basic\":{\"description\":\"Basic ticket\",\"price_range\":\"100\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"Standard Ticket\",\"price_range\":\"200\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"VIP ticket\",\"price_range\":\"1000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-01 17:12:23'),
(4, 2, 'Hello World', 'travel', 'Other Events and Programs', 'active', NULL, NULL, 'Grand Plaza', 'Kathmandu,Nepal', 0, '{\"basic\":{\"description\":\"Basic travel\",\"price\":\"25000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"Standard travel\",\"price\":\"50000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"VIP travel\",\"price\":\"100000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-03 07:10:11'),
(5, 2, 'HilaHila', 'ajanan', 'Weddings', 'active', NULL, NULL, 'Royal Palace', 'Kathmandu,Nepal', 0, '{\"basic\":{\"description\":\"smjms\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"shjjs\",\"price\":\"5000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sjsjms\",\"price\":\"10000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-04 07:02:44'),
(6, 2, 'HilaHila', 'ajanan', 'Weddings', 'active', NULL, NULL, 'Royal Palace', 'Kathmandu,Nepal', 0, '{\"basic\":{\"description\":\"smjms\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"shjjs\",\"price\":\"5000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sjsjms\",\"price\":\"10000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-04 07:02:54'),
(7, 2, 'HilaHila', 'ssjxjjas', 'Weddings', 'active', NULL, NULL, 'Royal Palace', 'Kathmandu,Nepal', 0, '{\"basic\":{\"description\":\"smjms\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"shjjs\",\"price\":\"5000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sjsjms\",\"price\":\"10000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-04 07:06:51'),
(8, 2, 'HilaHila', 'ajanan', 'Weddings', 'active', NULL, NULL, 'Royal Palace', 'Kathmandu,Nepal', 0, '{\"basic\":{\"description\":\"smjms\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"shjjs\",\"price\":\"5000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sjsjms\",\"price\":\"10000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-04 07:06:57'),
(10, 2, 'Hello', 'ajajaj', 'Cultural Events', 'active', NULL, NULL, 'Royal Palace', 'Kathmandu,Nepal', 0, '{\"basic\":{\"description\":\"smjms\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"shjjs\",\"price\":\"5000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sjsjms\",\"price\":\"10000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-04 07:08:24'),
(11, 1, 'bye bye', 'hewnsn', 'Family Functions', 'active', NULL, NULL, 'Grand Plaza', 'snsm', 0, '{\"basic\":{\"description\":\"smsm\",\"price\":\"1\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"zmzmz\",\"price\":\"2\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"amama\",\"price\":\"3\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-04 07:29:44'),
(20, 2, 'YellowStone', 'hello', 'Cultural Events', 'active', NULL, NULL, 'jns md', 'smskd', 0, '{\"basic\":{\"description\":\"ksksks\",\"price\":\"1727626\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"ksksk\",\"price\":\"162772\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sjsksk\",\"price\":\"126272\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 03:41:19'),
(21, 1, 'HelloStone', 'dklls', 'Family Functions', 'active', NULL, NULL, 'dkeoe', 'eoell', 0, '{\"basic\":{\"description\":\"eosls\",\"price\":\"2039390\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"lslsls,\",\"price\":\"2929020\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dlsl;sdl\",\"price\":\"182929\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 03:53:54'),
(22, 2, 'JJK', 'wlss;', 'Meetings', 'active', NULL, NULL, 'wpw;w;', 'pwpwp', 0, '{\"basic\":{\"description\":\"kesls\",\"price\":\"2838939\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"kl;lsl;s\",\"price\":\"182892\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dkslsl\",\"price\":\"1728298\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 03:55:17'),
(23, 1, 'Machine', 'kslls', 'Weddings', 'active', NULL, NULL, 'sdlsldl', 'slslsl', 0, '{\"basic\":{\"description\":\"dlkdll;d\",\"price\":\"2000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"issklsl\",\"price\":\"2000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dfkdlfl\",\"price\":\"200\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 04:17:02'),
(24, 2, 'glass', 'wkwlls', 'Weddings', 'active', NULL, NULL, 'dldl;ld', 'd,sl;s', 0, '{\"basic\":{\"description\":\"dlodl;l\",\"price\":\"1000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"slsl;s\",\"price\":\"1000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dlslsl\",\"price\":\"1000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 04:19:46'),
(25, 2, 'GlassFactory', 'kkdkd', 'Weddings', 'active', NULL, NULL, 'dndkdkkd', 'ldllds', 0, '{\"basic\":{\"description\":\"17282\",\"price\":\"1000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dkdldl\",\"price\":\"1000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dndkdk\",\"price\":\"1000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 04:35:32'),
(27, 1, 'Mommy', 'jjsjs', 'Weddings', 'active', NULL, NULL, 'elsls', 'wskskks', 0, '{\"basic\":{\"description\":\"skslsl\",\"price\":\"100\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"1000\",\"price\":\"171881\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"jsjsj\",\"price\":\"16172\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 06:51:46'),
(28, 1, 'cloud', 'djhdjdj', 'Meetings', 'active', NULL, NULL, 'dkdld', 'sdldl', 0, '{\"basic\":{\"description\":\"whwj\",\"price\":\"26262\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"jekdk\",\"price\":\"17282\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"wjhww\",\"price\":\"12727\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 06:55:36'),
(29, 2, 'Hello', 'jdkkd', 'Family Functions', 'active', NULL, NULL, 'djdkdk', 'skdkd', 0, '{\"basic\":{\"description\":\"djdkkd\",\"price\":\"1500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"fkjdkdk\",\"price\":\"2000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dhjddo\",\"price\":\"29200\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:09:06'),
(30, 2, 'Hello', 'dhdnm', 'Cultural Events', 'active', NULL, NULL, 'dkdlkd', 'sksk', 0, '{\"basic\":{\"description\":\"dm,dkdl\",\"price\":\"181982\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"mdlkdl\",\"price\":\"1818\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"xmsks\",\"price\":\"49499\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:10:43'),
(31, 2, 'helman', 'dkdld', 'Weddings', 'active', NULL, NULL, 'dkdlkd', 'dkdld', 0, '{\"basic\":{\"description\":\"mkdkd\",\"price\":\"18198\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dkdlk\",\"price\":\"282982\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dkdkd\",\"price\":\"29\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:12:13'),
(32, 2, 'papa', 'wkslsk', 'Meetings', 'active', NULL, NULL, 'slsksl', 'sksls', 0, '{\"basic\":{\"description\":\"slksls\",\"price\":\"18282\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"skslks\",\"price\":\"27282\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"slksls\",\"price\":\"818282\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:29:25'),
(33, 2, 'hepa', 'amkak', 'Weddings', 'active', NULL, NULL, 'a,kak', 'sskls', 0, '{\"basic\":{\"description\":\"slsls\",\"price\":\"2900\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"slsls\",\"price\":\"2900\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"skldl\",\"price\":\"2900\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:42:37'),
(34, 2, 'heloman', 'dkdk', 'Weddings', 'active', NULL, NULL, 'dldld', 'dldld', 0, '{\"basic\":{\"description\":\"dld;d;\",\"price\":\"1000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dkldld\",\"price\":\"1900\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dkdldl\",\"price\":\"1900\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:45:57'),
(35, 2, 'glassman', 'kskks', 'Weddings', 'active', NULL, NULL, 'dksk', 'slksls', 0, '{\"basic\":{\"description\":\"sklsls\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"slksl\",\"price\":\"2500\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"lsls\",\"price\":\"2500\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:55:29'),
(36, 2, 'helo', 'wlslsl', 'Weddings', 'active', NULL, NULL, 'dldlssksk', 'smsk', 0, '{\"basic\":{\"description\":\"slsls\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"slsls\",\"price\":\"2500\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"ddlld\",\"price\":\"2500\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 07:59:52'),
(37, 2, 'hello', 'elkld', 'Weddings', 'active', NULL, NULL, 'dkdkd', 'dldld', 0, '{\"basic\":{\"description\":\"d,dld\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dldld\",\"price\":\"2500\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"slksls\",\"price\":\"2500\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 08:15:17'),
(38, 2, 'dip', 'ahsa', 'Weddings', 'active', NULL, NULL, 'alal', 'aall', 0, '{\"basic\":{\"description\":\"llldl;\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dldl;d\",\"price\":\"2500\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dldld\",\"price\":\"2500\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 08:55:16'),
(39, 2, 'tree', 'sksk', 'Other Events and Programs', 'active', NULL, NULL, 'dlkdod', 'Baneshwor,Kathmandu', 0, '{\"basic\":{\"description\":\"dkdlk\",\"price\":\"1000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dlkdldl\",\"price\":\"1000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"lsosl\",\"price\":\"1000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-05 09:14:58'),
(41, 1, 'insdoe', 'sjsjk', 'Weddings', 'active', NULL, NULL, 'dldld', 's.s;;s', 0, '{\"basic\":{\"description\":\"d,dl1000\",\"price\":\"1000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"ckdk\",\"price\":\"1000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sksks\",\"price\":\"1000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-06 08:16:03'),
(43, 1, 'hello', 'odl', 'Weddings', 'active', NULL, NULL, 'flflf', 'd,lflf', 0, '{\"basic\":{\"description\":\"flfl;\",\"price\":\"1000\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dkdld\",\"price\":\"1000\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"sskl\",\"price\":\"2000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-06 08:25:00'),
(44, 2, 'helomam', 'ksksk', 'Weddings', 'active', NULL, NULL, 'skdkl', 'sksl', 0, '{\"basic\":{\"description\":\"dkdl\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dldld\",\"price\":\"2500\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"ekjeke\",\"price\":\"25000\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-06 08:32:15'),
(45, 2, 'helloba', 'ekek', 'Meetings', 'active', NULL, NULL, 'ekel', 'elld', 0, '{\"basic\":{\"description\":\"elel\",\"price\":\"2500\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dldl\",\"price\":\"2500\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"dld;l\",\"price\":\"2500\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-06 08:34:27'),
(47, 2, 'jocker', 'welkke', 'Weddings', 'active', '/EventManagementSystem/public/assets/images/events/event_69d3b76b47002_1775482731.png', NULL, 'dfllf', 'lrlr', 0, '{\"basic\":{\"description\":\"rlorrl\",\"price\":\"200\",\"items\":[{\"title\":\"Venue Setup\",\"description\":\"Standard seating and basic ambient lighting.\"},{\"title\":\"Essential Coordination\",\"description\":\"On-the-day event management and support.\"}]},\"standard\":{\"description\":\"dlele\",\"price\":\"200\",\"items\":[{\"title\":\"Decor Templates\",\"description\":\"Choice of 5 thematic floral arrangements.\"},{\"title\":\"Entertainment Selection\",\"description\":\"Live acoustic band or professional DJ.\"}]},\"premium\":{\"description\":\"fldldl\",\"price\":\"200\",\"items\":[{\"title\":\"Full Management\",\"description\":\"End-to-end event concierge and coordination.\"},{\"title\":\"Exclusive Catering & Decor\",\"description\":\"Premium 5-course meal and luxury imported floral arrangements.\"}]}}', '2026-04-06 13:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `related_id`, `is_read`, `created_at`) VALUES
(47, 4, 'New Event Launched by Admin!', 'A new official event \'bheza\' has been created. Register now!', 'event', 13, 0, '2026-04-04 10:35:01'),
(48, 5, 'New Event Launched by Admin!', 'A new official event \'bheza\' has been created. Register now!', 'event', 13, 0, '2026-04-04 10:35:01'),
(53, 4, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 10:44:10'),
(54, 5, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 10:44:10'),
(57, 4, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 10:49:54'),
(58, 5, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 10:49:54'),
(60, 4, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 12:01:34'),
(61, 5, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 12:01:34'),
(63, 4, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 12:02:00'),
(64, 5, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Description) Please review.', 'event_update', 13, 0, '2026-04-04 12:02:00'),
(66, 4, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Title) Please review.', 'event_update', 13, 0, '2026-04-04 12:02:25'),
(67, 5, 'Event Details Updated by Admin', 'The administration has updated the details for \'bheza\'. (Fields updated: Title) Please review.', 'event_update', 13, 0, '2026-04-04 12:02:25'),
(69, 4, 'Event Details Updated', 'The organizer has updated the details for \'hello\'. (Fields updated: Title) Please review.', 'event_update', 12, 0, '2026-04-04 12:03:41'),
(70, 5, 'Event Details Updated', 'The organizer has updated the details for \'hello\'. (Fields updated: Title) Please review.', 'event_update', 12, 0, '2026-04-04 12:03:41'),
(73, 4, 'Event Details Updated', 'The organizer has updated the details for \'hell\'. (Fields updated: Title) Please review.', 'event_update', 12, 0, '2026-04-04 12:03:56'),
(74, 5, 'Event Details Updated', 'The organizer has updated the details for \'hell\'. (Fields updated: Title) Please review.', 'event_update', 12, 0, '2026-04-04 12:03:56'),
(78, 4, 'New Event Launched by Admin!', 'A new official event \'The Sorry\' has been created. Register now!', 'event', 14, 0, '2026-04-04 12:15:13'),
(79, 5, 'New Event Launched by Admin!', 'A new official event \'The Sorry\' has been created. Register now!', 'event', 14, 0, '2026-04-04 12:15:13'),
(83, 4, 'New Event Launched by Admin!', 'A new official event \'Thawn\' has been created. Register now!', 'event', 15, 0, '2026-04-04 12:16:48'),
(84, 5, 'New Event Launched by Admin!', 'A new official event \'Thawn\' has been created. Register now!', 'event', 15, 0, '2026-04-04 12:16:48'),
(92, 4, 'New Event Launched!', 'A new event \'chakra\' has been created by Event Organizer. Check it out!', 'event', 16, 0, '2026-04-04 12:26:06'),
(93, 5, 'New Event Launched!', 'A new event \'chakra\' has been created by Event Organizer. Check it out!', 'event', 16, 0, '2026-04-04 12:26:06'),
(96, 4, 'New Event Launched!', 'A new event \'chakra2\' has been created by Event Organizer. Check it out!', 'event', 17, 0, '2026-04-04 12:27:25'),
(97, 5, 'New Event Launched!', 'A new event \'chakra2\' has been created by Event Organizer. Check it out!', 'event', 17, 0, '2026-04-04 12:27:25'),
(105, 4, 'New Event Launched by Admin!', 'A new official event \'makeover\' has been created. Register now!', 'event', 18, 0, '2026-04-04 12:30:17'),
(106, 5, 'New Event Launched by Admin!', 'A new official event \'makeover\' has been created. Register now!', 'event', 18, 0, '2026-04-04 12:30:17'),
(108, 4, 'New Event Launched by Admin!', 'A new official event \'makeover2\' has been created. Register now!', 'event', 19, 0, '2026-04-04 12:31:09'),
(109, 5, 'New Event Launched by Admin!', 'A new official event \'makeover2\' has been created. Register now!', 'event', 19, 0, '2026-04-04 12:31:09'),
(119, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'Hello\'. Please review the details.', 'booking', 29, 0, '2026-04-04 12:34:53'),
(121, 2, 'Booking Cancelled', 'Roz Chaudhary has cancelled their booking for \'Hello\'.', 'booking_cancel', 29, 0, '2026-04-04 12:35:15'),
(125, 4, 'New Event Launched!', 'A new event \'YellowStone\' has been created by Event Organizer. Check it out!', 'event', 20, 0, '2026-04-05 03:41:19'),
(126, 5, 'New Event Launched!', 'A new event \'YellowStone\' has been created by Event Organizer. Check it out!', 'event', 20, 0, '2026-04-05 03:41:19'),
(128, 4, 'Event Details Updated', 'The organizer has updated the details for \'YellowStone\'. (Fields updated: Category) Please review.', 'event_update', 20, 0, '2026-04-05 03:41:41'),
(129, 5, 'Event Details Updated', 'The organizer has updated the details for \'YellowStone\'. (Fields updated: Category) Please review.', 'event_update', 20, 0, '2026-04-05 03:41:41'),
(131, 2, 'Booking Cancelled', 'Roz Chaudhary has cancelled their booking for \'HilaHila\'.', 'booking_cancel', 25, 0, '2026-04-05 03:47:00'),
(134, 4, 'New Event Launched by Admin!', 'A new official event \'HelloStone\' has been created. Register now!', 'event', 21, 0, '2026-04-05 03:53:54'),
(135, 5, 'New Event Launched by Admin!', 'A new official event \'HelloStone\' has been created. Register now!', 'event', 21, 0, '2026-04-05 03:53:54'),
(138, 4, 'New Event Launched!', 'A new event \'JJK\' has been created by Event Organizer. Check it out!', 'event', 22, 0, '2026-04-05 03:55:17'),
(139, 5, 'New Event Launched!', 'A new event \'JJK\' has been created by Event Organizer. Check it out!', 'event', 22, 0, '2026-04-05 03:55:17'),
(141, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'YellowStone\'. Please review the details.', 'booking', 30, 0, '2026-04-05 03:56:21'),
(144, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'JJK\'. Please review the details.', 'booking', 31, 0, '2026-04-05 03:57:08'),
(147, 2, 'New Advance Payment', 'A 50% advance payment of NPR 864,149.00 has been made by  for your event: JJK.', 'payment_alert', 31, 0, '2026-04-05 03:58:12'),
(149, 4, 'New Event Launched by Admin!', 'A new official event \'Machine\' has been created. Register now!', 'event', 23, 0, '2026-04-05 04:17:02'),
(150, 5, 'New Event Launched by Admin!', 'A new official event \'Machine\' has been created. Register now!', 'event', 23, 0, '2026-04-05 04:17:02'),
(158, 4, 'New Event Launched!', 'A new event \'glass\' has been created by Event Organizer. Check it out!', 'event', 24, 0, '2026-04-05 04:19:46'),
(159, 5, 'New Event Launched!', 'A new event \'glass\' has been created by Event Organizer. Check it out!', 'event', 24, 0, '2026-04-05 04:19:46'),
(161, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'glass\'. Please review the details.', 'booking', 33, 0, '2026-04-05 04:20:23'),
(164, 2, 'New Advance Payment', 'A 50% advance payment of NPR 500.00 has been made by Roz Chaudhary for your event: glass.', 'payment_alert', 33, 0, '2026-04-05 04:20:47'),
(171, 4, 'New Event Launched!', 'A new event \'GlassFactory\' has been created by Event Organizer. Check it out!', 'event', 25, 0, '2026-04-05 04:35:32'),
(172, 5, 'New Event Launched!', 'A new event \'GlassFactory\' has been created by Event Organizer. Check it out!', 'event', 25, 0, '2026-04-05 04:35:32'),
(174, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'GlassFactory\'. Please review the details.', 'booking', 35, 0, '2026-04-05 04:36:30'),
(177, 2, 'New Advance Payment', 'A 50% advance payment of NPR 500.00 has been made by Roz Chaudhary for your event: GlassFactory.', 'payment_alert', 35, 0, '2026-04-05 04:36:42'),
(179, 2, 'Booking Cancelled', 'Roz Chaudhary has cancelled their booking for \'GlassFactory\'.', 'booking_cancel', 35, 0, '2026-04-05 06:44:43'),
(183, 4, 'New Event Launched!', 'A new event \'HelloMom\' has been created by Event Organizer. Check it out!', 'event', 26, 0, '2026-04-05 06:49:26'),
(184, 5, 'New Event Launched!', 'A new event \'HelloMom\' has been created by Event Organizer. Check it out!', 'event', 26, 0, '2026-04-05 06:49:26'),
(186, 4, 'Event Details Updated', 'The organizer has updated the details for \'HelloMom\'. (Fields updated: Basic Package) Please review.', 'event_update', 26, 0, '2026-04-05 06:49:58'),
(187, 5, 'Event Details Updated', 'The organizer has updated the details for \'HelloMom\'. (Fields updated: Basic Package) Please review.', 'event_update', 26, 0, '2026-04-05 06:49:58'),
(191, 4, 'New Event Launched by Admin!', 'A new official event \'Mommy\' has been created. Register now!', 'event', 27, 0, '2026-04-05 06:51:46'),
(192, 5, 'New Event Launched by Admin!', 'A new official event \'Mommy\' has been created. Register now!', 'event', 27, 0, '2026-04-05 06:51:46'),
(194, 4, 'Event Details Updated by Admin', 'The administration has updated the details for \'Mommy\'. Please review.', 'event_update', 27, 0, '2026-04-05 06:52:08'),
(195, 5, 'Event Details Updated by Admin', 'The administration has updated the details for \'Mommy\'. Please review.', 'event_update', 27, 0, '2026-04-05 06:52:08'),
(200, 4, 'New Event Launched by Admin!', 'A new official event \'cloud\' has been created. Register now!', 'event', 28, 0, '2026-04-05 06:55:36'),
(201, 5, 'New Event Launched by Admin!', 'A new official event \'cloud\' has been created. Register now!', 'event', 28, 0, '2026-04-05 06:55:36'),
(209, 4, 'New Event Launched!', 'A new event \'Hello\' has been created by Event Organizer. Check it out!', 'event', 29, 0, '2026-04-05 07:09:06'),
(210, 5, 'New Event Launched!', 'A new event \'Hello\' has been created by Event Organizer. Check it out!', 'event', 29, 0, '2026-04-05 07:09:06'),
(212, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'Hello\'. Please review the details.', 'booking', 38, 0, '2026-04-05 07:09:39'),
(216, 4, 'New Event Launched!', 'A new event \'Hello\' has been created by Event Organizer. Check it out!', 'event', 30, 0, '2026-04-05 07:10:43'),
(217, 5, 'New Event Launched!', 'A new event \'Hello\' has been created by Event Organizer. Check it out!', 'event', 30, 0, '2026-04-05 07:10:43'),
(219, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'Hello\'. Please review the details.', 'booking', 39, 0, '2026-04-05 07:11:07'),
(223, 4, 'New Event Launched!', 'A new event \'helman\' has been created by Event Organizer. Check it out!', 'event', 31, 0, '2026-04-05 07:12:13'),
(224, 5, 'New Event Launched!', 'A new event \'helman\' has been created by Event Organizer. Check it out!', 'event', 31, 0, '2026-04-05 07:12:13'),
(226, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'helman\'. Please review the details.', 'booking', 40, 0, '2026-04-05 07:13:28'),
(229, 2, 'New Advance Payment', 'A 50% advance payment of NPR 141,491.00 has been made by Roz Chaudhary for your event: helman.', 'payment_alert', 40, 0, '2026-04-05 07:14:16'),
(232, 2, 'New Advance Payment', 'A 50% advance payment of NPR 24,749.50 has been made by Roz Chaudhary for your event: Hello.', 'payment_alert', 39, 0, '2026-04-05 07:18:05'),
(235, 4, 'New Event Launched!', 'A new event \'papa\' has been created by Event Organizer. Check it out!', 'event', 32, 0, '2026-04-05 07:29:25'),
(236, 5, 'New Event Launched!', 'A new event \'papa\' has been created by Event Organizer. Check it out!', 'event', 32, 0, '2026-04-05 07:29:25'),
(238, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'papa\'. Please review the details.', 'booking', 41, 0, '2026-04-05 07:29:55'),
(241, 2, 'New Advance Payment', 'A 50% advance payment of NPR 13,641.00 has been made by Roz Chaudhary for your event: papa.', 'payment_alert', 41, 0, '2026-04-05 07:30:30'),
(244, 4, 'New Event Launched!', 'A new event \'hepa\' has been created by Event Organizer. Check it out!', 'event', 33, 0, '2026-04-05 07:42:37'),
(245, 5, 'New Event Launched!', 'A new event \'hepa\' has been created by Event Organizer. Check it out!', 'event', 33, 0, '2026-04-05 07:42:37'),
(248, 4, 'New Event Launched!', 'A new event \'heloman\' has been created by Event Organizer. Check it out!', 'event', 34, 0, '2026-04-05 07:45:57'),
(249, 5, 'New Event Launched!', 'A new event \'heloman\' has been created by Event Organizer. Check it out!', 'event', 34, 0, '2026-04-05 07:45:57'),
(251, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'heloman\'. Please review the details.', 'booking', 42, 0, '2026-04-05 07:46:41'),
(254, 2, 'New Advance Payment', 'A 50% advance payment of NPR 950.00 has been made by Roz Chaudhary for your event: heloman.', 'payment_alert', 42, 0, '2026-04-05 07:47:28'),
(257, 4, 'New Event Launched!', 'A new event \'glassman\' has been created by Event Organizer. Check it out!', 'event', 35, 0, '2026-04-05 07:55:29'),
(258, 5, 'New Event Launched!', 'A new event \'glassman\' has been created by Event Organizer. Check it out!', 'event', 35, 0, '2026-04-05 07:55:29'),
(260, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'glassman\'. Please review the details.', 'booking', 43, 0, '2026-04-05 07:55:55'),
(263, 2, 'New Advance Payment', 'A 50% advance payment of NPR 1,250.00 has been made by Roz Chaudhary for your event: glassman.', 'payment_alert', 43, 0, '2026-04-05 07:57:04'),
(266, 4, 'New Event Launched!', 'A new event \'helo\' has been created by Event Organizer. Check it out!', 'event', 36, 0, '2026-04-05 07:59:52'),
(267, 5, 'New Event Launched!', 'A new event \'helo\' has been created by Event Organizer. Check it out!', 'event', 36, 0, '2026-04-05 07:59:52'),
(269, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'helo\'. Please review the details.', 'booking', 44, 0, '2026-04-05 08:00:24'),
(272, 2, 'New Advance Payment', 'A 50% advance payment of NPR 1,250.00 has been made by Roz Chaudhary for your event: helo.', 'payment_alert', 44, 0, '2026-04-05 08:01:04'),
(275, 4, 'New Event Launched!', 'A new event \'hello\' has been created by Event Organizer. Check it out!', 'event', 37, 0, '2026-04-05 08:15:17'),
(276, 5, 'New Event Launched!', 'A new event \'hello\' has been created by Event Organizer. Check it out!', 'event', 37, 0, '2026-04-05 08:15:17'),
(278, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'hello\'. Please review the details.', 'booking', 45, 0, '2026-04-05 08:18:57'),
(281, 2, 'New Advance Payment', 'A 50% advance payment of NPR 1,250.00 has been made by  for your event: hello.', 'payment_alert', 45, 0, '2026-04-05 08:19:28'),
(284, 4, 'New Event Launched!', 'A new event \'dip\' has been created by Event Organizer. Check it out!', 'event', 38, 0, '2026-04-05 08:55:16'),
(285, 5, 'New Event Launched!', 'A new event \'dip\' has been created by Event Organizer. Check it out!', 'event', 38, 0, '2026-04-05 08:55:16'),
(287, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'dip\'. Please review the details.', 'booking', 46, 0, '2026-04-05 08:55:47'),
(290, 2, 'New Advance Payment', 'A 50% advance payment of NPR 1,250.00 has been made by Roz Chaudhary for your event: dip.', 'payment_alert', 46, 0, '2026-04-05 08:56:16'),
(294, 4, 'New Event Launched!', 'A new event \'tree\' has been created by Event Organizer. Check it out!', 'event', 39, 0, '2026-04-05 09:14:58'),
(295, 5, 'New Event Launched!', 'A new event \'tree\' has been created by Event Organizer. Check it out!', 'event', 39, 0, '2026-04-05 09:14:58'),
(297, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'tree\'. Please review the details.', 'booking', 47, 0, '2026-04-05 09:15:26'),
(300, 2, 'New Advance Payment', 'A 50% advance payment of NPR 500.00 has been made by Roz Chaudhary for your event: tree.', 'payment_alert', 47, 0, '2026-04-05 09:15:40'),
(318, 2, 'Cash Payment Received', 'An administrator has marked the booking for \'heloman\' as Fully Paid (Cash).', 'payment_alert', 42, 0, '2026-04-06 08:07:29'),
(321, 4, 'New Event Launched!', 'A new event \'Inside\' has been created by Event Organizer. Check it out!', 'event', 40, 0, '2026-04-06 08:14:03'),
(322, 5, 'New Event Launched!', 'A new event \'Inside\' has been created by Event Organizer. Check it out!', 'event', 40, 0, '2026-04-06 08:14:03'),
(324, 3, 'New Event Launched by Admin!', 'A new official event \'insdoe\' has been created. Register now!', 'event', 41, 0, '2026-04-06 08:16:03'),
(325, 4, 'New Event Launched by Admin!', 'A new official event \'insdoe\' has been created. Register now!', 'event', 41, 0, '2026-04-06 08:16:03'),
(326, 5, 'New Event Launched by Admin!', 'A new official event \'insdoe\' has been created. Register now!', 'event', 41, 0, '2026-04-06 08:16:03'),
(328, 3, 'New Event Launched!', 'A new event \'hel\' has been created by Event Organizer. Check it out!', 'event', 42, 0, '2026-04-06 08:17:55'),
(329, 4, 'New Event Launched!', 'A new event \'hel\' has been created by Event Organizer. Check it out!', 'event', 42, 0, '2026-04-06 08:17:55'),
(330, 5, 'New Event Launched!', 'A new event \'hel\' has been created by Event Organizer. Check it out!', 'event', 42, 0, '2026-04-06 08:17:55'),
(332, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'hel\'. Please review the details.', 'booking', 48, 0, '2026-04-06 08:19:14'),
(333, 3, 'Booking Request Received', 'Your \'hel\' booking request has been received and is being reviewed.', 'booking', 48, 0, '2026-04-06 08:19:14'),
(334, 3, 'Payment Received', 'We have received your 50% advance payment of NPR 2,500.00 for event: hel. Your booking is now pending organizer approval.', 'payment', 48, 0, '2026-04-06 08:21:57'),
(335, 2, 'New Advance Payment', 'A 50% advance payment of NPR 2,500.00 has been made by Roz Chaudhary for your event: hel.', 'payment_alert', 48, 0, '2026-04-06 08:21:57'),
(338, 3, 'Event Cancelled by Organizer', 'Sorry, the event \'hel\' has been removed by the organizer. we will refund your money as soon as possible as the event is cancelled and you already booked the event.', 'event_delete', 0, 0, '2026-04-06 08:22:25'),
(339, 3, 'New Event Launched by Admin!', 'A new official event \'hello\' has been created. Register now!', 'event', 43, 0, '2026-04-06 08:25:00'),
(340, 4, 'New Event Launched by Admin!', 'A new official event \'hello\' has been created. Register now!', 'event', 43, 0, '2026-04-06 08:25:00'),
(341, 5, 'New Event Launched by Admin!', 'A new official event \'hello\' has been created. Register now!', 'event', 43, 0, '2026-04-06 08:25:00'),
(344, 3, 'Booking Request Received', 'Your \'hello\' booking request has been received and is being reviewed.', 'booking', 49, 0, '2026-04-06 08:25:53'),
(345, 3, 'Payment Received', 'We have received your 50% advance payment of NPR 500.00 for event: hello. Your booking is now pending organizer approval.', 'payment', 49, 0, '2026-04-06 08:26:06'),
(348, 3, 'New Event Launched!', 'A new event \'helomam\' has been created by Event Organizer. Check it out!', 'event', 44, 0, '2026-04-06 08:32:15'),
(349, 4, 'New Event Launched!', 'A new event \'helomam\' has been created by Event Organizer. Check it out!', 'event', 44, 0, '2026-04-06 08:32:15'),
(350, 5, 'New Event Launched!', 'A new event \'helomam\' has been created by Event Organizer. Check it out!', 'event', 44, 0, '2026-04-06 08:32:15'),
(352, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'helomam\'. Please review the details.', 'booking', 50, 0, '2026-04-06 08:33:05'),
(353, 3, 'Booking Request Received', 'Your \'helomam\' booking request has been received and is being reviewed.', 'booking', 50, 0, '2026-04-06 08:33:05'),
(354, 3, 'Payment Received', 'We have received your 50% advance payment of NPR 1,250.00 for event: helomam. Your booking is now pending organizer approval.', 'payment', 50, 0, '2026-04-06 08:33:21'),
(355, 2, 'New Advance Payment', 'A 50% advance payment of NPR 1,250.00 has been made by Roz Chaudhary for your event: helomam.', 'payment_alert', 50, 0, '2026-04-06 08:33:21'),
(358, 3, 'New Event Launched!', 'A new event \'helloba\' has been created by Event Organizer. Check it out!', 'event', 45, 0, '2026-04-06 08:34:27'),
(359, 4, 'New Event Launched!', 'A new event \'helloba\' has been created by Event Organizer. Check it out!', 'event', 45, 0, '2026-04-06 08:34:27'),
(360, 5, 'New Event Launched!', 'A new event \'helloba\' has been created by Event Organizer. Check it out!', 'event', 45, 0, '2026-04-06 08:34:27'),
(362, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'helloba\'. Please review the details.', 'booking', 51, 0, '2026-04-06 08:35:08'),
(363, 3, 'Booking Request Received', 'Your \'helloba\' booking request has been received and is being reviewed.', 'booking', 51, 0, '2026-04-06 08:35:08'),
(364, 3, 'Payment Received', 'We have received your 50% advance payment of NPR 1,250.00 for event: helloba. Your booking is now pending organizer approval.', 'payment', 51, 0, '2026-04-06 08:35:52'),
(365, 2, 'New Advance Payment', 'A 50% advance payment of NPR 1,250.00 has been made by Roz Chaudhary for your event: helloba.', 'payment_alert', 51, 0, '2026-04-06 08:35:52'),
(367, 2, 'Booking Cancelled', 'Roz Chaudhary has cancelled their booking for \'helloba\'.', 'booking_cancel', 51, 0, '2026-04-06 08:36:14'),
(370, 3, 'New Event Launched!', 'A new event \'hello\' has been created by Event Organizer. Check it out!', 'event', 46, 0, '2026-04-06 13:06:13'),
(371, 4, 'New Event Launched!', 'A new event \'hello\' has been created by Event Organizer. Check it out!', 'event', 46, 0, '2026-04-06 13:06:13'),
(372, 5, 'New Event Launched!', 'A new event \'hello\' has been created by Event Organizer. Check it out!', 'event', 46, 0, '2026-04-06 13:06:13'),
(373, 3, 'Event Details Updated', 'The organizer has updated the details for \'hello\'. (Fields updated: Event Image) Please review.', 'event_update', 46, 0, '2026-04-06 13:07:02'),
(374, 4, 'Event Details Updated', 'The organizer has updated the details for \'hello\'. (Fields updated: Event Image) Please review.', 'event_update', 46, 0, '2026-04-06 13:07:02'),
(375, 5, 'Event Details Updated', 'The organizer has updated the details for \'hello\'. (Fields updated: Event Image) Please review.', 'event_update', 46, 0, '2026-04-06 13:07:02'),
(378, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'hello\'. Please review the details.', 'booking', 52, 0, '2026-04-06 13:08:15'),
(379, 3, 'Booking Request Received', 'Your \'hello\' booking request has been received and is being reviewed.', 'booking', 52, 0, '2026-04-06 13:08:15'),
(380, 3, 'Payment Received', 'We have received your 50% advance payment of NPR 500.00 for event: hello. Your booking is now pending organizer approval.', 'payment', 52, 0, '2026-04-06 13:09:15'),
(381, 2, 'New Advance Payment', 'A 50% advance payment of NPR 500.00 has been made by Roz Chaudhary for your event: hello.', 'payment_alert', 52, 0, '2026-04-06 13:09:15'),
(384, 3, 'Event Cancelled by Organizer', 'Sorry, the event \'hello\' has been removed by the organizer. we will refund your money as soon as possible as the event is cancelled and you already booked the event.', 'event_delete', 0, 0, '2026-04-06 13:10:16'),
(385, 3, 'Booking Confirmed', 'Your booking for \'helomam\' has been confirmed by the administration.', 'booking_approve', 50, 0, '2026-04-06 13:11:14'),
(386, 3, 'Payment Fully Paid (Cash)', 'Your payment for \'helomam\' has been recorded as Fully Paid (Cash). Thank you!', 'payment', 50, 0, '2026-04-06 13:11:26'),
(387, 2, 'Cash Payment Received', 'An administrator has marked the booking for \'helomam\' as Fully Paid (Cash).', 'payment_alert', 50, 0, '2026-04-06 13:11:26'),
(389, 3, 'New Event Launched!', 'A new event \'jocker\' has been created by Event Organizer. Check it out!', 'event', 47, 0, '2026-04-06 13:38:51'),
(390, 4, 'New Event Launched!', 'A new event \'jocker\' has been created by Event Organizer. Check it out!', 'event', 47, 0, '2026-04-06 13:38:51'),
(391, 5, 'New Event Launched!', 'A new event \'jocker\' has been created by Event Organizer. Check it out!', 'event', 47, 0, '2026-04-06 13:38:51'),
(393, 2, 'New Booking for Your Event', 'Roz Chaudhary has booked \'jocker\'. Please review the details.', 'booking', 53, 0, '2026-04-06 13:40:00'),
(394, 3, 'Booking Request Received', 'Your \'jocker\' booking request has been received and is being reviewed.', 'booking', 53, 0, '2026-04-06 13:40:00'),
(395, 3, 'Payment Received', 'We have received your 50% advance payment of NPR 100.00 for event: jocker. Your booking is now pending organizer approval.', 'payment', 53, 0, '2026-04-06 13:40:15'),
(396, 2, 'New Advance Payment', 'A 50% advance payment of NPR 100.00 has been made by Roz Chaudhary for your event: jocker.', 'payment_alert', 53, 0, '2026-04-06 13:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'card',
  `status` enum('succeeded','failed') NOT NULL DEFAULT 'succeeded',
  `stripe_session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `client_id`, `transaction_id`, `amount`, `payment_method`, `status`, `stripe_session_id`, `created_at`) VALUES
(1, 31, 3, 'pi_3TIhp4CnY2a26LqE0UDq8qwo', 864149.00, 'card', 'succeeded', 'cs_test_a1BZYTwijpHCFhHZzwEd1mNHgehz9BauLyMgdPPbyBX2Mq182HLy5Vr7fw', '2026-04-05 03:58:12'),
(2, 32, 3, 'pi_3TIi8nCnY2a26LqE1sVQPCAW', 1000.00, 'card', 'succeeded', 'cs_test_a1SVHQZQJBVuo3FNWa5WyqeAnihcs7goyhbn4daEalv6kUVy3rgLwtgArt', '2026-04-05 04:18:35'),
(3, 33, 3, 'pi_3TIiAwCnY2a26LqE0JdgCmFf', 500.00, 'card', 'succeeded', 'cs_test_a1gMnlKdqZCGFm0OqfhI8ONoRNBHGNV78akM6bKSgAG0QYks7VsCsUSAQN', '2026-04-05 04:20:47'),
(4, 35, 3, 'pi_3TIiQLCnY2a26LqE0REkwuEJ', 500.00, 'card', 'succeeded', 'cs_test_a1A9FGWadMahz6eQq2d42V2cvXfwwFWpGkmzjwTUZdCLlcqeVrjiaweOAU', '2026-04-05 04:36:42'),
(5, 37, 3, 'pi_3TIkdbCnY2a26LqE1niPcjJd', 13131.00, 'card', 'succeeded', 'cs_test_a1KdHrB4FNczkoT2VLBNuEXLe0jOYPt94L35inSLixt2mgEyYuIIwQwNmL', '2026-04-05 06:58:32'),
(6, 40, 3, 'pi_3TIksoCnY2a26LqE0N9Yf8kg', 141491.00, 'card', 'succeeded', 'cs_test_a1lhVxUZ80Cnp55W07ifKVxZ7nEFCSHty2w3TNDKlCx95RfnWsjhbnFFOb', '2026-04-05 07:14:16'),
(7, 39, 3, 'pi_3TIkwVCnY2a26LqE0euUywWQ', 24749.50, 'card', 'succeeded', 'cs_test_a149JdtJn4VIwJ27PUcFf2xfTbCBA9glMpBNG5sBjsdWTRv2c8LUGYcdGt', '2026-04-05 07:18:05'),
(8, 41, 3, 'pi_3TIl8XCnY2a26LqE0oTsrfC7', 13641.00, 'card', 'succeeded', 'cs_test_a16gx1YB6cfGbg1Xoen2pHNPd7QlxpevLWoDi5ejBkxwpnByVBzUxsxLRi', '2026-04-05 07:30:30'),
(9, 42, 3, 'pi_3TIlOxCnY2a26LqE0WxomSkZ', 950.00, 'card', 'succeeded', 'cs_test_a1ufnSBtHL3dSArauNUGzjcX5u7OXUUIeshppg4hO0WkRan0qaauofQpUM', '2026-04-05 07:47:28'),
(10, 43, 3, 'pi_3TIlYFCnY2a26LqE02OBlgNO', 1250.00, 'card', 'succeeded', 'cs_test_a1zBp4geBwu7Wh6LjYEYaXPIDFcIfdOlTRTuVnt8QEkeM9GawFJqxK6Tr4', '2026-04-05 07:57:04'),
(11, 44, 3, 'pi_3TIlc7CnY2a26LqE1TI6d6M5', 1250.00, 'card', 'succeeded', 'cs_test_a1Y9sjeQCw7F7OWgl7eqSn95XzFqNnzydwBQmDkorO8yb7ANO0YKWI9CGg', '2026-04-05 08:01:04'),
(12, 45, 3, 'pi_3TIltvCnY2a26LqE0jKgpwCx', 1250.00, 'card', 'succeeded', 'cs_test_a1ATjyz1Umy6eQ3Uwn9jXB3pSCz5mLWm0zgVb8abTmaes6zD6JZmN4Tfdz', '2026-04-05 08:19:28'),
(13, 46, 3, 'pi_3TImTXCnY2a26LqE17oC8odI', 1250.00, 'card', 'succeeded', 'cs_test_a15SXeUedoyRtv2SxSU3Amn9w8mXLq2wVY0N5klFhC1mgGlMauts61bFWX', '2026-04-05 08:56:16'),
(14, 47, 3, 'pi_3TImmJCnY2a26LqE0XmvgfOn', 500.00, 'card', 'succeeded', 'cs_test_a12sXZ2lcGmaG533D1KQs1Or67jwM8V5X1TpvVh4xoRXyOUSvON5PYhYOj', '2026-04-05 09:15:40'),
(16, 49, 3, 'pi_3TJ8TtCnY2a26LqE05yzPyWp', 500.00, 'card', 'succeeded', 'cs_test_a1aPFI3TLZ76KmqDDbGaIiG3Q6yyyPfw9ee8q1EAoFW19EOmarnTd5aqsx', '2026-04-06 08:26:06'),
(17, 50, 3, 'pi_3TJ8atCnY2a26LqE15OBpWGJ', 1250.00, 'card', 'succeeded', 'cs_test_a1Su3YKQd1g5Yqm0asaYuu9AbCK0imKVao18umZ2rfPhxQ1NFDmkewRLFT', '2026-04-06 08:33:21'),
(18, 51, 3, 'pi_3TJ8dLCnY2a26LqE0Vi4FZu5', 1250.00, 'card', 'succeeded', 'cs_test_a1KPR6c0G4xumFRf50qQPPnBOcfykzP07qngS92VDTcSdftvxezK6uyRdu', '2026-04-06 08:35:52'),
(20, 53, 3, 'pi_3TJDNuCnY2a26LqE0TY4Zu3h', 100.00, 'card', 'succeeded', 'cs_test_a1enTaC4P1joiF1WvZ6v9rcyO1YtQdbBOvEXK2acbWgimVPwZY11KYcU14', '2026-04-06 13:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','organizer','client') NOT NULL DEFAULT 'client',
  `is_verified` tinyint(1) DEFAULT 0,
  `is_blocked` tinyint(1) DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `is_verified`, `is_blocked`, `otp_code`, `otp_expires_at`, `profile_picture`, `created_at`) VALUES
(1, 'System Admin', 'admin@ems.com', 'admin123', 'admin', 1, 0, NULL, NULL, NULL, '2026-03-26 08:09:25'),
(2, 'Event Organizer', 'organizer@ems.com', 'org123', 'organizer', 1, 0, NULL, NULL, NULL, '2026-03-26 08:09:25'),
(3, 'Roz Chaudhary', 'benduliaroz@gmail.com', '$2y$10$0F4ajF6AM9AtBtCX8.Uaue130frbUlb/RQ/OrVmBfPbe2N4bmySOi', 'client', 1, 0, '232279', '2026-03-31 03:55:29', '/EventManagementSystem/public/assets/images/profiles/profile_3_1775481111.png', '2026-03-26 08:13:50'),
(4, 'Test Client', 'testclient@example.com', '$2y$10$KXws00iA9l6xLPfXj800KewjgXGJ4uFNk2E.EuBsSi/oZGQwMjQFS', 'client', 0, 0, '344000', '2026-04-03 05:49:17', NULL, '2026-04-01 17:01:33'),
(5, 'Test User', 'testuser@example.com', '$2y$10$6Ya5jjG17O0ICeGgO/jg4OeJ1DJyVBuYca9DdozPCg1g95QIhD9Xe', 'client', 0, 0, '423195', '2026-04-01 19:17:08', NULL, '2026-04-01 17:07:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `client_id` (`client_id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=398;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
