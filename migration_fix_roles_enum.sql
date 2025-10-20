-- Migración para corregir los valores del ENUM de roles
-- Problema: El modelo User usa 'usuario_editor' pero la BD tiene 'editor'

USE biblioteca_digital;

-- Verificar la estructura actual del ENUM
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'biblioteca_digital' 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'rol';

-- Actualizar el ENUM para que coincida con el modelo User
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('administrador','usuario_editor','colaborador','viewer') 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'viewer';

-- Verificar la nueva estructura
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'biblioteca_digital' 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'rol';

SELECT 'ENUM de roles actualizado exitosamente' AS mensaje;
