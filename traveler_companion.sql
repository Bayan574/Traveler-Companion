-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 01:11 AM
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
-- Database: `traveler_companion`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `name`, `created_at`) VALUES
(1, 'admin@airport.local', '$2y$10$60asGJMIU6JZ9z8W4G7KS.5buG/WK5M1yY60k.fOk9zw2wN2EU4kq', 'System Administrator', '2025-11-22 20:49:05');

-- --------------------------------------------------------

--
-- Table structure for table `crowd_status`
--

CREATE TABLE `crowd_status` (
  `place_id` int(11) NOT NULL,
  `crowd_count` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crowd_status`
--

INSERT INTO `crowd_status` (`place_id`, `crowd_count`, `updated_at`) VALUES
(13, 0, '2025-12-08 23:02:07'),
(14, 0, '2025-11-29 00:48:58'),
(15, 0, '2025-12-01 06:27:09'),
(16, 0, '2025-12-06 15:06:00'),
(17, 0, '2025-11-25 01:45:03'),
(18, 0, '2025-11-23 13:00:52'),
(19, 0, '2025-11-23 13:00:52'),
(20, 0, '2025-11-25 01:45:03'),
(21, 0, '2025-11-29 00:48:58'),
(22, 0, '2025-11-30 06:55:18'),
(23, 0, '2025-12-07 06:47:06'),
(24, 0, '2025-11-25 01:45:03');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `place_id`, `item_name`, `price`, `img`, `created_at`) VALUES
(23, 13, 'Mac Crispy', 30.00, 'menu_69222fe0898155.26841772_1763848160.jpg', '2025-11-22 21:49:20'),
(24, 13, 'Spicy Big Tast', 30.00, 'menu_692230c6a31d41.33243645_1763848390.jpg', '2025-11-22 21:53:10'),
(25, 13, 'Grand Chicken Special', 28.00, 'menu_6922310fc669f9.98334919_1763848463.jpg', '2025-11-22 21:54:24'),
(26, 13, 'Grand Chicken Deluxe Meal', 33.00, 'menu_6922314b31dcc7.58010852_1763848523.jpg', '2025-11-22 21:55:23'),
(27, 13, 'Mac Royal', 30.00, 'menu_6922316b0e0903.80803337_1763848555.jpg', '2025-11-22 21:55:55'),
(28, 13, 'Arabic Mac', 23.00, 'menu_69223188a9d9a1.43288006_1763848584.jpg', '2025-11-22 21:56:24'),
(29, 13, 'Galaxy Cheesy', 20.00, 'menu_692231a8744829.72642954_1763848616.jpg', '2025-11-22 21:56:56'),
(30, 13, 'Wedges Fries', 8.00, 'menu_692231c5d0e6a1.52306833_1763848645.jpg', '2025-11-22 21:57:26'),
(31, 13, 'Fries', 7.00, 'menu_692231e56d8845.13737948_1763848677.jpg', '2025-11-22 21:57:57'),
(32, 13, 'Caesar Salad', 10.00, 'menu_6922320675e674.32545978_1763848710.jpg', '2025-11-22 21:58:30'),
(33, 13, 'Chicken Strips', 9.00, 'menu_69223235de8ea7.48508526_1763848757.jpg', '2025-11-22 21:59:17'),
(34, 13, 'Chicken Mac Wings', 9.00, 'menu_6922324e0fb876.81940552_1763848782.jpg', '2025-11-22 21:59:42'),
(35, 13, 'Cola', 12.00, 'menu_69223273df4903.28621330_1763848819.jpg', '2025-11-22 22:00:19'),
(36, 13, 'Water', 3.00, 'menu_6922328e5a5563.78326121_1763848846.jpg', '2025-11-22 22:00:46'),
(37, 13, 'Fanta', 9.00, 'menu_692232a670b832.87037053_1763848870.jpg', '2025-11-22 22:01:10'),
(38, 13, 'Orange Juice', 4.00, 'menu_692232db1f3480.49514989_1763848923.jpg', '2025-11-22 22:02:03'),
(39, 13, 'Apple Juice', 4.00, 'menu_692232f78a84e3.53275778_1763848951.jpg', '2025-11-22 22:02:31'),
(40, 14, 'Burger Bakes', 11.00, 'menu_69223db74336d1.29820077_1763851703.jpg', '2025-11-22 22:48:23'),
(41, 14, 'Big Bake', 15.00, 'menu_69223ddaa3e158.93103949_1763851738.jpg', '2025-11-22 22:48:58'),
(42, 14, 'Chicken Sandwich', 13.00, 'menu_69223df4c1e275.32842356_1763851764.jpg', '2025-11-22 22:49:24'),
(43, 14, 'Tawook', 11.00, 'menu_69223e2a4f6015.88650107_1763851818.jpg', '2025-11-22 22:50:18'),
(44, 14, 'Egg and Cheese Sandwich', 8.00, 'menu_69223e5f8d0477.13951797_1763851871.jpg', '2025-11-22 22:51:11'),
(45, 14, 'Broasted Chicken', 18.00, 'menu_69223e7ec2b163.76351421_1763851902.jpg', '2025-11-22 22:51:42'),
(46, 14, 'Garlic Sauce', 5.00, 'menu_69223ea42321b5.80636987_1763851940.jpg', '2025-11-22 22:52:20'),
(47, 14, 'Tahini Sauce', 5.00, 'menu_69223ec37fc651.15721260_1763851971.jpg', '2025-11-22 22:52:51'),
(48, 14, 'Tahini Sauce', 5.00, 'menu_69223eda268701.98355852_1763851994.jpg', '2025-11-22 22:53:14'),
(49, 14, '2 Pieces Bread', 4.00, 'menu_69223f0416ef04.46155430_1763852036.jpg', '2025-11-22 22:53:56'),
(50, 14, 'Mashboos Sauce', 5.00, 'menu_69223f1f9ed610.15979358_1763852063.jpg', '2025-11-22 22:54:23'),
(51, 14, 'Tartar Sauce', 5.00, 'menu_69223f3d87bc18.93357502_1763852093.jpg', '2025-11-22 22:54:53'),
(52, 14, '7UP', 8.00, 'menu_69223f59121d35.94046004_1763852121.jpg', '2025-11-22 22:55:21'),
(53, 14, 'Pepsi', 8.00, 'menu_69223f74d8ee14.15316327_1763852148.jpg', '2025-11-22 22:55:48'),
(54, 14, 'Water', 5.00, 'menu_69223f8caa7bf1.94460360_1763852172.jpg', '2025-11-22 22:56:12'),
(55, 14, 'Citrys', 8.00, 'menu_69223faf5b8501.41267954_1763852207.jpg', '2025-11-22 22:56:47'),
(56, 14, 'Mirinda', 8.00, 'menu_69223fc88c2e81.37951355_1763852232.jpg', '2025-11-22 22:57:12'),
(57, 14, 'Diet Pepsi', 8.00, 'menu_69223fdce70683.49148782_1763852252.jpg', '2025-11-22 22:57:32'),
(58, 15, 'Margarita', 39.00, 'menu_692249cf6fedc9.61998963_1763854799.jpg', '2025-11-22 23:39:59'),
(59, 15, 'Lemon Pizza', 130.00, 'menu_692249ea990fa5.02189479_1763854826.jpg', '2025-11-22 23:40:26'),
(60, 15, 'Single Malt', 30.00, 'menu_69224a03e8a6a2.52384844_1763854851.jpg', '2025-11-22 23:40:52'),
(61, 15, 'Dynamite', 40.00, 'menu_69224a25f1ba09.42336173_1763854885.jpg', '2025-11-22 23:41:26'),
(62, 15, 'Barbecue', 30.00, 'menu_69224a4b416df2.91464877_1763854923.jpg', '2025-11-22 23:42:03'),
(63, 15, 'Pepperroni', 30.00, 'menu_69224a687d3960.53165403_1763854952.jpg', '2025-11-22 23:42:32'),
(64, 15, 'Creamy Ranch', 5.00, 'menu_69224a83390682.88393373_1763854979.jpg', '2025-11-22 23:42:59'),
(65, 15, 'Fiery Peri', 5.00, 'menu_69224aaa37a287.40197218_1763855018.jpg', '2025-11-22 23:43:38'),
(66, 15, 'Solo Salad', 14.00, 'menu_69224ac4efbfd5.16982805_1763855044.jpg', '2025-11-22 23:44:04'),
(67, 15, 'Chipotle Barbecue', 5.00, 'menu_69224ae0acb454.87297016_1763855072.jpg', '2025-11-22 23:44:32'),
(68, 15, 'Wedges Potatoes', 11.00, 'menu_69224afdad54e6.52645705_1763855101.jpg', '2025-11-22 23:45:01'),
(69, 15, 'Garlic Bread', 10.00, 'menu_69224b1441d417.01809367_1763855124.jpg', '2025-11-22 23:45:24'),
(70, 15, '7UP', 8.00, 'menu_69224b2ff112f5.86275856_1763855151.jpg', '2025-11-22 23:45:51'),
(71, 15, 'Pepsi', 8.00, 'menu_69224b511e9581.99443114_1763855185.jpg', '2025-11-22 23:46:25'),
(72, 15, 'water', 5.00, 'menu_69224b6e6a1121.61237329_1763855214.jpg', '2025-11-22 23:46:54'),
(73, 15, 'Diet Pepsi', 8.00, 'menu_69224b88984452.21180663_1763855240.jpg', '2025-11-22 23:47:20'),
(74, 15, 'Mirinda', 8.00, 'menu_69224b9e229319.85558905_1763855262.jpg', '2025-11-22 23:47:42'),
(75, 15, 'Orange Juice', 7.00, 'menu_69224bb7d6ef64.30162730_1763855287.jpg', '2025-11-22 23:48:07'),
(76, 16, 'Dew Burger', 11.00, 'menu_692257313940d7.32491713_1763858225.jpg', '2025-11-23 00:37:05'),
(77, 16, 'Twister Meal', 15.00, 'menu_692257d8b06775.40426699_1763858392.jpg', '2025-11-23 00:37:31'),
(78, 16, 'Zinger Meal', 20.00, 'menu_6922576c909662.10192576_1763858284.jpg', '2025-11-23 00:38:04'),
(79, 16, 'Combo', 33.00, 'menu_6922578a682575.81356387_1763858314.jpg', '2025-11-23 00:38:34'),
(80, 16, 'Cheesy Lava Meal', 30.00, 'menu_692257c21286b4.64564218_1763858370.jpg', '2025-11-23 00:39:30'),
(81, 16, 'Mighty Zinger', 40.00, 'menu_692257fc3c87d7.92346348_1763858428.jpg', '2025-11-23 00:40:28'),
(82, 16, 'Fries', 8.00, 'menu_692258157fa436.81720638_1763858453.jpg', '2025-11-23 00:40:53'),
(83, 16, 'Onion Rings', 5.00, 'menu_6922583a56abb6.82674484_1763858490.jpg', '2025-11-23 00:41:30'),
(84, 16, 'Nugget', 10.00, 'menu_6922585334fe80.30535689_1763858515.jpg', '2025-11-23 00:41:55'),
(85, 16, 'Colesalw', 11.00, 'menu_6922586be91d46.67256792_1763858539.jpg', '2025-11-23 00:42:20'),
(86, 16, 'Family Fries', 14.00, 'menu_692258825c2339.88489627_1763858562.jpg', '2025-11-23 00:42:42'),
(87, 16, 'Barbecue Sauce', 5.00, 'menu_692258975db970.70338539_1763858583.jpg', '2025-11-23 00:43:03'),
(88, 16, 'Mirinda Clitrus', 8.00, 'menu_692258b447acf0.41201826_1763858612.jpg', '2025-11-23 00:43:32'),
(89, 16, 'Pepsi', 8.00, 'menu_692258ce85bb41.27677054_1763858638.jpg', '2025-11-23 00:43:58'),
(90, 16, 'Pepsi Zero', 8.00, 'menu_692258e641f951.61413673_1763858662.jpg', '2025-11-23 00:44:22'),
(91, 16, 'Mirinda', 8.00, 'menu_692259030575e3.49672615_1763858691.jpg', '2025-11-23 00:44:51'),
(92, 16, '7 UP', 8.00, 'menu_6922591c006ca1.49022383_1763858716.jpg', '2025-11-23 00:45:16'),
(93, 17, 'Chicken Slaw Meal', 30.00, 'menu_692261f59f1eb3.24879055_1763860981.jpg', '2025-11-23 01:20:29'),
(94, 17, 'Steak Loded', 33.00, 'menu_69226179f0e829.44236954_1763860857.jpg', '2025-11-23 01:20:58'),
(95, 17, 'Big Deluxe', 35.00, 'menu_692261987f6932.37107983_1763860888.jpg', '2025-11-23 01:21:28'),
(96, 17, 'Spicy Chicken Slaw', 35.00, 'menu_692261d375dfb2.30228508_1763860947.jpg', '2025-11-23 01:22:27'),
(97, 17, 'Thick Burger', 33.00, 'menu_69226215abc7d5.73560986_1763861013.jpg', '2025-11-23 01:23:33'),
(98, 17, 'Super Star', 33.00, 'menu_6922622c552c98.97297729_1763861036.jpg', '2025-11-23 01:23:56'),
(99, 17, 'Curly Fries', 9.00, 'menu_69226257d4f296.29481614_1763861079.jpg', '2025-11-23 01:24:39'),
(100, 17, 'Golden Bites', 10.00, 'menu_692262747b13d4.44367682_1763861108.jpg', '2025-11-23 01:25:08'),
(101, 17, 'Potatoes With Fillet', 14.00, 'menu_6922628ca3e6c5.65147272_1763861132.jpg', '2025-11-23 01:25:32'),
(102, 17, 'Mushroom Sauce', 5.00, 'menu_692262a452d6f1.87649792_1763861156.jpg', '2025-11-23 01:25:56'),
(103, 17, 'Loaded Potatoes', 15.00, 'menu_692262ba1eda98.42246668_1763861178.jpg', '2025-11-23 01:26:18'),
(104, 17, 'Honey Mustard', 5.00, 'menu_692262d1391f30.51233426_1763861201.jpg', '2025-11-23 01:26:41'),
(105, 17, 'Orange Juice', 6.00, 'menu_692262e73903e1.33877256_1763861223.jpg', '2025-11-23 01:27:03'),
(106, 17, 'Mirinda', 8.00, 'menu_692262fd997814.54507973_1763861245.jpg', '2025-11-23 01:27:25'),
(107, 17, 'Mountain Dew', 8.00, 'menu_692263127b9088.81030256_1763861266.jpg', '2025-11-23 01:27:46'),
(108, 17, 'Diet Pepsi', 8.00, 'menu_69226329e48c72.91741263_1763861289.jpg', '2025-11-23 01:28:10'),
(109, 17, 'Pepsi', 8.00, 'menu_69226340d12ba8.98670292_1763861312.jpg', '2025-11-23 01:28:32'),
(110, 17, '7 UP', 8.00, 'menu_69226359820fd9.02325138_1763861337.jpg', '2025-11-23 01:28:57'),
(111, 18, 'Broasted', 20.00, 'menu_69226e85d284d8.36810336_1763864197.jpg', '2025-11-23 02:16:37'),
(112, 18, 'Sup Chicken Sandwich', 15.00, 'menu_69226ed0af49d7.23785241_1763864272.jpg', '2025-11-23 02:17:52'),
(113, 18, 'Club Sandwich', 15.00, 'menu_69226f0b8d53c4.72838302_1763864331.jpg', '2025-11-23 02:18:51'),
(114, 18, 'Shrimp Sandwich', 17.00, 'menu_69226f2bc0d6b3.96680240_1763864363.jpg', '2025-11-23 02:19:23'),
(115, 18, 'Maxican Sandwich', 18.00, 'menu_69226f4834b2a0.64457470_1763864392.jpg', '2025-11-23 02:19:52'),
(116, 18, 'Fajita Sandwich', 17.00, 'menu_69226f60df5142.11417155_1763864416.jpg', '2025-11-23 02:20:17'),
(117, 18, 'Cheese Fries', 12.00, 'menu_69226f7f6c1bf7.41168673_1763864447.jpg', '2025-11-23 02:20:47'),
(118, 18, 'Chicken Fries', 14.00, 'menu_69226f9f7b9907.63331079_1763864479.jpg', '2025-11-23 02:21:19'),
(119, 18, 'Corn Cup', 8.00, 'menu_69226fde0a2c36.82681807_1763864542.jpg', '2025-11-23 02:22:22'),
(120, 18, 'Coleslaw Salad', 10.00, 'menu_69226ffbdedfb3.16378005_1763864571.jpg', '2025-11-23 02:22:51'),
(121, 18, 'Green Salad', 9.00, 'menu_69227013e44870.25456521_1763864595.jpg', '2025-11-23 02:23:16'),
(122, 18, 'Onion Rings', 11.00, 'menu_6922702af31cb2.55401188_1763864618.jpg', '2025-11-23 02:23:39'),
(123, 18, 'Water', 5.00, 'menu_69227043cd5263.19106368_1763864643.jpg', '2025-11-23 02:24:03'),
(124, 18, 'Pepsi', 8.00, 'menu_6922705959d392.42108847_1763864665.jpg', '2025-11-23 02:24:25'),
(125, 18, 'Citrus', 8.00, 'menu_6922706e48f634.45825931_1763864686.jpg', '2025-11-23 02:24:46'),
(126, 18, 'Diet Pepsi', 8.00, 'menu_69227084917519.29020517_1763864708.jpg', '2025-11-23 02:25:08'),
(127, 18, 'Mirinda', 8.00, 'menu_69227099e3d671.43163417_1763864729.jpg', '2025-11-23 02:25:30'),
(128, 18, '7 UP', 8.00, 'menu_692270afcbd2c7.15723237_1763864751.jpg', '2025-11-23 02:25:51'),
(129, 19, 'Flat White', 20.00, 'menu_692275d935cd55.25234458_1763866073.jpg', '2025-11-23 02:47:53'),
(130, 19, 'Pistachio Latte', 22.00, 'menu_692275fac986c3.18658519_1763866106.jpg', '2025-11-23 02:48:27'),
(131, 19, 'Caffe Americano', 12.00, 'menu_6922761558df29.50624849_1763866133.jpg', '2025-11-23 02:48:53'),
(132, 19, 'Cappuccino', 19.00, 'menu_69227682ecdb16.16744214_1763866242.jpg', '2025-11-23 02:50:43'),
(133, 19, 'Hot Chocolate', 23.00, 'menu_692276a22bb1c3.41927506_1763866274.jpg', '2025-11-23 02:51:14'),
(134, 19, 'Caramel Macchiato', 21.00, 'menu_692276bddb13d9.31803390_1763866301.jpg', '2025-11-23 02:51:42'),
(135, 19, 'Iced Brewed Coffee', 23.00, 'menu_692276e47e1896.16486597_1763866340.jpg', '2025-11-23 02:52:20'),
(136, 19, 'Iced Dulce De Leche', 26.00, 'menu_6922770cdd1515.01251840_1763866380.jpg', '2025-11-23 02:53:00'),
(137, 19, 'Iced White Mocha', 23.00, 'menu_6922772a8c24a9.51266302_1763866410.jpg', '2025-11-23 02:53:30'),
(138, 19, 'Iced Matcha Latte', 22.00, 'menu_69227743043d30.57696823_1763866435.jpg', '2025-11-23 02:53:55'),
(139, 19, 'Iced Caramel Cream', 20.00, 'menu_6922775cc52435.41598983_1763866460.jpg', '2025-11-23 02:54:20'),
(140, 19, 'Iced Pistachio Latte', 25.00, 'menu_692277873616e2.21209298_1763866503.jpg', '2025-11-23 02:55:03'),
(141, 19, 'Double Chocolate Muffin', 20.00, 'menu_692277b2d0e0f1.27075392_1763866546.jpg', '2025-11-23 02:55:46'),
(142, 19, 'Bluebettry Muffin', 22.00, 'menu_692277cca27ac5.14449007_1763866572.jpg', '2025-11-23 02:56:12'),
(143, 19, 'Carrot Walnut Cake', 30.00, 'menu_692277e65a8112.68383836_1763866598.jpg', '2025-11-23 02:56:38'),
(144, 19, 'Mini Cookies', 21.00, 'menu_6922780d0da735.68152705_1763866637.jpg', '2025-11-23 02:57:17'),
(145, 19, 'Cheesecake Bites', 10.00, 'menu_692278260a7fc5.94387642_1763866662.jpg', '2025-11-23 02:57:42'),
(146, 19, 'Chocolate Bar', 15.00, 'menu_6922784172aca6.47161705_1763866689.jpg', '2025-11-23 02:58:09'),
(147, 20, 'Hazelnut Mocha', 19.00, 'menu_69228008be9537.32869086_1763868680.jpg', '2025-11-23 03:31:20'),
(148, 20, 'Hazelunt Hot Chocolate', 19.00, 'menu_69228028e80917.96036033_1763868712.jpg', '2025-11-23 03:31:53'),
(149, 20, 'Original Coffee', 12.00, 'menu_6922804385a015.52719610_1763868739.jpg', '2025-11-23 03:32:19'),
(150, 20, 'Cappuccino', 13.00, 'menu_69228068c92556.34527781_1763868776.jpg', '2025-11-23 03:32:57'),
(151, 20, 'Latte', 12.00, 'menu_692280851eb7c4.65414297_1763868805.jpg', '2025-11-23 03:33:25'),
(152, 20, 'Frozen Dunkaccino', 19.00, 'menu_692280a69604a5.36241567_1763868838.jpg', '2025-11-23 03:33:58'),
(153, 20, 'Cold Brew', 10.00, 'menu_692280c37e9ec9.75439093_1763868867.jpg', '2025-11-23 03:34:27'),
(154, 20, 'Iced Carmel', 19.00, 'menu_692280f0e31d44.33187956_1763868912.jpg', '2025-11-23 03:35:12'),
(155, 20, 'Iced Spanish Latte', 15.00, 'menu_6922812948c011.23725199_1763868969.jpg', '2025-11-23 03:36:09'),
(156, 20, 'Iced Mocha', 11.00, 'menu_692281430c2425.34452295_1763868995.jpg', '2025-11-23 03:36:35'),
(157, 20, 'Iced Coffee', 19.00, 'menu_69228160d390b5.06306517_1763869024.jpg', '2025-11-23 03:37:04'),
(158, 20, 'Long John', 12.00, 'menu_6922818b46d568.69426760_1763869067.jpg', '2025-11-23 03:37:47'),
(159, 20, 'Glazed Donut', 14.00, 'menu_692281a993f768.90029506_1763869097.jpg', '2025-11-23 03:38:17'),
(160, 20, 'Chocolate Frosted', 15.00, 'menu_692281ce369771.04822166_1763869134.jpg', '2025-11-23 03:38:54'),
(161, 20, 'Cotton Candy', 11.00, 'menu_692281e510a9f3.36431322_1763869157.jpg', '2025-11-23 03:39:17'),
(162, 20, 'Salted Caramel', 13.00, 'menu_692281fe708332.72443269_1763869182.jpg', '2025-11-23 03:39:42'),
(163, 20, 'Coffee Roll', 11.00, 'menu_6922821803a671.49137022_1763869208.jpg', '2025-11-23 03:40:08'),
(164, 21, 'Brewed Coffee', 15.00, 'menu_69230854a0c2f1.29300232_1763903572.jpg', '2025-11-23 13:12:52'),
(165, 21, 'Dark Roast Coffee', 16.00, 'menu_6923086eef4c78.82646575_1763903598.jpg', '2025-11-23 13:13:19'),
(166, 21, 'Americano', 12.00, 'menu_6923088a126526.10749396_1763903626.jpg', '2025-11-23 13:13:46'),
(167, 21, 'Flat White', 14.00, 'menu_692308a9e9a021.47100976_1763903657.jpg', '2025-11-23 13:14:17'),
(168, 21, 'White Chocolate Matcha', 23.00, 'menu_692308c47028e6.48889800_1763903684.jpg', '2025-11-23 13:14:44'),
(169, 21, 'Spanish Latte', 19.00, 'menu_692308e63ede73.49288377_1763903718.jpg', '2025-11-23 13:15:18'),
(170, 21, 'Matcha Mango', 21.00, 'menu_6923090478c2b5.62689247_1763903748.jpg', '2025-11-23 13:15:48'),
(171, 21, 'Matcha Strawberry', 21.00, 'menu_69230921d67f39.98702109_1763903777.jpg', '2025-11-23 13:16:17'),
(172, 21, 'Matcha Blueberry', 21.00, 'menu_6923093a45f508.07938861_1763903802.jpg', '2025-11-23 13:16:42'),
(173, 21, 'Cold Brew', 18.00, 'menu_69230960f2d283.34451334_1763903840.jpg', '2025-11-23 13:17:21'),
(174, 21, 'Iced Americano', 22.00, 'menu_6923097a42f636.62207583_1763903866.jpg', '2025-11-23 13:17:46'),
(175, 21, 'Iced Caramel', 20.00, 'menu_692309b7943dc7.04021311_1763903927.jpg', '2025-11-23 13:18:47'),
(176, 21, 'Chocolate Fudge', 30.00, 'menu_69230adc75ba56.64305444_1763904220.jpg', '2025-11-23 13:23:40'),
(177, 21, 'Carrot Pecan Cake', 33.00, 'menu_69230afddbba43.17987768_1763904253.jpg', '2025-11-23 13:24:14'),
(178, 21, 'Red Velvet Cake', 30.00, 'menu_69230b1926fe79.83865296_1763904281.jpg', '2025-11-23 13:24:41'),
(179, 22, 'Tea', 11.00, 'menu_69235c84da4376.04184907_1763925124.jpg', '2025-11-23 19:12:04'),
(180, 22, 'Green Tea', 15.00, 'menu_69235c9d6c52b9.19508571_1763925149.jpg', '2025-11-23 19:12:29'),
(181, 22, 'Hot Chocolate', 17.00, 'menu_69235cc9969242.97669682_1763925193.jpg', '2025-11-23 19:13:13'),
(182, 22, 'Chai Latte', 18.00, 'menu_69235cf9333767.38525089_1763925241.jpg', '2025-11-23 19:14:01'),
(183, 22, 'Flat White', 14.00, 'menu_69235d146a5dd5.65654660_1763925268.jpg', '2025-11-23 19:14:28'),
(184, 22, 'Latte', 20.00, 'menu_69235d34b0a759.96658295_1763925300.jpg', '2025-11-23 19:15:00'),
(185, 22, 'Pavlova', 23.00, 'menu_69235d4e06b9a8.92726233_1763925326.jpg', '2025-11-23 19:15:26'),
(186, 22, 'Chocolate Frappé', 21.00, 'menu_69235d63ebf426.30140770_1763925347.jpg', '2025-11-23 19:15:48'),
(187, 22, 'Coffee Cream', 23.00, 'menu_69235d7dae33b0.69579258_1763925373.jpg', '2025-11-23 19:16:13'),
(188, 22, 'Milk Chocolate Cookie', 12.00, 'menu_69235d9811d6f1.12561699_1763925400.jpg', '2025-11-23 19:16:40'),
(189, 22, 'Blueberry Muffin', 16.00, 'menu_69235db55342c7.07078963_1763925429.jpg', '2025-11-23 19:17:09'),
(190, 22, 'Cinnamon Muffin', 16.00, 'menu_69235dccaf9229.78061129_1763925452.jpg', '2025-11-23 19:17:32'),
(191, 23, 'Flat White', 14.00, 'menu_6923675f1309b4.64894332_1763927903.jpg', '2025-11-23 19:58:23'),
(192, 23, 'Espresso', 15.00, 'menu_692367772fa3c7.58273092_1763927927.jpg', '2025-11-23 19:58:47'),
(193, 23, 'Hot Coffee of the Day', 10.00, 'menu_6923678faf0b29.17720241_1763927951.jpg', '2025-11-23 19:59:11'),
(194, 23, 'Alfredo', 21.00, 'menu_692367a6bd8e96.37178972_1763927974.jpg', '2025-11-23 19:59:34'),
(195, 23, 'Cold Water', 5.00, 'menu_692367c0bbc344.57020788_1763928000.jpg', '2025-11-23 20:00:00'),
(196, 23, 'Hibiscus', 21.00, 'menu_692367dc3fa640.49876247_1763928028.jpg', '2025-11-23 20:00:28'),
(197, 23, 'Stuffed Dates', 16.00, 'menu_692367f6056e43.07838343_1763928054.jpg', '2025-11-23 20:00:54'),
(198, 23, 'Galaxy Cheesecake', 30.00, 'menu_6923680df3d958.93973937_1763928077.jpg', '2025-11-23 20:01:18'),
(199, 23, 'Coco Mousse', 20.00, 'menu_692368392a6612.03310692_1763928121.jpg', '2025-11-23 20:02:01'),
(200, 23, 'Gandoora Maamoul', 20.00, 'menu_6923685217ed56.97212533_1763928146.jpg', '2025-11-23 20:02:26'),
(201, 24, 'Hot Coffee of the Day', 16.00, 'menu_692369f8c7cb03.01420874_1763928568.jpg', '2025-11-23 20:09:28'),
(202, 24, 'Saudi Coffee', 12.00, 'menu_69236a126a16c7.25726681_1763928594.jpg', '2025-11-23 20:09:54'),
(203, 24, 'Latte', 19.00, 'menu_69236a3215fed8.02521045_1763928626.jpg', '2025-11-23 20:10:26'),
(204, 24, 'Lemon Mint', 20.00, 'menu_69236a4b7cee44.70126096_1763928651.jpg', '2025-11-23 20:10:51'),
(205, 24, 'Orange Juice', 20.00, 'menu_69236a65c5cb68.77854338_1763928677.jpg', '2025-11-23 20:11:17'),
(206, 24, 'Crepe Fettuccine', 30.00, 'menu_69236a82110b48.92577148_1763928706.jpg', '2025-11-23 20:11:46'),
(207, 24, 'Croissant Pistachio', 30.00, 'menu_69236a9d815097.90075948_1763928733.jpg', '2025-11-23 20:12:13'),
(208, 24, 'Crepe Sauce', 30.00, 'menu_69236ab4928e67.06948952_1763928756.jpg', '2025-11-23 20:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `place_id`, `sender_name`, `sender_email`, `message`, `created_at`) VALUES
(4, 13, 'بيان', 'bassddhh765432@gmail.com', 'الاكل لذيذ', '2025-11-22 22:03:18'),
(5, 14, 'قيصل', 'faas@gmail.com', 'التحضير بطي جدا', '2025-11-22 22:58:54'),
(6, 15, 'بيان', 'bassddhh765432@gmail.com', 'بيتزا لذيذه', '2025-11-22 23:48:46'),
(7, 16, 'قيصل', 'faas@gmail.com', 'الاكل لذيذ', '2025-11-23 00:45:59'),
(8, 17, 'بيان', 'bassddhh765432@gmail.com', 'من افضل المطاعم', '2025-11-23 01:29:42'),
(9, 18, 'بيان', 'bassddhh765432@gmail.com', 'التحضير بطي', '2025-11-23 02:26:32'),
(10, 19, 'بيان', 'bassddhh765432@gmail.com', 'القهوه لذيذه', '2025-11-23 02:58:47'),
(11, 20, 'بيان', 'bassddhh765432@gmail.com', 'القهوه دايم بارده', '2025-11-23 03:40:49'),
(12, 21, 'بيان', 'bassddhh765432@gmail.com', 'الحلويات جميله', '2025-11-23 13:25:15'),
(13, 22, 'قيصل', 'faas@gmail.com', 'من افضل الكوفيهات', '2025-11-23 19:18:19'),
(14, 23, 'بيان', 'bassddhh765432@gmail.com', 'القهوه دام بارده', '2025-11-23 20:03:57'),
(15, 24, 'بيان', 'bassddhh765432@gmail.com', 'ا', '2025-11-23 20:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `place_id` int(11) NOT NULL,
  `role` enum('owner') DEFAULT 'owner',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `email`, `password`, `place_id`, `role`, `created_at`) VALUES
(4, 'mcdonalds@airport.local', '$2y$10$5qUgdWrHaSBdeMUnGLwm8eSymgupDutpizVSLlgueYWAf0bqQ3cMa', 13, 'owner', '2025-11-22 21:44:48'),
(5, 'albaik@airport.local', '$2y$10$URozFR4PatLMNJHP/7kogu028fiyD0n.leSnOTorL9uXZJWJDqAuq', 14, 'owner', '2025-11-22 22:10:22'),
(6, 'PizzaHut@airporat.local', '$2y$10$AIMc8osi2r6AgmcdxjCO2OxE4t5.pg4p4nGBiUX/XhUiFvulqZj/C', 15, 'owner', '2025-11-22 23:39:02'),
(7, 'KFC@airport.local', '$2y$10$LHRQkRZWzeyjXx6MLUQKReiqD4Xv/pjBH.5uOMb61N/xF31LfLrqC', 16, 'owner', '2025-11-23 00:35:49'),
(8, 'Hardees@airport.local', '$2y$10$4HgCLEvd9TJ20AYusp523OxLLAs5aDWic7OpVnP8E0CTFoyUpE8/e', 17, 'owner', '2025-11-23 00:50:55'),
(9, 'MaxFood@airpot.local', '$2y$10$e0bdVvFIPz.3q0JdpL7ihuSiHbrXtGZ.8vBG5gauwfuybhrNxyUVW', 18, 'owner', '2025-11-23 01:34:08'),
(10, 'Starbucks@airport.local', '$2y$10$5WDFl64bMVbUndBTXY4Ocez7vLShBwybkrN4FcR4456pdtRLNRMSS', 19, 'owner', '2025-11-23 02:33:20'),
(11, 'DunkinDonuts@airport.local', '$2y$10$9GmX4Ico2CYsIU069tlrzebq1sHojUUUBnulFM4KrMsE9XMZih15y', 20, 'owner', '2025-11-23 03:30:00'),
(12, 'TimHortons@airport.local', '$2y$10$ey0F48IpRrZxzGRdFloj.uI28f31l.b/wM9Bh6kbvCHwm68TB9.Ae', 21, 'owner', '2025-11-23 13:11:24'),
(13, 'CostaCoffee@airport.local', '$2y$10$ncTEOj6Jdh9TFemyHKr0.eaq16mc1Uk4Ba18gXk1DSx6JNEzJt/yO', 22, 'owner', '2025-11-23 18:37:21'),
(14, 'ZIDQHWATI@airport.local', '$2y$10$sXpJqwmSvEU8pOJ8w2rb.uL.NXihE9TU7/2zsFsltqxCaGNUQ/.YG', 23, 'owner', '2025-11-23 19:24:12'),
(15, 'Kyan@airprt.local', '$2y$10$izzgBy/r2MnnUTfuoL0rj.AbI3Cc7PLsPMVvmO36F1fykbns4Wiuu', 24, 'owner', '2025-11-23 20:07:20');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('restaurant','cafe') NOT NULL,
  `location_in_airport` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `type`, `location_in_airport`, `logo`, `description`, `is_visible`, `created_at`) VALUES
(13, 'McDonalds', 'restaurant', 'Gate1', 'logo_69222ed0978340.12223384_1763847888.jfif', 'McDonald’s is a global fast-food restaurant chain founded in 1940 by brothers Maurice and Richard McDonald. It is known for its fast meals such as burgers and French fries. The company has expanded to become one of the largest restaurant chains in the world, operating over 40,000 outlets in more than 100 countries and serving millions of customers daily.', 1, '2025-11-22 21:44:48'),
(14, 'AL Baik', 'restaurant', 'Gate2', 'logo_692234ce9ba4a9.20111817_1763849422.jfif', 'AlBaik is a Saudi fast-food restaurant chain founded in Jeddah in 1974 by Shakour Abu Ghazaleh. It is famous for serving fried chicken (broasted) and seafood with a distinctive spice blend. The chain has succeeded by offering high-quality products and maintaining strict health standards. Its branches have expanded throughout Saudi Arabia and into other countries such as Bahrain and the UAE', 1, '2025-11-22 22:10:22'),
(15, 'Pizza Hut', 'restaurant', 'Gate3', 'logo_692249961e6576.80979688_1763854742.jfif', 'Pizza Hut is an American pizza restaurant chain founded in 1958 in Wichita, Kansas, by brothers Dan and Frank Carney. It is known for serving pizza, pasta, appetizers, and desserts. It is the largest pizza chain in the world in terms of number of branches, with more than 19,000 restaurants worldwide. It is currently owned by Yum! Brands.', 1, '2025-11-22 23:39:02'),
(16, 'KFC', 'restaurant', 'Gate4', 'logo_692256e5984815.54516490_1763858149.png', 'KFC is a global American fast-food restaurant chain specializing in fried chicken, founded in 1952 by Colonel Harland Sanders. The company is famous for its secret chicken recipe made with 11 herbs and spices, and it is the second largest fast-food chain in the world in terms of sales after McDonald’s.', 1, '2025-11-23 00:35:49'),
(17, 'Hardees', 'restaurant', 'Gate5', 'logo_69225a6f520bc5.24879429_1763859055.jfif', 'Hardee’s is a global fast-food restaurant chain founded in the United States in 1960. It is known for its charbroiled burgers and fresh beef. The chain has expanded to include thousands of restaurants in more than 40 countries, especially in the Middle East and Africa. It has been present in the region since 1981 and is locally managed by companies such as Americana Group.', 1, '2025-11-23 00:50:55'),
(18, 'Max Food', 'restaurant', 'Gate6', 'logo_6922649007c238.56411796_1763861648.png', 'Max Food Restaurant is a fast-food restaurant in Hail, Saudi Arabia, known for serving crispy fried chicken (broast), sandwiches, and burgers. The restaurant was founded in 2022 and has become known for offering a variety of deals such as value meals and strips meals. It also has several branches.', 1, '2025-11-23 01:34:08'),
(19, 'Starbucks', 'cafe', 'Gate1', 'logo_6922726fdad3a9.50549400_1763865199.jfif', 'Starbucks is a global American coffeehouse company founded in 1971 in Seattle, Washington. It began as a roastery selling whole coffee beans. The company expanded significantly to become the largest coffeehouse chain in the world. It is known for offering a wide variety of hot and cold coffee beverages, as well as pastries and light snacks. Starbucks focuses on creating a unique customer experience through quality, social connection, and sustainability', 1, '2025-11-23 02:33:19'),
(20, 'Dunkin Donuts', 'cafe', 'Gate2', 'logo_69227fb89339f5.14793588_1763868600.jpg', 'Dunkin’ is a global brand specializing in coffee and donuts, founded in the United States in 1950. The company is known for offering a wide variety of baked goods and coffee, including different types of donuts, hot and cold coffee, Munchkins, as well as sandwiches and muffins. Dunkin’ has thousands of branches in dozens of countries around the world and is considered one of the largest coffee and donut chains.', 1, '2025-11-23 03:30:00'),
(21, 'Tim Hortons', 'cafe', 'Gate3', 'logo_692307fc1992b0.83751144_1763903484.jfif', 'Tim Hortons is a famous Canadian coffee shop and restaurant chain, founded in 1964 by Canadian hockey player Tim Horton. It is known primarily for serving coffee and donuts. It is considered one of the largest fast-food chains in Canada, with a wide global presence. Its menu also includes sandwiches, baked goods, and light meals.', 1, '2025-11-23 13:11:24'),
(22, 'Costa Coffee', 'cafe', 'Gate4', 'logo_6923546180e454.70508566_1763923041.jfif', 'Costa Coffee is a global British coffeehouse chain founded in 1971 in London by the Italian brothers Sergio and Bruno Costa. It began as a coffee roastery supplying coffee to specialized Italian coffee shops and restaurants, and later expanded to become the largest coffeehouse chain in the United Kingdom and the second-largest coffeehouse chain in the world. In 2019,', 1, '2025-11-23 18:37:21'),
(23, 'ZID QHWATI', 'cafe', 'Gate5', 'logo_69235f5cc55931.19850136_1763925852.jpg', 'Zed Qahwati” is the name of a well-known café in Hail. It is a destination for coffee lovers, offering a variety of beverages in a calm and relaxing atmosphere.', 1, '2025-11-23 19:24:12'),
(24, 'Kyan', 'cafe', 'Gate6', 'logo_692369787e47f3.66385126_1763928440.jfif', 'Kyan Café is a Saudi brand founded in 2009, specializing in serving coffee, desserts, and baked goods. It started as a local idea in Unaizah and has grown to achieve global reach with more than 400 branches worldwide. The café aims to blend Saudi authenticity with innovation, focusing on high quality and providing a distinctive experience for customers.', 1, '2025-11-23 20:07:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `crowd_status`
--
ALTER TABLE `crowd_status`
  ADD PRIMARY KEY (`place_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crowd_status`
--
ALTER TABLE `crowd_status`
  ADD CONSTRAINT `crowd_status_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `owners`
--
ALTER TABLE `owners`
  ADD CONSTRAINT `owners_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
