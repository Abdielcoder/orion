-- Migración para actualizar los roles de usuario basados en Google Drive
-- Fecha: 2025-01-03

-- Actualizar la tabla usuarios para incluir los nuevos roles de Google Drive
ALTER TABLE `usuarios` 
MODIFY COLUMN `rol` ENUM('admin','owner','editor','commenter','viewer') 
COLLATE utf8mb4_unicode_ci DEFAULT 'viewer';

-- Actualizar roles existentes si es necesario
-- Mantener admin como está
-- Cambiar manager a owner
UPDATE `usuarios` SET `rol` = 'owner' WHERE `rol` = 'manager';

-- Los roles editor y viewer se mantienen igual
-- El rol commenter es nuevo y se puede asignar manualmente

-- Crear tabla para roles con descripciones detalladas
CREATE TABLE IF NOT EXISTS `roles_sistema` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `permisos` json DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar los roles de Google Drive con sus permisos
INSERT INTO `roles_sistema` (`codigo`, `nombre`, `descripcion`, `permisos`) VALUES
('admin', 'Administrador', 'Control total del sistema. Puede gestionar usuarios, configuraciones y todos los recursos.', 
 '{"sistema": ["crear", "leer", "actualizar", "eliminar", "administrar"], "usuarios": ["crear", "leer", "actualizar", "eliminar"], "archivos": ["crear", "leer", "actualizar", "eliminar", "compartir", "transferir"], "carpetas": ["crear", "leer", "actualizar", "eliminar", "compartir", "administrar"]}'),

('owner', 'Propietario', 'Control total del archivo/carpeta. Puede ver, comentar, editar, eliminar y transferir la propiedad. Decide quién tiene acceso y con qué nivel de permisos.', 
 '{"archivos": ["crear", "leer", "actualizar", "eliminar", "comentar", "compartir", "transferir"], "carpetas": ["crear", "leer", "actualizar", "eliminar", "compartir", "administrar"]}'),

('editor', 'Editor', 'Puede ver y modificar el archivo. Puede añadir, cambiar o borrar contenido. En carpetas: puede agregar/eliminar archivos. No puede cambiar la propiedad ni quitar al propietario.', 
 '{"archivos": ["crear", "leer", "actualizar", "eliminar", "comentar"], "carpetas": ["crear", "leer", "actualizar", "eliminar"]}'),

('commenter', 'Comentarista', 'Puede ver el archivo. Puede agregar comentarios y sugerencias, pero no modificar directamente el contenido. Útil para revisiones o feedback.', 
 '{"archivos": ["leer", "comentar"], "carpetas": ["leer"]}'),

('viewer', 'Lector/Visualizador', 'Solo puede ver el archivo o carpeta. No puede comentar ni editar. Es el nivel de acceso más restrictivo sin quitar visibilidad.', 
 '{"archivos": ["leer"], "carpetas": ["leer"]}');

-- Crear tabla para auditoría de cambios de roles
CREATE TABLE IF NOT EXISTS `auditoria_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `rol_anterior` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_nuevo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cambiado_por` int NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `fecha_cambio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_cambiado_por` (`cambiado_por`),
  KEY `idx_fecha_cambio` (`fecha_cambio`),
  CONSTRAINT `auditoria_roles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_roles_ibfk_2` FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
