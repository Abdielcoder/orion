-- Migración para el nuevo sistema de roles
-- Fecha: 2025-01-03

-- 1. ACTUALIZAR ROLES DE SISTEMA
-- Cambiar los roles de usuarios a solo: administrador y usuario_editor
ALTER TABLE `usuarios` 
MODIFY COLUMN `rol` ENUM('administrador','usuario_editor') 
COLLATE utf8mb4_unicode_ci DEFAULT 'usuario_editor';

-- Actualizar usuarios existentes
UPDATE `usuarios` SET `rol` = 'administrador' WHERE `rol` IN ('admin', 'owner');
UPDATE `usuarios` SET `rol` = 'usuario_editor' WHERE `rol` IN ('editor', 'commenter', 'viewer', 'manager');

-- 2. CREAR TABLA DE GRUPOS
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

-- 3. CREAR TABLA DE MIEMBROS DE GRUPOS
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

-- 4. ACTUALIZAR TABLA DE PERMISOS DE ARCHIVOS
-- Agregar nuevos campos para el sistema de compartición avanzado
ALTER TABLE `permisos_archivos` 
ADD COLUMN `tipo_comparticion` ENUM('usuario','grupo','enlace') COLLATE utf8mb4_unicode_ci DEFAULT 'usuario',
ADD COLUMN `grupo_id` int DEFAULT NULL,
ADD COLUMN `enlace_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
ADD COLUMN `puede_descargar` tinyint(1) DEFAULT '1',
ADD COLUMN `puede_imprimir` tinyint(1) DEFAULT '1',
ADD COLUMN `puede_copiar` tinyint(1) DEFAULT '1',
ADD COLUMN `notificar_cambios` tinyint(1) DEFAULT '0';

-- Actualizar permisos existentes para usar los nuevos roles de compartición
UPDATE `permisos_archivos` SET `permiso` = 'propietario' WHERE `permiso` = 'delete';
UPDATE `permisos_archivos` SET `permiso` = 'editor' WHERE `permiso` = 'write';
UPDATE `permisos_archivos` SET `permiso` = 'comentarista' WHERE `permiso` = 'comment';
UPDATE `permisos_archivos` SET `permiso` = 'lector' WHERE `permiso` = 'read';

-- Actualizar la columna permiso con los nuevos valores
ALTER TABLE `permisos_archivos` 
MODIFY COLUMN `permiso` ENUM('propietario','editor','comentarista','lector') COLLATE utf8mb4_unicode_ci NOT NULL;

-- Agregar índices para los nuevos campos
ALTER TABLE `permisos_archivos`
ADD KEY `idx_grupo_id` (`grupo_id`),
ADD KEY `idx_enlace_token` (`enlace_token`),
ADD KEY `idx_tipo_comparticion` (`tipo_comparticion`);

-- Agregar constraint para grupo_id
ALTER TABLE `permisos_archivos`
ADD CONSTRAINT `permisos_archivos_ibfk_4` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE;

-- 5. ACTUALIZAR TABLA DE PERMISOS DE CARPETAS
-- Agregar los mismos campos que en archivos
ALTER TABLE `permisos_carpetas` 
ADD COLUMN `tipo_comparticion` ENUM('usuario','grupo','enlace') COLLATE utf8mb4_unicode_ci DEFAULT 'usuario',
ADD COLUMN `grupo_id` int DEFAULT NULL,
ADD COLUMN `enlace_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
ADD COLUMN `puede_descargar` tinyint(1) DEFAULT '1',
ADD COLUMN `puede_imprimir` tinyint(1) DEFAULT '1',
ADD COLUMN `puede_copiar` tinyint(1) DEFAULT '1',
ADD COLUMN `notificar_cambios` tinyint(1) DEFAULT '0';

-- Actualizar permisos existentes
UPDATE `permisos_carpetas` SET `permiso` = 'propietario' WHERE `permiso` IN ('delete', 'admin');
UPDATE `permisos_carpetas` SET `permiso` = 'editor' WHERE `permiso` = 'write';
UPDATE `permisos_carpetas` SET `permiso` = 'lector' WHERE `permiso` = 'read';

-- Actualizar la columna permiso
ALTER TABLE `permisos_carpetas` 
MODIFY COLUMN `permiso` ENUM('propietario','editor','comentarista','lector') COLLATE utf8mb4_unicode_ci NOT NULL;

-- Agregar índices
ALTER TABLE `permisos_carpetas`
ADD KEY `idx_grupo_id` (`grupo_id`),
ADD KEY `idx_enlace_token` (`enlace_token`),
ADD KEY `idx_tipo_comparticion` (`tipo_comparticion`);

-- Agregar constraint
ALTER TABLE `permisos_carpetas`
ADD CONSTRAINT `permisos_carpetas_ibfk_4` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE;

-- 6. ACTUALIZAR TABLA DE ENLACES COMPARTIDOS
-- Agregar campos para restricciones y configuración avanzada
ALTER TABLE `enlaces_compartidos`
ADD COLUMN `rol_acceso` ENUM('propietario','editor','comentarista','lector') COLLATE utf8mb4_unicode_ci DEFAULT 'lector',
ADD COLUMN `puede_descargar` tinyint(1) DEFAULT '1',
ADD COLUMN `puede_imprimir` tinyint(1) DEFAULT '1',
ADD COLUMN `puede_copiar` tinyint(1) DEFAULT '1',
ADD COLUMN `requiere_autenticacion` tinyint(1) DEFAULT '0',
ADD COLUMN `dominios_permitidos` text COLLATE utf8mb4_unicode_ci,
ADD COLUMN `notificar_accesos` tinyint(1) DEFAULT '0';

-- 7. ACTUALIZAR TABLA ROLES_SISTEMA
-- Limpiar y actualizar con los nuevos roles
DELETE FROM `roles_sistema`;

INSERT INTO `roles_sistema` (`codigo`, `nombre`, `descripcion`, `permisos`) VALUES
-- ROLES DE SISTEMA
('administrador', 'Administrador', 'Control total del sistema. Puede gestionar usuarios, configuraciones, ver todos los archivos y carpetas de todos los usuarios.', 
 '{"sistema": ["crear", "leer", "actualizar", "eliminar", "administrar"], "usuarios": ["crear", "leer", "actualizar", "eliminar"], "archivos": ["ver_todos", "crear", "leer", "actualizar", "eliminar", "compartir", "transferir"], "carpetas": ["ver_todas", "crear", "leer", "actualizar", "eliminar", "compartir", "administrar"]}'),

('usuario_editor', 'Usuario Editor', 'Control de su propio drive. Puede crear, editar, eliminar y compartir sus propios documentos y carpetas. No puede ver archivos de otros usuarios a menos que se los compartan.', 
 '{"archivos": ["crear", "leer_propios", "actualizar_propios", "eliminar_propios", "compartir_propios"], "carpetas": ["crear", "leer_propias", "actualizar_propias", "eliminar_propias", "compartir_propias"]}'),

-- ROLES DE COMPARTICIÓN
('propietario', 'Propietario', 'Control total del archivo/carpeta compartido. Puede modificar, eliminar, transferir propiedad y gestionar permisos de otros usuarios.', 
 '{"recurso": ["leer", "actualizar", "eliminar", "comentar", "compartir", "transferir_propiedad", "gestionar_permisos", "descargar", "imprimir", "copiar"]}'),

('editor', 'Editor', 'Puede ver y modificar el contenido. Puede descargar, imprimir y copiar. No puede eliminar ni gestionar permisos.', 
 '{"recurso": ["leer", "actualizar", "comentar", "descargar", "imprimir", "copiar"]}'),

('comentarista', 'Comentarista', 'Puede ver el contenido y agregar comentarios. Puede descargar por defecto, pero se puede restringir.', 
 '{"recurso": ["leer", "comentar", "descargar"]}'),

('lector', 'Lector/Visualizador', 'Solo puede ver el contenido. Los permisos de descarga, impresión y copia pueden ser restringidos.', 
 '{"recurso": ["leer"]}');

-- 8. CREAR TABLA DE NOTIFICACIONES
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo` ENUM('comparticion','acceso','modificacion','comentario','sistema') COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `recurso_tipo` ENUM('archivo','carpeta','usuario','sistema') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurso_id` int DEFAULT NULL,
  `datos_adicionales` json DEFAULT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_leida` (`leida`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. CREAR TABLA DE HISTORIAL DE ACCESOS
CREATE TABLE IF NOT EXISTS `historial_accesos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `recurso_tipo` ENUM('archivo','carpeta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso_id` int NOT NULL,
  `accion` ENUM('ver','descargar','editar','comentar','compartir') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `via_enlace` tinyint(1) DEFAULT '0',
  `enlace_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_acceso` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_recurso` (`recurso_tipo`, `recurso_id`),
  KEY `idx_accion` (`accion`),
  KEY `idx_fecha_acceso` (`fecha_acceso`),
  KEY `idx_enlace_token` (`enlace_token`),
  CONSTRAINT `historial_accesos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
