-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql308.infinityfree.com
-- Tiempo de generación: 18-07-2025 a las 14:15:26
-- Versión del servidor: 11.4.7-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_39489517_alquimia_technologic`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Software Personalizado', 'Soluciones de software a medida para tu negocio', NULL, 'software-personalizado', 'active', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(2, 'Aceites Esenciales', 'Aceites esenciales premium de alta calidad', NULL, 'aceites-esenciales', 'active', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(3, 'Figuras en Yeso', 'Figuras artesanales hechas a mano', NULL, 'figuras-yeso', 'active', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(4, 'Suscripciones Premium', 'Acceso a plataformas de contenido premium', NULL, 'suscripciones-premium', 'active', '2025-07-17 00:09:44', '2025-07-17 00:09:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `premium_subscriptions`
--

CREATE TABLE `premium_subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscription_type` varchar(100) NOT NULL,
  `platform` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(100) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `short_description`, `price`, `discount_price`, `sku`, `stock_quantity`, `category_id`, `images`, `features`, `specifications`, `weight`, `dimensions`, `status`, `is_featured`, `meta_title`, `meta_description`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Sistema de Gestión Empresarial', 'Software completo para gestión de inventario, ventas y clientes', 'ERP personalizado para tu empresa', '2500.00', NULL, 'ERP-001', 1, 1, NULL, NULL, NULL, NULL, NULL, 'inactive', 1, NULL, NULL, 'sistema-gestion-empresarial', '2025-07-17 00:09:44', '2025-07-17 01:20:02'),
(2, 'Aceite Esencial de Lavanda', 'Aceite esencial puro de lavanda francesa, ideal para relajación', 'Aceite esencial puro de lavanda francesa, ideal para relajación', '25.99', NULL, 'AE-LAV-001', 50, 2, '[\"assets\\/images\\/products\\/LAVANDA_68784fb90e1a9.png\"]', NULL, NULL, NULL, NULL, 'active', 1, NULL, NULL, 'aceite-esencial-de-lavanda', '2025-07-17 00:09:44', '2025-07-17 01:19:54'),
(3, 'Figura Decorativa Zen', 'Figura artesanal en yeso con diseño zen para decoración', 'Figura artesanal en yeso con diseño zen para decoración', '1.00', NULL, 'FY-ZEN-001', 20, 3, '[\"assets\\/images\\/products\\/yeso1_68784ffbc49b1.jpeg\"]', NULL, NULL, NULL, NULL, 'active', 0, NULL, NULL, 'figura-decorativa-zen', '2025-07-17 00:09:44', '2025-07-17 01:25:08'),
(4, 'Suscripción ChatGpt Plus', 'Acceso completo a ChatGpt Plus por 1 mes.', 'Acceso completo a ChatGpt Plus por 1 mes.', '3.00', NULL, 'SUB-NET-001', 100, 4, '[]', NULL, NULL, NULL, NULL, 'inactive', 1, NULL, NULL, 'suscripci-n-chatgpt-plus', '2025-07-17 00:09:44', '2025-07-17 01:25:17'),
(5, 'ChatGpt Plus', 'ChatGpt Plus Por 1 mes', 'ChatGpt Plus Por 1 mes', '3.00', NULL, 'SKU-6878512ae7411', 100, 4, '[\"assets\\/images\\/products\\/WhatsApp_Image_2025-07-16_at_8_28_30_PM_687851e5070a7.jpeg\"]', NULL, NULL, NULL, NULL, 'active', 1, NULL, NULL, 'chatgpt-plus', '2025-07-17 01:26:02', '2025-07-17 01:29:10'),
(6, 'Aceite Esencial de Eucalipto  5ml', 'Aceite Esencial', 'Aceite Esencial', '4.00', NULL, 'SKU-6878577e066df', 42, 2, '[\"assets\\/images\\/products\\/1000214126_6878579f0fdf5.jpg\"]', NULL, NULL, NULL, NULL, 'active', 0, NULL, NULL, 'aceite-esencial-de-eucalipto-5ml', '2025-07-17 01:53:02', '2025-07-17 01:53:47'),
(7, 'Aceite Esencial de Menta 5ml', 'Aceite Esencial de Menta', 'Aceite Esencial de Menta', '9.50', NULL, 'SKU-6878f7bcdcd95', 20, 2, '[]', NULL, NULL, NULL, NULL, 'active', 0, NULL, NULL, 'aceite-esencial-de-menta-5ml', '2025-07-17 13:16:44', '2025-07-17 13:16:44'),
(8, 'Aceite Esencial de Palo Santo 5ml', 'aceite esenciql de palo santo', 'aceite esenciql de palo santo', '5.00', NULL, 'SKU-687a8da3c0a4c', 14, 2, '[\"assets\\/images\\/products\\/TREE_687a8db3cca34.webp\"]', NULL, NULL, NULL, NULL, 'active', 0, NULL, NULL, 'aceite-esencial-de-palo-santo-5ml', '2025-07-18 18:08:35', '2025-07-18 18:08:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'AlquimiaTechnologic', 'text', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(2, 'site_description', 'Especialistas en productos y servicios de alta calidad', 'text', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(3, 'site_email', 'info@alquimiatechnologic.com', 'text', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(4, 'site_phone', '+1 234 567 8900', 'text', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(5, 'site_address', 'Calle Principal 123, Ciudad', 'text', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(6, 'currency', 'USD', 'text', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(7, 'tax_rate', '0.15', 'number', '2025-07-17 00:09:44', '2025-07-17 00:09:44'),
(8, 'shipping_cost', '10.00', 'number', '2025-07-17 00:09:44', '2025-07-17 00:09:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `city`, `country`, `postal_code`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@alquimiatechnologic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'AlquimiaTech', NULL, NULL, NULL, NULL, NULL, 'admin', 'active', '2025-07-17 00:09:44', '2025-07-17 00:09:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `premium_subscriptions`
--
ALTER TABLE `premium_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `premium_subscriptions`
--
ALTER TABLE `premium_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
