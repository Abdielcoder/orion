-- ============================================
-- SCRIPT DE INSTALACIÓN - BIBLIOTECA DIGITAL
-- Sistema de Gestión de Archivos en la Nube
-- Versión: 1.0
-- ============================================

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- CREAR BASE DE DATOS
-- ============================================

CREATE DATABASE IF NOT EXISTS `biblioteca_digital` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `biblioteca_digital`;

-- ============================================
-- TABLA: usuarios
-- Almacena información de usuarios del sistema
-- ============================================

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('administrador','editor','colaborador','viewer') COLLATE utf8mb4_unicode_ci DEFAULT 'viewer',
  `cuota_almacenamiento` bigint DEFAULT '5368709120' COMMENT 'Cuota en bytes (default: 5GB)',
  `almacenamiento_usado` bigint DEFAULT '0' COMMENT 'Espacio usado en bytes',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: carpetas
-- Estructura jerárquica de carpetas
-- ============================================

CREATE TABLE IF NOT EXISTS `carpetas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `google_folder_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `padre_id` int DEFAULT NULL COMMENT 'NULL = carpeta raíz',
  `nivel` int DEFAULT '0',
  `ruta_completa` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `propietario_id` int NOT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etiqueta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_etiqueta` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Color hex #RRGGBB',
  `icono_personalizado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emoji o clase FA',
  `activa` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_propietario` (`propietario_id`),
  KEY `idx_padre` (`padre_id`),
  KEY `idx_activa` (`activa`),
  CONSTRAINT `fk_carpeta_propietario` FOREIGN KEY (`propietario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_carpeta_padre` FOREIGN KEY (`padre_id`) REFERENCES `carpetas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: archivos
-- Almacena información de archivos
-- ============================================

CREATE TABLE IF NOT EXISTS `archivos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `google_file_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo_mime` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamaño` bigint DEFAULT NULL COMMENT 'Tamaño en bytes',
  `carpeta_id` int DEFAULT NULL COMMENT 'NULL = raíz del usuario',
  `propietario_id` int NOT NULL,
  `version` int DEFAULT '1',
  `url_descarga` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_vista` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `ruta_local` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_propietario` (`propietario_id`),
  KEY `idx_carpeta` (`carpeta_id`),
  KEY `idx_activo` (`activo`),
  KEY `idx_extension` (`extension`),
  CONSTRAINT `fk_archivo_propietario` FOREIGN KEY (`propietario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_archivo_carpeta` FOREIGN KEY (`carpeta_id`) REFERENCES `carpetas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: permisos_recursos
-- Control de acceso compartido a archivos/carpetas
-- ============================================

CREATE TABLE IF NOT EXISTS `permisos_recursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recurso_tipo` enum('archivo','carpeta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `nivel_acceso` enum('viewer','editor','propietario') COLLATE utf8mb4_unicode_ci DEFAULT 'viewer',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_recurso_usuario` (`recurso_tipo`,`recurso_id`,`usuario_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_recurso` (`recurso_tipo`,`recurso_id`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `fk_permiso_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: enlaces_compartidos
-- Links públicos para compartir archivos/carpetas
-- ============================================

CREATE TABLE IF NOT EXISTS `enlaces_compartidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_tipo` enum('archivo','carpeta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `propietario_id` int NOT NULL,
  `nombre_recurso` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel_acceso` enum('ver','descargar','editar') COLLATE utf8mb4_unicode_ci DEFAULT 'ver',
  `fecha_expiracion` datetime DEFAULT NULL,
  `limite_accesos` int DEFAULT NULL,
  `accesos_actuales` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_propietario` (`propietario_id`),
  KEY `idx_recurso` (`recurso_tipo`,`recurso_id`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `fk_enlace_propietario` FOREIGN KEY (`propietario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: actividad
-- Registro de auditoría del sistema
-- ============================================

CREATE TABLE IF NOT EXISTS `actividad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `accion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_recurso` enum('archivo','carpeta','usuario','sistema') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int DEFAULT NULL,
  `detalles` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `fecha_actividad` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha` (`fecha_actividad`),
  KEY `idx_tipo_recurso` (`tipo_recurso`,`recurso_id`),
  CONSTRAINT `fk_actividad_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: user_settings
-- Configuraciones personalizadas por usuario
-- ============================================

CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_setting` (`user_id`,`setting_key`),
  CONSTRAINT `fk_user_settings` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTAR USUARIO ADMINISTRADOR POR DEFECTO
-- Email: admin@biblioteca.com
-- Password: admin123 (CAMBIAR INMEDIATAMENTE)
-- ============================================

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `cuota_almacenamiento`, `almacenamiento_usado`, `activo`) VALUES
(1, 'Administrador', 'admin@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 107374182400, 0, 1);
-- Password hasheado: admin123

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: archivos con información del propietario
CREATE OR REPLACE VIEW `v_archivos_completos` AS
SELECT 
    a.id,
    a.nombre,
    a.nombre_original,
    a.tipo_mime,
    a.extension,
    a.tamaño,
    a.carpeta_id,
    c.nombre AS carpeta_nombre,
    a.propietario_id,
    u.nombre AS propietario_nombre,
    u.email AS propietario_email,
    a.fecha_creacion,
    a.fecha_modificacion
FROM archivos a
LEFT JOIN carpetas c ON a.carpeta_id = c.id
LEFT JOIN usuarios u ON a.propietario_id = u.id
WHERE a.activo = 1;

-- Vista: uso de almacenamiento por usuario
CREATE OR REPLACE VIEW `v_almacenamiento_usuarios` AS
SELECT 
    u.id,
    u.nombre,
    u.email,
    u.cuota_almacenamiento,
    u.almacenamiento_usado,
    ROUND((u.almacenamiento_usado / u.cuota_almacenamiento) * 100, 2) AS porcentaje_usado,
    (u.cuota_almacenamiento - u.almacenamiento_usado) AS espacio_disponible,
    COUNT(DISTINCT a.id) AS total_archivos,
    COUNT(DISTINCT c.id) AS total_carpetas
FROM usuarios u
LEFT JOIN archivos a ON u.id = a.propietario_id AND a.activo = 1
LEFT JOIN carpetas c ON u.id = c.propietario_id AND c.activa = 1
GROUP BY u.id;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

DELIMITER $$

-- Procedimiento: Actualizar uso de almacenamiento de un usuario
CREATE PROCEDURE `sp_actualizar_almacenamiento_usuario`(IN p_usuario_id INT)
BEGIN
    UPDATE usuarios 
    SET almacenamiento_usado = (
        SELECT COALESCE(SUM(tamaño), 0) 
        FROM archivos 
        WHERE propietario_id = p_usuario_id 
        AND activo = 1
    )
    WHERE id = p_usuario_id;
END$$

-- Procedimiento: Eliminar archivo y actualizar almacenamiento
CREATE PROCEDURE `sp_eliminar_archivo`(IN p_archivo_id INT)
BEGIN
    DECLARE v_propietario_id INT;
    DECLARE v_tamaño BIGINT;
    
    -- Obtener información del archivo
    SELECT propietario_id, tamaño INTO v_propietario_id, v_tamaño
    FROM archivos WHERE id = p_archivo_id;
    
    -- Marcar archivo como inactivo
    UPDATE archivos SET activo = 0 WHERE id = p_archivo_id;
    
    -- Actualizar almacenamiento usado
    IF v_propietario_id IS NOT NULL THEN
        CALL sp_actualizar_almacenamiento_usuario(v_propietario_id);
    END IF;
END$$

-- Procedimiento: Limpiar enlaces expirados
CREATE PROCEDURE `sp_limpiar_enlaces_expirados`()
BEGIN
    UPDATE enlaces_compartidos 
    SET activo = 0 
    WHERE fecha_expiracion IS NOT NULL 
    AND fecha_expiracion < NOW()
    AND activo = 1;
    
    SELECT ROW_COUNT() AS enlaces_desactivados;
END$$

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER $$

-- Trigger: Actualizar almacenamiento al insertar archivo
CREATE TRIGGER `trg_archivo_insert` 
AFTER INSERT ON `archivos`
FOR EACH ROW
BEGIN
    IF NEW.activo = 1 THEN
        UPDATE usuarios 
        SET almacenamiento_usado = almacenamiento_usado + NEW.tamaño
        WHERE id = NEW.propietario_id;
    END IF;
END$$

-- Trigger: Actualizar almacenamiento al eliminar archivo
CREATE TRIGGER `trg_archivo_update` 
AFTER UPDATE ON `archivos`
FOR EACH ROW
BEGIN
    IF OLD.activo = 1 AND NEW.activo = 0 THEN
        UPDATE usuarios 
        SET almacenamiento_usado = almacenamiento_usado - OLD.tamaño
        WHERE id = OLD.propietario_id;
    END IF;
END$$

DELIMITER ;

-- ============================================
-- EVENTOS PROGRAMADOS (opcional)
-- ============================================

SET GLOBAL event_scheduler = ON;

-- Evento: Limpiar enlaces expirados diariamente
CREATE EVENT IF NOT EXISTS `evt_limpiar_enlaces`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    CALL sp_limpiar_enlaces_expirados();

-- ============================================
-- PERMISOS RECOMENDADOS
-- ============================================

-- Crear usuario de aplicación (reemplazar 'tu_password' y 'tu_host')
-- CREATE USER 'biblioteca_app'@'localhost' IDENTIFIED BY 'tu_password_seguro';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON biblioteca_digital.* TO 'biblioteca_app'@'localhost';
-- GRANT EXECUTE ON PROCEDURE biblioteca_digital.sp_actualizar_almacenamiento_usuario TO 'biblioteca_app'@'localhost';
-- GRANT EXECUTE ON PROCEDURE biblioteca_digital.sp_eliminar_archivo TO 'biblioteca_app'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

CREATE INDEX idx_archivos_fecha ON archivos(fecha_creacion DESC);
CREATE INDEX idx_carpetas_fecha ON carpetas(fecha_creacion DESC);
CREATE INDEX idx_actividad_fecha_usuario ON actividad(usuario_id, fecha_actividad DESC);
CREATE INDEX idx_enlaces_token_activo ON enlaces_compartidos(token, activo);

-- ============================================
-- RESTAURAR CONFIGURACIÓN
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICACIÓN DE INSTALACIÓN
-- ============================================

SELECT 'Base de datos instalada correctamente' AS status;
SELECT COUNT(*) AS total_tablas FROM information_schema.tables WHERE table_schema = 'biblioteca_digital';
SELECT * FROM usuarios WHERE id = 1;

-- ============================================
-- NOTAS IMPORTANTES
-- ============================================

/*
1. CAMBIAR PASSWORD DEL ADMINISTRADOR:
   - Email: admin@biblioteca.com
   - Password por defecto: admin123
   - CAMBIAR INMEDIATAMENTE después de la instalación

2. CONFIGURAR EN config/database.php:
   - Host del servidor
   - Nombre de la base de datos
   - Usuario y contraseña

3. CREAR DIRECTORIO DE ALMACENAMIENTO:
   - Crear: storage/files/
   - Permisos: 755 o 775
   - Owner: usuario del servidor web

4. CONFIGURAR PHP.INI:
   - upload_max_filesize = 256M
   - post_max_size = 256M
   - max_execution_time = 300
   - memory_limit = 512M

5. SEGURIDAD:
   - Cambiar credenciales por defecto
   - Configurar SSL/HTTPS en producción
   - Revisar permisos de archivos y directorios
   - Mantener actualizado PHP y MySQL

6. BACKUP:
   - Configurar backups automáticos diarios
   - Backup de base de datos y carpeta storage/

7. MONITOREO:
   - Revisar logs de errores regularmente
   - Monitorear uso de almacenamiento
   - Verificar enlaces expirados

Para más información, consultar la documentación en:
MANUAL_SISTEMA_BIBLIOTECA.md
*/
