-- Migración paso a paso para actualizar roles

-- Paso 1: Expandir el ENUM para incluir los nuevos valores
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('admin','manager','editor','viewer','administrador','usuario_editor') 
COLLATE utf8mb4_unicode_ci DEFAULT 'viewer';

-- Paso 2: Actualizar los datos existentes
UPDATE usuarios SET rol = 'administrador' WHERE rol = 'admin';
UPDATE usuarios SET rol = 'usuario_editor' WHERE rol IN ('manager', 'editor', 'viewer');

-- Paso 3: Reducir el ENUM solo a los nuevos valores
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('administrador','usuario_editor') 
COLLATE utf8mb4_unicode_ci DEFAULT 'usuario_editor';

-- Verificar el resultado
SELECT 'Migración completada exitosamente' as status;
SELECT id, email, nombre, rol FROM usuarios;
