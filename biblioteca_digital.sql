-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-10-2025 a las 16:59:19
-- Versión del servidor: 8.0.43-0ubuntu0.24.04.2
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca_digital`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`apexlabs`@`localhost` PROCEDURE `sp_actualizar_almacenamiento_usuario` (IN `p_usuario_id` INT)   BEGIN
    UPDATE usuarios 
    SET almacenamiento_usado = (
        SELECT COALESCE(SUM(tamaño), 0) 
        FROM archivos 
        WHERE propietario_id = p_usuario_id 
        AND activo = 1
    )
    WHERE id = p_usuario_id$$

CREATE DEFINER=`apexlabs`@`localhost` PROCEDURE `sp_eliminar_archivo` (IN `p_archivo_id` INT)   BEGIN
    DECLARE v_propietario_id INT$$

CREATE DEFINER=`apexlabs`@`localhost` PROCEDURE `sp_limpiar_enlaces_expirados` ()   BEGIN
    UPDATE enlaces_compartidos 
    SET activo = 0 
    WHERE fecha_expiracion IS NOT NULL 
    AND fecha_expiracion < NOW()
    AND activo = 1$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalcularUsoAlmacenamiento` (`usuario_id` INT) RETURNS BIGINT DETERMINISTIC READS SQL DATA BEGIN
    DECLARE total_bytes BIGINT DEFAULT 0$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad`
--

CREATE TABLE `actividad` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_recurso` enum('archivo','carpeta','usuario','sistema') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int DEFAULT NULL,
  `detalles` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_actividad` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos`
--

CREATE TABLE `archivos` (
  `id` int NOT NULL,
  `google_file_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_original` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo_mime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamaño` bigint DEFAULT NULL,
  `carpeta_id` int DEFAULT NULL,
  `propietario_id` int NOT NULL,
  `version` int DEFAULT '1',
  `url_descarga` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_vista` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `ruta_local` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `archivos`
--

INSERT INTO `archivos` (`id`, `google_file_id`, `nombre`, `nombre_original`, `descripcion`, `tipo_mime`, `extension`, `tamaño`, `carpeta_id`, `propietario_id`, `version`, `url_descarga`, `url_vista`, `thumbnail`, `tags`, `metadata`, `ruta_local`, `activo`, `fecha_creacion`, `fecha_modificacion`, `fecha_actualizacion`) VALUES
(1, NULL, 'kdeconnect-kde-master-5378-macos-clang-arm64.dmg', 'kdeconnect-kde-master-5378-macos-clang-arm64.dmg', NULL, 'application/zlib', 'dmg', 119569951, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, '/var/www/html/biblioteca/storage/files/1/c4ab13fb7d9087e98a5fff0daf410805.dmg', 0, '2025-10-07 23:47:41', '2025-10-08 17:48:43', '2025-10-08 17:48:43'),
(2, NULL, 'Billing History.pdf', 'Billing History.pdf', NULL, 'application/pdf', 'pdf', 77881, 2, 1, 1, NULL, NULL, NULL, NULL, NULL, '/var/www/html/biblioteca/storage/files/1/6101f659d96ad591d12cf4e9b6b07dda.pdf', 1, '2025-10-07 23:48:16', '2025-10-07 23:48:16', '2025-10-07 23:48:16'),
(3, NULL, 'C25.330 PRESUPUESTO RINO RISK CORRECCIONES OPC1.pdf', 'C25.330 PRESUPUESTO RINO RISK CORRECCIONES OPC1.pdf', NULL, 'application/pdf', 'pdf', 699386, 2, 1, 1, NULL, NULL, NULL, NULL, NULL, '/var/www/html/biblioteca/storage/files/1/51ae006c8055b33efe0ca21c5dc9d553.pdf', 1, '2025-10-07 23:48:23', '2025-10-07 23:48:23', '2025-10-07 23:48:23'),
(4, NULL, 'XML_COMPROBANTE_4_0_ASE931116231_CLIENTE_VGE7823587_2509020102265525170.pdf', 'XML_COMPROBANTE_4_0_ASE931116231_CLIENTE_VGE7823587_2509020102265525170.pdf', NULL, 'application/pdf', 'pdf', 183865, 2, 1, 1, NULL, NULL, NULL, NULL, NULL, '/var/www/html/biblioteca/storage/files/1/279a383e71ed734973a0930b8d5614ff.pdf', 1, '2025-10-07 23:48:34', '2025-10-07 23:48:34', '2025-10-07 23:48:34'),
(5, NULL, 'flexplux_integral.zip', 'flexplux_integral.zip', NULL, 'application/zip', 'zip', 32017731, 3, 1, 1, NULL, NULL, NULL, NULL, NULL, '/var/www/html/biblioteca/storage/files/1/6246ce6f381aa574e4607b5fac054bf5.zip', 1, '2025-10-15 15:32:03', '2025-10-15 15:32:03', '2025-10-15 15:32:03'),
(6, NULL, 'plan_integral.jpg', 'plan_integral.jpg', NULL, 'image/jpeg', 'jpg', 1650796, 3, 1, 1, NULL, NULL, NULL, NULL, NULL, '/var/www/html/biblioteca/storage/files/1/9ddb9ad99726bdac5ee493110678be8d.jpg', 1, '2025-10-15 15:32:04', '2025-10-15 15:32:04', '2025-10-15 15:32:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_cuotas`
--

CREATE TABLE `auditoria_cuotas` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `cuota_anterior` bigint DEFAULT NULL,
  `cuota_nueva` bigint DEFAULT NULL,
  `cambiado_por` int DEFAULT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_cambio` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_roles`
--

CREATE TABLE `auditoria_roles` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `rol_anterior` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_nuevo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cambiado_por` int NOT NULL,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_cambio` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carpetas`
--

CREATE TABLE `carpetas` (
  `id` int NOT NULL,
  `google_folder_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `padre_id` int DEFAULT NULL,
  `nivel` int DEFAULT '0',
  `ruta_completa` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `propietario_id` int NOT NULL,
  `departamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etiqueta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_etiqueta` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icono_personalizado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carpetas`
--

INSERT INTO `carpetas` (`id`, `google_folder_id`, `nombre`, `descripcion`, `padre_id`, `nivel`, `ruta_completa`, `propietario_id`, `departamento`, `etiqueta`, `color_etiqueta`, `icono_personalizado`, `activa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, NULL, 'Programas', NULL, NULL, 0, NULL, 1, NULL, 'Soft', '#e74c3c', 'fas fa-rocket', 1, '2025-10-07 23:11:42', '2025-10-08 18:02:31'),
(2, NULL, 'Pdfss', NULL, NULL, 0, NULL, 1, NULL, 'Documentos', '#f1c40f', NULL, 1, '2025-10-07 23:47:57', '2025-10-07 23:54:54'),
(3, NULL, 'Mkt', NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, 1, '2025-10-15 15:27:49', '2025-10-15 15:27:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compartidos`
--

CREATE TABLE `compartidos` (
  `id` int NOT NULL,
  `archivo_id` int NOT NULL,
  `compartido_con` int NOT NULL,
  `compartido_por` int NOT NULL,
  `permisos` enum('read','write','delete') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'read',
  `fecha_compartido` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compartidos_grupos`
--

CREATE TABLE `compartidos_grupos` (
  `id` int NOT NULL,
  `recurso_tipo` enum('archivo','carpeta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `grupo_id` int NOT NULL,
  `permiso` enum('lector','comentarista','editor','propietario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lector',
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_cambios` tinyint(1) DEFAULT '0',
  `fecha_expiracion` datetime DEFAULT NULL,
  `compartido_por` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id` int NOT NULL,
  `clave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo` enum('string','number','boolean','json') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `fecha_actualizacion`) VALUES
(1, 'app_name', 'Biblioteca Digital Corporativa', 'Nombre de la aplicación', 'string', '2025-08-28 16:24:10'),
(2, 'max_file_size', '104857600', 'Tamaño máximo de archivo en bytes (100MB)', 'number', '2025-08-28 16:24:10'),
(3, 'session_timeout', '7200', 'Tiempo de expiración de sesión en segundos', 'number', '2025-08-28 16:24:10'),
(4, 'require_approval', 'true', 'Requiere aprobación para nuevos usuarios', 'boolean', '2025-08-28 16:24:10'),
(5, 'backup_enabled', 'true', 'Habilitar respaldos automáticos', 'boolean', '2025-08-28 16:24:10'),
(6, 'notification_email', 'admin@biblioteca.com', 'Email para notificaciones del sistema', 'string', '2025-08-28 16:24:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enlaces_compartidos`
--

CREATE TABLE `enlaces_compartidos` (
  `id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('archivo','carpeta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_tipo` enum('archivo','carpeta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `creado_por` int NOT NULL,
  `propietario_id` int NOT NULL,
  `nombre_recurso` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel_acceso` enum('ver','descargar','editar') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ver',
  `fecha_expiracion` datetime DEFAULT NULL,
  `limite_accesos` int DEFAULT NULL,
  `accesos_actuales` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rol_acceso` enum('propietario','editor','comentarista','lector') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'lector',
  `requiere_autenticacion` tinyint(1) DEFAULT '0',
  `dominios_permitidos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_accesos` tinyint(1) DEFAULT '0',
  `contraseña` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `enlaces_compartidos`
--

INSERT INTO `enlaces_compartidos` (`id`, `token`, `tipo`, `recurso_tipo`, `recurso_id`, `creado_por`, `propietario_id`, `nombre_recurso`, `password`, `nivel_acceso`, `fecha_expiracion`, `limite_accesos`, `accesos_actuales`, `activo`, `fecha_creacion`, `rol_acceso`, `requiere_autenticacion`, `dominios_permitidos`, `puede_descargar`, `puede_imprimir`, `puede_copiar`, `notificar_accesos`, `contraseña`) VALUES
(1, '59552bed0dbfa724c8eb2fcc2b6f86354f0496b10a1edc68d8a54414736b6311', 'archivo', 'archivo', 6, 1, 1, 'plan_integral.jpg', NULL, 'editar', '2025-12-31 00:00:00', NULL, 0, 1, '2025-10-15 15:42:38', 'editor', 0, '', 1, 1, 1, 0, NULL),
(2, '15479e293b837f2932f73c8c6a95e25b8ae93b86dc46f86b237576db0eadfbd5', 'archivo', 'archivo', 5, 1, 1, 'flexplux_integral.zip', NULL, 'editar', '2025-12-31 00:00:00', NULL, 0, 1, '2025-10-15 15:43:30', 'editor', 0, '', 1, 1, 1, 0, NULL),
(3, '0eddd739b9fe0decb2c021fa98ed9e2a5412499036c166589b3ff2dae0b9fe02', 'archivo', 'archivo', 6, 1, 1, 'plan_integral.jpg', NULL, 'ver', '2025-10-31 00:00:00', NULL, 0, 1, '2025-10-15 16:55:08', 'lector', 0, '', 1, 1, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` int NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_por` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `nombre`, `descripcion`, `creado_por`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administradores', 'Grupo de administradores del sistema', 1, 1, '2025-09-03 19:59:41', '2025-09-03 19:59:41'),
(2, 'Editores', 'Usuarios con permisos de edición', 1, 1, '2025-09-03 19:59:41', '2025-09-03 19:59:41'),
(3, 'Lectores', 'Usuarios con permisos de solo lectura', 1, 1, '2025-09-03 19:59:41', '2025-09-03 19:59:41'),
(4, 'Agentes', 'Esto es un grupo vip', 1, 1, '2025-09-03 22:45:18', '2025-09-03 22:45:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_miembros`
--

CREATE TABLE `grupo_miembros` (
  `id` int NOT NULL,
  `grupo_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `agregado_por` int NOT NULL,
  `fecha_agregado` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `recurso_tipo` enum('archivo','carpeta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurso_id` int DEFAULT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_leida` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `titulo`, `mensaje`, `recurso_tipo`, `recurso_id`, `leida`, `fecha_creacion`, `fecha_leida`) VALUES
(1, 2, 'comparticion', 'Nuevo recurso compartido', 'Abdiel Carrasco ha compartido un archivo contigo con permisos de Editor\n\nMensaje: Te mando archivo.', 'archivo', 34, 0, '2025-09-03 20:29:20', NULL),
(2, 2, 'comparticion', 'Nuevo recurso compartido', 'Abdiel Carrasco ha compartido un carpeta contigo con permisos de Lector/Visualizador\n\nMensaje: mmm', 'carpeta', 335, 0, '2025-09-03 20:39:37', NULL),
(3, 5, 'comparticion', 'Nuevo recurso compartido', 'Abdiel Carrasco ha compartido un carpeta contigo con permisos de Editor', 'carpeta', 335, 0, '2025-09-03 20:55:43', NULL),
(4, 2, 'comparticion', 'Nuevo recurso compartido', 'Abdiel Carrasco ha compartido un carpeta contigo con permisos de Lector/Visualizador\n\nMensaje: Test de compartición de carpeta', 'carpeta', 337, 0, '2025-09-03 21:11:04', NULL),
(5, 2, 'comparticion', 'Nuevo recurso compartido', 'Abdiel Carrasco ha compartido un carpeta contigo con permisos de Editor', 'carpeta', 335, 0, '2025-09-03 21:26:18', NULL),
(6, 2, 'comparticion', 'Nuevo recurso compartido contigo', 'Te comparto este documento', 'archivo', 37, 0, '2025-09-05 17:22:52', NULL),
(7, 5, 'comparticion', 'Nuevo recurso compartido contigo', 'Te comparto este documento', 'archivo', 37, 0, '2025-09-05 17:22:52', NULL),
(8, 2, 'comparticion', 'Nuevo recurso compartido contigo', 'Se ha compartido un archivo contigo con permisos de editor', 'archivo', 44, 0, '2025-09-05 17:24:15', NULL),
(9, 2, 'comparticion', 'Nuevo recurso compartido contigo', 'Se ha compartido un archivo contigo con permisos de editor', 'archivo', 51, 0, '2025-09-05 20:43:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_archivos`
--

CREATE TABLE `permisos_archivos` (
  `id` int NOT NULL,
  `archivo_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `permiso` enum('propietario','editor','comentarista','lector') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otorgado_por` int NOT NULL,
  `fecha_otorgado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `tipo_comparticion` enum('usuario','grupo','enlace') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'usuario',
  `grupo_id` int DEFAULT NULL,
  `enlace_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_cambios` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_carpetas`
--

CREATE TABLE `permisos_carpetas` (
  `id` int NOT NULL,
  `carpeta_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `permiso` enum('propietario','editor','comentarista','lector') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otorgado_por` int NOT NULL,
  `fecha_otorgado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `tipo_comparticion` enum('usuario','grupo','enlace') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'usuario',
  `grupo_id` int DEFAULT NULL,
  `enlace_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_cambios` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_recursos`
--

CREATE TABLE `permisos_recursos` (
  `id` int NOT NULL,
  `recurso_tipo` enum('archivo','carpeta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `nivel_acceso` enum('viewer','editor','propietario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'viewer',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_id` int NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `datos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ultima_actividad` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 1, 'background_type', 'default', '2025-10-08 18:02:41'),
(3, 1, 'background_color', NULL, '2025-10-08 18:02:41'),
(4, 1, 'background_image', NULL, '2025-10-08 18:02:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('administrador','editor','colaborador','viewer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'viewer',
  `cuota_almacenamiento` bigint DEFAULT '5368709120' COMMENT '5GB en bytes',
  `almacenamiento_usado` bigint DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `cuota_almacenamiento`, `almacenamiento_usado`, `activo`, `fecha_registro`, `ultimo_acceso`) VALUES
(1, 'Abdiel Carrasco', 'admin@biblioteca.com', '123456', 'administrador', 107374182400, 34629659, 1, '2025-10-07 22:23:32', NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_cuotas_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_cuotas_usuarios` (
`id` int
,`nombre` varchar(255)
,`email` varchar(255)
,`cuota_almacenamiento` bigint
,`almacenamiento_usado` bigint
,`cuota_mb` decimal(22,2)
,`usado_mb` decimal(22,2)
,`porcentaje_usado` decimal(25,2)
,`espacio_disponible` bigint
,`disponible_mb` decimal(23,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_cuotas_usuarios`
--
DROP TABLE IF EXISTS `vista_cuotas_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_cuotas_usuarios`  AS SELECT `u`.`id` AS `id`, `u`.`nombre` AS `nombre`, `u`.`email` AS `email`, `u`.`cuota_almacenamiento` AS `cuota_almacenamiento`, `u`.`almacenamiento_usado` AS `almacenamiento_usado`, round(((`u`.`cuota_almacenamiento` / 1024) / 1024),2) AS `cuota_mb`, round(((`u`.`almacenamiento_usado` / 1024) / 1024),2) AS `usado_mb`, round(((`u`.`almacenamiento_usado` / `u`.`cuota_almacenamiento`) * 100),2) AS `porcentaje_usado`, (`u`.`cuota_almacenamiento` - `u`.`almacenamiento_usado`) AS `espacio_disponible`, round((((`u`.`cuota_almacenamiento` - `u`.`almacenamiento_usado`) / 1024) / 1024),2) AS `disponible_mb` FROM `usuarios` AS `u` WHERE (`u`.`activo` = 1) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha_actividad`);

--
-- Indices de la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_propietario` (`propietario_id`),
  ADD KEY `idx_carpeta` (`carpeta_id`);

--
-- Indices de la tabla `auditoria_cuotas`
--
ALTER TABLE `auditoria_cuotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `cambiado_por` (`cambiado_por`);

--
-- Indices de la tabla `auditoria_roles`
--
ALTER TABLE `auditoria_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_cambiado_por` (`cambiado_por`),
  ADD KEY `idx_fecha_cambio` (`fecha_cambio`);

--
-- Indices de la tabla `carpetas`
--
ALTER TABLE `carpetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_propietario` (`propietario_id`),
  ADD KEY `idx_padre` (`padre_id`);

--
-- Indices de la tabla `compartidos`
--
ALTER TABLE `compartidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archivo_id` (`archivo_id`);

--
-- Indices de la tabla `compartidos_grupos`
--
ALTER TABLE `compartidos_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recurso` (`recurso_tipo`,`recurso_id`),
  ADD KEY `idx_grupo_id` (`grupo_id`),
  ADD KEY `idx_compartido_por` (`compartido_por`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`),
  ADD KEY `idx_clave` (`clave`);

--
-- Indices de la tabla `enlaces_compartidos`
--
ALTER TABLE `enlaces_compartidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_propietario` (`propietario_id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_creado_por` (`creado_por`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `grupo_miembros`
--
ALTER TABLE `grupo_miembros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_grupo_usuario` (`grupo_id`,`usuario_id`),
  ADD KEY `idx_grupo_id` (`grupo_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_agregado_por` (`agregado_por`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_leida` (`leida`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`),
  ADD KEY `idx_recurso` (`recurso_tipo`,`recurso_id`);

--
-- Indices de la tabla `permisos_archivos`
--
ALTER TABLE `permisos_archivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permiso` (`archivo_id`,`usuario_id`,`permiso`),
  ADD KEY `otorgado_por` (`otorgado_por`),
  ADD KEY `idx_archivo_id` (`archivo_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_grupo_id` (`grupo_id`),
  ADD KEY `idx_enlace_token` (`enlace_token`),
  ADD KEY `idx_tipo_comparticion` (`tipo_comparticion`);

--
-- Indices de la tabla `permisos_carpetas`
--
ALTER TABLE `permisos_carpetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permiso` (`carpeta_id`,`usuario_id`,`permiso`),
  ADD KEY `otorgado_por` (`otorgado_por`),
  ADD KEY `idx_carpeta_id` (`carpeta_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_grupo_id` (`grupo_id`),
  ADD KEY `idx_enlace_token` (`enlace_token`),
  ADD KEY `idx_tipo_comparticion` (`tipo_comparticion`);

--
-- Indices de la tabla `permisos_recursos`
--
ALTER TABLE `permisos_recursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_recurso_usuario` (`recurso_tipo`,`recurso_id`,`usuario_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_ultima_actividad` (`ultima_actividad`);

--
-- Indices de la tabla `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_setting` (`user_id`,`setting_key`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad`
--
ALTER TABLE `actividad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `archivos`
--
ALTER TABLE `archivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `auditoria_cuotas`
--
ALTER TABLE `auditoria_cuotas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria_roles`
--
ALTER TABLE `auditoria_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carpetas`
--
ALTER TABLE `carpetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compartidos`
--
ALTER TABLE `compartidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compartidos_grupos`
--
ALTER TABLE `compartidos_grupos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `enlaces_compartidos`
--
ALTER TABLE `enlaces_compartidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `grupo_miembros`
--
ALTER TABLE `grupo_miembros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `permisos_archivos`
--
ALTER TABLE `permisos_archivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos_carpetas`
--
ALTER TABLE `permisos_carpetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_recursos`
--
ALTER TABLE `permisos_recursos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria_cuotas`
--
ALTER TABLE `auditoria_cuotas`
  ADD CONSTRAINT `auditoria_cuotas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `auditoria_cuotas_ibfk_2` FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `auditoria_roles`
--
ALTER TABLE `auditoria_roles`
  ADD CONSTRAINT `auditoria_roles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `auditoria_roles_ibfk_2` FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compartidos`
--
ALTER TABLE `compartidos`
  ADD CONSTRAINT `compartidos_ibfk_1` FOREIGN KEY (`archivo_id`) REFERENCES `archivos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compartidos_grupos`
--
ALTER TABLE `compartidos_grupos`
  ADD CONSTRAINT `compartidos_grupos_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compartidos_grupos_ibfk_2` FOREIGN KEY (`compartido_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grupo_miembros`
--
ALTER TABLE `grupo_miembros`
  ADD CONSTRAINT `grupo_miembros_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grupo_miembros_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grupo_miembros_ibfk_3` FOREIGN KEY (`agregado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_archivos`
--
ALTER TABLE `permisos_archivos`
  ADD CONSTRAINT `permisos_archivos_ibfk_1` FOREIGN KEY (`archivo_id`) REFERENCES `archivos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_archivos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_archivos_ibfk_3` FOREIGN KEY (`otorgado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_archivos_ibfk_4` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`apexlabs`@`localhost` EVENT `evt_limpiar_enlaces` ON SCHEDULE EVERY 1 DAY STARTS '2025-10-07 22:17:45' ON COMPLETION NOT PRESERVE ENABLE DO CALL sp_limpiar_enlaces_expirados()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
