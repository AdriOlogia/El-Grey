-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 13-04-2021 a las 17:42:44
-- Versión del servidor: 5.7.26
-- Versión de PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `elgreyid`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frontend_contenidos`
--

DROP TABLE IF EXISTS `frontend_contenidos`;
CREATE TABLE IF NOT EXISTS `frontend_contenidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `controlador` varchar(50) NOT NULL,
  `metadata` text NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `parametros` int(11) NOT NULL DEFAULT '0',
  `en_menu` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int(11) NOT NULL DEFAULT '999',
  `es_default` tinyint(1) NOT NULL DEFAULT '0',
  `esta_protegido` tinyint(1) NOT NULL DEFAULT '0',
  `estado` enum('HAB','DES','ELI') NOT NULL DEFAULT 'HAB',
  `last_modif` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `frontend_contenidos`
--

INSERT INTO `frontend_contenidos` (`id`, `alias`, `nombre`, `controlador`, `metadata`, `parent_id`, `parametros`, `en_menu`, `orden`, `es_default`, `esta_protegido`, `estado`, `last_modif`) VALUES
(1, '404', '{\"esp\":\"Error HTTP 404\",\"eng\":\"Error HTTP 404\"}', '404', '', 0, 0, 0, 1, 0, 0, 'HAB', NULL),
(2, 'inicio', '{\"esp\":\"Transformando el arte a través de Blockchain |\",\"eng\":\"Leveraging art through Blockchain technology |\"}', 'pagina', '{\"vista\":\"inicio\",\"css\":\"inicio,msgerr.balloon,frmAutocomplet\",\"js\":\"inicio,jquery.fmdautocomplete,checkforms,contactus\",\"menutag\":\"Leveraging art through Blockchain technology | El Grey ID\",\"description\":\"We empower the art world by creating innovative Authenticity Certificates. We invite artists, collectionists and gallery owners to be part of the future\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(3, 'deployer', '', 'deployer', '{}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(4, 'art', '{\"esp\":\"Nuestra tecnología en el mundo del arte |\",\"eng\":\"Blockchain in the art scene |\"}', 'pagina', '{\"vista\":\"art\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"We strive to materialize the origin, techniques, materiales and storytelling of an artwork in a transparent way through Blockchain Authenticity Certificates\",\"description\":\"SWe strive to materialize the origin, techniques, materiales and storytelling of an artwork in a transparent way through Blockchain Authenticity Certificates\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(5, 'aboutus', 'about us', 'pagina', '{\"vista\":\"aboutus\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"aboutus\",\"description\":\"Something cool is coming soon\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(6, 'contact', '{\"esp\":\"Conoce nuestra tecnología Blockchain |\",\"eng\":\"Join our art community |\"}', 'pagina', '{\"vista\":\"contact\",\"css\":\"inicio,msgerr.balloon,frmAutocomplet\",\"js\":\"inicio,jquery.fmdautocomplete,checkforms,contactus\",\"menutag\":\"contact\",\"description\":\"Something cool is coming soon\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(7, 'how-is-certificate', '{\"esp\":\"Certificados de autenticidad Blockchain |\",\"eng\":\"Certificates of authenticity using Blockchain |\"}', 'pagina', '{\"vista\":\"how-is-certificate\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"Through Blockchain we encrypt the information of an artwork, store this information and our verification service validates its authenticity.\",\"description\":\"Through Blockchain we encrypt the information of an artwork, store this information and our verification service validates its authenticity.\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(8, 'news', '{\"esp\":\"El arte y la tecnología Blockchain |\",\"eng\":\"Blockchain technology in the art scene |\"}', 'pagina', '{\"vista\":\"news\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"The latest news and interesting facts about Blockchain Technology and Certificates of Authenticity in the art scene\",\"description\":\"The latest news and interesting facts about Blockchain Technology and Certificates of Authenticity in the art scene\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(9, 'purpose', '{\"esp\":\"Identidad mediante certificados de autenticidad |\",\"eng\":\"Preserving identity with authenticity certificates |\"}', 'pagina', '{\"vista\":\"purpose\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"We value identity and strive to immortalize the bond between an artwork and its artist to preserve its essence and trace through time using Blockchain\",\"description\":\"We value identity and strive to immortalize the bond between an artwork and its artist to preserve its essence and trace through time using Blockchain\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(10, 'what-is-blockchain', '{\"esp\":\"¿Qué es la tecnología Blockchain\",\"eng\":\"What is Blockchain Technology |\"}', 'pagina', '{\"vista\":\"what-is-blockchain\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"what-is-blockchain\",\"description\":\"Something cool is coming soon\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL),
(11, 'what-we-do', '{\"esp\":\"Registra obras de arte con tecnología Blockchain |\",\"eng\":\"Register pieces of art with Blockchain technology |\"}', 'pagina', '{\"vista\":\"what-we-do\",\"css\":\"inicio\",\"js\":\"\",\"menutag\":\"Blockchain is an electronic ledger that records a secure and immutable transaction of value, such as documents, certificates or money.\",\"description\":\"Blockchain is an electronic ledger that records a secure and immutable transaction of value, such as documents, certificates or money.\",\"keywords\":\"El Grey ID, Grey ID\",\"tooltip\":\"Volver al inicio del sistema\",\"hascarrusel\":true,\"editable\":true}', 0, 0, 1, 1, 0, 0, 'HAB', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
