-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2025 at 11:21 PM
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
-- Database: `agriconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `agri_officers`
--

CREATE TABLE `agri_officers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agri_officers`
--

INSERT INTO `agri_officers` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`) VALUES
(1, 'Rayhanul', 'Islam', 'rayhan@gmail.com', '$2y$10$DTauROb468/9oR8J1ZX/1O8ONZTsbEuxPChprSCzpAq/b/SukIzUm', '2025-04-21 11:24:11'),
(2, 'Sakib', 'Bosunia', 'sakib@gmail.com', '$2y$10$YHprbkjOlxmMlz0O9V7Vk.gc3qvg//GS9I25bKHe7CBBgEsKytivG', '2025-07-22 09:21:45'),
(3, 'Rayhanul', 'Islam', 'Rayhan2005@gmail.com', '$2y$10$FUrdN5BvHZGNUNGf4dKDk.5xSTQjNxuRym9pCJYONfC7mRstyLJr2', '2025-08-08 04:05:24'),
(4, 'Safanur', 'Islam', 'safa@gmail.com', '$2y$10$ffNfKM4RVUYY8yxgDGNgcOd9nGB8vCG3IJs5I/tsB/VrGTEdsmUrK', '2025-08-08 04:50:10'),
(5, 'Ayesha', 'Siddika', 'ayesha@gmail.com', '$2y$10$zmkI5p55gnmbp6ySgV599OdJIxcUxOv.fMpkWEgHUBbp.upAc2lAa', '2025-08-08 04:55:51'),
(6, 'Shohedozzaman', 'Shakib', 'bosu@gmail.com', '$2y$10$uV6pnP7rWM54zW3y3.zMA.TIUBtS2b9jvreBQoSv91s1tkrt4SXia', '2025-08-12 06:14:20'),
(7, 'Basunia', 'Shakib', 'shakibbasunia6@gmail.com', '$2y$10$wqSwO30TN3T.AqhCZBs59.nHE9HFU07sb7AXzd3wgz0V5HzzDH37u', '2025-08-15 10:38:44'),
(9, 'jebfbE', 'hjbdsdsd', 'shakibbasunia46@gmail.com', '$2y$10$UWd8CRNlbFlz6S8BbZQiXu49REO127LUBpWn4fUazhrBj8epyKIjS', '2025-08-15 19:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pdf_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `message`, `created_at`, `pdf_path`) VALUES
(5, '📢 বিভাগীয় কৃষি অফিস থেকে সরাসরি আপডেট\r\n\r\nআজকের ধান • শাক • সার • বীজের দাম\r\n\r\n📅 [২২/৭/২০২৫]\r\n\r\n🌾 ধান: ব্রি-২৮ – ৳১,০৫০/মন\r\n🥬 শাকসবজি: লাউ – ৳৪৫/কেজি\r\n🧪 সার: ইউরিয়া – ৳১৬/কেজি\r\n🌱 বীজ: ধান বীজ – ৳৩৫/কেজি', '2025-07-22 09:04:37', 'uploads/1754630508_krishi_crops_announcement.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `crop_name`) VALUES
(1, 'Rice'),
(2, 'wheat'),
(3, 'Tomato'),
(4, 'Jute'),
(5, 'Spinach'),
(6, 'Carrot'),
(7, 'Onion'),
(8, 'Potato');

-- --------------------------------------------------------

--
-- Table structure for table `crop_prices`
--

CREATE TABLE `crop_prices` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(100) DEFAULT NULL,
  `min_price` decimal(10,2) DEFAULT NULL,
  `max_price` decimal(10,2) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_prices`
--

INSERT INTO `crop_prices` (`id`, `crop_name`, `min_price`, `max_price`, `updated_at`) VALUES
(7, 'wheat', 100.00, 150.00, '2025-04-22 22:07:58'),
(16, 'Lentils (Masoor)', NULL, NULL, '2025-07-22 09:12:28'),
(17, 'Rice', 65.00, 95.00, '2025-07-22 09:12:59'),
(19, 'Egg', NULL, NULL, '2025-08-15 20:29:13'),
(21, 'Tomato', NULL, NULL, '2025-08-15 20:53:14'),
(22, 'Onion', NULL, NULL, '2025-08-15 20:53:25'),
(23, 'Carrot', NULL, NULL, '2025-08-15 20:53:41'),
(24, 'Mango', NULL, NULL, '2025-08-15 20:54:00'),
(25, 'Papaya', NULL, NULL, '2025-08-15 20:54:09'),
(26, 'Guava', NULL, NULL, '2025-08-15 20:54:19'),
(27, 'jute ', NULL, NULL, '2025-08-15 20:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_rentals`
--

CREATE TABLE `equipment_rentals` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qty_available` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_rentals`
--

INSERT INTO `equipment_rentals` (`id`, `farmer_id`, `name`, `description`, `price`, `contact_phone`, `media`, `created_at`, `qty_available`) VALUES
(3, 2, 'Tractor', 'rafi tractor', 2000.00, '01842542469', 'uploads/1745334431_874df0f2-9b3d-4ea1-9db3-34342e24e0d6.png', '2025-04-22 15:07:11', 7);

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `nid` varchar(30) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `first_name`, `last_name`, `email`, `phone`, `nid`, `area`, `image`, `password`) VALUES
(1, 'Abdullah ', 'Rafi', 'Rafi@gmail.com', '01842542469', '5527719784', 'Kaliakair', 'uploads/ef029bd0-46a2-476b-99fa-472ab5fcb2f3.png', '$2y$10$luk5fEWb3Uq7/lY/Xag8XOy83gagIWxrj6Ch/Cu77E6PkP.q8Nse2'),
(2, 'Abdullah ', 'RafiMan', 'rafiabdullah1507@gmail.com', '01776677228', '5527719784', 'bangladesh', 'uploads/51792fd9-57f9-4cbf-be1f-1883c8e45bf2.png', '$2y$10$nzqyJRn8VLdbRR5GUXKBu.6FSNRbHtOtVzzYKmtToyEGcKhlmMhj6'),
(101, 'মোঃ সজল', 'আলী', 'sojal.ali@gmail.com', '01710000001', '1990010100001', 'Salna', NULL, '123456'),
(102, 'রহিম', 'উদ্দিন', 'rahim.uddin@gmail.com', '01710000002', '1990020200002', 'Fulbaria', NULL, '123456'),
(103, 'জাহিদ', 'হোসেন', 'jahid.hossain@gmail.com', '01710000003', '1990030300003', 'Kaliakair', NULL, '123456'),
(104, 'মোছা', 'ফাতেমা', 'fatema.khatun@gmail.com', '01710000004', '1990040400004', 'Boliyadi', NULL, '123456'),
(105, 'আব্দুল', 'কাদের', 'abdul.quader@gmail.com', '01710000005', '1990050500005', 'Kaliakair', NULL, '123456'),
(106, 'Basunia', 'Shakib', 'shakibbasunia6@gmail.com', '01706783199', '', 'kaliakoir, GAzipur', 'uploads/296A1313.JPG', '$2y$10$Hh0kpY8OPDsdlOkDSh0Ic.i4Dws7rpUUki13amyWWIAy.3uQShjFK'),
(107, 'Rayhan', 'Islam', '200203@icte.bdu.ac.bd', '01706883147', '', 'Hijoltoli', 'uploads/689d8805130836.58316085.jpg', '$2y$10$by3kIOAwO/CBSFwlsFS3IOa0XyBhSjpZckNU/o1CXHTMzVtzOESEy');

-- --------------------------------------------------------

--
-- Table structure for table `farmer_crops`
--

CREATE TABLE `farmer_crops` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `crop_name` varchar(100) DEFAULT NULL,
  `quantity_kg` float DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_crops`
--

INSERT INTO `farmer_crops` (`id`, `farmer_id`, `crop_name`, `quantity_kg`, `address`, `phone`, `submitted_at`) VALUES
(1, 1, 'Rice', 20, 'Kaliakair', '01842542469', '2025-04-22 21:37:24'),
(2, 1, 'Rice', 20, 'Kaliakair', '01842542469', '2025-04-22 21:37:55'),
(3, 1, 'Rice', 20, 'Kaliakair', '01842542469', '2025-04-22 22:07:21'),
(4, 1, 'wheat', 200, 'Kaliakair', '01776677228', '2025-04-22 22:07:34'),
(6, 1, 'rice', 20, 'Kaliakair', '01842542469', '2025-04-22 22:12:26'),
(9, 1, 'Lentils (Masoor)', 20, 'Dhaka', '01701036189', '2025-08-08 08:12:44'),
(11, 106, 'Rice', 100, 'Simultoli', '01720847702', '2025-08-15 20:36:10'),
(12, 107, 'Rice', 100, '60', '1706984188', '2025-08-15 20:59:30'),
(13, 107, 'Onion', 100, '120', '1706984188', '2025-08-15 20:59:55'),
(14, 107, 'Carrot', 200, 'Sripur', '1706984188', '2025-08-15 21:00:17');

-- --------------------------------------------------------

--
-- Table structure for table `farmer_cultivations`
--

CREATE TABLE `farmer_cultivations` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `crop_id` int(11) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `quantity_kg` float DEFAULT NULL,
  `sell_price_per_kg` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_cultivations`
--

INSERT INTO `farmer_cultivations` (`id`, `farmer_id`, `crop_id`, `year`, `quantity_kg`, `sell_price_per_kg`) VALUES
(4, 1, 1, '2002', 12, 10),
(5, 1, 1, '2002', 50000, 20),
(6, 2, 1, '2002', 123, 500),
(7, 2, 1, '2002', 123, 500),
(8, 2, 2, '2002', 20000, 100),
(9, 106, 3, '2025', 50, 20),
(10, 106, 3, '2024', 40, 25),
(11, 106, 4, '2025', 500, 400),
(12, 106, 6, '2025', 200, 60),
(13, 106, 8, '2025', 400, 60);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_products`
--

CREATE TABLE `farmer_products` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_products`
--

INSERT INTO `farmer_products` (`id`, `farmer_id`, `product_name`, `quantity`, `price_per_kg`, `image`, `description`, `created_at`) VALUES
(3, 1, 'ধান', 480, 55.00, 'uploads/6895b68971f898.08888347.jpg', '🌾 ব্রি ধান-২৮ বিক্রি করা হবে!\r\nউচ্চফলনশীল, চিকন চালের ধান। একদম প্রিমিয়াম কোয়ালিটি।\r\nযোগাযোগ করুন এখনই – 📞 017102102020\r\nদ্রুত নিন, স্টক সীমিত!', '2025-08-08 08:34:17'),
(6, 1, 'পেঁয়াজ', 280, 55.00, 'uploads/onion.jpeg', 'তাজা পেঁয়াজ, বাজারজাতের জন্য প্রস্তুত', '2025-08-08 06:40:00'),
(12, 1, 'লাউ', 89, 50.00, 'uploads/bottleground.jpg', 'বড় লাউ, বাজারে চাহিদা বেশি', '2025-08-08 07:10:00'),
(13, 106, 'Potato', 30, 20.00, 'uploads/689f9bd1629291.70650138.jpg', 'Frash Tomato', '2025-08-15 20:42:57'),
(14, 107, 'Rice', 0, 20.00, 'uploads/689fa00ed82977.63950688.png', 'Chini gura rice', '2025-08-15 21:01:02'),
(15, 107, 'Potato', 490, 20.00, 'uploads/689fa038c0ad01.78277726.jpg', 'red ', '2025-08-15 21:01:44');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `wholeseller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `quantity` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_type` enum('farmer','agri_officer') NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_type`, `user_id`, `content`, `image`, `video`, `audio`, `created_at`, `attachment`) VALUES
(1, 'farmer', 101, 'স্যার, ধানের পাতায় বাদামি দাগ দেখা যাচ্ছে এবং গাছগুলো হলুদ হয়ে যাচ্ছে। কীটনাশক দিয়েও ঠিক হচ্ছে না। দয়া করে পরামর্শ দিন।', NULL, NULL, NULL, '2025-08-08 04:30:00', NULL),
(7, 'farmer', 101, 'স্যার, ধানের পাতায় বাদামি দাগ দেখা যাচ্ছে এবং গাছগুলো হলুদ হয়ে যাচ্ছে। কীটনাশক দিয়েও ঠিক হচ্ছে না। দয়া করে পরামর্শ দিন।', NULL, NULL, NULL, '2025-08-08 04:30:00', NULL),
(8, 'farmer', 102, 'আমার জমির মাটিতে বারবার চাষ করলেও ফলন কমে যাচ্ছে। কোন সার ব্যবহার করবো?', NULL, NULL, NULL, '2025-08-08 05:00:00', NULL),
(9, 'farmer', 103, 'আলু গাছ দ্রুত পচে যাচ্ছে, স্যার। এর প্রতিকার কী?', NULL, NULL, NULL, '2025-08-08 05:15:00', NULL),
(10, 'farmer', 104, 'জৈব সার তৈরি করার সহজ পদ্ধতি জানতে চাই। কোনো টিউটোরিয়াল থাকলে দেবেন দয়া করে।', NULL, 'https://www.youtube.com/watch?v=examplevideo', NULL, '2025-08-08 05:30:00', NULL),
(11, 'farmer', 105, 'সার ও বীজের দাম অনেক বেড়ে গেছে। সরকারিভাবে কোন সহায়তা পাওয়া যাবে কি?', NULL, NULL, NULL, '2025-08-08 05:45:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rental_bookings`
--

CREATE TABLE `rental_bookings` (
  `id` int(11) NOT NULL,
  `rental_id` int(11) DEFAULT NULL,
  `booked_by_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `status` enum('Booked','Completed') DEFAULT 'Booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qty` int(11) NOT NULL DEFAULT 1,
  `time_slot` varchar(50) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental_bookings`
--

INSERT INTO `rental_bookings` (`id`, `rental_id`, `booked_by_id`, `customer_name`, `location`, `date`, `time`, `contact_phone`, `status`, `created_at`, `qty`, `time_slot`, `contact_number`) VALUES
(1, 2, 2, 'Abdullah  RafiMan', 'Kaliakir', '2025-04-23', NULL, NULL, 'Completed', '2025-04-22 15:04:51', 1, 'Afternoon', '01842542469'),
(2, 3, 1, 'Abdullah  Rafi', 'Kaliakir', '2025-04-23', NULL, NULL, 'Completed', '2025-04-22 15:08:16', 3, 'Evening', '01842542469'),
(3, 2, 2, 'Farmer X', 'Kaliakir', '2025-04-24', NULL, NULL, 'Completed', '2025-04-22 15:30:28', 1, 'Morning', '01842542469');

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `parent_reply_id` int(11) DEFAULT NULL,
  `user_type` enum('farmer','agri_officer') NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`id`, `post_id`, `parent_reply_id`, `user_type`, `user_id`, `content`, `image`, `video`, `audio`, `created_at`, `attachment`) VALUES
(2, 11, NULL, 'agri_officer', 5, 'সরকারি ভাবে কৃষকদের জন্য সার ও বীজে ভর্তুকি বা ছাড় পাওয়া যায়। স্থানীয় কৃষি অফিসে যোগাযোগ করলে সাহায্য মিলবে।', NULL, NULL, NULL, '2025-08-08 05:58:45', NULL),
(3, 10, NULL, 'agri_officer', 5, 'জৈব সার তৈরি করার সহজ পদ্ধতি হলো:\r\n\r\n১. গোবর, পাতা, রান্নার বর্জ্য একসঙ্গে মাটির খুঁড়ে রাখুন।\r\n২. পানি দিয়ে ভেজানো রাখুন।\r\n৩. সময় সময় চুলোটা মিশিয়ে দিন, ২-৩ মাসে সার প্রস্তুত হয়ে যাবে।', NULL, NULL, NULL, '2025-08-08 05:59:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `seminars`
--

CREATE TABLE `seminars` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `seminar_date` date NOT NULL,
  `seminar_time` time NOT NULL,
  `video_link` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seminars`
--

INSERT INTO `seminars` (`id`, `title`, `seminar_date`, `seminar_time`, `video_link`, `created_at`) VALUES
(1, 'টেকসই কৃষি প্রযুক্তি ও ভবিষ্যৎ পরিকল্পনা', '2025-08-12', '10:30:00', 'https://youtu.be/agri_seminar1', '2025-08-08 03:00:00'),
(2, 'জৈব সার ব্যবহারে কৃষকের অভিজ্ঞতা', '2025-08-15', '11:00:00', 'https://youtu.be/agri_seminar2', '2025-08-08 03:30:00'),
(3, 'আধুনিক সেচ ব্যবস্থাপনা', '2025-08-18', '09:00:00', 'https://youtu.be/agri_seminar3', '2025-08-08 04:00:00'),
(4, 'ড্রোন প্রযুক্তি ও ফসল পর্যবেক্ষণ', '2025-08-20', '14:00:00', 'https://youtu.be/agri_seminar4', '2025-08-08 04:30:00'),
(5, 'কৃষিপণ্যের ডিজিটাল বিপণন', '2025-08-22', '15:00:00', 'https://youtu.be/agri_seminar5', '2025-08-08 05:00:00'),
(7, 'জৈব চাষাবাদ ও পরিবেশবান্ধব কৃষি: টেকসই ভবিষ্যতের পথে', '2025-06-19', '14:00:00', 'https://www.google.com/', '2025-07-22 08:42:00'),
(8, 'কৃষিতে জল ব্যবস্থাপনা: পানির সঠিক ব্যবহার ও সংরক্ষণ কৌশল', '2025-07-21', '13:00:00', 'https://www.google.com/', '2025-07-22 08:42:33'),
(10, 'বীজ কেনার সময় কীভাবে ভালো বীজ চিহ্নিত করব?', '2025-09-02', '15:00:00', 'https://www.google.com/', '2025-07-22 09:49:59'),
(14, 'Farm Digital Tools & Equipment ', '2025-08-19', '02:43:00', 'https://calendar.google.com/calendar/u/0/r?hl=en&pli=1', '2025-08-15 19:46:33');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `wholeseller_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `shipment_status` enum('In Transit','Shipped','Received') DEFAULT 'In Transit',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `farmer_id`, `wholeseller_id`, `product_name`, `amount`, `shipment_status`, `transaction_date`, `product_id`, `quantity`) VALUES
(1, 1, 1, 'Rice', 92.00, 'Received', '2025-04-28 09:33:46', 1, 0),
(2, 1, 1, 'Rice', 46.00, 'In Transit', '2025-04-28 09:34:24', 1, 0),
(3, 1, 1, 'Rice', 900.00, 'Received', '2025-04-28 10:04:17', 2, 0),
(4, 1, 3, 'পেঁয়াজ', 1100.00, 'In Transit', '2025-08-08 09:24:58', 6, 0),
(5, 1, 3, 'ধান', 1100.00, 'In Transit', '2025-08-08 09:35:33', 3, 0),
(6, 107, 9, 'Potato', 200.00, 'In Transit', '2025-08-15 21:07:14', 15, 0),
(7, 107, 9, 'Rice', 400.00, 'In Transit', '2025-08-15 21:07:44', 14, 0),
(8, 107, 9, 'Rice', 200.00, 'In Transit', '2025-08-15 21:15:45', 14, 0),
(9, 1, 9, 'লাউ', 50.00, 'In Transit', '2025-08-15 21:19:50', 12, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tutorials`
--

CREATE TABLE `tutorials` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media` varchar(255) NOT NULL,
  `agri_officer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorials`
--

INSERT INTO `tutorials` (`id`, `title`, `description`, `media`, `agri_officer_id`, `created_at`) VALUES
(8, '', '', '', 1, '2025-07-22 09:09:23'),
(9, 'বীজতলা ধানের চারাগাছ ব্লাইট রোগ: লক্ষণ, কারণ ও প্রতিকার', 'ধানের বীজতলা ব্লাইট রোগের বর্ণনা:\r\n\r\nবীজতলা ধানের চারাগাছ ব্লাইট রোগ এক ধরনের ফসলের রোগ, যা সাধারণত ধানের চারা বা বীজতলায় দেখা দেয়। এই রোগের কারণে ধানের চারাগাছের পাতা বা লিফ ব্লাইট হয়ে শুকিয়ে যেতে পারে, এবং এতে চারা দুর্বল হয়ে পড়ে। ব্লাইট রোগ সাধারণত মোল্ড বা ফাঙ্গাসের কারণে হয়, বিশেষত পাতা, বীজপত্র, এবং মূলের মধ্যে আক্রমণ করে।\r\n\r\nলক্ষণ:\r\n\r\nপাতায় হলুদ দাগ দেখা দেয়, যা পরে বাদামী হয়ে শুকিয়ে যায়।\r\n\r\nচারাগাছের বৃদ্ধি কমে যায়, এবং কিছু ক্ষেত্রে গাছ মারা যেতে পারে।\r\n\r\nআক্রান্ত স্থানে সাদা ফাঙ্গাসের উপস্থিতি দেখা যেতে পারে।\r\n\r\nকারণ:\r\n\r\nঅতিরিক্ত আর্দ্রতা এবং অল্প বাতাসে এই রোগের বিস্তার ঘটে।\r\n\r\nফাঙ্গাল ইনফেকশন বা মাটির অস্বাস্থ্যকর পরিস্থিতি।\r\n\r\nপ্রতিকার:\r\n\r\nআক্রান্ত গাছ অপসারণ করা।\r\n\r\nচারা রোপণের আগে মাটি ভালোভাবে পরিষ্কার করা।\r\n\r\nকীটনাশক বা ফাঙ্গিসাইড প্রয়োগ করা।\r\n\r\nএই রোগের মোকাবেলা করে ধানের উৎপাদন ভালো রাখা সম্ভব।', 'https://www.youtube.com/watch?v=3PImEaTYcjw', 1, '2025-07-22 10:23:38'),
(10, 'Farm Digital Tools & Equipment Pictures and Names', 'Farm Digital Tools & Equipment Pictures and Names', 'uploads/1755287474_1.jpg', 7, '2025-08-15 19:51:14'),
(11, 'Farm Digital Tools & Equipment part 2', 'Farm Digital Tools & Equipment Pictures and Names part 2', 'uploads/1755287535_2.jpg', 7, '2025-08-15 19:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_queries`
--

CREATE TABLE `tutorial_queries` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `query` text NOT NULL,
  `status` enum('Pending','Answered') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorial_queries`
--

INSERT INTO `tutorial_queries` (`id`, `tutorial_id`, `farmer_id`, `query`, `status`, `created_at`) VALUES
(3, 8, 1, 'ধানের কোন জাত ভালো ফলন দেবে এই এলাকায়?\r\n', 'Pending', '2025-07-22 09:35:02'),
(4, 8, 1, 'সঠিক সেচ ব্যবস্থা কিভাবে করব?\n', 'Pending', '2025-07-22 09:35:10'),
(5, 8, 1, 'কৃষি যন্ত্রপাতি ব্যবহারে কি ধরনের সহায়তা পাওয়া যায়?\r\n\r\n', 'Pending', '2025-07-22 09:35:20');

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_replies`
--

CREATE TABLE `tutorial_replies` (
  `id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL,
  `agri_officer_id` int(11) NOT NULL,
  `reply` text NOT NULL,
  `media` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wholesellers`
--

CREATE TABLE `wholesellers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `nid` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesellers`
--

INSERT INTO `wholesellers` (`id`, `name`, `email`, `phone`, `nid`, `password`, `created_at`) VALUES
(1, 'Abdullah Al Rafi', 'Rafi@gmail.com', '01842542469', '5527719784', '$2y$10$Hz13ssS7fkfe0R4GZnNzceBEWXNbbTQmx.pEy2thovl3SSW2/KM/m', '2025-04-27 20:29:57'),
(2, 'Sakib Bosunia', 'sakib@gmail.com', '1234567892', '235465', '$2y$10$JnfF3al5H1t.lsL4P.FQHuvj4XWhVutWXC4ftUSD/9tNemGdRjx72', '2025-07-22 09:45:21'),
(3, 'Rayhanul Islam', 'rayhan@gmail.com', '1234567892', '64575345', '$2y$10$xkLawC1zOBdR5m0sD/2yqe5IPLoH67nDGgM2RVgRDJDsxX2QNaIUS', '2025-07-22 10:33:44'),
(6, 'Rayhanul Islam', 'rayhann@gmail.com', '12345678922', '645753452', '$2y$10$.fJRRvMzcRJX2/RM/JdqYOii2dkDejt2o/p1Y/8sSLMvNeDYL/ub.', '2025-07-22 10:36:01'),
(8, 'Sakib Emon', 'emon@gmail.com', '12345567992', '571562286', '$2y$10$3CabWVY3mKiBfTeukWC5DetdsDChXFp9oLi6SXCgcJIIQ2i352Wdi', '2025-08-08 09:55:59'),
(9, 'MD Shohedozzaman Basunia Shakib', 'shakibbasunia6@gmail.com', '01706783199', '3313825063', '$2y$10$GCUUZJ8AaHRVQzDjgKszj.j8sFFbCtYQHXH3bSkdgO7fWk70DuTnO', '2025-08-15 21:06:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agri_officers`
--
ALTER TABLE `agri_officers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_prices`
--
ALTER TABLE `crop_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crop_name` (`crop_name`);

--
-- Indexes for table `equipment_rentals`
--
ALTER TABLE `equipment_rentals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmers`
--
ALTER TABLE `farmers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `farmer_crops`
--
ALTER TABLE `farmer_crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmer_cultivations`
--
ALTER TABLE `farmer_cultivations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `farmer_products`
--
ALTER TABLE `farmer_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wholeseller_id` (`wholeseller_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rental_bookings`
--
ALTER TABLE `rental_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_booker` (`booked_by_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `parent_reply_id` (`parent_reply_id`);

--
-- Indexes for table `seminars`
--
ALTER TABLE `seminars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`),
  ADD KEY `wholeseller_id` (`wholeseller_id`);

--
-- Indexes for table `tutorials`
--
ALTER TABLE `tutorials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agri_officer_id` (`agri_officer_id`);

--
-- Indexes for table `tutorial_queries`
--
ALTER TABLE `tutorial_queries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorial_id` (`tutorial_id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `tutorial_replies`
--
ALTER TABLE `tutorial_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `query_id` (`query_id`),
  ADD KEY `agri_officer_id` (`agri_officer_id`);

--
-- Indexes for table `wholesellers`
--
ALTER TABLE `wholesellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agri_officers`
--
ALTER TABLE `agri_officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `crop_prices`
--
ALTER TABLE `crop_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `equipment_rentals`
--
ALTER TABLE `equipment_rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `farmer_crops`
--
ALTER TABLE `farmer_crops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `farmer_cultivations`
--
ALTER TABLE `farmer_cultivations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `farmer_products`
--
ALTER TABLE `farmer_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rental_bookings`
--
ALTER TABLE `rental_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seminars`
--
ALTER TABLE `seminars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tutorials`
--
ALTER TABLE `tutorials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tutorial_queries`
--
ALTER TABLE `tutorial_queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tutorial_replies`
--
ALTER TABLE `tutorial_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wholesellers`
--
ALTER TABLE `wholesellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `farmer_cultivations`
--
ALTER TABLE `farmer_cultivations`
  ADD CONSTRAINT `farmer_cultivations_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`),
  ADD CONSTRAINT `farmer_cultivations_ibfk_2` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`);

--
-- Constraints for table `farmer_products`
--
ALTER TABLE `farmer_products`
  ADD CONSTRAINT `farmer_products_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `farmers` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `farmers` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`wholeseller_id`) REFERENCES `wholesellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `farmer_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rental_bookings`
--
ALTER TABLE `rental_bookings`
  ADD CONSTRAINT `fk_booker` FOREIGN KEY (`booked_by_id`) REFERENCES `farmers` (`id`);

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`parent_reply_id`) REFERENCES `replies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`wholeseller_id`) REFERENCES `wholesellers` (`id`);

--
-- Constraints for table `tutorials`
--
ALTER TABLE `tutorials`
  ADD CONSTRAINT `tutorials_ibfk_1` FOREIGN KEY (`agri_officer_id`) REFERENCES `agri_officers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tutorial_queries`
--
ALTER TABLE `tutorial_queries`
  ADD CONSTRAINT `tutorial_queries_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tutorial_queries_ibfk_2` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tutorial_replies`
--
ALTER TABLE `tutorial_replies`
  ADD CONSTRAINT `tutorial_replies_ibfk_1` FOREIGN KEY (`query_id`) REFERENCES `tutorial_queries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tutorial_replies_ibfk_2` FOREIGN KEY (`agri_officer_id`) REFERENCES `agri_officers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
