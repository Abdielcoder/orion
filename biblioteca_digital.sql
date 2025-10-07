-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:8889
-- Tiempo de generación: 02-09-2025 a las 18:40:59
-- Versión del servidor: 8.0.40
-- Versión de PHP: 8.3.14

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad`
--

CREATE TABLE `actividad` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `accion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_recurso` enum('archivo','carpeta','usuario','sistema') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int DEFAULT NULL,
  `detalles` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `fecha_actividad` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos`
--

CREATE TABLE `archivos` (
  `id` int NOT NULL,
  `google_file_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo_mime` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamaño` bigint DEFAULT NULL,
  `carpeta_id` int NOT NULL,
  `propietario_id` int NOT NULL,
  `version` int DEFAULT '1',
  `url_descarga` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_vista` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ruta_local` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fecha_modificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carpetas`
--

CREATE TABLE `carpetas` (
  `id` int NOT NULL,
  `google_folder_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `padre_id` int DEFAULT NULL,
  `nivel` int DEFAULT '0',
  `ruta_completa` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `propietario_id` int NOT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carpetas`
--

INSERT INTO `carpetas` (`id`, `google_folder_id`, `nombre`, `descripcion`, `padre_id`, `nivel`, `ruta_completa`, `propietario_id`, `departamento`, `activa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(301, NULL, 'Administración', 'Carpeta principal de Administración', NULL, 0, NULL, 1, 'Administración', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(302, NULL, 'Recursos Humanos', 'Subcarpeta de Administración', 301, 0, NULL, 1, 'Administración', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(303, NULL, 'Finanzas', 'Subcarpeta de Administración', 301, 0, NULL, 1, 'Administración', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(304, NULL, 'Contabilidad', 'Subcarpeta de Administración', 301, 0, NULL, 1, 'Administración', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(305, NULL, 'Legal', 'Subcarpeta de Administración', 301, 0, NULL, 1, 'Administración', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(306, NULL, 'Operaciones', 'Carpeta principal de Operaciones', NULL, 0, NULL, 1, 'Operaciones', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(307, NULL, 'Proyectos', 'Subcarpeta de Operaciones', 306, 0, NULL, 1, 'Operaciones', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(308, NULL, 'Procesos', 'Subcarpeta de Operaciones', 306, 0, NULL, 1, 'Operaciones', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(309, NULL, 'Documentación Técnica', 'Subcarpeta de Operaciones', 306, 0, NULL, 1, 'Operaciones', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(310, NULL, 'Manuales', 'Subcarpeta de Operaciones', 306, 0, NULL, 1, 'Operaciones', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(311, NULL, 'Marketing', 'Carpeta principal de Marketing', NULL, 0, NULL, 1, 'Marketing', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(312, NULL, 'Campañas', 'Subcarpeta de Marketing', 311, 0, NULL, 1, 'Marketing', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(313, NULL, 'Materiales Promocionales', 'Subcarpeta de Marketing', 311, 0, NULL, 1, 'Marketing', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(314, NULL, 'Análisis de Mercado', 'Subcarpeta de Marketing', 311, 0, NULL, 1, 'Marketing', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(315, NULL, 'Redes Sociales', 'Subcarpeta de Marketing', 311, 0, NULL, 1, 'Marketing', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(316, NULL, 'Ventas', 'Carpeta principal de Ventas', NULL, 0, NULL, 1, 'Ventas', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(317, NULL, 'Propuestas', 'Subcarpeta de Ventas', 316, 0, NULL, 1, 'Ventas', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(318, NULL, 'Contratos', 'Subcarpeta de Ventas', 316, 0, NULL, 1, 'Ventas', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(319, NULL, 'Presentaciones', 'Subcarpeta de Ventas', 316, 0, NULL, 1, 'Ventas', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57'),
(320, NULL, 'Reportes', 'Subcarpeta de Ventas', 316, 0, NULL, 1, 'Ventas', 1, '2025-08-28 17:49:57', '2025-08-28 17:49:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compartidos`
--

CREATE TABLE `compartidos` (
  `id` int NOT NULL,
  `archivo_id` int NOT NULL,
  `compartido_con` int NOT NULL,
  `compartido_por` int NOT NULL,
  `permisos` enum('read','write','delete') COLLATE utf8mb4_unicode_ci DEFAULT 'read',
  `fecha_compartido` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id` int NOT NULL,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('string','number','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
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
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('archivo','carpeta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `creado_por` int NOT NULL,
  `permisos` json DEFAULT NULL,
  `requiere_password` tinyint(1) DEFAULT '0',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `limite_descargas` int DEFAULT '0',
  `contador_accesos` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_archivos`
--

CREATE TABLE `permisos_archivos` (
  `id` int NOT NULL,
  `archivo_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `permiso` enum('read','write','delete','share','comment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `otorgado_por` int NOT NULL,
  `fecha_otorgado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_carpetas`
--

CREATE TABLE `permisos_carpetas` (
  `id` int NOT NULL,
  `carpeta_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `permiso` enum('read','write','delete','share','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `otorgado_por` int NOT NULL,
  `fecha_otorgado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_id` int NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `datos` text COLLATE utf8mb4_unicode_ci,
  `ultima_actividad` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `google_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol` enum('admin','manager','editor','viewer') COLLATE utf8mb4_unicode_ci DEFAULT 'viewer',
  `departamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `intentos_login` int DEFAULT '0',
  `bloqueado_hasta` timestamp NULL DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `google_id`, `email`, `nombre`, `apellidos`, `avatar`, `password`, `rol`, `departamento`, `activo`, `ultimo_acceso`, `intentos_login`, `bloqueado_hasta`, `fecha_creacion`, `fecha_actualizacion`, `fecha_ultimo_acceso`) VALUES
(1, 'admin', 'admin@biblioteca.com', 'Administrador del Sistema', NULL, NULL, '$argon2id$v=19$m=65536,t=4,p=3$eUxkbGFlazVTMmdob3cuaA$0JQPfdDTGQC3SUUXkACaQLucHQlekg+XMnpJwzPn/P8', 'admin', NULL, 1, NULL, 0, NULL, '2025-08-28 16:24:10', '2025-08-28 16:40:54', NULL),
(2, NULL, 'usuario@biblioteca.com', 'Usuario de Prueba', NULL, NULL, '$argon2id$v=19$m=65536,t=4,p=3$eUxkbGFlazVTMmdob3cuaA$0JQPfdDTGQC3SUUXkACaQLucHQlekg+XMnpJwzPn/P8', 'editor', NULL, 1, NULL, 0, NULL, '2025-08-28 16:35:17', '2025-08-28 16:40:54', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_accion` (`accion`),
  ADD KEY `idx_tipo_recurso` (`tipo_recurso`),
  ADD KEY `idx_fecha_actividad` (`fecha_actividad`);

--
-- Indices de la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_file_id` (`google_file_id`),
  ADD KEY `idx_google_file_id` (`google_file_id`),
  ADD KEY `idx_carpeta_id` (`carpeta_id`),
  ADD KEY `idx_propietario_id` (`propietario_id`),
  ADD KEY `idx_tipo_mime` (`tipo_mime`),
  ADD KEY `idx_extension` (`extension`);
ALTER TABLE `archivos` ADD FULLTEXT KEY `idx_nombre_descripcion` (`nombre`,`descripcion`);

--
-- Indices de la tabla `carpetas`
--
ALTER TABLE `carpetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_folder_id` (`google_folder_id`),
  ADD KEY `idx_google_folder_id` (`google_folder_id`),
  ADD KEY `idx_padre_id` (`padre_id`),
  ADD KEY `idx_propietario_id` (`propietario_id`),
  ADD KEY `idx_departamento` (`departamento`);

--
-- Indices de la tabla `compartidos`
--
ALTER TABLE `compartidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archivo_id` (`archivo_id`);

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
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_tipo_recurso` (`tipo`,`recurso_id`),
  ADD KEY `idx_creado_por` (`creado_por`);

--
-- Indices de la tabla `permisos_archivos`
--
ALTER TABLE `permisos_archivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permiso` (`archivo_id`,`usuario_id`,`permiso`),
  ADD KEY `otorgado_por` (`otorgado_por`),
  ADD KEY `idx_archivo_id` (`archivo_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `permisos_carpetas`
--
ALTER TABLE `permisos_carpetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permiso` (`carpeta_id`,`usuario_id`,`permiso`),
  ADD KEY `otorgado_por` (`otorgado_por`),
  ADD KEY `idx_carpeta_id` (`carpeta_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_ultima_actividad` (`ultima_actividad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_google_id` (`google_id`),
  ADD KEY `idx_rol` (`rol`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carpetas`
--
ALTER TABLE `carpetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT de la tabla `compartidos`
--
ALTER TABLE `compartidos`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_archivos`
--
ALTER TABLE `permisos_archivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_carpetas`
--
ALTER TABLE `permisos_carpetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD CONSTRAINT `actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD CONSTRAINT `archivos_ibfk_1` FOREIGN KEY (`carpeta_id`) REFERENCES `carpetas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `archivos_ibfk_2` FOREIGN KEY (`propietario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `carpetas`
--
ALTER TABLE `carpetas`
  ADD CONSTRAINT `carpetas_ibfk_1` FOREIGN KEY (`padre_id`) REFERENCES `carpetas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carpetas_ibfk_2` FOREIGN KEY (`propietario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compartidos`
--
ALTER TABLE `compartidos`
  ADD CONSTRAINT `compartidos_ibfk_1` FOREIGN KEY (`archivo_id`) REFERENCES `archivos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `enlaces_compartidos`
--
ALTER TABLE `enlaces_compartidos`
  ADD CONSTRAINT `enlaces_compartidos_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_archivos`
--
ALTER TABLE `permisos_archivos`
  ADD CONSTRAINT `permisos_archivos_ibfk_1` FOREIGN KEY (`archivo_id`) REFERENCES `archivos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_archivos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_archivos_ibfk_3` FOREIGN KEY (`otorgado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_carpetas`
--
ALTER TABLE `permisos_carpetas`
  ADD CONSTRAINT `permisos_carpetas_ibfk_1` FOREIGN KEY (`carpeta_id`) REFERENCES `carpetas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_carpetas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_carpetas_ibfk_3` FOREIGN KEY (`otorgado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
