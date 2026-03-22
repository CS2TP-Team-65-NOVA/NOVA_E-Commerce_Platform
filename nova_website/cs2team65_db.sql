-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2025 at 05:51 PM
-- Server version: 8.0.44-0ubuntu0.22.04.1
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*CREATE DATABASE cs2team65_db;*/
USE cs2team65_db;

-- --------------------------------------------------------

--
-- Table structure for table `basket`
--

CREATE TABLE `basket` (
  `basket_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `session_id` char(36) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `status` enum('active','converted','abandoned') COLLATE utf8mb4_unicode_520_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `basket_items`
--

CREATE TABLE `basket_items` (
  `basket_item_id` int NOT NULL,
  `basket_id` int NOT NULL,
  `size_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `size_id` int NOT NULL,
  `product_id` int NOT NULL,
  `size_label` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `stock_qty` int NOT NULL DEFAULT '0',
  `low_stock_alert_status` tinyint(1) DEFAULT '0',
  `status` enum('in_stock','low_stock','out_of_stock') COLLATE utf8mb4_unicode_520_ci DEFAULT 'in_stock',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int NOT NULL,
  `size_id` int NOT NULL,
  `change_type` enum('sale','restock','return','manual') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `quantity_changed` int NOT NULL,
  `quantity_before` int NOT NULL,
  `quantity_after` int NOT NULL,
  `reference_type` enum('order','stock','return','manual') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `reference_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `order_number` varchar(30) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `payment_status` enum('success','pending','refunded') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `delivery_status` enum('processing','shipped','delivered','returned') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `shipping_amount` decimal(10,2) DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_520_ci,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_520_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_items_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `size_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `line_total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `payment_method` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `payment_status` enum('success','pending','refunded') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_versions`
--

CREATE TABLE `product_versions` (
  `size_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `sku` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `size_ml` decimal(10,2) DEFAULT NULL,
  `price` int DEFAULT NULL,
  `stock_qty` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `discount_percentage` decimal(5,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_520_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `full_name` varchar(84) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `phone_number` varchar(12) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `role` enum('customer','admin') COLLATE utf8mb4_unicode_520_ci DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`basket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `basket_items`
--
ALTER TABLE `basket_items`
  ADD PRIMARY KEY (`basket_item_id`),
  ADD UNIQUE KEY `basket_id` (`basket_id`,`size_id`),
  ADD KEY `fk_basket_items_size` (`size_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_items_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `product_versions`
--
ALTER TABLE `product_versions`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `fk_versions_product` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`user_id`),
  ADD KEY `fk_review_users` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `basket`
--
ALTER TABLE `basket`
  MODIFY `basket_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `basket_items`
--
ALTER TABLE `basket_items`
  MODIFY `basket_item_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `size_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_items_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_versions`
--
ALTER TABLE `product_versions`
  MODIFY `size_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `basket_items`
--
ALTER TABLE `basket_items`
  ADD CONSTRAINT `fk_basket_items_basket` FOREIGN KEY (`basket_id`) REFERENCES `basket` (`basket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_basket_items_size` FOREIGN KEY (`size_id`) REFERENCES `product_versions` (`size_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`size_id`) REFERENCES `inventory` (`size_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `inventory` (`size_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_versions`
--
ALTER TABLE `product_versions`
  ADD CONSTRAINT `fk_versions_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/* ===========================================
   1. CATEGORIES (9 total)
   =========================================== */

INSERT INTO categories (category, description) VALUES
('Exclusive Perfumes', 'High-end limited edition Nova fragrances.'),
('Women - Citrus Perfumes', 'Fresh, zesty citrus scents for women.'),
('Women - Floral Perfumes', 'Soft and elegant floral fragrances for women.'),
('Women - Spicy Perfumes', 'Warm and bold spicy notes for women.'),
('Men - Citrus Perfumes', 'Energetic citrus fragrances for men.'),
('Men - Oriental Perfumes', 'Rich oriental scents for men.'),
('Men - Spicy Perfumes', 'Intense and spicy fragrances for men.'),
('Sale', 'Discounted and promotional Nova fragrances.'),
('Gift Box', 'Curated perfume gift sets and boxes.');


/* ===========================================
   2. PRODUCTS (75 perfumes)
   =========================================== */

INSERT INTO products (category_id, brand_id, description, name, price, image)
VALUES
-- EXCLUSIVE (cat 1)
(1, NULL, 'A rich blend of oud, rose and amber for special evenings.', 'Nova Royal Essence', 129.00, 'exclusive_royal_essence.jpg'),
(1, NULL, 'Dark woods and smoky oud wrapped in vanilla.', 'Nova Midnight Oud', 139.00, 'exclusive_midnight_oud.jpg'),
(1, NULL, 'White musk and iris with a clean, luxurious trail.', 'Nova Imperial Musk', 135.00, 'exclusive_imperial_musk.jpg'),
(1, NULL, 'Warm amber, vanilla and tonka bean with long-lasting depth.', 'Nova Velvet Amber', 132.00, 'exclusive_velvet_amber.jpg'),
(1, NULL, 'Intense leather and spice with a modern twist.', 'Nova Noir Intense', 145.00, 'exclusive_noir_intense.jpg'),

-- WOMEN – CITRUS (cat 2)
(2, NULL, 'Sparkling bergamot and lemon over soft white flowers.', 'Aurora Citrus Bloom', 69.00, 'w_citrus_aurora_citrus_bloom.jpg'),
(2, NULL, 'Grapefruit, mandarin and sea breeze notes.', 'Summer Zest Nova', 72.00, 'w_citrus_summer_zest.jpg'),
(2, NULL, 'Fresh lime and neroli with a light musky base.', 'Lime Gardenia Mist', 65.00, 'w_citrus_lime_gardenia_mist.jpg'),
(2, NULL, 'Sweet orange and peach with a soft floral heart.', 'Clementine Petals', 71.00, 'w_citrus_clementine_petals.jpg'),
(2, NULL, 'Lemon, verbena and green tea for an everyday fresh scent.', 'Verbena Citrus Sheen', 68.00, 'w_citrus_verbena_sheen.jpg'),
(2, NULL, 'Sparkling citrus fusion with mandarin and neroli.', 'Mandarin Crystal', 70.00, 'w_citrus_mandarin_crystal.jpg'),
(2, NULL, 'Fresh lime zest with a soft jasmine undertone.', 'Lime Breeze Flora', 67.00, 'w_citrus_lime_breeze_flora.jpg'),
(2, NULL, 'Citrus-green apple blend with modern musky notes.', 'Citrus Apple Mist', 73.00, 'w_citrus_apple_mist.jpg'),
(2, NULL, 'Lemon verbena and soft herbal layers.', 'Verbena Dew', 69.00, 'w_citrus_verbena_dew.jpg'),
(2, NULL, 'Grapefruit blossom and lily accord.', 'Grapefruit Blossom Nova', 72.00, 'w_citrus_grapefruit_blossom.jpg'),

-- WOMEN – FLORAL (cat 3)
(3, NULL, 'Classic rose bouquet with soft musk and amber.', 'Rose Étoile', 79.00, 'w_floral_rose_etoile.jpg'),
(3, NULL, 'Jasmine, tuberose and ylang-ylang in a creamy blend.', 'Jasmine Velvet Nova', 82.00, 'w_floral_jasmine_velvet.jpg'),
(3, NULL, 'Lily of the valley and peony with a powdery trail.', 'Peony Whisper', 76.00, 'w_floral_peony_whisper.jpg'),
(3, NULL, 'Iris, violet and soft woods for an elegant daytime scent.', 'Iris Silk Bouquet', 85.00, 'w_floral_iris_silk.jpg'),
(3, NULL, 'Orange blossom and white rose with a hint of honey.', 'Blossom Lace', 78.00, 'w_floral_blossom_lace.jpg'),
(3, NULL, 'Soft rose and vanilla with powdery undertones.', 'Rose Petal Mist', 82.00, 'w_floral_rose_petal_mist.jpg'),
(3, NULL, 'Orchid, jasmine, and musk in a graceful bouquet.', 'Orchid Serenade', 88.00, 'w_floral_orchid_serenade.jpg'),
(3, NULL, 'Gardenia, lotus, and creamy woods.', 'Gardenia Silk', 85.00, 'w_floral_gardenia_silk.jpg'),
(3, NULL, 'Magnolia, lily and white woods.', 'Magnolia Essence', 80.00, 'w_floral_magnolia_essence.jpg'),
(3, NULL, 'Blooming bouquet with peony and pink rose.', 'Pink Bloom Harmony', 86.00, 'w_floral_pink_bloom_harmony.jpg'),

-- WOMEN – SPICY (cat 4)
(4, NULL, 'Pink pepper, vanilla and sandalwood for a cozy warmth.', 'Crimson Spice', 88.00, 'w_spicy_crimson_spice.jpg'),
(4, NULL, 'Cinnamon and clove with a sweet amber base.', 'Amber Chai', 90.00, 'w_spicy_amber_chai.jpg'),
(4, NULL, 'Cardamom, rose and tonka bean with a modern twist.', 'Spiced Rose Nova', 92.00, 'w_spicy_spiced_rose.jpg'),
(4, NULL, 'Ginger and mandarin over creamy vanilla.', 'Ginger Bloom', 87.00, 'w_spicy_ginger_bloom.jpg'),
(4, NULL, 'Peppery opening with smooth cashmere woods.', 'Velvet Ember', 89.00, 'w_spicy_velvet_ember.jpg'),
(4, NULL, 'Warm cinnamon and patchouli with smooth musk.', 'Cinnamon Rose', 91.00, 'w_spicy_cinnamon_rose.jpg'),
(4, NULL, 'Peppery vanilla and saffron blend.', 'Saffron Heat', 95.00, 'w_spicy_saffron_heat.jpg'),
(4, NULL, 'Sweet spice and amber with a creamy base.', 'Amber Spice Veil', 89.00, 'w_spicy_amber_spice_veil.jpg'),
(4, NULL, 'Ginger, pink pepper and soft florals.', 'Ginger Flame', 92.00, 'w_spicy_ginger_flame.jpg'),
(4, NULL, 'Nutmeg and tonka bean with a modern edge.', 'Nutmeg Ember', 90.00, 'w_spicy_nutmeg_ember.jpg'),

-- MEN – CITRUS (cat 5)
(5, NULL, 'Lemon, bergamot and marine notes with a woody base.', 'Nova Marine Citrus', 74.00, 'm_citrus_marine_citrus.jpg'),
(5, NULL, 'Sharp grapefruit and vetiver with a clean dry-down.', 'Graphite Citrus Vetiver', 79.00, 'm_citrus_vetiver.jpg'),
(5, NULL, 'Mandarin and mint with light cedarwood.', 'Citrus Rush Nova', 72.00, 'm_citrus_rush.jpg'),
(5, NULL, 'Lime, ginger and musk for a sporty feel.', 'Sport Lime Drive', 73.00, 'm_citrus_sport_lime.jpg'),
(5, NULL, 'Orange zest and rosemary with aromatic notes.', 'Citrus Aromatique', 77.00, 'm_citrus_aromatique.jpg'),
(5, NULL, 'Bright citrus burst with bergamot and mint.', 'Bergamot Flash', 78.00, 'm_citrus_bergamot_flash.jpg'),
(5, NULL, 'Cool lemon and cedar with fresh aromatics.', 'Cedar Lemon Frost', 76.00, 'm_citrus_cedar_lemon_frost.jpg'),
(5, NULL, 'Orange zest and smoky vetiver.', 'Orange Vetiver Pulse', 81.00, 'm_citrus_orange_vetiver_pulse.jpg'),
(5, NULL, 'Lime twist with ginger and musk.', 'Lime Fusion Nova', 79.00, 'm_citrus_lime_fusion.jpg'),
(5, NULL, 'Mandarin and marine freshness for daily wear.', 'Mandarin Tide', 75.00, 'm_citrus_mandarin_tide.jpg'),

-- MEN – ORIENTAL (cat 6)
(6, NULL, 'Oud, saffron and rose in an oriental accord.', 'Oud Saffron Royale', 99.00, 'm_oriental_oud_saffron.jpg'),
(6, NULL, 'Incense, labdanum and warm resins.', 'Nova Incense Shadow', 95.00, 'm_oriental_incense_shadow.jpg'),
(6, NULL, 'Vanilla, amber and dark woods for evenings.', 'Amber Nightfall', 97.00, 'm_oriental_amber_nightfall.jpg'),
(6, NULL, 'Patchouli, cocoa and spicy notes with depth.', 'Oriental Noir Code', 102.00, 'm_oriental_noir_code.jpg'),
(6, NULL, 'Tonka bean, benzoin and smoky woods.', 'Smoked Amber Nova', 98.00, 'm_oriental_smoked_amber.jpg'),
(6, NULL, 'Deep oud and vanilla contrasted with spice.', 'Oud Ember King', 109.00, 'm_oriental_oud_ember_king.jpg'),
(6, NULL, 'Dark amber, incense and sweet resin.', 'Amber Resin Royale', 112.00, 'm_oriental_amber_resin_royale.jpg'),
(6, NULL, 'Warm tobacco and cocoa with smoky woods.', 'Tobacco Noir Nova', 115.00, 'm_oriental_tobacco_noir.jpg'),
(6, NULL, 'Saffron, leather and deep cedar.', 'Saffron Leather Blaze', 118.00, 'm_oriental_saffron_leather_blaze.jpg'),
(6, NULL, 'Spiced amber and benzoin with warm depth.', 'Amber Forge', 113.00, 'm_oriental_amber_forge.jpg'),

-- MEN – SPICY (cat 7)
(7, NULL, 'Black pepper and cedar with a masculine finish.', 'Black Pepper Woods', 86.00, 'm_spicy_black_pepper_woods.jpg'),
(7, NULL, 'Nutmeg, cardamom and leather accord.', 'Cardamom Leather', 89.00, 'm_spicy_cardamom_leather.jpg'),
(7, NULL, 'Warm spices over smoky vetiver.', 'Spiced Vetiver Nova', 91.00, 'm_spicy_vetiver.jpg'),
(7, NULL, 'Chili, saffron and dark woods for a bold trail.', 'Crimson Ember Man', 93.00, 'm_spicy_crimson_ember.jpg'),
(7, NULL, 'Coriander, ginger and amber for evening wear.', 'Ginger Ember Trail', 88.00, 'm_spicy_ginger_ember_trail.jpg'),
(7, NULL, 'Cedarwood, pepper and smoky spices.', 'Cedar Spice Storm', 92.00, 'm_spicy_cedar_spice_storm.jpg'),
(7, NULL, 'Bold chili and amber with masculine depth.', 'Chili Ember Nova', 95.00, 'm_spicy_chili_ember_nova.jpg'),
(7, NULL, 'Nutmeg, patchouli and dark woods.', 'Nutmeg Shadow', 94.00, 'm_spicy_nutmeg_shadow.jpg'),
(7, NULL, 'Ginger and resin for warm evening scent.', 'Ginger Resin Flame', 91.00, 'm_spicy_ginger_resin_flame.jpg'),
(7, NULL, 'Black pepper and earthy tones.', 'Black Pepper Intenso', 96.00, 'm_spicy_black_pepper_intenso.jpg'),

-- SALE (cat 8)
(8, NULL, 'Fresh citrus-floral blend offered at a special price.', 'Nova Daylight Sale Edition', 49.00, 'sale_daylight.jpg'),
(8, NULL, 'Soft vanilla musk, perfect everyday scent.', 'Soft Aura Sale Edition', 45.00, 'sale_soft_aura.jpg'),
(8, NULL, 'Sporty citrus and woods in a value bottle.', 'Nova Sprint Sale Edition', 42.00, 'sale_sprint.jpg'),
(8, NULL, 'Sweet berry and floral notes discounted.', 'Berry Bloom Sale', 39.00, 'sale_berry_bloom.jpg'),
(8, NULL, 'Warm amber and sandalwood clearance edition.', 'Amber Drift Sale', 44.00, 'sale_amber_drift.jpg'),

-- GIFT BOX (cat 9)
(9, NULL, 'Set of three mini citrus perfumes.', 'Citrus Discovery Gift Box', 59.00, 'gift_citrus_discovery_box.jpg'),
(9, NULL, 'Mixed floral minis in a keepsake box.', 'Floral Elegance Gift Box', 62.00, 'gift_floral_elegance_box.jpg'),
(9, NULL, 'His & Hers oriental duo set.', 'Oriental Duo Gift Set', 69.00, 'gift_oriental_duo.jpg'),
(9, NULL, 'Four best-selling unisex minis.', 'Nova Signature Collection Box', 75.00, 'gift_signature_box.jpg'),
(9, NULL, 'Holiday limited edition selection of spicy scents.', 'Spice Trail Gift Chest', 72.00, 'gift_spice_trail_chest.jpg');

/* ===========================================
   6. SIZE OPTIONS + STOCK
   (50 / 100 / 150 / 200 ml)
   =========================================== */

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE inventory_logs;
TRUNCATE TABLE inventory;
TRUNCATE TABLE product_versions;
SET FOREIGN_KEY_CHECKS = 1;

-- 50 ml
INSERT INTO product_versions (product_id, sku, size_ml, price, stock_qty)
SELECT 
    p.product_id,
    CONCAT('P', p.product_id, '_50'),
    50.00,
    ROUND(p.price * 0.70),
    15
FROM products p;

-- 100 ml (some out of stock)
INSERT INTO product_versions (product_id, sku, size_ml, price, stock_qty)
SELECT 
    p.product_id,
    CONCAT('P', p.product_id, '_100'),
    100.00,
    ROUND(p.price * 1.00),
    CASE WHEN p.product_id % 6 = 0 THEN 0 ELSE 20 END
FROM products p;

-- 150 ml (some low stock)
INSERT INTO product_versions (product_id, sku, size_ml, price, stock_qty)
SELECT 
    p.product_id,
    CONCAT('P', p.product_id, '_150'),
    150.00,
    ROUND(p.price * 1.30),
    CASE WHEN p.product_id % 5 = 0 THEN 3 ELSE 10 END
FROM products p;

-- 200 ml (some out of stock)
INSERT INTO product_versions (product_id, sku, size_ml, price, stock_qty)
SELECT 
    p.product_id,
    CONCAT('P', p.product_id, '_200'),
    200.00,
    ROUND(p.price * 1.60),
    CASE WHEN p.product_id % 4 = 0 THEN 0 ELSE 5 END
FROM products p;

-- Mirror into inventory table
INSERT INTO inventory (size_id, product_id, size_label, stock_qty, low_stock_alert_status, status)
SELECT 
    v.size_id,
    v.product_id,
    CONCAT(v.size_ml, ' ml'),
    v.stock_qty,
    CASE WHEN v.stock_qty > 0 AND v.stock_qty < 5 THEN 1 ELSE 0 END,
    CASE 
        WHEN v.stock_qty = 0 THEN 'out_of_stock'
        WHEN v.stock_qty < 5 THEN 'low_stock'
        ELSE 'in_stock'
    END
FROM product_versions v;

/* ===========================================
   8. SCENT, DESIGN & UNIQUE NOTES PER PRODUCT
   (top_notes, heart_notes, base_notes, launch_info,
    scent_story, design_story)
   =========================================== */

-- ===== EXCLUSIVE LINE =====

ALTER TABLE products
  ADD COLUMN top_notes    TEXT NULL AFTER description,
  ADD COLUMN heart_notes  TEXT NULL AFTER top_notes,
  ADD COLUMN base_notes   TEXT NULL AFTER heart_notes,
  ADD COLUMN launch_info  VARCHAR(255) NULL AFTER base_notes,
  ADD COLUMN scent_story  TEXT NULL AFTER launch_info,
  ADD COLUMN design_story TEXT NULL AFTER scent_story,
  -- Extra images
  ADD COLUMN image_2 varchar(255) NULL AFTER image,
  ADD COLUMN image_3 varchar(255) NULL AFTER image_2,
  ADD COLUMN image_4 varchar(255) NULL AFTER image_3,
  ADD COLUMN image_5 varchar(255) NULL AFTER image_4;


UPDATE products 
SET top_notes   = 'Bright citrus, aromatic spices and a hint of saffron.',
    heart_notes = 'Regal rose, smooth oud and warm incense.',
    base_notes  = 'Amber, sandalwood and soft musk.',
    launch_info = 'Created as a hero scent in the NOVA Exclusive line.',
    scent_story = 'Nova Royal Essence opens with luminous citrus and aromatic spices before settling into a regal blend of oud, rose and amber for an evening statement.',
    design_story = 'The bottle is finished in deep ink blue glass with clean Nova branding, created to feel weighty and special in the hand and to stand out on a dresser.'
WHERE name = 'Nova Royal Essence';

UPDATE products
SET top_notes   = 'Smoky woods lifted with dark citrus.',
    heart_notes = 'Oud, black vanilla and dried fruits.',
    base_notes  = 'Leather, amber woods and lingering musk.',
    launch_info = 'Night edition developed for the NOVA Exclusive collection.',
    scent_story = 'Nova Midnight Oud wraps smoky woods and dark vanilla around rich oud for a mysterious, late night signature that feels smooth rather than heavy.',
    design_story = 'A tall, midnight toned bottle with subtle gloss highlights and a minimal label mirrors the dark, shadowy character of the fragrance.'
WHERE name = 'Nova Midnight Oud';

UPDATE products
SET top_notes   = 'Cool iris, sheer aldehydes and soft citrus.',
    heart_notes = 'Powdery musk, violet and light woods.',
    base_notes  = 'Cashmere woods, white musk and a touch of vanilla.',
    launch_info = 'Signature musk introduced with the first NOVA Exclusive drop.',
    scent_story = 'Nova Imperial Musk blends soft iris, powdery musk and delicate woods to create a clean skin like scent that feels quietly luxurious all day.',
    design_story = 'Frosted glass, rounded shoulders and a simple metal collar give the bottle a light, airy look that matches the silky texture of the perfume.'
WHERE name = 'Nova Imperial Musk';

UPDATE products
SET top_notes   = 'Warm spices, plum and golden resins.',
    heart_notes = 'Velvety amber, tonka and gentle florals.',
    base_notes  = 'Vanilla, benzoin and soft woods.',
    launch_info = 'Limited Velvet chapter in the NOVA Exclusive line.',
    scent_story = 'Nova Velvet Amber combines warm resins, vanilla and tonka with a touch of spice to create a cocooning trail that feels like cashmere on the skin.',
    design_story = 'The bottle is coated in a warm neutral gradient with a satin finish, echoing the soft, comforting feel of the fragrance itself.'
WHERE name = 'Nova Velvet Amber';

UPDATE products
SET top_notes   = 'Aromatic lavender, cardamom and grapefruit.',
    heart_notes = 'Oud, leather and dark woods.',
    base_notes  = 'Patchouli, amber and smoky musk.',
    launch_info = 'Intense night release from the NOVA Exclusive series.',
    scent_story = 'Nova Noir Intense contrasts bright aromatic notes with dense woods, leather and spice to create a bold, confident scent that lingers long into the night.',
    design_story = 'A sleek black lacquered bottle with sharp edges and a heavy cap is designed to feel modern, powerful and unmistakably Nova.'
WHERE name = 'Nova Noir Intense';


-- ===== WOMEN – CITRUS =====
UPDATE products
SET top_notes   = 'Sparkling bergamot, lemon zest and mandarin.',
    heart_notes = 'Orange blossom, white petals and green leaves.',
    base_notes  = 'Soft musk and pale woods.',
    launch_info = 'Fresh floral citrus created for the Aurora capsule collection.',
    scent_story = 'Aurora Citrus Bloom opens with sparkling bergamot and lemon before easing into soft white flowers and clean musk for a fresh but feminine aura.',
    design_story = 'The clear bottle shows a pale citrus toned juice and is topped with a rounded cap, giving a light, sunrise inspired look on the shelf.'
WHERE name = 'Aurora Citrus Bloom';

UPDATE products
SET top_notes   = 'Juicy grapefruit, sweet mandarin and sea breeze accord.',
    heart_notes = 'Solar florals and soft coconut water.',
    base_notes  = 'Driftwood, white musk and ambergris style notes.',
    launch_info = 'Seasonal Summer Zest edition for sun-ready days.',
    scent_story = 'Summer Zest Nova blends juicy grapefruit, mandarin and sea breeze accords to capture the feeling of sun on skin and days by the water.',
    design_story = 'Horizontal shoulders and a bright accent label are paired with a compact bottle that slips easily into a beach bag or weekend case.'
WHERE name = 'Summer Zest Nova';

UPDATE products
SET top_notes   = 'Tart lime, crushed leaves and neroli.',
    heart_notes = 'Creamy gardenia, jasmine and muguet.',
    base_notes  = 'Sheer woods and powdery musk.',
    launch_info = 'Everyday garden citrus launched in the core women’s line.',
    scent_story = 'Lime Gardenia Mist combines tart lime with soft gardenia petals to create a crisp, uplifting everyday scent with a gentle floral veil.',
    design_story = 'The slim, slightly curved bottle and light green details are inspired by dew on garden leaves in early morning light.'
WHERE name = 'Lime Gardenia Mist';

UPDATE products
SET top_notes   = 'Clementine, peach skin and sparkling lemon.',
    heart_notes = 'Soft peony, rose and freesia.',
    base_notes  = 'Smooth musk and blond woods.',
    launch_info = 'Playful citrus floral added to the Clementine story range.',
    scent_story = 'Clementine Petals opens with sweet orange and peach before revealing a bouquet of soft florals, giving a playful yet polished impression.',
    design_story = 'A softly faceted bottle catches the light, while a delicate blush label reflects the juicy fruit tones of the fragrance.'
WHERE name = 'Clementine Petals';

UPDATE products
SET top_notes   = 'Lemon verbena, lime and green tea.',
    heart_notes = 'Herbal nuances and white florals.',
    base_notes  = 'Light woods and cotton musk.',
    launch_info = 'Clean, easy citrus introduced as a daily NOVA favourite.',
    scent_story = 'Verbena Citrus Sheen mixes lemon verbena, green tea and light woods to create a clean, refreshing scent ideal for busy days and simple outfits.',
    design_story = 'Minimal lines and a fine mist sprayer are paired with a fresh lime coloured strip, keeping the design as clean as the fragrance smells.'
WHERE name = 'Verbena Citrus Sheen';

UPDATE products
SET top_notes   = 'Bright mandarin, neroli and petitgrain.',
    heart_notes = 'Orange blossom and jasmine petals.',
    base_notes  = 'Soft musk and gentle vanilla.',
    launch_info = 'Crystal clear citrus launched in the Nova Daylight story.',
    scent_story = 'Mandarin Crystal offers a bright mandarin and neroli opening that melts into soft musk, giving a sparkling start and a gentle, skin like dry down.',
    design_story = 'A compact glass block with subtle bevels and a clear cap lets the radiance of the orange toned juice shine through.'
WHERE name = 'Mandarin Crystal';

UPDATE products
SET top_notes   = 'Fresh lime, bergamot and cool mint.',
    heart_notes = 'Jasmine, green leaves and a touch of lily.',
    base_notes  = 'Cedarwood and white amber.',
    launch_info = 'Breezy citrus floral introduced for warm-weather wear.',
    scent_story = 'Lime Breeze Flora balances tart lime with a whisper of jasmine, delivering a cool, breezy citrus scent that never feels sharp or heavy.',
    design_story = 'The bottle carries a light gradient from clear to soft green, echoing the movement of a breeze across a citrus grove.'
WHERE name = 'Lime Breeze Flora';

UPDATE products
SET top_notes   = 'Crisp green apple, lemon and pear.',
    heart_notes = 'Apple blossom, peony and musk.',
    base_notes  = 'Clean woods and soft amber.',
    launch_info = 'Youthful citrus mist created for the Nova Weekend range.',
    scent_story = 'Citrus Apple Mist layers crisp green apple over citrus and white musk, creating a youthful, energising trail for day to day wear.',
    design_story = 'Rounded glass and a fresh apple green band make the bottle feel fun and approachable while still very Nova.'
WHERE name = 'Citrus Apple Mist';

UPDATE products
SET top_notes   = 'Verbena leaf, lime zest and crushed herbs.',
    heart_notes = 'Green florals and aromatic tea.',
    base_notes  = 'Transparent woods and fresh musk.',
    launch_info = 'Cooling verbena water launched as an all-day splash.',
    scent_story = 'Verbena Dew is a cool, green citrus fragrance built around verbena, herbs and light woods for a relaxed, effortless freshness.',
    design_story = 'The tall, slim bottle with a soft matte label is inspired by simple apothecary lines and everyday ease.'
WHERE name = 'Verbena Dew';

UPDATE products
SET top_notes   = 'Grapefruit zest, pomelo and lemon.',
    heart_notes = 'White blossoms and rose petals.',
    base_notes  = 'Pale woods and skin-soft musk.',
    launch_info = 'Modern citrus floral created for the Grapefruit Blossom theme.',
    scent_story = 'Grapefruit Blossom Nova blends zesty grapefruit with delicate petals to create a crisp, slightly bitter citrus floral that feels sophisticated.',
    design_story = 'A clear cylindrical bottle with a subtle blush band mirrors the contrast between bright fruit and soft blossom inside.'
WHERE name = 'Grapefruit Blossom Nova';


-- ===== WOMEN – FLORAL =====
UPDATE products
SET top_notes   = 'Sparkling citrus, pink pepper and lychee.',
    heart_notes = 'Velvety rose, peony and violet.',
    base_notes  = 'Soft amber, musk and delicate woods.',
    launch_info = 'Star rose centrepiece of the NOVA Floral chapter.',
    scent_story = 'Rose Étoile captures a modern rose with sparkling top notes, a velvety floral heart and a soft amber musk base for timeless romance.',
    design_story = 'The bottle features gentle curves and a rose gold collar, designed to echo the glow of evening light on rose petals.'
WHERE name = 'Rose Étoile';

UPDATE products
SET top_notes   = 'Creamy ylang, jasmine petals and orange blossom.',
    heart_notes = 'Tuberose, jasmine absolute and heliotrope.',
    base_notes  = 'Vanilla, sandalwood and musk.',
    launch_info = 'Velvet white floral introduced as an evening signature.',
    scent_story = 'Jasmine Velvet Nova weaves creamy jasmine, tuberose and ylang into a rich floral cloud that feels indulgent yet wearable.',
    design_story = 'A slightly opaque bottle with a satin finish and a sculpted cap reflects the velvety character of the fragrance.'
WHERE name = 'Jasmine Velvet Nova';

UPDATE products
SET top_notes   = 'Pink berries, pear and mandarin.',
    heart_notes = 'Airy peony, muguet and rose.',
    base_notes  = 'Soft woods and powdery musk.',
    launch_info = 'Whisper-light bouquet created for daytime wear.',
    scent_story = 'Peony Whisper layers airy peony, muguet and soft woods to create a delicate, powdery floral trail perfect for daytime.',
    design_story = 'The bottle is compact with rounded edges, designed to look like a smooth pebble shaped by water and time.'
WHERE name = 'Peony Whisper';

UPDATE products
SET top_notes   = 'Iris leaf, bergamot and soft aldehydes.',
    heart_notes = 'Iris, violet and orris butter.',
    base_notes  = 'Cashmere woods, musk and a hint of amber.',
    launch_info = 'Silken iris floral designed for polished occasions.',
    scent_story = 'Iris Silk Bouquet highlights cool iris and violet over light woods and musk, giving a polished, understated scent ideal for the office and evenings.',
    design_story = 'Frosted glass and a slim profile give the bottle a fabric like softness and a quietly luxurious feel.'
WHERE name = 'Iris Silk Bouquet';

UPDATE products
SET top_notes   = 'Neroli, orange blossom and honeyed citrus.',
    heart_notes = 'White rose, jasmine and floral lace accord.',
    base_notes  = 'Soft amber, musk and light woods.',
    launch_info = 'Romantic lace inspired floral in the Blossom story.',
    scent_story = 'Blossom Lace combines orange blossom, white rose and a gentle honey note for a glowing, feminine floral impression.',
    design_story = 'The bottle carries a lace inspired print on its label, paired with a pale golden juice that catches the light beautifully.'
WHERE name = 'Blossom Lace';

UPDATE products
SET top_notes   = 'Rosewater, mandarin and freesia.',
    heart_notes = 'Soft rose, peony and creamy vanilla.',
    base_notes  = 'Cashmere woods and white musk.',
    launch_info = 'Comforting petal mist launched for cosy everyday use.',
    scent_story = 'Rose Petal Mist wraps soft rose and vanilla in airy musk, creating a comforting floral fragrance that feels like a favourite scarf.',
    design_story = 'A slightly domed cap and soft blush details complete a bottle design focused on comfort and easy elegance.'
WHERE name = 'Rose Petal Mist';

UPDATE products
SET top_notes   = 'Crisp pear, citrus and dewy greens.',
    heart_notes = 'Orchid accord, jasmine and lily.',
    base_notes  = 'Smooth woods and sheer amber.',
    launch_info = 'Elegant orchid floral introduced to the Nova Classics line.',
    scent_story = 'Orchid Serenade blends orchid, jasmine and gentle woods into a smooth, sophisticated floral that moves easily from day to night.',
    design_story = 'A clear bottle with a deep violet accent stripe nods to the richness of orchid petals while staying modern and clean.'
WHERE name = 'Orchid Serenade';

UPDATE products
SET top_notes   = 'Gardenia leaf, bergamot and watery greens.',
    heart_notes = 'Creamy gardenia, lotus and jasmine.',
    base_notes  = 'Skin musk and pale woods.',
    launch_info = 'Silk-smooth white floral brought to life in the Silk range.',
    scent_story = 'Gardenia Silk pairs creamy gardenia with lotus and soft woods for a floral scent that is plush yet not overpowering.',
    design_story = 'Fine vertical facets along the bottle create a silk like sheen when they catch the light.'
WHERE name = 'Gardenia Silk';

UPDATE products
SET top_notes   = 'Magnolia bud, lemon and leafy greens.',
    heart_notes = 'Magnolia flower, lily and white petals.',
    base_notes  = 'Soft woods and tranquil musk.',
    launch_info = 'Serene magnolia fragrance launched for calm moments.',
    scent_story = 'Magnolia Essence focuses on magnolia and lily, supported by gentle woods, for a fresh floral impression with a serene, calm mood.',
    design_story = 'The tall, slim bottle with a minimal white label is designed to feel as pure and simple as the scent itself.'
WHERE name = 'Magnolia Essence';

UPDATE products
SET top_notes   = 'Red berries, citrus and pink pepper.',
    heart_notes = 'Peony, pink rose and soft fruits.',
    base_notes  = 'Musk, amber and creamy woods.',
    launch_info = 'Joyful bloom scent introduced to the Pink Harmony series.',
    scent_story = 'Pink Bloom Harmony mixes peony, pink rose and soft fruits to create a joyful floral fragrance with a bright, modern personality.',
    design_story = 'The bottle is tinted with a faint pink wash and finished with a glossy cap for a youthful but chic look.'
WHERE name = 'Pink Bloom Harmony';


-- ===== WOMEN – SPICY =====
UPDATE products
SET top_notes   = 'Pink pepper, mandarin and spicy citrus.',
    heart_notes = 'Warm vanilla, jasmine and sandalwood.',
    base_notes  = 'Amber, tonka and creamy woods.',
    launch_info = 'Crimson spice aura launched as a cosy evening scent.',
    scent_story = 'Crimson Spice opens with pink pepper over smooth vanilla and sandalwood, giving a warm, enveloping scent that still feels polished.',
    design_story = 'The bottle is accented with a rich crimson band that hints at the inner heat of the fragrance.'
WHERE name = 'Crimson Spice';

UPDATE products
SET top_notes   = 'Cinnamon, clove and cardamom.',
    heart_notes = 'Milky woods, chai accord and soft florals.',
    base_notes  = 'Vanilla, sandalwood and amber.',
    launch_info = 'Tea-inspired fragrance in the NOVA Chai Stories line.',
    scent_story = 'Amber Chai blends cinnamon, clove and milky woods to evoke the comfort of a favourite spiced drink on a cool day.',
    design_story = 'A softly rounded bottle and warm caramel toned label are used to mirror the cosy comfort of the scent.'
WHERE name = 'Amber Chai';

UPDATE products
SET top_notes   = 'Rose pepper, bergamot and cardamom.',
    heart_notes = 'Rose, saffron and tonka.',
    base_notes  = 'Patchouli, amber and soft woods.',
    launch_info = 'Spiced rose entry to the modern floral-spice family.',
    scent_story = 'Spiced Rose Nova combines rose, cardamom and tonka to create a floral that blooms with subtle warmth and depth.',
    design_story = 'The clear bottle showcases a soft rose toned juice, framed by a simple black collar for contrast.'
WHERE name = 'Spiced Rose Nova';

UPDATE products
SET top_notes   = 'Fresh ginger, mandarin and lemon.',
    heart_notes = 'Orange blossom, jasmine and spicy vanilla.',
    base_notes  = 'Amber, musk and soft woods.',
    launch_info = 'Lively ginger floral launched for cool evenings.',
    scent_story = 'Ginger Bloom contrasts sparkling ginger with mandarin and creamy vanilla for a lively opening that dries down to a gentle sweetness.',
    design_story = 'Fine etching on the bottle shoulders adds movement, inspired by swirling steam from hot ginger tea.'
WHERE name = 'Ginger Bloom';

UPDATE products
SET top_notes   = 'Pink pepper, plum and citrus.',
    heart_notes = 'Cashmere woods, floral notes and amber.',
    base_notes  = 'Sandalwood, vanilla and musk.',
    launch_info = 'Glowing ember scent introduced for the Velvet capsule.',
    scent_story = 'Velvet Ember layers peppery notes over cashmere woods for a close to the skin scent that glows softly for hours.',
    design_story = 'A smoke tinted bottle paired with a fabric textured label emphasises the idea of embers under velvet.'
WHERE name = 'Velvet Ember';

UPDATE products
SET top_notes   = 'Warm cinnamon, rose pepper and bergamot.',
    heart_notes = 'Rose, patchouli and spices.',
    base_notes  = 'Amber, tonka and woods.',
    launch_info = 'Modern boho rose launched in the Nova Spice line.',
    scent_story = 'Cinnamon Rose pairs warm spices with rose and patchouli, creating a romantic, slightly bohemian fragrance.',
    design_story = 'Classic floral curves are balanced with a deep red accent, giving the bottle a nostalgic yet modern feel.'
WHERE name = 'Cinnamon Rose';

UPDATE products
SET top_notes   = 'Saffron threads, citrus and aromatic spice.',
    heart_notes = 'Floral notes, spice and amber.',
    base_notes  = 'Vanilla, woods and musk.',
    launch_info = 'Saffron-rich composition introduced as the Heat edition.',
    scent_story = 'Saffron Heat uses saffron, spice and vanilla to create an addictive, gently exotic trail that works day or night.',
    design_story = 'The bottle carries a golden saffron coloured band and a clean silhouette that keeps the focus on the glowing juice inside.'
WHERE name = 'Saffron Heat';

UPDATE products
SET top_notes   = 'Soft citrus, pink pepper and spice.',
    heart_notes = 'Amber, floral veil and warm resins.',
    base_notes  = 'Vanilla, tonka and smoke-tinged woods.',
    launch_info = 'Sheer amber spice veil created for layering and solo wear.',
    scent_story = 'Amber Spice Veil wraps soft spices and amber in a sheer, diffused cloud, giving warmth without weight.',
    design_story = 'A semi transparent label and subtle gradient glass are used to echo the veil like character of the scent.'
WHERE name = 'Amber Spice Veil';

UPDATE products
SET top_notes   = 'Ginger root, mandarin and bright pepper.',
    heart_notes = 'Spiced florals, amber and woods.',
    base_notes  = 'Vanilla, benzoin and musk.',
    launch_info = 'Energetic ginger flame launched as a bold statement scent.',
    scent_story = 'Ginger Flame brings together ginger, pink pepper and florals to create a lively, energetic fragrance with a warm base.',
    design_story = 'An angled cap and bright accent stripe across the bottle emphasise the dynamic nature of the fragrance.'
WHERE name = 'Ginger Flame';

UPDATE products
SET top_notes   = 'Nutmeg, cinnamon and citrus rind.',
    heart_notes = 'Spiced woods, florals and tonka.',
    base_notes  = 'Smoky woods, vanilla and amber.',
    launch_info = 'Comforting ember fragrance added to the winter story.',
    scent_story = 'Nutmeg Ember combines nutmeg, woods and tonka into a slightly smoky, comforting perfume ideal for cooler weather.',
    design_story = 'The deep amber toned bottle with a squared profile is inspired by glowing embers and carved wood.'
WHERE name = 'Nutmeg Ember';


-- ===== MEN – CITRUS =====
UPDATE products
SET top_notes   = 'Lemon, bergamot and marine droplets.',
    heart_notes = 'Aromatic herbs, sea breeze and florals.',
    base_notes  = 'Cedarwood, amber and musk.',
    launch_info = 'Marine citrus pillar scent of the Nova men’s line.',
    scent_story = 'Nova Marine Citrus opens with lemon, bergamot and marine notes, drying down to woods and musk for a clean, aquatic masculine scent.',
    design_story = 'A tall, clear bottle with a blue gradient suggests sea spray rising against a glass shoreline.'
WHERE name = 'Nova Marine Citrus';

UPDATE products
SET top_notes   = 'Grapefruit, bitter orange and black pepper.',
    heart_notes = 'Vetiver, aromatic herbs and geranium.',
    base_notes  = 'Dry woods and smoky vetiver.',
    launch_info = 'Office-sharp vetiver citrus launched for daily wear.',
    scent_story = 'Graphite Citrus Vetiver sharpens grapefruit with dry vetiver and woods, giving a crisp, focused scent ideal for the office.',
    design_story = 'The bottle has a graphite coloured cap and base, reflecting the clean lines and sober character of the fragrance.'
WHERE name = 'Graphite Citrus Vetiver';

UPDATE products
SET top_notes   = 'Mandarin, mint and lemon zest.',
    heart_notes = 'Aromatic herbs, cedar and watery notes.',
    base_notes  = 'Cedarwood, amber and musk.',
    launch_info = 'High-energy Rush edition designed for sport-inspired days.',
    scent_story = 'Citrus Rush Nova mixes mandarin, mint and cedarwood to deliver a fast, energetic freshness that keeps up with an active day.',
    design_story = 'The ergonomic bottle sits easily in the hand, with bold Nova branding that hints at movement and speed.'
WHERE name = 'Citrus Rush Nova';

UPDATE products
SET top_notes   = 'Lime, ginger and frosted citrus.',
    heart_notes = 'Spicy accord, green notes and musk.',
    base_notes  = 'Woody amber and soft woods.',
    launch_info = 'Sport-driven lime fragrance released with the Drive series.',
    scent_story = 'Sport Lime Drive blends lime, ginger and musk, creating a sporty, invigorating fragrance that works from gym to street.',
    design_story = 'A textured grip detail along the side of the bottle is designed for quick grab and go use.'
WHERE name = 'Sport Lime Drive';

UPDATE products
SET top_notes   = 'Orange zest, bergamot and rosemary.',
    heart_notes = 'Aromatic herbs, lavender and geranium.',
    base_notes  = 'Cedarwood, moss and musk.',
    launch_info = 'Smart-casual aromatic citrus introduced to the barbershop line.',
    scent_story = 'Citrus Aromatique pairs orange zest with rosemary and aromatic herbs for a smart casual scent that feels both fresh and grown up.',
    design_story = 'The bottle combines a clear body with a brushed metal collar, echoing classic barbershop style with a modern twist.'
WHERE name = 'Citrus Aromatique';

UPDATE products
SET top_notes   = 'Bergamot, lemon and fresh lavender.',
    heart_notes = 'Clary sage, herbal notes and florals.',
    base_notes  = 'Clean woods and ambergris style notes.',
    launch_info = 'Flash of bergamot launched as a confident signature.',
    scent_story = 'Bergamot Flash delivers a burst of bergamot and lavender over clean woods for a bright, confident signature.',
    design_story = 'Faceted glass and a fine spray atomiser help create a light, diffused mist that suits the sparkling character of the scent.'
WHERE name = 'Bergamot Flash';

UPDATE products
SET top_notes   = 'Icy lemon, grapefruit and aromatic greens.',
    heart_notes = 'Cedar, ginger and herbal notes.',
    base_notes  = 'Dry woods, musk and amber.',
    launch_info = 'Frosted citrus-woody scent introduced for crisp days.',
    scent_story = 'Cedar Lemon Frost contrasts cool lemon and cedarwood, producing a crisp, slightly icy scent that feels neat and precise.',
    design_story = 'A frosted bottle shoulder and cool grey detailing underline the chilled impression of the fragrance.'
WHERE name = 'Cedar Lemon Frost';

UPDATE products
SET top_notes   = 'Orange zest, mandarin and spice.',
    heart_notes = 'Vetiver, cedar and smoky woods.',
    base_notes  = 'Patchouli, amber and musk.',
    launch_info = 'Urban citrus-vetiver launched with the Pulse capsule.',
    scent_story = 'Orange Vetiver Pulse builds on orange zest with smoky vetiver, giving a citrus scent with a deeper, more serious backbone.',
    design_story = 'The thick glass base and linear silhouette of the bottle are inspired by city architecture at sunset.'
WHERE name = 'Orange Vetiver Pulse';

UPDATE products
SET top_notes   = 'Lime, ginger and sparkling citrus.',
    heart_notes = 'Aromatic notes, herbs and light florals.',
    base_notes  = 'Soft woods and musk.',
    launch_info = 'Fusion of lime and ginger released as a modern fresh scent.',
    scent_story = 'Lime Fusion Nova fuses lime, ginger and soft musk, offering an easy wearing freshness with a modern twist.',
    design_story = 'A slim rectangular bottle with a lime coloured centre band keeps the look simple yet instantly recognisable.'
WHERE name = 'Lime Fusion Nova';

UPDATE products
SET top_notes   = 'Mandarin, marine accord and citrus peel.',
    heart_notes = 'Aquatic florals, herbs and woods.',
    base_notes  = 'Driftwood, musk and amber.',
    launch_info = 'Tide-inspired citrus launched for weekends and holidays.',
    scent_story = 'Mandarin Tide marries mandarin, marine notes and light woods to create a relaxed, laid back scent suited to weekends and holidays.',
    design_story = 'The bottle features a gentle blue wash rising from the base, echoing the pull of the tide on the shore.'
WHERE name = 'Mandarin Tide';


-- ===== MEN – ORIENTAL =====
UPDATE products
SET top_notes   = 'Saffron, spicy citrus and aromatic notes.',
    heart_notes = 'Rich oud, rose and resins.',
    base_notes  = 'Amber, woods and musk.',
    launch_info = 'Regal oud saffron edition in the Nova oriental chapter.',
    scent_story = 'Oud Saffron Royale combines oud, saffron and rose into a rich, opulent composition that feels dressed up and confident.',
    design_story = 'A dark bottle with gold detailing and a weighty cap is designed to reflect the luxurious character of the fragrance.'
WHERE name = 'Oud Saffron Royale';

UPDATE products
SET top_notes   = 'Incense smoke, black pepper and citrus.',
    heart_notes = 'Resins, incense and dark woods.',
    base_notes  = 'Amber, patchouli and smoky musk.',
    launch_info = 'Shadowy incense scent launched for evening wear.',
    scent_story = 'Nova Incense Shadow layers incense, resins and woods to create a smoky, atmospheric scent that sits close to the skin.',
    design_story = 'The bottle uses smoked glass and a soft matte label to echo swirling incense in a darkened room.'
WHERE name = 'Nova Incense Shadow';

UPDATE products
SET top_notes   = 'Ambered citrus, spices and dried fruit.',
    heart_notes = 'Vanilla, amber and smoky woods.',
    base_notes  = 'Benzoin, patchouli and musk.',
    launch_info = 'Nightfall oriental introduced as a smooth evening signature.',
    scent_story = 'Amber Nightfall builds amber, vanilla and deep woods into a smooth, sensual evening fragrance with impressive staying power.',
    design_story = 'A gradient from deep amber to near black runs up the bottle, inspired by the sky at the end of the day.'
WHERE name = 'Amber Nightfall';

UPDATE products
SET top_notes   = 'Bitter orange, cocoa peel and spice.',
    heart_notes = 'Patchouli, cocoa and resins.',
    base_notes  = 'Dark woods, amber and musk.',
    launch_info = 'Coded oriental noir launched for modern nightlife.',
    scent_story = 'Oriental Noir Code is a complex blend of patchouli, cocoa and spices, giving a modern oriental with a slightly mysterious edge.',
    design_story = 'Sharp angles and a glossy black finish give the bottle a coded, almost secretive feel.'
WHERE name = 'Oriental Noir Code';

UPDATE products
SET top_notes   = 'Smoky incense, ambered citrus and spice.',
    heart_notes = 'Amber, tonka and woods.',
    base_notes  = 'Smoked woods, vanilla and resin.',
    launch_info = 'Softly smoked amber introduced to the Nova core line.',
    scent_story = 'Smoked Amber Nova wraps amber and tonka in smoky woods, offering a cosy yet sophisticated take on the amber family.',
    design_story = 'The bottle’s warm brown tone and subtle smoke print recall firelight fading at the end of the evening.'
WHERE name = 'Smoked Amber Nova';

UPDATE products
SET top_notes   = 'Spiced citrus, saffron and cardamom.',
    heart_notes = 'Oud, leather and warm spices.',
    base_notes  = 'Vanilla, amber and deep woods.',
    launch_info = 'Commanding oud ember statement in the King series.',
    scent_story = 'Oud Ember King brings together intense oud, warm spices and vanilla for a commanding scent with serious presence.',
    design_story = 'The heavy square bottle and bold metallic collar underline the idea of a powerful, crown like fragrance.'
WHERE name = 'Oud Ember King';

UPDATE products
SET top_notes   = 'Resinous citrus, spices and incense.',
    heart_notes = 'Amber resins, labdanum and balsams.',
    base_notes  = 'Vanilla, woods and musk.',
    launch_info = 'Opulent resin fragrance launched as Royale edition.',
    scent_story = 'Amber Resin Royale focuses on dark amber, incense and sweet resins to create a rich, enveloping trail.',
    design_story = 'Faceted sides and deep golden juice give the bottle a jewel like appearance under the light.'
WHERE name = 'Amber Resin Royale';

UPDATE products
SET top_notes   = 'Tobacco leaf, cocoa and spicy citrus.',
    heart_notes = 'Tobacco, woods and tonka.',
    base_notes  = 'Vanilla, amber and smoky woods.',
    launch_info = 'Warm tobacco oriental added to the Nova classics.',
    scent_story = 'Tobacco Noir Nova blends tobacco leaf, cocoa and woods, giving a warm, slightly smoky scent that feels both classic and modern.',
    design_story = 'The bottle combines a tobacco brown tone with clean Nova lines for a refined but approachable look.'
WHERE name = 'Tobacco Noir Nova';

UPDATE products
SET top_notes   = 'Saffron, cardamom and citrus zest.',
    heart_notes = 'Leather, cedar and smoky woods.',
    base_notes  = 'Amber, patchouli and musk.',
    launch_info = 'Urban leather blaze launched for bold nights out.',
    scent_story = 'Saffron Leather Blaze layers saffron, leather and cedar into a striking, urban oriental that stands out in a crowd.',
    design_story = 'A matte finish and a strap inspired label nod to leather goods and crafted detail.'
WHERE name = 'Saffron Leather Blaze';

UPDATE products
SET top_notes   = 'Spiced citrus, pepper and resins.',
    heart_notes = 'Amber accord, benzoin and woods.',
    base_notes  = 'Labdanum, vanilla and smoky woods.',
    launch_info = 'Forge-inspired amber created for depth and intensity.',
    scent_story = 'Amber Forge mixes spiced amber, benzoin and woods to create a glowing, resinous scent with real depth.',
    design_story = 'The bottle features industrial style typography and a solid base, suggesting molten metal cooling into form.'
WHERE name = 'Amber Forge';


-- ===== MEN – SPICY =====
UPDATE products
SET top_notes   = 'Black pepper, mandarin and cedar leaf.',
    heart_notes = 'Cedar, dry woods and spice.',
    base_notes  = 'Vetiver, patchouli and musk.',
    launch_info = 'Core spicy-woody signature of the Nova men’s line.',
    scent_story = 'Black Pepper Woods opens with a blast of black pepper before settling into cedar and dry woods for a bold, masculine signature.',
    design_story = 'The bottle wears a strong black and silver palette, echoing its straightforward, no nonsense character.'
WHERE name = 'Black Pepper Woods';

UPDATE products
SET top_notes   = 'Cardamom, nutmeg and bergamot.',
    heart_notes = 'Soft leather, spices and florals.',
    base_notes  = 'Amber, woods and musk.',
    launch_info = 'Dressed-up leather scent launched for evening wear.',
    scent_story = 'Cardamom Leather combines cardamom, nutmeg and supple leather for a smooth, dressed up evening scent.',
    design_story = 'A deep brown wrap around label and metal cap borrow cues from classic leather accessories.'
WHERE name = 'Cardamom Leather';

UPDATE products
SET top_notes   = 'Spicy citrus, pink pepper and ginger.',
    heart_notes = 'Vetiver, herbs and woods.',
    base_notes  = 'Patchouli, amber and musk.',
    launch_info = 'Dynamic vetiver spice introduced to the Nova men range.',
    scent_story = 'Spiced Vetiver Nova teams warm spices with earthy vetiver, giving a grounded yet energetic fragrance.',
    design_story = 'Fine vertical ridges along the bottle are inspired by the texture of vetiver roots.'
WHERE name = 'Spiced Vetiver Nova';

UPDATE products
SET top_notes   = 'Chili, saffron and pepper.',
    heart_notes = 'Dark woods, spices and resins.',
    base_notes  = 'Amber, leather nuance and smoky woods.',
    launch_info = 'Fiery Ember Man edition created for bold personalities.',
    scent_story = 'Crimson Ember Man mixes chili, saffron and dark woods, creating a fiery, attention grabbing scent for bold wearers.',
    design_story = 'The bottle’s crimson centre band and strong shoulders communicate intensity from the first glance.'
WHERE name = 'Crimson Ember Man';

UPDATE products
SET top_notes   = 'Fresh ginger, coriander and citrus.',
    heart_notes = 'Amber, spices and soft woods.',
    base_notes  = 'Warm woods, vanilla and musk.',
    launch_info = 'Casual ember trail designed for relaxed evenings.',
    scent_story = 'Ginger Ember Trail blends ginger, coriander and amber for a warm, approachable spicy scent with a casual edge.',
    design_story = 'A smoked gradient and simple black cap keep the design relaxed and wearable.'
WHERE name = 'Ginger Ember Trail';

UPDATE products
SET top_notes   = 'Black pepper, cedar leaf and citrus.',
    heart_notes = 'Cedarwood, smoky spices and herbs.',
    base_notes  = 'Vetiver, amber and musk.',
    launch_info = 'Storm-inspired spicy woods launched for outdoors lovers.',
    scent_story = 'Cedar Spice Storm layers cedar, pepper and smoky spices for a rugged, outdoors leaning fragrance.',
    design_story = 'Angular shoulders and a textured side strip hint at storm clouds and rough weather.'
WHERE name = 'Cedar Spice Storm';

UPDATE products
SET top_notes   = 'Chili accord, citrus and saffron.',
    heart_notes = 'Spiced amber, florals and woods.',
    base_notes  = 'Vanilla, benzoin and musk.',
    launch_info = 'Daring chili ember launched as a standout statement scent.',
    scent_story = 'Chili Ember Nova brings chili heat into a modern amber accord for a daring but surprisingly smooth signature.',
    design_story = 'The bottle features a narrow vertical window of red tint, suggesting a flame contained within glass.'
WHERE name = 'Chili Ember Nova';

UPDATE products
SET top_notes   = 'Nutmeg, citrus and smoky spice.',
    heart_notes = 'Patchouli, woods and resins.',
    base_notes  = 'Amber, tonka and musk.',
    launch_info = 'Shadowy nutmeg fragrance created for late evenings.',
    scent_story = 'Nutmeg Shadow uses nutmeg, patchouli and woods to create a deep, quietly powerful scent with a shadowy edge.',
    design_story = 'A darkened glass body with minimal branding supports the idea of understated strength.'
WHERE name = 'Nutmeg Shadow';

UPDATE products
SET top_notes   = 'Ginger, citrus and aromatic spice.',
    heart_notes = 'Resins, amber and woods.',
    base_notes  = 'Vanilla, benzoin and smoky woods.',
    launch_info = 'Flame-like resin scent launched in the Ember collection.',
    scent_story = 'Ginger Resin Flame brings resin, ginger and amber together, creating a long lasting warmth that glows on skin.',
    design_story = 'The bottle design plays with a warm orange amber tone and a refined, minimal label.'
WHERE name = 'Ginger Resin Flame';

UPDATE products
SET top_notes   = 'Black pepper, citrus and resin.',
    heart_notes = 'Pepper, woods and spices.',
    base_notes  = 'Amber, patchouli and musk.',
    launch_info = 'Intense pepper fragrance introduced as a power scent.',
    scent_story = 'Black Pepper Intenso focuses on black pepper, woods and subtle resins for a striking, high impact fragrance.',
    design_story = 'The glossy black bottle with bold white type is made to look as direct and intense as the scent smells.'
WHERE name = 'Black Pepper Intenso';


-- ===== SALE LINE =====
UPDATE products
SET top_notes   = 'Citrus peel, mandarin and grapefruit.',
    heart_notes = 'Soft florals and fresh greens.',
    base_notes  = 'Light woods and clean musk.',
    launch_info = 'Value Daylight edition released as part of the sale line.',
    scent_story = 'Nova Daylight Sale Edition is a bright citrus floral blend designed as an easy, uplifting everyday choice at a great price.',
    design_story = 'The bottle keeps the classic Nova look but with a light, sunny label treatment to signal the Daylight edition.'
WHERE name = 'Nova Daylight Sale Edition';

UPDATE products
SET top_notes   = 'Airy aldehydes, citrus and musk.',
    heart_notes = 'Soft florals and vanilla.',
    base_notes  = 'Musk, sandalwood and cashmere.',
    launch_info = 'Soft Aura value scent created for gentle everyday wear.',
    scent_story = 'Soft Aura Sale Edition offers soft vanilla musk and gentle florals in a simple, wearable composition that suits almost any occasion.',
    design_story = 'A minimal, clean bottle with subtle silver details reflects the airy softness of the scent.'
WHERE name = 'Soft Aura Sale Edition';

UPDATE products
SET top_notes   = 'Sporty citrus, lime and bergamot.',
    heart_notes = 'Woods, herbs and spices.',
    base_notes  = 'Cedar, amber and musk.',
    launch_info = 'Sprint edition launched as a sport-fresh option in the sale line.',
    scent_story = 'Nova Sprint Sale Edition mixes sporty citrus notes with woods for a casual fragrance that works from day to weekend.',
    design_story = 'The bottle design uses bold Nova branding and a compact shape for easy, on the go use.'
WHERE name = 'Nova Sprint Sale Edition';

UPDATE products
SET top_notes   = 'Juicy berries, red fruits and citrus.',
    heart_notes = 'Floral notes, rose and peony.',
    base_notes  = 'Soft musk and light woods.',
    launch_info = 'Playful berry bloom special release at promotional value.',
    scent_story = 'Berry Bloom Sale combines juicy berries with floral tones, creating a cheerful, playful scent offered at special value.',
    design_story = 'A clear bottle with a soft berry coloured label hints at the fruity character inside.'
WHERE name = 'Berry Bloom Sale';

UPDATE products
SET top_notes   = 'Ambered citrus, soft spices and woods.',
    heart_notes = 'Amber, sandalwood and warm resins.',
    base_notes  = 'Tonka, musk and vanilla.',
    launch_info = 'Drift edition added as a comforting amber in the sale range.',
    scent_story = 'Amber Drift Sale brings warm amber and sandalwood together in a simple, comforting fragrance with a budget friendly twist.',
    design_story = 'The familiar Nova silhouette is paired with a warm amber panel to mark out the Drift edition.'
WHERE name = 'Amber Drift Sale';


-- ===== GIFT BOXES =====
UPDATE products
SET top_notes   = 'Assorted citrus peels from the three minis.',
    heart_notes = 'Floral and herbal nuances from each blend.',
    base_notes  = 'Soft woods and musk across the set.',
    launch_info = 'Discovery box launched as an introduction to NOVA citrus scents.',
    scent_story = 'Citrus Discovery Gift Box contains three mini citrus fragrances, each exploring a different facet from sharp lime to juicy mandarin.',
    design_story = 'The set is presented in a sturdy Nova gift box with citrus inspired artwork and snug interior fit for each mini bottle.'
WHERE name = 'Citrus Discovery Gift Box';

UPDATE products
SET top_notes   = 'Light citrus touches from the mini collection.',
    heart_notes = 'Blend of rose, white florals and petals.',
    base_notes  = 'Amber and musk from the combined drydowns.',
    launch_info = 'Elegant floral gift box created for easy gifting.',
    scent_story = 'Floral Elegance Gift Box brings together a trio of floral minis ranging from soft rose to luminous white petals.',
    design_story = 'Delicate ribbon artwork and a pale floral palette on the box make this an effortless ready wrapped gift.'
WHERE name = 'Floral Elegance Gift Box';

UPDATE products
SET top_notes   = 'Spiced citrus facets from both orientals.',
    heart_notes = 'Amber, cocoa and woods.',
    base_notes  = 'Rich resins, vanilla and musk.',
    launch_info = 'Duo set launched to showcase NOVA oriental favourites.',
    scent_story = 'Oriental Duo Gift Set pairs two richer, oriental leaning scents, ideal for evening wear or special occasions.',
    design_story = 'The duo is housed in a slimline Nova case with separate wells for each bottle, making it easy to travel or gift.'
WHERE name = 'Oriental Duo Gift Set';

UPDATE products
SET top_notes   = 'Mixed citrus and aromatic notes from four signatures.',
    heart_notes = 'Floral, woody and musk accords.',
    base_notes  = 'Amber, woods and soft resins.',
    launch_info = 'Signature box released as a curated NOVA mini collection.',
    scent_story = 'Nova Signature Collection Box features four of the brand’s best selling unisex favourites in miniature form.',
    design_story = 'A clean, monochrome box interior and simple outer sleeve keep the focus on the iconic mini bottles inside.'
WHERE name = 'Nova Signature Collection Box';

UPDATE products
SET top_notes   = 'Assorted spices, citrus and herbs from the trail set.',
    heart_notes = 'Warm florals, woods and resins.',
    base_notes  = 'Amber, vanilla and musk across the chest.',
    launch_info = 'Spice Trail chest launched as a journey through NOVA spices.',
    scent_story = 'Spice Trail Gift Chest offers a curated selection of spicy and warm scents designed to feel like a journey through different spice markets.',
    design_story = 'The box uses map inspired graphics and rich colour blocking to underline the idea of travel and discovery.'
WHERE name = 'Spice Trail Gift Chest';

UPDATE products
SET image_2 = CONCAT(SUBSTRING_INDEX(image, '.', 1), '_2.', SUBSTRING_INDEX(image, '.', -1)),
    image_3 = CONCAT(SUBSTRING_INDEX(image, '.', 1), '_3.', SUBSTRING_INDEX(image, '.', -1)),
    image_4 = CONCAT(SUBSTRING_INDEX(image, '.', 1), '_4.', SUBSTRING_INDEX(image, '.', -1)),
    image_5 = CONCAT(SUBSTRING_INDEX(image, '.', 1), '_5.', SUBSTRING_INDEX(image, '.', -1))
WHERE image IS NOT NULL AND image <> '';

-- 1) Make sure password column is big enough for hashes
ALTER TABLE users
  MODIFY password VARCHAR(255) NOT NULL;


-- 2) Create (or reset) the admin user
DELETE FROM users WHERE email = 'admin@nova.com';

INSERT INTO users (full_name, email, password, phone_number, role)
VALUES (
    'Site Admin',
    'admin@nova.com',
    '$2b$12$aIKpppoORlMZIFAbh09CUe4S1VSpgB52.STGDlRzyo.G8q.FyW3tC',
    '0000000000',
    'admin'
);

COMMIT;

