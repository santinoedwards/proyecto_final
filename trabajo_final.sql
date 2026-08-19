-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-08-2026 a las 23:06:04
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `trabajo_final`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `ID_Empresa` int(11) NOT NULL,
  `Nombre_Empresa` varchar(45) NOT NULL,
  `Direccion` text NOT NULL,
  `Informacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`ID_Empresa`, `Nombre_Empresa`, `Direccion`, `Informacion`) VALUES
(1, 'google', 'general artiga 512', 'empresa de tecnologia y software internacional');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulante`
--

CREATE TABLE `postulante` (
  `ID_Postulante` int(11) NOT NULL,
  `Nombre_Postulante` varchar(45) NOT NULL,
  `Edad` int(11) NOT NULL,
  `Telefono` text NOT NULL,
  `Email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `postulante`
--

INSERT INTO `postulante` (`ID_Postulante`, `Nombre_Postulante`, `Edad`, `Telefono`, `Email`) VALUES
(1, 'Ana Perez', 23, '12345', 'AnaP@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `ID_Publicacion` int(11) NOT NULL,
  `ID_Empresa` int(11) NOT NULL,
  `Fecha` text NOT NULL,
  `Sueldo` decimal(10,0) NOT NULL,
  `Requisitos` text NOT NULL,
  `Descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`ID_Publicacion`, `ID_Empresa`, `Fecha`, `Sueldo`, `Requisitos`, `Descripcion`) VALUES
(1, 1, '17/7', 2500, 'dispocision a la mañana,cursos de bachillerato de informatica acabado', 'buscamos un programador para ayudarnos a diseñar un nuevo sitio web'),
(2, 1, '17/7', 2500, 'disposición a la mañana, cursos de bachillerato de informática acabado', 'buscamos un programador para ayudarnos a diseñar un nuevo sitio web'),
(3, 1, '18/7', 2600, 'disposición a la mañana, cursos de bachillerato de informática acabado', 'buscamos un diseñador web con experiencia en HTML y CSS'),
(4, 1, '19/7', 2700, 'disposición a la mañana, conocimientos en JavaScript', 'se requiere programador frontend para proyecto dinámico'),
(5, 1, '20/7', 2800, 'experiencia en bases de datos y PHP', 'desarrollador backend para integración de APIs'),
(6, 1, '21/7', 2900, 'manejo de frameworks JS', 'buscamos desarrollador full stack para nueva plataforma'),
(7, 1, '22/7', 3000, 'buenas habilidades de comunicación', 'programador para trabajar en equipo de desarrollo ágil'),
(8, 1, '23/7', 3100, 'conocimiento en diseño UX/UI', 'desarrollador frontend para mejora de experiencia usuario'),
(9, 1, '24/7', 3200, 'manejo de control de versiones Git', 'programador para mantenimiento y nuevas funcionalidades'),
(10, 1, '25/7', 3300, 'conocimiento en testing automatizado', 'ingeniero de software para asegurar calidad del código'),
(11, 1, '26/7', 3400, 'experiencia en cloud computing', 'desarrollador para migración a la nube'),
(12, 1, '27/7', 3500, 'experiencia en desarrollo móvil', 'programador para app móvil multiplataforma'),
(13, 1, '28/7', 3600, 'conocimiento en seguridad informática', 'especialista en seguridad para proteger aplicaciones web'),
(14, 1, '29/7', 3700, 'manejo de metodologías ágiles', 'scrum master para equipo de desarrollo'),
(15, 1, '30/7', 3800, 'habilidades de liderazgo y comunicación', 'líder técnico para proyectos de software'),
(16, 1, '31/7', 3900, 'experiencia en IA y machine learning', 'desarrollador para proyectos de inteligencia artificial');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`ID_Empresa`);

--
-- Indices de la tabla `postulante`
--
ALTER TABLE `postulante`
  ADD PRIMARY KEY (`ID_Postulante`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`ID_Publicacion`),
  ADD KEY `ID_Empresa` (`ID_Empresa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `ID_Empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `postulante`
--
ALTER TABLE `postulante`
  MODIFY `ID_Postulante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `ID_Publicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`ID_Empresa`) REFERENCES `empresa` (`ID_Empresa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
