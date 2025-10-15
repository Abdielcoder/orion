-- Migración para corregir la estructura de la tabla enlaces_compartidos
-- Fecha: 2025-10-15
-- Descripción: Agrega columnas faltantes para la funcionalidad de compartir enlaces públicos

USE biblioteca_digital;

-- Verificar y agregar columnas faltantes en enlaces_compartidos

-- 1. Cambiar columna 'tipo' si no existe (en algunas versiones es 'recurso_tipo')
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'tipo');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `tipo` ENUM(\'archivo\',\'carpeta\') COLLATE utf8mb4_unicode_ci NOT NULL AFTER `token`',
    'SELECT "Column tipo already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 2. Cambiar columna 'creado_por' si no existe (en algunas versiones es 'propietario_id')
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'creado_por');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `creado_por` INT NOT NULL AFTER `recurso_id`',
    'SELECT "Column creado_por already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 3. Agregar columna 'rol_acceso'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'rol_acceso');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `rol_acceso` ENUM(\'propietario\',\'editor\',\'comentarista\',\'lector\') COLLATE utf8mb4_unicode_ci DEFAULT \'lector\'',
    'SELECT "Column rol_acceso already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 4. Agregar columna 'requiere_autenticacion'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'requiere_autenticacion');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `requiere_autenticacion` TINYINT(1) DEFAULT 0',
    'SELECT "Column requiere_autenticacion already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 5. Agregar columna 'dominios_permitidos'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'dominios_permitidos');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `dominios_permitidos` TEXT COLLATE utf8mb4_unicode_ci',
    'SELECT "Column dominios_permitidos already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 6. Agregar columna 'puede_descargar'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'puede_descargar');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `puede_descargar` TINYINT(1) DEFAULT 1',
    'SELECT "Column puede_descargar already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 7. Agregar columna 'puede_imprimir'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'puede_imprimir');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `puede_imprimir` TINYINT(1) DEFAULT 1',
    'SELECT "Column puede_imprimir already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 8. Agregar columna 'puede_copiar'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'puede_copiar');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `puede_copiar` TINYINT(1) DEFAULT 1',
    'SELECT "Column puede_copiar already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 9. Agregar columna 'notificar_accesos'
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'notificar_accesos');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `notificar_accesos` TINYINT(1) DEFAULT 0',
    'SELECT "Column notificar_accesos already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- 10. Agregar columna 'contraseña' (para códigos de acceso)
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE table_schema = 'biblioteca_digital' 
               AND table_name = 'enlaces_compartidos' 
               AND column_name = 'contraseña');

SET @query := IF(@exist = 0, 
    'ALTER TABLE `enlaces_compartidos` ADD COLUMN `contraseña` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL',
    'SELECT "Column contraseña already exists"');
PREPARE stmt FROM @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- Verificar que todas las columnas necesarias existen
SELECT 
    'Enlaces compartidos - Columnas existentes:' as Mensaje,
    GROUP_CONCAT(column_name ORDER BY ordinal_position) as Columnas
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE table_schema = 'biblioteca_digital' 
AND table_name = 'enlaces_compartidos';
