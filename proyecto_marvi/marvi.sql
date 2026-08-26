-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2026 at 10:47 PM
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
-- Database: `marvi`
--

-- --------------------------------------------------------

--
-- Table structure for table `postulaciones`
--

CREATE TABLE `postulaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `puesto_postulado` varchar(150) DEFAULT NULL,
  `carta_presentacion` text DEFAULT NULL,
  `ruta_cv` varchar(512) DEFAULT NULL,
  `fecha_postulacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `postulaciones`
--

INSERT INTO `postulaciones` (`id`, `nombre`, `email`, `telefono`, `puesto_postulado`, `carta_presentacion`, `ruta_cv`, `fecha_postulacion`) VALUES
(1, 'Pela', 'pela222@example.com', '099342871', 'Panadero/a', 'Si', 'backend/uploads/cv_6a8f3eee71f8e.pdf', '2026-08-26 16:30:54'),
(2, 'Bado', 'bado123@gmail.com', '091334267', 'Limpieza', 'yolo', 'backend/uploads/cv_6a8f4ff5c365c.pdf', '2026-08-26 17:43:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `puesto_postulado` (`puesto_postulado`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
