-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3307
-- 生成日期： 2025-01-23 12:54:23
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `shoes_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `admin_phone` varchar(20) DEFAULT NULL,
  `admin_status` enum('Active','Terminated') NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `admin_password`, `admin_name`, `admin_email`, `admin_phone`, `admin_status`, `role`) VALUES
(1, '$2y$10$g4tEFRcy//z4/nA/s5bL7e1RyjTGpiQwbb.kYCt/euUqVIyrjLwKm', 'Bryan Ng Jun Jie', 'bryanngjj@gmail.com', '+60 12-666 8841', 'Active', 'admin'),
(4, '$2y$10$TQ3p95zwcE.NtMT9d/T2K.G2dYJtU5351ASzJwAaxn4LxSjpRTHFK', 'marcus', 'marcus@gmail.com', '+60 12-684 5157', 'Active', 'super_admin');

-- --------------------------------------------------------

--
-- 表的结构 `brand`
--

CREATE TABLE `brand` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `brand`
--

INSERT INTO `brand` (`id`, `name`) VALUES
(1, 'Onitsuka Tiger'),
(2, 'Lacoste'),
(3, 'Nike');

-- --------------------------------------------------------

--
-- 表的结构 `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`) VALUES
(12, 'WOMEN', 'women_shoes_category.png\r\n\r\n'),
(14, 'MEN', 'men_shoes_category.jpeg\r\n');

-- --------------------------------------------------------

--
-- 表的结构 `color`
--

CREATE TABLE `color` (
  `id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `color`
--

INSERT INTO `color` (`id`, `color_name`) VALUES
(1, 'Red'),
(2, 'Blue'),
(3, 'Green'),
(4, 'Black'),
(5, 'White'),
(6, 'Yellow'),
(7, 'Purple'),
(8, 'Orange'),
(9, 'Pink'),
(10, 'Brown'),
(11, 'Gray'),
(12, 'Beige'),
(13, 'Cyan'),
(14, 'Magenta'),
(15, 'Navy'),
(16, 'Olive'),
(17, 'Maroon'),
(18, 'Teal'),
(19, 'Lavender');

-- --------------------------------------------------------

--
-- 表的结构 `deleted_brands`
--

CREATE TABLE `deleted_brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `deleted_brands`
--

INSERT INTO `deleted_brands` (`id`, `name`, `deleted_at`) VALUES
(2, 'Clarks', '2025-01-23 19:30:48');

-- --------------------------------------------------------

--
-- 表的结构 `deleted_categories`
--

CREATE TABLE `deleted_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `deleted_colors`
--

CREATE TABLE `deleted_colors` (
  `id` int(11) NOT NULL,
  `color_name` varchar(255) NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `deleted_colors`
--

INSERT INTO `deleted_colors` (`id`, `color_name`, `deleted_at`) VALUES
(2, 'Gold', '2025-01-23 19:31:21');

-- --------------------------------------------------------

--
-- 表的结构 `deleted_products`
--

CREATE TABLE `deleted_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp(),
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image1_thumb` varchar(255) DEFAULT NULL,
  `image2_thumb` varchar(255) DEFAULT NULL,
  `image3_thumb` varchar(255) DEFAULT NULL,
  `image4_thumb` varchar(255) DEFAULT NULL,
  `image1_display` varchar(255) DEFAULT NULL,
  `image2_display` varchar(255) DEFAULT NULL,
  `image3_display` varchar(255) DEFAULT NULL,
  `image4_display` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `deleted_product_sizes`
--

CREATE TABLE `deleted_product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `deleted_product_sizes`
--

INSERT INTO `deleted_product_sizes` (`id`, `product_id`, `size`, `color`, `stock`, `price`, `deleted_at`) VALUES
(24, 102, '10', 'Black', 1, 123.00, '2025-01-23 19:28:57');

-- --------------------------------------------------------

--
-- 表的结构 `deleted_sizes`
--

CREATE TABLE `deleted_sizes` (
  `id` int(11) NOT NULL,
  `size` varchar(255) NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `deleted_sizes`
--

INSERT INTO `deleted_sizes` (`id`, `size`, `deleted_at`) VALUES
(2, '1', '2025-01-23 19:31:45');

-- --------------------------------------------------------

--
-- 表的结构 `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(1, 2, 'Chew Hong Yi', 'chewhonyi@gmail.com', '0126845157', 'hi, my name is Chew Hong Yi Ya.'),
(2, 2, 'bryan', 'bryan618@gmail.com', '0126845157', 'hi'),
(3, 2, 'Chew Hong Yi', 'chewhonyi@gmail.com', '0126845157', '78');

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `shipping_fullname` varchar(255) DEFAULT NULL,
  `shipping_address_line` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_post_code` varchar(20) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `order_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `total_products`, `total_price`, `placed_on`, `payment_status`, `shipping_fullname`, `shipping_address_line`, `shipping_city`, `shipping_post_code`, `shipping_state`, `order_number`) VALUES
(55, 10, '', '', '', 'Debit Card', '', 1230, '2025-01-23 14:55:16', 'Completed', 'Chew Hong Yi', 'Lot67-23, Jalan Treh, Taman Sri Treh,', 'Muar', '84000', 'Johor', 'ORD-20250123-1270'),
(56, 10, '', '', '', 'cimb', '', 1100, '2025-01-23 18:45:05', 'Completed', 'Chew Hong Yi', 'Lot67-23, Jalan Treh, Taman Sri Treh,', 'Muar', '84000', 'Johor', 'ORD-20250123-7945');

-- --------------------------------------------------------

--
-- 表的结构 `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `name`, `size`, `color`, `quantity`, `price`, `image`) VALUES
(82, 55, 1, 'CALIFORNIA 78 EX', '6', 'White', 1, 510.00, 'img/showcase/shoe1-White/6790ff9dcf853_imgg1.png'),
(83, 55, 2, 'MEXICO 66 CACTFUL-S', '9', 'Green', 1, 720.00, 'img/showcase/shoe2-1/imgg1.png'),
(84, 56, 4, 'TIGER POINTY', '6', 'Brown', 1, 1100.00, 'img/showcase/shoe3-1/imgg1.png');

-- --------------------------------------------------------

--
-- 表的结构 `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `brand`) VALUES
(1, 'CALIFORNIA 78 EX', 'MEN', 'Onitsuka Tiger'),
(2, 'MEXICO 66 CACTFUL-S', 'MEN', 'Onitsuka Tiger'),
(4, 'TIGER POINTY', 'MEN', 'Onitsuka Tiger'),
(5, 'LACOSTE CROCO 1.0', 'MEN', 'Lacoste'),
(6, 'AIR MAX 97', 'MEN', 'Nike'),
(7, '530 UNISEX SNEAKER SHOES', 'MEN', 'Nike'),
(15, 'ZOYA85 WALK', 'WOMEN', 'Clarks'),
(17, 'STAYSO STRIPE', 'WOMEN', 'Clarks'),
(18, 'FAWNA SKY', 'WOMEN', 'Clarks'),
(19, 'VIOLET55 RAE', 'WOMEN', 'Clarks'),
(20, 'JOCELYNNE CAM', 'WOMEN', 'Clarks'),
(21, 'WALLABEE W X MANCHESTER', 'WOMEN', 'Clarks'),
(22, 'MAYHILL COVE', 'WOMEN', 'Clarks'),
(45, 'WALLABEE W', 'WOMEN', 'Clarks'),
(102, 'DESERT BOOT', 'MEN', 'Clarks');

-- --------------------------------------------------------

--
-- 表的结构 `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` int(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `price` int(11) NOT NULL,
  `image1_thumb` varchar(255) DEFAULT NULL,
  `image2_thumb` varchar(255) DEFAULT NULL,
  `image3_thumb` varchar(255) DEFAULT NULL,
  `image4_thumb` varchar(255) DEFAULT NULL,
  `image1_display` varchar(255) DEFAULT NULL,
  `image2_display` varchar(255) DEFAULT NULL,
  `image3_display` varchar(255) DEFAULT NULL,
  `image4_display` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `color`, `stock`, `price`, `image1_thumb`, `image2_thumb`, `image3_thumb`, `image4_thumb`, `image1_display`, `image2_display`, `image3_display`, `image4_display`) VALUES
(1, 1, 5, 'White', 1, 510, 'img/showcase/thumbs/shoe1-White/6791006cae725_imgg1.jpg', 'img/showcase/thumbs/shoe1-White/6791006cae8e5_1183A355_201_SB_BT_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caea2a_1183A355_201_SB_BK_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caeb3f_1183A355_201_SB_TP_GLB.webp', 'img/showcase/shoe1-White/6790ff9dcf853_imgg1.png', 'img/showcase/shoe1-White/6790ff9dcfa4d_imgg2.png', 'img/showcase/shoe1-White/6790ff9dcfb8c_imgg3.png', 'img/showcase/shoe1-White/6790ff9dcfcab_imgg4.png'),
(2, 1, 6, 'White', 1, 510, 'img/showcase/thumbs/shoe1-White/6791006cae725_imgg1.jpg', 'img/showcase/thumbs/shoe1-White/6791006cae8e5_1183A355_201_SB_BT_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caea2a_1183A355_201_SB_BK_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caeb3f_1183A355_201_SB_TP_GLB.webp', 'img/showcase/shoe1-White/6790ff9dcf853_imgg1.png', 'img/showcase/shoe1-White/6790ff9dcfa4d_imgg2.png', 'img/showcase/shoe1-White/6790ff9dcfb8c_imgg3.png', 'img/showcase/shoe1-White/6790ff9dcfcab_imgg4.png'),
(3, 1, 7, 'White', 1, 510, 'img/showcase/thumbs/shoe1-White/6791006cae725_imgg1.jpg', 'img/showcase/thumbs/shoe1-White/6791006cae8e5_1183A355_201_SB_BT_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caea2a_1183A355_201_SB_BK_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caeb3f_1183A355_201_SB_TP_GLB.webp', 'img/showcase/shoe1-White/6790ff9dcf853_imgg1.png', 'img/showcase/shoe1-White/6790ff9dcfa4d_imgg2.png', 'img/showcase/shoe1-White/6790ff9dcfb8c_imgg3.png', 'img/showcase/shoe1-White/6790ff9dcfcab_imgg4.png'),
(4, 1, 8, 'White', 1, 510, 'img/showcase/thumbs/shoe1-White/6791006cae725_imgg1.jpg', 'img/showcase/thumbs/shoe1-White/6791006cae8e5_1183A355_201_SB_BT_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caea2a_1183A355_201_SB_BK_GLB.webp', 'img/showcase/thumbs/shoe1-White/6791006caeb3f_1183A355_201_SB_TP_GLB.webp', 'img/showcase/shoe1-White/6790ff9dcf853_imgg1.png', 'img/showcase/shoe1-White/6790ff9dcfa4d_imgg2.png', 'img/showcase/shoe1-White/6790ff9dcfb8c_imgg3.png', 'img/showcase/shoe1-White/6790ff9dcfcab_imgg4.png'),
(7, 1, 7, 'Gold', 1, 510, 'img/showcase/thumbs/shoe1-Gold/6790ffee7f1ce_imgg1.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f331_imgg2.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f443_imgg3.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f66f_imgg4.jpg', 'img/showcase/shoe1-Gold/6790ffee7f7a9_imgg1.png', 'img/showcase/shoe1-Gold/6790ffee7f8c0_imgg2.png', 'img/showcase/shoe1-Gold/6790ffee7f9b2_imgg3.png', 'img/showcase/shoe1-Gold/6790ffee7fb4a_imgg4.png'),
(8, 1, 8, 'Gold', 1, 510, 'img/showcase/thumbs/shoe1-Gold/6790ffee7f1ce_imgg1.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f331_imgg2.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f443_imgg3.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f66f_imgg4.jpg', 'img/showcase/shoe1-Gold/6790ffee7f7a9_imgg1.png', 'img/showcase/shoe1-Gold/6790ffee7f8c0_imgg2.png', 'img/showcase/shoe1-Gold/6790ffee7f9b2_imgg3.png', 'img/showcase/shoe1-Gold/6790ffee7fb4a_imgg4.png'),
(10, 1, 6, 'Green', 1, 510, 'img/showcase/thumbs/shoe1-Green/6790fbe3c7dd9_imgg1.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c7f3f_imgg2.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c811b_imgg3.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c8478_imgg4.jpg', 'img/showcase/shoe1-Green/6790fbe3c87f9_imgg1.png', 'img/showcase/shoe1-Green/6790fbe3c89b5_imgg2.png', 'img/showcase/shoe1-Green/6790fbe3c8c1d_imgg3.png', 'img/showcase/shoe1-Green/6790fbe3c8f58_imgg4.png'),
(11, 1, 7, 'Green', 1, 510, 'img/showcase/thumbs/shoe1-Green/6790fbe3c7dd9_imgg1.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c7f3f_imgg2.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c811b_imgg3.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c8478_imgg4.jpg', 'img/showcase/shoe1-Green/6790fbe3c87f9_imgg1.png', 'img/showcase/shoe1-Green/6790fbe3c89b5_imgg2.png', 'img/showcase/shoe1-Green/6790fbe3c8c1d_imgg3.png', 'img/showcase/shoe1-Green/6790fbe3c8f58_imgg4.png'),
(12, 1, 8, 'Green', 1, 510, 'img/showcase/thumbs/shoe1-Green/6790fbe3c7dd9_imgg1.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c7f3f_imgg2.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c811b_imgg3.jpg', 'img/showcase/thumbs/shoe1-Green/6790fbe3c8478_imgg4.jpg', 'img/showcase/shoe1-Green/6790fbe3c87f9_imgg1.png', 'img/showcase/shoe1-Green/6790fbe3c89b5_imgg2.png', 'img/showcase/shoe1-Green/6790fbe3c8c1d_imgg3.png', 'img/showcase/shoe1-Green/6790fbe3c8f58_imgg4.png'),
(13, 2, 9, 'Green', 1, 720, 'img/showcase/thumbs/shoe2-1/imgg1.jpg', 'img/showcase/thumbs/shoe2-1/imgg2.jpg', 'img/showcase/thumbs/shoe2-1/imgg3.jpg', 'img/showcase/thumbs/shoe2-1/imgg4.jpg', 'img/showcase/shoe2-1/imgg1.png', 'img/showcase/shoe2-1/imgg2.png', 'img/showcase/shoe2-1/imgg3.png', 'img/showcase/shoe2-1/imgg4.png'),
(14, 2, 1, 'Green', 1, 720, 'img/showcase/thumbs/shoe2-1/imgg1.jpg', 'img/showcase/thumbs/shoe2-1/imgg2.jpg', 'img/showcase/thumbs/shoe2-1/imgg3.jpg', 'img/showcase/thumbs/shoe2 -1/imgg4.jpg', 'img/showcase/shoe2-1/imgg1.png', 'img/showcase/shoe2-1/imgg2.png', 'img/showcase/shoe2-1/imgg3.png', 'img/showcase/shoe2-1/imgg4.png'),
(15, 2, 4, 'Green', 1, 720, 'img/showcase/thumbs/shoe2-1/imgg1.jpg', 'img/showcase/thumbs/shoe2-1/imgg2.jpg', 'img/showcase/thumbs/shoe2-1/imgg3.jpg', 'img/showcase/thumbs/shoe2-1/imgg4.jpg', 'img/showcase/shoe2-1/imgg1.png', 'img/showcase/shoe2-1/imgg2.png', 'img/showcase/shoe2-1/imgg3.png', 'img/showcase/shoe2-1/imgg4.png'),
(16, 2, 3, 'Green', 1, 720, 'img/showcase/thumbs/shoe2-1/imgg1.jpg', 'img/showcase/thumbs/shoe2-1/imgg2.jpg', 'img/showcase/thumbs/shoe2-1/imgg3.jpg', 'img/showcase/thumbs/shoe2-1/imgg4.jpg', 'img/showcase/shoe2-1/imgg1.png', 'img/showcase/shoe2-1/imgg2.png', 'img/showcase/shoe2-1/imgg3.png', 'img/showcase/shoe2-1/imgg4.png'),
(17, 4, 5, 'Brown', 2, 1100, 'img/showcase/thumbs/shoe3-1/imgg1.jpg', 'img/showcase/thumbs/shoe3-1/imgg2.jpg', 'img/showcase/thumbs/shoe3-1/imgg3.jpg', 'img/showcase/thumbs/shoe3-1/imgg4.jpg', 'img/showcase/shoe3-1/imgg1.png', 'img/showcase/shoe3-1/imgg2.png', 'img/showcase/shoe3-1/imgg3.png', 'img/showcase/shoe3-1/imgg4.png'),
(18, 4, 6, 'Brown', 1, 1100, 'img/showcase/thumbs/shoe3-1/imgg1.jpg', 'img/showcase/thumbs/shoe3-1/imgg2.jpg', 'img/showcase/thumbs/shoe3-1/imgg3.jpg', 'img/showcase/thumbs/shoe3-1/imgg4.jpg', 'img/showcase/shoe3-1/imgg1.png', 'img/showcase/shoe3-1/imgg2.png', 'img/showcase/shoe3-1/imgg3.png', 'img/showcase/shoe3-1/imgg4.png'),
(19, 4, 7, 'Brown', 1, 1100, 'img/showcase/thumbs/shoe3-1/imgg1.jpg', 'img/showcase/thumbs/shoe3-1/imgg2.jpg', 'img/showcase/thumbs/shoe3-1/imgg3.jpg', 'img/showcase/thumbs/shoe3-1/imgg4.jpg', 'img/showcase/shoe3-1/imgg1.png', 'img/showcase/shoe3-1/imgg2.png', 'img/showcase/shoe3-1/imgg3.png', 'img/showcase/shoe3-1/imgg4.png'),
(20, 4, 8, 'Brown', 1, 1100, 'img/showcase/thumbs/shoe3-1/imgg1.jpg', 'img/showcase/thumbs/shoe3-1/imgg2.jpg', 'img/showcase/thumbs/shoe3-1/imgg3.jpg', 'img/showcase/thumbs/shoe3-1/imgg4.jpg', 'img/showcase/shoe3-1/imgg1.png', 'img/showcase/shoe3-1/imgg2.png', 'img/showcase/shoe3-1/imgg3.png', 'img/showcase/shoe3-1/imgg4.png'),
(21, 5, 5, 'White', 1, 90, 'img/show case/thumbs/shoe4-1/imgg1.jpg', 'img/showcase/thumbs/shoe4-1/imgg2.jpg', 'img/showcase/thumbs/shoe4-1/imgg3.jpg', 'img/showcase/thumbs/shoe4-1/imgg4.jpg', 'img/showcase/shoe4-1/imgg1.png', 'img/showcase/shoe4-1/imgg2.png', 'img/showcase/shoe4-1/imgg3.png', 'img/showcase/shoe4-1/imgg4.png'),
(22, 5, 6, 'White', 1, 90, 'img/showcase/thumbs/shoe4-1/imgg1.jpg', 'img/showcase/thumbs/shoe4-1/imgg2.jpg', 'img/showcase/thumbs/shoe4-1/imgg3.jpg', 'img/showcase/thumbs/shoe4-1/imgg4.jpg', 'img/showcase/shoe4-1/imgg1.png', 'img/showcase/shoe4-1/imgg2.png', 'img/showcase/shoe4-1/imgg3.png', 'img/showcase/shoe4-1/imgg4.png'),
(23, 5, 7, 'White', 1, 90, 'img/showcase/thumbs/shoe4-1/imgg1.jpg', 'img/showcase/thumbs/shoe4-1/imgg2.jpg', 'img/showcase/thumbs/shoe4-1/imgg3.jpg', 'img/showcase/thumbs/shoe4-1/imgg4.jpg', 'img/showcase/shoe4-1/imgg1.png', 'img/showcase/shoe4-1/imgg2.png', 'img/showcase/shoe4-1/imgg3.png', 'img/showcase/shoe4-1/imgg4.png'),
(24, 5, 8, 'White', 1, 90, 'img/showcase/thumbs/shoe4-1/imgg1.jpg', 'img/showcase/thumbs/shoe4-1/imgg2.jpg', 'img/showcase/thumbs/shoe4-1/imgg3.jpg', 'img/showcase/thumbs/shoe4-1/imgg4.jpg', 'img/showcase/shoe4-1/imgg1.png', 'img/showcase/shoe4-1/imgg2.png', 'img/showcase/shoe4-1/imgg3.png', 'img/showcase/shoe4-1/imgg4.png'),
(25, 6, 5, 'Black', 1, 530, 'img/showcase/thumbs/shoe5-1/imgg1.jpg', 'img/showcase/thumbs/shoe5-1/imgg2.jpg', 'img/showcase/thumbs/shoe5-1/imgg3.jpg', 'img/showcase/thumbs/shoe5-1/imgg4.jpg', 'img/showcase/shoe5-1/imgg1.png', 'img/showcase/shoe5-1/imgg2.png', 'img/showcase/shoe5-1/imgg3.png', 'img/showcase/shoe5-1/imgg4.jpg'),
(26, 6, 6, 'Black', 1, 530, 'img/showcase/thumbs/shoe5-1/imgg1.jpg', 'img/showcase/thumbs/shoe5-1/imgg2.jpg', 'img/showcase/thumbs/shoe5-1/imgg3.jpg', 'img/showcase/thumbs/shoe5-1/imgg4.jpg', 'img/showcase/shoe5-1/imgg1.png', 'img/showcase/shoe5-1/imgg2.png', 'img/showcase/shoe5-1/imgg3.png', 'img/showcase/shoe5-1/imgg4.jpg'),
(27, 6, 7, 'Black', 1, 530, 'img/showcase/thumbs/shoe5-1/imgg1.jpg', 'img/showcase/thumbs/shoe5-1/imgg2.jpg', 'img/showcase/thumbs/shoe5-1/imgg3.jpg', 'img/showcase/thumbs/shoe5-1/imgg4.jpg', 'img/showcase/shoe5-1/imgg1.png', 'img/showcase/shoe5-1/imgg2.png', 'img/showcase/shoe5-1/imgg3.png', 'img/showcase/shoe5-1/imgg4.jpg'),
(28, 6, 8, 'Black', 1, 530, 'img/showcase/thumbs/shoe5-1/imgg1.jpg', 'img/show case/thumbs/shoe5-1/imgg2.jpg', 'img/showcase/thumbs/shoe5-1/imgg3.jpg', 'img/showcase/thumbs/shoe5-1/imgg4.jpg', 'img/showcase/shoe5-1/imgg1.png', 'img/showcase/shoe5-1/imgg2.png', 'img/showcase/shoe5-1/imgg3.png', 'img/showcase/shoe5-1/imgg4.jpg'),
(29, 7, 5, 'White', 1, 250, 'img/showcase/thumbs/shoe6-1/imgg1.jpg', 'img/showcase/thumbs/shoe6-1/imgg2.jpg', 'img/showcase/thumbs/shoe6-1/imgg3.jpg', 'img/showcase/thumbs/shoe6-1/imgg4.jpg', 'img/showcase/shoe6-1/imgg1.png', 'img/showcase/shoe6-1/imgg2.png', 'img/showcase/shoe6-1/imgg3.png', 'img/showcase/shoe6-1/imgg4.png'),
(30, 7, 6, 'White', 1, 250, 'img/showcase/thumbs/shoe6-1/imgg1.jpg', 'img/showcase/thumbs/shoe6-1/imgg2.jpg', 'img/showcase/thumbs/shoe6-1/imgg3.jpg', 'img/showcase/thumbs/shoe6-1/imgg4.jpg', 'img/showcase/shoe6-1/imgg1.png', 'img/showcase/shoe6-1/imgg2.png', 'img/showcase/shoe6-1/imgg3.png', 'img/showcase/shoe6-1/imgg4.png'),
(31, 7, 7, 'White', 1, 250, 'img/showcase/thumbs/shoe6-1/imgg1.jpg', 'img/showcase/thumbs/shoe6-1/imgg2.jpg', 'img/showcase/thumbs/shoe6-1/imgg3.jpg', 'img/showcase/thumbs/shoe6-1/imgg4.jpg', 'img/showcase/shoe6-1/imgg1.png', 'img/showcase/shoe6-1/imgg2.png', 'img/showcase/shoe6-1/imgg3.png', 'img/showcase/shoe6-1/imgg4.png'),
(32, 7, 8, 'White', 1, 250, 'img/showcase/thumbs/shoe6-1/imgg1.jpg', 'img/showcase/thumbs/shoe6-1/imgg2.jpg', 'img/showcase/thumbs/shoe6-1/imgg3.jpg', 'img/showcase/thumbs/shoe6-1/imgg4.jpg', 'img/showcase/shoe6-1/imgg1.png', 'img/showcase/shoe6-1/imgg2.png', 'img/showcase/shoe6-1/imgg3.png', 'img/showcase/shoe6-1/imgg4.png'),
(33, 15, 5, 'Brown', 1, 450, 'img/showcase/thumbs/shoe7-1/imgg1.jpg', 'img/showcase/thumbs/shoe7-1/imgg2.jpg', 'img/showcase/thumbs/shoe7-1/imgg3.jpg', 'img/showcase/thumbs/shoe7-1/imgg4.jpg', 'img/showcase/shoe7-1/imgg1.png', 'img/showcase/shoe7-1/imgg2.png', 'img/showcase/shoe7-1/imgg3.png', 'img/showcase/shoe7-1/imgg4.png'),
(34, 15, 6, 'Brown', 1, 450, 'img/showcase/thumbs/shoe7-1/imgg1.jpg', 'img/showcase/thumbs/shoe7-1/imgg2.jpg', 'img/showcase/thumbs/shoe7-1/imgg3.jpg', 'img/showcase/thumbs/shoe7-1/imgg4.jpg', 'img/showcase/shoe7-1/imgg1.png', 'img/showcase/shoe7-1/imgg2.png', 'img/showcase/shoe7- 1/imgg3.png', 'img/showcase/shoe7-1/imgg4.jpg'),
(35, 15, 7, 'Brown', 1, 450, 'img/showcase/thumbs/shoe7-1/imgg1.jpg', 'img/showcase/thumbs/shoe7-1/imgg2.jpg', 'img/showcase/thumbs/shoe7-1/imgg3.jpg', 'img/showcase/thumbs/shoe7-1/imgg4.jpg', 'img/showcase/shoe7-1/imgg1.png', 'img/showcase/shoe7-1/imgg2.png', 'img/showcase/shoe7-1/imgg3.png', 'img/showcase/shoe7-1/imgg4.jpg'),
(36, 15, 8, 'Brown', 1, 450, 'img/showcase/thumbs/shoe7-1/imgg1.jpg', 'img/showcase/thumbs/shoe7-1/imgg2.jpg', 'img/showcase/thumbs/shoe7-1/imgg3.jpg', 'img/showcase/thumbs/shoe7-1/imgg4.jpg', 'img/showcase/shoe7-1/imgg1.png', 'img/showcase/shoe7-1/imgg2.png', 'img/showcase/shoe7-1/imgg3.png', 'img/showcase/shoe7-1/imgg4.jpg'),
(37, 17, 5, 'Black', 1, 300, 'img/showcase/thumbs/shoe8-1/imgg1.jpg', 'img/showcase/thumbs/shoe8-1/imgg2.jpg', 'img/showcase/thumbs/shoe8-1/imgg3.jpg', 'img/showcase/thumbs/shoe8-1/imgg4.jpg', 'img/showcase/shoe8-1/imgg1.png', 'img/showcase/shoe8-1/imgg2.png', 'img/showcase/shoe8-1/imgg3.png', 'img/showcase/shoe8-1/imgg4.jpg'),
(38, 17, 6, 'Black', 1, 300, 'img/showcase/thumbs/shoe8-1/imgg1.jpg', 'img/showcase/thumbs/shoe8-1/imgg2.jpg', 'img/showcase/thumbs/shoe8-1/imgg3.jpg', 'img/showcase/thumbs/shoe8-1/imgg4.jpg', 'img/showcase/shoe8-1/imgg1.png', 'img/showcase/shoe8-1/imgg2.png', 'img/showcase/shoe8-1/imgg3.png', 'img/showcase/shoe8-1/imgg4.jpg'),
(39, 17, 7, 'Black', 1, 300, 'img/showcase/thumbs/shoe8-1/imgg1.jpg', 'img/showcase/thumbs/shoe8-1/imgg2.jpg', 'img/showcase/thumbs/shoe8-1/imgg3.jpg', 'img/showcase/thumbs/shoe8-1/imgg4.jpg', 'img/showcase/shoe8-1/imgg1.png', 'img/showcase/shoe8-1/imgg2.png', 'img/showcase/shoe8-1/imgg3.png', 'img/showcase/shoe8-1/imgg4.jpg'),
(40, 17, 8, 'Black', 1, 300, 'img/showcase/thumbs/shoe8-1/imgg1.jpg', 'img/showcase/thumbs/shoe8-1/imgg2.jpg', 'img/showcase/thumbs/shoe8-1/imgg3.jpg', 'img/showcase/thumbs/shoe8-1/imgg4.jpg', 'img/showcase/shoe8-1/imgg1.png', 'img/showcase/shoe8-1/imgg2.png', 'img/showcase/shoe8-1/imgg3.png', 'img/showcase/shoe8-1/imgg4.jpg'),
(41, 18, 5, 'Red', 1, 470, 'img/showcase/thumbs/shoe9-1/imgg1.jpg', 'img/showcase/thumbs/shoe9-1/imgg2.jpg', 'img/showcase/thumbs/shoe9-1/imgg3.jpg', 'img/showcase/thumbs/sh oe9-1/imgg4.jpg', 'img/showcase/shoe9-1/imgg1.png', 'img/showcase/shoe9-1/imgg2.png', 'img/showcase/shoe9-1/imgg3.png', 'img/showcase/shoe9-1/imgg4.png'),
(42, 18, 6, 'Red', 1, 470, 'img/showcase/thumbs/shoe9-1/imgg1.jpg', 'img/showcase/thumbs/shoe9-1/imgg2.jpg', 'img/showcase/thumbs/shoe9-1/imgg3.jpg', 'img/showcase/thumbs/shoe9-1/imgg4.jpg', 'img/showcase/shoe9-1/imgg1.png', 'img/showcase/shoe9-1/imgg2.png', 'img/showcase/shoe9-1/imgg3.png', 'img/showcase/shoe9-1/imgg4.png'),
(43, 18, 7, 'Red', 1, 470, 'img/showcase/thumbs/shoe9-1/imgg1.jpg', 'img/showcase/thumbs/shoe9-1/imgg2.jpg', 'img/showcase/thumbs/shoe9-1/imgg3.jpg', 'img/showcase/thumbs/shoe9-1/imgg4.jpg', 'img/showcase/shoe9-1/imgg1.png', 'img/showcase/shoe9-1/imgg2.png', 'img/showcase/shoe9-1/imgg3.png', 'img/showcase/shoe9-1/imgg4.png'),
(44, 18, 8, 'Red', 1, 470, 'img/showcase/thumbs/shoe9-1/imgg1.jpg', 'img/showcase/thumbs/shoe9-1/imgg2.jpg', 'img/showcase/thumbs/shoe9-1/imgg3.jpg', 'img/showcase/thumbs/shoe9-1/imgg4.jpg', 'img/showcase/shoe9-1/imgg1.png', 'img/showcase/shoe9-1/imgg2.png', 'img/showcase/shoe9-1/imgg3.png', 'img/showcase/shoe9-1/imgg4.png'),
(45, 19, 5, 'Gold', 1, 220, 'img/showcase/thumbs/shoe10-1/imgg1.jpg', 'img/showcase/thumbs/shoe10-1/imgg2.jpg', 'img/showcase/thumbs/shoe10-1/imgg3.jpg', 'img/showcase/thumbs/shoe10-1/imgg4.jpg', 'img/showcase/shoe10-1/imgg1.png', 'img/showcase/shoe10-1/imgg2.png', 'img/showcase/shoe10-1/imgg3.png', 'img/showcase/shoe10-1/imgg4.png'),
(46, 19, 6, 'Gold', 1, 220, 'img/showcase/thumbs/shoe10-1/imgg1.jpg', 'img/showcase/thumbs/shoe10-1/imgg2.jpg', 'img/showcase/thumbs/shoe10-1/imgg3.jpg', 'img/showcase/thumbs/shoe10-1/imgg4.jpg', 'img/showcase/shoe10-1/imgg1.png', 'img/showcase/shoe10-1/imgg2.png', 'img/showcase/shoe10-1/imgg3.png', 'img/showcase/shoe10-1/imgg4.png'),
(47, 19, 7, 'Gold', 1, 220, 'img/showcase/thumbs/shoe10-1/imgg1.jpg', 'img/showcase/thumbs/shoe10-1/imgg2.jpg', 'img/showcase/thumbs/shoe10-1/imgg3.jpg', 'img/showcase/thumbs/shoe10-1/imgg4.jpg', 'img/showcase/shoe10-1/imgg1.png', 'img/showcase/shoe10-1/imgg2.png', 'img/showcase/shoe10-1/imgg3.png', 'img/showcase/shoe10-1/imgg4.png'),
(48, 19, 8, 'Gold', 1, 220, 'img/showcase/thumbs/shoe10-1/imgg1.jpg', 'img/showcase/thumbs/shoe10-1/imgg2.jpg', 'img/showcase/thumbs/shoe10-1/imgg3.jpg', 'img/showcase/thumbs/shoe10-1/imgg4.jpg', 'img/showcase/sh oe10-1/imgg1.png', 'img/showcase/shoe10-1/imgg2.png', 'img/showcase/shoe10-1/imgg3.png', 'img/showcase/shoe10-1/imgg4.png'),
(49, 20, 9, 'Black', 2, 320, 'img/showcase/thumbs/shoe11-1/imgg1.jpg', 'img/showcase/thumbs/shoe11-1/imgg2.jpg', 'img/showcase/thumbs/shoe11-1/imgg3.jpg', 'img/showcase/thumbs/shoe11-1/imgg4.jpg', 'img/showcase/shoe11-1/imgg1.png', 'img/showcase/shoe11-1/imgg2.png', 'img/showcase/shoe11-1/imgg3.png', 'img/showcase/shoe11-1/imgg4.png'),
(50, 20, 6, 'Black', 1, 320, 'img/showcase/thumbs/shoe11-1/imgg1.jpg', 'img/showcase/thumbs/shoe11-1/imgg2.jpg', 'img/showcase/thumbs/shoe11-1/imgg3.jpg', 'img/showcase/thumbs/shoe11-1/imgg4.jpg', 'img/showcase/shoe11-1/imgg1.png', 'img/showcase/shoe11-1/imgg2.png', 'img/showcase/shoe11-1/imgg3.png', 'img/showcase/shoe11-1/imgg4.png'),
(51, 20, 7, 'Black', 1, 320, 'img/showcase/thumbs/shoe11-1/imgg1.jpg', 'img/showcase/thumbs/shoe11-1/imgg2.jpg', 'img/showcase/thumbs/shoe11-1/imgg3.jpg', 'img/showcase/thumbs/shoe11-1/imgg4.jpg', 'img/showcase/shoe11-1/imgg1.png', 'img/showcase/shoe11-1/imgg2.png', 'img/showcase/shoe11-1/imgg3.png', 'img/showcase/shoe11-1/imgg4.png'),
(52, 20, 8, 'Black', 1, 320, 'img/showcase/thumbs/shoe11-1/imgg1.jpg', 'img/showcase/thumbs/shoe11-1/imgg2.jpg', 'img/showcase/thumbs/shoe11-1/imgg3.jpg', 'img/showcase/thumbs/shoe11-1/imgg4.jpg', 'img/showcase/shoe11-1/imgg1.png', 'img/showcase/shoe11-1/imgg2.png', 'img/showcase/shoe11-1/imgg3.png', 'img/showcase/shoe11-1/imgg4.png'),
(54, 21, 6, 'Blue', 1, 650, 'img/showcase/thumbs/shoe12-1/imgg1.jpg', 'img/showcase/thumbs/shoe12-1/imgg2.jpg', 'img/showcase/thumbs/shoe12-1/imgg3.jpg', 'img/showcase/thumbs/shoe12-1/imgg4.jpg', 'img/showcase/shoe12-1/imgg1.png', 'img/showcase/shoe12-1/imgg2.png', 'img/showcase/shoe12-1/imgg3.png', 'img/showcase/shoe12-1/imgg4.png'),
(55, 21, 7, 'Blue', 1, 650, 'img/showcase/thumbs/shoe12-1/imgg1.jpg', 'img /showcase/thumbs/shoe12-1/imgg2.jpg', 'img/showcase/thumbs/shoe12-1/imgg3.jpg', 'img/showcase/thumbs/shoe12-1/imgg4.jpg', 'img/showcase/shoe12-1/imgg1.png', 'img/showcase/shoe12-1/imgg2.png', 'img/showcase/shoe12-1/imgg3.png', 'img/showcase/shoe12-1/imgg4.png'),
(56, 21, 8, 'Blue', 1, 650, 'img/showcase/thumbs/shoe12-1/imgg1.jpg', 'img/showcase/thumbs/shoe12-1/imgg2.jpg', 'img/showcase/thumbs/shoe12-1/imgg3.jpg', 'img/showcase/thumbs/shoe12-1/imgg4.jpg', 'img/showcase/shoe12-1/imgg1.png', 'img/showcase/shoe12-1/imgg2.png', 'img/showcase/shoe12-1/imgg3.png', 'img/showcase/shoe12-1/imgg4.png'),
(58, 22, 6, 'Black', 1, 430, 'img/showcase/thumbs/shoe13-1/imgg1.jpg', 'img/showcase/thumbs/shoe13-1/imgg2.jpg', 'img/showcase/thumbs/shoe13-1/imgg3.jpg', 'img/showcase/thumbs/shoe13-1/imgg4.jpg', 'img/showcase/shoe13-1/imgg1.png', 'img/showcase/shoe13-1/imgg2.png', 'img/showcase/shoe13-1/imgg3.png', 'img/showcase/shoe13-1/imgg4.png'),
(59, 22, 7, 'Black', 1, 430, 'img/showcase/thumbs/shoe13-1/imgg1.jpg', 'img/showcase/thumbs/shoe13-1/imgg2.jpg', 'img/showcase/thumbs/shoe13-1/imgg3.jpg', 'img/showcase/thumbs/shoe13-1/imgg4.jpg', 'img/showcase/shoe13-1/imgg1.png', 'img/showcase/shoe13-1/imgg2.png', 'img/showcase/shoe13-1/imgg3.png', 'img/showcase/shoe13-1/imgg4.png'),
(60, 22, 8, 'Black', 1, 430, 'img/showcase/thumbs/shoe13-1/imgg1.jpg', 'img/showcase/thumbs/shoe13-1/imgg2.jpg', 'img/showcase/thumbs/shoe13-1/imgg3.jpg', 'img/showcase/thumbs/shoe13-1/imgg4.jpg', 'img/showcase/shoe13-1/imgg1.png', 'img/showcase/shoe13-1/imgg2.png', 'img/showcase/shoe13-1/imgg3.png', 'img/showcase/shoe13-1/imgg4.png'),
(138, 1, 6, 'Gold', 1, 510, 'img/showcase/thumbs/shoe1-Gold/6790ffee7f1ce_imgg1.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f331_imgg2.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f443_imgg3.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f66f_imgg4.jpg', 'img/showcase/shoe1-Gold/6790ffee7f7a9_imgg1.png', 'img/showcase/shoe1-Gold/6790ffee7f8c0_imgg2.png', 'img/showcase/shoe1-Gold/6790ffee7f9b2_imgg3.png', 'img/showcase/shoe1-Gold/6790ffee7fb4a_imgg4.png'),
(142, 45, 8, 'Pink', 1, 751, 'img/showcase/thumbs/shoe14-1/imgg1.jpg', 'img/showcase/thumbs/shoe14-1/imgg2.jpg', 'img/showcase/thumbs/shoe14-1/imgg3.jpg', 'img/showcase/thumbs/shoe14-1/imgg4.jpg', 'img/showcase/shoe14-1/imgg1.png', 'img/showcase/shoe14-1/imgg2.png', 'img/showcase/shoe14-1/imgg3.png', 'img/showcase/shoe14-1/imgg4.png'),
(156, 1, 9, 'Gold', 2, 510, 'img/showcase/thumbs/shoe1-Gold/6790ffee7f1ce_imgg1.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f331_imgg2.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f443_imgg3.jpg', 'img/showcase/thumbs/shoe1-Gold/6790ffee7f66f_imgg4.jpg', 'img/showcase/shoe1-Gold/6790ffee7f7a9_imgg1.png', 'img/showcase/shoe1-Gold/6790ffee7f8c0_imgg2.png', 'img/showcase/shoe1-Gold/6790ffee7f9b2_imgg3.png', 'img/showcase/shoe1-Gold/6790ffee7fb4a_imgg4.png'),
(234, 102, 6, 'Black', 1, 123, 'img/showcase/thumbs/shoe98-1/6790f0d252624_imgg1.png', 'img/showcase/thumbs/shoe98-1/6790f0d252733_imgg2.webp', 'img/showcase/thumbs/shoe98-1/6790f0d2545d2_imgg3.webp', 'img/showcase/thumbs/shoe98-1/6790f0d255e41_imgg4.webp', 'img/showcase/shoe98-1/6790f0d255f62_imgg1.png', 'img/showcase/shoe98-1/6790f0d256030_imgg2.webp', 'img/showcase/shoe98-1/6790f0d2560e3_imgg3.webp', 'img/showcase/shoe98-1/6790f0d25623d_imgg4.webp');

-- --------------------------------------------------------

--
-- 表的结构 `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `sizes`
--

INSERT INTO `sizes` (`id`, `size`) VALUES
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 12),
(11, 13);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `status` enum('active','terminated') NOT NULL DEFAULT 'active',
  `termination_date` datetime DEFAULT NULL,
  `verification_token` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `reset_token`, `reset_token_expiry`, `status`, `termination_date`, `verification_token`, `is_verified`) VALUES
(10, 'Chew Hong Yi', 'chewhonyi@gmail.com', '+60126845157', '$2y$10$405HdccVEMaC5HqFAI2H0ufHT66dXzkcZ8KGTZ0BqmQAcFNPQk7sG', NULL, NULL, 'terminated', '2025-01-23 19:39:02', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `state` varchar(100) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_shipping_address` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `fullname`, `address_line`, `city`, `postcode`, `state`, `is_default`, `is_shipping_address`) VALUES
(70, 10, 'Chew Hong Yi', 'Lot67-23, Jalan Treh, Taman Sri Treh,', 'Muar', '84000', 'Johor', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `user_shipping_addresses`
--
-- 读取表 shoes_db.user_shipping_addresses 的结构时出错： #1932 - 表'shoes_db.user_shipping_addresses'在引擎中不存在
-- 读取表 shoes_db.user_shipping_addresses 的数据时发生错误： #1064 - 您的 SQL 语法有错误；请查看相关文档 附近'FROM `shoes_db`.`user_shipping_addresses`'在第1行

-- --------------------------------------------------------

--
-- 表的结构 `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pid` (`pid`);

--
-- 表的索引 `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `deleted_brands`
--
ALTER TABLE `deleted_brands`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `deleted_categories`
--
ALTER TABLE `deleted_categories`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `deleted_colors`
--
ALTER TABLE `deleted_colors`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `deleted_products`
--
ALTER TABLE `deleted_products`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `deleted_product_sizes`
--
ALTER TABLE `deleted_product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- 表的索引 `deleted_sizes`
--
ALTER TABLE `deleted_sizes`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- 表的索引 `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- 表的索引 `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product` (`product_id`);

--
-- 表的索引 `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 表的索引 `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 表的索引 `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`,`color`),
  ADD KEY `product_id` (`product_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=523;

--
-- 使用表AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `color`
--
ALTER TABLE `color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- 使用表AUTO_INCREMENT `deleted_brands`
--
ALTER TABLE `deleted_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `deleted_categories`
--
ALTER TABLE `deleted_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `deleted_colors`
--
ALTER TABLE `deleted_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `deleted_products`
--
ALTER TABLE `deleted_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- 使用表AUTO_INCREMENT `deleted_product_sizes`
--
ALTER TABLE `deleted_product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- 使用表AUTO_INCREMENT `deleted_sizes`
--
ALTER TABLE `deleted_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- 使用表AUTO_INCREMENT `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- 使用表AUTO_INCREMENT `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- 使用表AUTO_INCREMENT `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- 使用表AUTO_INCREMENT `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- 使用表AUTO_INCREMENT `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- 限制导出的表
--

--
-- 限制表 `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `products` (`id`);

--
-- 限制表 `deleted_product_sizes`
--
ALTER TABLE `deleted_product_sizes`
  ADD CONSTRAINT `deleted_product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- 限制表 `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- 限制表 `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- 限制表 `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 限制表 `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
