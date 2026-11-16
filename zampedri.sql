-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-11-2026 a las 01:03:40
-- Versión del servidor: 10.11.16-MariaDB
-- Versión de PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `zampedri`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `imagen`) VALUES
(1, 'Aceite Castrol Actevo 4T 20W-50', 18000, NULL),
(2, 'Aceite Motul 5100 4T semisintético, 1 litro', 35000, NULL),
(3, 'Aceite Yamalube para motores 2T, 1 litro', 15000, NULL),
(4, 'Bujía NGK C7HSA', 8000, NULL),
(5, 'Filtro de aceite para moto', 14000, NULL),
(6, 'Lubricante para cadena de moto', 25000, NULL),
(7, 'Grasa lubricante para cadena Penetrit', 10000, NULL),
(8, 'Limpiador de cadena para moto', 15000, NULL),
(9, 'Líquido de frenos DOT 4', 18000, NULL),
(10, 'Pastillas de freno para moto', 30000, NULL),
(11, 'Filtro de nafta universal', 5000, NULL),
(12, 'Cámara de aire para moto rodado 18', 13000, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

DROP TABLE IF EXISTS `turnos`;
CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `marca` varchar(30) NOT NULL,
  `servicio` varchar(60) NOT NULL,
  `fecha` date NOT NULL,
  `mensaje` varchar(300) DEFAULT NULL,
  `atendido` tinyint(1) NOT NULL DEFAULT 0,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
