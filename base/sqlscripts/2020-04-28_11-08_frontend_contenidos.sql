-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-04-2020 a las 17:48:32
-- Versión del servidor: 10.4.8-MariaDB
-- Versión de PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `test_ombutech`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frontend_contenidos`
--

CREATE TABLE `frontend_contenidos` (
  `id` int(11) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `controlador` varchar(50) NOT NULL,
  `metadata` text NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `parametros` int(11) NOT NULL DEFAULT 0,
  `en_menu` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 999,
  `es_default` tinyint(1) NOT NULL DEFAULT 0,
  `esta_protegido` tinyint(1) NOT NULL DEFAULT 0,
  `estado` enum('HAB','DES','ELI') NOT NULL DEFAULT 'HAB',
  `last_modif` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `frontend_contenidos`
--

INSERT INTO `frontend_contenidos` (`id`, `alias`, `nombre`, `controlador`, `metadata`, `parent_id`, `parametros`, `en_menu`, `orden`, `es_default`, `esta_protegido`, `estado`, `last_modif`) VALUES
(1, '404', 'Error HTTP 404', '404', '', 0, 0, 0, 1, 0, 0, 'HAB', NULL),
(2, 'inicio', 'Home', 'pagina', '{\r\n\"vista\":\"inicio\",\r\n\"css\":\"inicio,msgerr.balloon,frmAutocomplete\",\r\n\"js\":\"jquery.fmdautocomplete,checkforms,contactus,inicio\",\r\n\"menutag\":\"Inicio\",\r\n\"description\":\"Something cool is coming soon\",\r\n\"keywords\":\"Ombú, Vivus, préstamos, online, tarjeta de credito, tarjeta credito, Visa, Credial, Mastercard, Diners, Naranja\",\r\n\"tooltip\":\"Volver al inicio del sistema\",\r\n\"hascarrusel\":true,\r\n\"editable\":true\r\n}', 0, 0, 1, 1, 1, 0, 'HAB', NULL),
(3, 'deployer', '', 'deployer', '{\r\n}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(4, 'products', 'Productos Ombutech', 'pagina', '{\r\n\"vista\":\"products\",\r\n\"css\":\"inicio\",\r\n\"js\":\"inicio\",\r\n\"menutag\":\"Productos\",\r\n\"description\":\"Productos Ombu Tech\",\r\n\"keywords\":\"Ombú, Vivus, préstamos, online, tarjeta de credito, tarjeta credito, Visa, Credial, Mastercard, Diners, Naranja\",\r\n\"tooltip\":\"Volver al inicio del sistema\",\r\n\"hascarrusel\":true,\r\n\"editable\":true\r\n}', 0, 0, 1, 2, 0, 0, 'HAB', NULL),
(5, 'services', 'Servicios Ombutech', 'pagina', '{\r\n\"vista\":\"services\",\r\n\"css\":\"inicio\",\r\n\"js\":\"inicio\",\r\n\"menutag\":\"Servicios\",\r\n\"description\":\"Ombu Tech\",\r\n\"keywords\":\"Ombú, Vivus, préstamos, online, tarjeta de credito, tarjeta credito, Visa, Credial, Mastercard, Diners, Naranja\",\r\n\"tooltip\":\"Volver al inicio del sistema\",\r\n\"hascarrusel\":true,\r\n\"editable\":true\r\n}', 0, 0, 1, 3, 0, 0, 'HAB', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `frontend_contenidos`
--
ALTER TABLE `frontend_contenidos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `frontend_contenidos`
--
ALTER TABLE `frontend_contenidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
