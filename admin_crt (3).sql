-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 03 Eki 2025, 04:42:45
-- Sunucu sürümü: 10.6.23-MariaDB
-- PHP Sürümü: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `admin_crt`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'Apple', 'Apple marka cihazlar', 'active', '2025-10-02 22:00:31'),
(2, 'Samsung', 'Samsung marka cihazlar', 'active', '2025-10-02 22:00:31'),
(3, 'Xiaomi', 'Xiaomi marka cihazlar', 'active', '2025-10-02 22:00:31'),
(4, 'Huawei', 'Huawei marka cihazlar', 'active', '2025-10-02 22:00:31'),
(5, 'Oppo', 'Oppo marka cihazlar', 'active', '2025-10-02 22:00:31'),
(6, 'Apple', 'Apple marka cihazlar', 'active', '2025-10-02 22:01:20'),
(7, 'Samsung', 'Samsung marka cihazlar', 'active', '2025-10-02 22:01:20'),
(8, 'Xiaomi', 'Xiaomi marka cihazlar', 'active', '2025-10-02 22:01:20'),
(9, 'Huawei', 'Huawei marka cihazlar', 'active', '2025-10-02 22:01:20'),
(10, 'Oppo', 'Oppo marka cihazlar', 'active', '2025-10-02 22:01:20'),
(11, 'Apple', 'Apple marka cihazlar', 'active', '2025-10-02 22:02:57'),
(12, 'Samsung', 'Samsung marka cihazlar', 'active', '2025-10-02 22:02:57'),
(13, 'Xiaomi', 'Xiaomi marka cihazlar', 'active', '2025-10-02 22:02:57'),
(14, 'Huawei', 'Huawei marka cihazlar', 'active', '2025-10-02 22:02:57'),
(15, 'Oppo', 'Oppo marka cihazlar', 'active', '2025-10-02 22:02:57'),
(16, 'Apple', 'Apple marka cihazlar', 'active', '2025-10-02 22:04:53'),
(17, 'Samsung', 'Samsung marka cihazlar', 'active', '2025-10-02 22:04:53'),
(18, 'Xiaomi', 'Xiaomi marka cihazlar', 'active', '2025-10-02 22:04:53'),
(19, 'Huawei', 'Huawei marka cihazlar', 'active', '2025-10-02 22:04:53'),
(20, 'Oppo', 'Oppo marka cihazlar', 'active', '2025-10-02 22:04:53');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'Akıllı Telefon', 'Akıllı telefonlar', 'active', '2025-10-02 22:04:53'),
(2, 'Tablet', 'Tablet cihazlar', 'active', '2025-10-02 22:04:53'),
(3, 'Dizüstü Bilgisayar', 'Laptop bilgisayarlar', 'active', '2025-10-02 22:04:53'),
(4, 'Aksesuar', 'Telefon aksesuarları', 'active', '2025-10-02 22:04:53'),
(5, 'Yedek Parça', 'Telefon yedek parçaları', 'active', '2025-10-02 22:04:53');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `id_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `usd_to_try` decimal(10,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `imei_pool`
--

CREATE TABLE `imei_pool` (
  `id` int(11) NOT NULL,
  `imei_number` varchar(15) NOT NULL,
  `status` enum('available','assigned','used') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `cost_price_usd` decimal(10,2) DEFAULT NULL,
  `cost_price_try` decimal(10,2) DEFAULT NULL,
  `selling_price_try` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `product_imei`
--

CREATE TABLE `product_imei` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `imei_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price_usd` decimal(10,2) DEFAULT NULL,
  `unit_price_try` decimal(10,2) DEFAULT NULL,
  `total_amount_usd` decimal(10,2) DEFAULT NULL,
  `total_amount_try` decimal(10,2) DEFAULT NULL,
  `exchange_rate` decimal(10,4) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `imei_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price_try` decimal(10,2) DEFAULT NULL,
  `total_amount_try` decimal(10,2) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('completed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `imei_id` int(11) DEFAULT NULL,
  `issue_description` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `estimated_completion_date` date DEFAULT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `service_steps`
--

CREATE TABLE `service_steps` (
  `id` int(11) NOT NULL,
  `service_request_id` int(11) DEFAULT NULL,
  `step_name` varchar(255) DEFAULT NULL,
  `step_description` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', 'admin@ozhan.fr', 'admin', 'active', '2025-10-02 21:37:47', '2025-10-02 21:37:47');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `imei_pool`
--
ALTER TABLE `imei_pool`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `imei_number` (`imei_number`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `product_imei`
--
ALTER TABLE `product_imei`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `imei_id` (`imei_id`);

--
-- Tablo için indeksler `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Tablo için indeksler `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `imei_id` (`imei_id`);

--
-- Tablo için indeksler `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `imei_id` (`imei_id`);

--
-- Tablo için indeksler `service_steps`
--
ALTER TABLE `service_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_request_id` (`service_request_id`);

--
-- Tablo için indeksler `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `imei_pool`
--
ALTER TABLE `imei_pool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `product_imei`
--
ALTER TABLE `product_imei`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `service_steps`
--
ALTER TABLE `service_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Tablo kısıtlamaları `product_imei`
--
ALTER TABLE `product_imei`
  ADD CONSTRAINT `product_imei_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_imei_ibfk_2` FOREIGN KEY (`imei_id`) REFERENCES `imei_pool` (`id`);

--
-- Tablo kısıtlamaları `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Tablo kısıtlamaları `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`imei_id`) REFERENCES `imei_pool` (`id`);

--
-- Tablo kısıtlamaları `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `service_requests_ibfk_3` FOREIGN KEY (`imei_id`) REFERENCES `imei_pool` (`id`);

--
-- Tablo kısıtlamaları `service_steps`
--
ALTER TABLE `service_steps`
  ADD CONSTRAINT `service_steps_ibfk_1` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
