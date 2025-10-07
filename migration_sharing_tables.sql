-- Migración simplificada para tablas de compartición
-- Fecha: 2025-01-03

-- 1. CREAR TABLA DE GRUPOS
CREATE TABLE IF NOT EXISTS `grupos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `creado_por` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_creado_por` (`creado_por`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CREAR TABLA DE MIEMBROS DE GRUPOS
CREATE TABLE IF NOT EXISTS `grupo_miembros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grupo_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `agregado_por` int NOT NULL,
  `fecha_agregado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_grupo_usuario` (`grupo_id`, `usuario_id`),
  KEY `idx_grupo_id` (`grupo_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_agregado_por` (`agregado_por`),
  CONSTRAINT `grupo_miembros_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grupo_miembros_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grupo_miembros_ibfk_3` FOREIGN KEY (`agregado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. CREAR TABLA DE PERMISOS DE RECURSOS (para archivos y carpetas)
CREATE TABLE IF NOT EXISTS `permisos_recursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recurso_tipo` ENUM('archivo','carpeta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `tipo_comparticion` ENUM('usuario','grupo','enlace') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usuario',
  `usuario_id` int DEFAULT NULL,
  `grupo_id` int DEFAULT NULL,
  `permiso` ENUM('lector','comentarista','editor','propietario') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lector',
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_cambios` tinyint(1) DEFAULT '0',
  `fecha_expiracion` datetime DEFAULT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `otorgado_por` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recurso` (`recurso_tipo`, `recurso_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_grupo_id` (`grupo_id`),
  KEY `idx_permiso` (`permiso`),
  KEY `idx_otorgado_por` (`otorgado_por`),
  KEY `idx_activo` (`activo`),
  KEY `idx_fecha_expiracion` (`fecha_expiracion`),
  CONSTRAINT `permisos_recursos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permisos_recursos_ibfk_2` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permisos_recursos_ibfk_3` FOREIGN KEY (`otorgado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. CREAR TABLA DE COMPARTIDOS CON GRUPOS
CREATE TABLE IF NOT EXISTS `compartidos_grupos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recurso_tipo` ENUM('archivo','carpeta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `grupo_id` int NOT NULL,
  `permiso` ENUM('lector','comentarista','editor','propietario') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lector',
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_cambios` tinyint(1) DEFAULT '0',
  `fecha_expiracion` datetime DEFAULT NULL,
  `compartido_por` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recurso` (`recurso_tipo`, `recurso_id`),
  KEY `idx_grupo_id` (`grupo_id`),
  KEY `idx_compartido_por` (`compartido_por`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `compartidos_grupos_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compartidos_grupos_ibfk_2` FOREIGN KEY (`compartido_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. ACTUALIZAR TABLA ENLACES_COMPARTIDOS PARA SOPORTAR NUEVAS FUNCIONES
-- Verificar si las columnas ya existen antes de agregarlas
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'recurso_tipo');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `recurso_tipo` ENUM(\'archivo\',\'carpeta\') COLLATE utf8mb4_unicode_ci DEFAULT \'archivo\'', 'SELECT "Column recurso_tipo already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'permiso');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `permiso` ENUM(\'lector\',\'comentarista\',\'editor\') COLLATE utf8mb4_unicode_ci DEFAULT \'lector\'', 'SELECT "Column permiso already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'puede_descargar');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `puede_descargar` tinyint(1) DEFAULT \'1\'', 'SELECT "Column puede_descargar already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'puede_imprimir');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `puede_imprimir` tinyint(1) DEFAULT \'1\'', 'SELECT "Column puede_imprimir already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'puede_copiar');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `puede_copiar` tinyint(1) DEFAULT \'1\'', 'SELECT "Column puede_copiar already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'requiere_autenticacion');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `requiere_autenticacion` tinyint(1) DEFAULT \'0\'', 'SELECT "Column requiere_autenticacion already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'contraseña');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `contraseña` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL', 'SELECT "Column contraseña already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'dominios_permitidos');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `dominios_permitidos` text COLLATE utf8mb4_unicode_ci', 'SELECT "Column dominios_permitidos already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'biblioteca_digital' AND table_name = 'enlaces_compartidos' AND column_name = 'notificar_accesos');
SET @query := IF(@exist = 0, 'ALTER TABLE `enlaces_compartidos` ADD COLUMN `notificar_accesos` tinyint(1) DEFAULT \'0\'', 'SELECT "Column notificar_accesos already exists"');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6. INSERTAR ALGUNOS GRUPOS DE EJEMPLO
INSERT IGNORE INTO `grupos` (`nombre`, `descripcion`, `creado_por`) VALUES
('Administradores', 'Grupo de administradores del sistema', 1),
('Editores', 'Usuarios con permisos de edición', 1),
('Lectores', 'Usuarios con permisos de solo lectura', 1);
