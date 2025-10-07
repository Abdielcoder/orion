-- Migración simple para actualizar roles
-- Actualizar usuarios existentes primero
UPDATE usuarios SET rol = 'administrador' WHERE rol = 'admin';
UPDATE usuarios SET rol = 'usuario_editor' WHERE rol = 'editor';

-- Ahora cambiar la estructura de la columna
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('administrador','usuario_editor') 
COLLATE utf8mb4_unicode_ci DEFAULT 'usuario_editor';

SELECT 'Roles actualizados exitosamente' as status;
SELECT id, email, nombre, rol FROM usuarios;
