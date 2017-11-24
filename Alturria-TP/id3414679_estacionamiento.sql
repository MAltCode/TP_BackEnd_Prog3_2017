-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2017 a las 04:52:04
-- Versión del servidor: 10.1.26-MariaDB
-- Versión de PHP: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `id3414679_estacionamiento`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cocheras`
--

CREATE TABLE `cocheras` (
  `id` int(11) NOT NULL,
  `piso` int(11) NOT NULL,
  `nroCochera` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `tipo` varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cocheras`
--

INSERT INTO `cocheras` (`id`, `piso`, `nroCochera`, `estado`, `tipo`) VALUES
(1, 1, '101', 'ocupado', 'especial'),
(2, 1, '102', 'ocupado', 'especial'),
(3, 1, '103', 'ocupado', 'especial'),
(4, 1, '104', 'ocupado', ''),
(5, 1, '105', 'ocupado', ''),
(6, 1, '106', 'libre', ''),
(7, 2, '201', 'ocupado', ''),
(8, 2, '202', 'ocupado', ''),
(9, 2, '203', 'libre', ''),
(10, 2, '204', 'libre', ''),
(11, 2, '205', 'libre', ''),
(12, 2, '206', 'libre', ''),
(13, 3, '301', 'ocupado', ''),
(14, 3, '302', 'ocupado', ''),
(15, 3, '303', 'libre', ''),
(16, 3, '304', 'libre', ''),
(17, 3, '305', 'libre', ''),
(18, 3, '306', 'libre', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operaciones`
--

CREATE TABLE `operaciones` (
  `id` int(11) NOT NULL,
  `idCochera` int(11) NOT NULL,
  `patente` varchar(10) NOT NULL,
  `idEmpleado` int(11) NOT NULL,
  `entrada` datetime NOT NULL,
  `salida` datetime DEFAULT NULL,
  `costo` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `operaciones`
--

INSERT INTO `operaciones` (`id`, `idCochera`, `patente`, `idEmpleado`, `entrada`, `salida`, `costo`) VALUES
(145, 302, 'FBI 321', 1, '2017-11-24 03:49:01', '2017-11-24 00:31:14', 3),
(146, 101, 'ASD321', 1, '2017-11-24 00:09:12', '2017-11-24 00:33:28', 4),
(147, 102, 'asdsd', 1, '2017-11-24 00:36:16', '2017-11-24 00:36:21', 0),
(148, 103, 'qqqqqqqqqq', 1, '2017-11-24 00:36:51', '2017-11-24 00:36:56', 0),
(149, 104, 'ffdsfdf', 1, '2017-11-24 00:38:33', '2017-11-24 00:38:39', 0),
(150, 105, 'dfsdsfdfs', 1, '2017-11-24 00:39:41', '2017-11-24 00:43:19', 1),
(151, 301, 'sarasa', 1, '2017-11-24 00:47:12', '2017-11-24 00:47:54', 0.12),
(152, 106, 'otro', 1, '2017-11-24 00:47:45', '2017-11-24 00:49:52', 0.35),
(153, 201, 'sasdsdasda', 1, '2017-11-24 00:49:27', NULL, NULL),
(154, 202, 'asdssd', 1, '2017-11-24 00:49:36', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(50) COLLATE utf16_spanish2_ci NOT NULL,
  `Apellido` varchar(50) COLLATE utf16_spanish2_ci NOT NULL,
  `Email` varchar(50) COLLATE utf16_spanish2_ci NOT NULL,
  `DNI` varchar(50) COLLATE utf16_spanish2_ci NOT NULL,
  `Clave` varchar(20) COLLATE utf16_spanish2_ci NOT NULL,
  `Perfil` varchar(10) COLLATE utf16_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Nombre`, `Apellido`, `Email`, `DNI`, `Clave`, `Perfil`) VALUES
(1, 'Admin', 'Admin', 'Admin', '1', '4321', 'Admin'),
(2, 'User', 'User', 'User', '1', '1234', 'empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL,
  `patente` varchar(30) COLLATE utf16_spanish2_ci NOT NULL,
  `marca` varchar(50) COLLATE utf16_spanish2_ci NOT NULL,
  `color` varchar(50) COLLATE utf16_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_spanish2_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `patente`, `marca`, `color`) VALUES
(13, 'FBI 321', 'Audi', 'Otro'),
(14, 'ASD321', 'Fiat', 'Rojo'),
(15, 'asdsd', 'Fiat', 'Rojo'),
(16, 'qqqqqqqqqq', 'Fiat', 'Rojo'),
(17, 'ffdsfdf', 'Fiat', 'Rojo'),
(18, 'dfsdsfdfs', 'Fiat', 'Rojo'),
(19, 'sarasa', 'Fiat', 'Rojo'),
(20, 'otro', 'Fiat', 'Rojo'),
(21, 'sasdsdasdasd', 'Fiat', 'Rojo'),
(22, 'asdssd', 'Fiat', 'Rojo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
