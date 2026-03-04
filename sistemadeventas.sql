-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-03-2026 a las 00:28:43
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
-- Base de datos: `sistemadeventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `apellido_cliente` varchar(100) NOT NULL,
  `cedula_cliente` varchar(20) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `nombre_cliente`, `telefono_cliente`, `apellido_cliente`, `cedula_cliente`, `metodo_pago`, `monto`, `referencia`, `reg_date`, `estado`) VALUES
(2, 'luis', '6549854', 'Melendez', '30251', 'Pago Movil', 5.00, '1234', '2026-02-28 23:00:05', 'entregado'),
(5, 'Pepe', '45654', 'pepito', '54654', 'Efectivo', 18.00, '', '2026-03-03 22:54:12', 'entregado'),
(7, 'ewtr', '456', 'sdf', '546', 'Transferencia', 0.00, '464', '2026-03-03 23:03:00', 'pendiente'),
(8, 'fgj', '573', 'fgh', '436', 'Transferencia', 10.00, '1324', '2026-03-03 23:04:48', 'pendiente'),
(9, 'jorge', '543', 'wer', '1234', 'Efectivo', 10.00, '', '2026-03-03 23:14:04', 'pendiente'),
(10, 'ewrew', '', '', '', 'Efectivo', 13.00, '', '2026-03-03 23:24:09', 'pendiente'),
(11, 'ewrew', '', '', '', 'Efectivo', 0.00, '', '2026-03-03 23:27:07', 'pendiente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
