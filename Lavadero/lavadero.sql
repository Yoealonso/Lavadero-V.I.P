-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2025 a las 17:19:24
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
-- Base de datos: `lavadero`
--
CREATE DATABASE IF NOT EXISTS `lavadero` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lavadero`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcliente`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idservicio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `duracion` varchar(15) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idservicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precio_servicio`
--

CREATE TABLE `precio_servicio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idservicio` int(11) NOT NULL,
  `tipo_vehiculo` varchar(15) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idservicio` (`idservicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculo`
--

CREATE TABLE `vehiculo` (
  `idvehiculo` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(15) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `anio` int(11) DEFAULT NULL,
  `patente` varchar(15) NOT NULL,
  `color` varchar(30) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `idcliente` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idvehiculo`),
  UNIQUE KEY `patente` (`patente`),
  KEY `idcliente` (`idcliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `idturno` int(11) NOT NULL AUTO_INCREMENT,
  `fechaReserva` date NOT NULL,
  `horaReserva` time NOT NULL,
  `estado` enum('pendiente','confirmado','cancelado','completado') NOT NULL DEFAULT 'pendiente',
  `precio_final` decimal(10,2) NOT NULL,
  `token_confirmacion` varchar(32) DEFAULT NULL,
  `fecha_confirmacion` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `idcliente` int(11) NOT NULL,
  `idvehiculo` int(11) NOT NULL,
  `idservicio` int(11) NOT NULL,
  PRIMARY KEY (`idturno`),
  UNIQUE KEY `token_confirmacion` (`token_confirmacion`),
  KEY `fechaReserva` (`fechaReserva`),
  KEY `estado` (`estado`),
  KEY `idcliente` (`idcliente`),
  KEY `idvehiculo` (`idvehiculo`),
  KEY `idservicio` (`idservicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`idservicio`, `nombre`, `descripcion`, `duracion`, `activo`) VALUES
(1, 'basico', 'Lavado exterior y aspirado interior', '1 hora', 1),
(2, 'premium', 'Lavado completo, cera protectora y limpieza de motor', '2 horas', 1),
(3, 'full', 'Tratamiento completo con cera, limpieza interior profunda y abrillantado de llantas', '3 horas', 1);

--
-- Volcado de datos para la tabla `precio_servicio`
--

INSERT INTO `precio_servicio` (`idservicio`, `tipo_vehiculo`, `precio`) VALUES
(1, 'auto', 1500.00),
(1, 'camioneta', 1800.00),
(1, 'suv', 2000.00),
(2, 'auto', 2500.00),
(2, 'camioneta', 3000.00),
(2, 'suv', 3500.00),
(3, 'auto', 3500.00),
(3, 'camioneta', 4000.00),
(3, 'suv', 4500.00);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `precio_servicio`
--
ALTER TABLE `precio_servicio`
  ADD CONSTRAINT `precio_servicio_ibfk_1` FOREIGN KEY (`idservicio`) REFERENCES `servicio` (`idservicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD CONSTRAINT `vehiculo_ibfk_1` FOREIGN KEY (`idcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `turno`
--
ALTER TABLE `turno`
  ADD CONSTRAINT `turno_ibfk_1` FOREIGN KEY (`idcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_2` FOREIGN KEY (`idvehiculo`) REFERENCES `vehiculo` (`idvehiculo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_3` FOREIGN KEY (`idservicio`) REFERENCES `servicio` (`idservicio`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;