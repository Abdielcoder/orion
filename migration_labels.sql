-- Migración para añadir etiquetas con colores e iconos a carpetas
-- Ejecutar en phpMyAdmin o cliente MySQL

ALTER TABLE `carpetas` 
ADD COLUMN `etiqueta` VARCHAR(100) NULL DEFAULT NULL AFTER `activa`,
ADD COLUMN `color_etiqueta` VARCHAR(7) NULL DEFAULT NULL AFTER `etiqueta`,
ADD COLUMN `icono_personalizado` VARCHAR(50) NULL DEFAULT NULL AFTER `color_etiqueta`;

-- Índices para búsquedas
ALTER TABLE `carpetas` 
ADD INDEX `idx_etiqueta` (`etiqueta`),
ADD INDEX `idx_icono` (`icono_personalizado`);
