-- Migración para agregar columnas faltantes en la tabla usuarios
-- Problema: El código PHP espera columnas que no existen en la base de datos

USE biblioteca_digital;

-- Agregar columna apellidos
ALTER TABLE usuarios 
ADD COLUMN apellidos VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER nombre;

-- Agregar columna departamento
ALTER TABLE usuarios 
ADD COLUMN departamento VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER rol;

-- Agregar columnas de fecha (manteniendo compatibilidad con las existentes)
ALTER TABLE usuarios 
ADD COLUMN fecha_creacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP 
AFTER activo;

ALTER TABLE usuarios 
ADD COLUMN fecha_ultimo_acceso TIMESTAMP NULL DEFAULT NULL 
AFTER fecha_creacion;

-- Copiar datos de las columnas antiguas a las nuevas (si existen datos)
UPDATE usuarios 
SET fecha_creacion = fecha_registro,
    fecha_ultimo_acceso = ultimo_acceso;

-- Verificar la estructura actualizada
DESCRIBE usuarios;

SELECT 'Migración completada exitosamente. Columnas agregadas: apellidos, departamento, fecha_creacion, fecha_ultimo_acceso' AS mensaje;

