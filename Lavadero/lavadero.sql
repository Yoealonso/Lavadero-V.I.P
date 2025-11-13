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
  `requiere_presupuesto` tinyint(1) DEFAULT 0,
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
  `plazas` int(11) DEFAULT 4,
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
  `numero_turno` int(11) DEFAULT 1,
  `estado` enum('pendiente','confirmado','cancelado','completado') NOT NULL DEFAULT 'pendiente',
  `precio_final` decimal(10,2) NOT NULL,
  `token_confirmacion` varchar(32) DEFAULT NULL,
  `fecha_confirmacion` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `recordatorio_enviado` tinyint(1) DEFAULT 0,
  `en_lista_espera` tinyint(1) DEFAULT 0,
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

INSERT INTO `servicio` (`idservicio`, `nombre`, `descripcion`, `duracion`, `activo`, `requiere_presupuesto`) VALUES
(1, 'pre-venta-basic', 'Limpieza de tapizados, limpieza de motor, lavado premium, encerado con maquina rotorbital', '3 horas', 1, 0),
(2, 'pre-venta-premium', 'Lavado y descontaminado de carroceria, abrillantado en un solo paso, sellado ceramico (12 meses de proteccion), limpieza de tapizados full, limpieza y acondicionado de motor y cofre', '4 horas', 1, 0),
(3, 'lavado-premium-auto', 'Lavado de carroceria con espuma de shampoo neutro, encerado de carroceria, aspirado/repaso/nutrido de interior, desinfeccion con amonio cuaternario, nutrido de plasticos exteriores y cubiertas', '2 horas', 1, 0),
(4, 'lavado-premium-camioneta', 'Lavado de carroceria con espuma de shampoo neutro, encerado de carroceria, aspirado/repaso/nutrido de interior, desinfeccion con amonio cuaternario, nutrido de plasticos exteriores y cubiertas', '3 horas', 1, 0),
(5, 'lavado-premium-suv', 'Lavado de carroceria con espuma de shampoo neutro, encerado de carroceria, aspirado/repaso/nutrido de interior, desinfeccion con amonio cuaternario, nutrido de plasticos exteriores y cubiertas', '2.5 horas', 1, 0),
(6, 'lavado-vip-extreme', 'Lavado de carroceria, limpieza de motor, limpieza de chasis, desarme y limpieza de llantas, limpieza de calipers y pasaruedas, encerado con maquina rotorbital (3 meses de proteccion), aspirado de interiores, descontaminado y nutrido de plasticos y/o cueros', '4 horas', 1, 1),
(7, 'tratamiento-ceramico', 'Descontaminado de carroceria, correcion de laca eliminando marcas circulares producidas por el lavado y rayas superficiales, abrillantado de realzado de color y brillo, sellado de laca con sellador ceramico', '5 horas', 1, 1),
(8, 'abrillantado-carroceria', 'Descontaminado de carroceria, pulido y abrillantado en un solo paso logrando eliminar marcas superficiales y brindando un brillo excepcional, sellado con cera fusso coat premium con 12 meses de proteccion', '3 horas', 1, 1),
(9, 'limpieza-motor', 'Limpieza de motor y cofre con espuma de alto poder desengrasante, hidratacion de plasticos y mangueras con dressing de base acuosa libre de siliconas que permite que no se resequen', '1.5 horas', 1, 0),
(10, 'pulido-opticas', 'Lijado de plastico cobertor con diferentes espesores de lija al agua dependiendo de el daño de las mismas, pulido y abrillantado en 2 etapas, mas sellado con cera japonesa de alto poder hidrofico', '2 horas', 1, 0),
(11, 'pintura-llantas', 'Lavado y descontaminado ferrico, lijado superficial para eliminar todo tipo de impurezas, pintura en 3 tipos de colores, pintura con alto poder al rechazo del residuo de pastilla de freno', '3 horas', 1, 1),
(12, 'limpieza-tapizados', 'Limpieza con maquina de inyeccion y extraccion, logrando una limpieza y desinfeccion optima. Descontaminacion de piso, techo, puertas, baul, consola central y torpedo, dejando un acabado de fabrica', '3 horas', 1, 0);

--
-- Volcado de datos para la tabla `precio_servicio`
--

INSERT INTO `precio_servicio` (`idservicio`, `tipo_vehiculo`, `precio`, `plazas`) VALUES
(1, 'auto', 200000.00, 4),
(1, 'camioneta', 200000.00, 4),
(1, 'suv', 200000.00, 4),
(2, 'auto', 300000.00, 4),
(2, 'camioneta', 300000.00, 4),
(2, 'suv', 300000.00, 4),
(3, 'auto', 40000.00, 4),
(3, 'camioneta', 40000.00, 4),
(3, 'suv', 40000.00, 4),
(4, 'auto', 50000.00, 4),
(4, 'camioneta', 50000.00, 4),
(4, 'suv', 50000.00, 4),
(5, 'auto', 45000.00, 4),
(5, 'camioneta', 45000.00, 4),
(5, 'suv', 45000.00, 4),
(6, 'auto', 120000.00, 4),
(6, 'camioneta', 145000.00, 4),
(6, 'suv', 170000.00, 4),
(7, 'auto', 200000.00, 4),
(7, 'camioneta', 240000.00, 4),
(7, 'suv', 280000.00, 4),
(8, 'auto', 90000.00, 4),
(8, 'camioneta', 110000.00, 4),
(8, 'suv', 130000.00, 4),
(9, 'auto', 40000.00, 4),
(9, 'camioneta', 40000.00, 4),
(9, 'suv', 40000.00, 4),
(10, 'auto', 60000.00, 4),
(10, 'camioneta', 60000.00, 4),
(10, 'suv', 60000.00, 4),
(11, 'auto', 70000.00, 4),
(11, 'camioneta', 85000.00, 4),
(11, 'suv', 100000.00, 4),
(12, 'auto', 150000.00, 4),
(12, 'auto', 200000.00, 7),
(12, 'camioneta', 150000.00, 4),
(12, 'camioneta', 200000.00, 7),
(12, 'suv', 150000.00, 4),
(12, 'suv', 200000.00, 7);

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