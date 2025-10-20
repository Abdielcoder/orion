# Solución: Error al Insertar Usuarios en el Panel de Administración

## Problema Identificado

Al intentar crear un nuevo usuario desde el panel de administración (`/admin/users`), la operación fallaba sin mostrar un error específico al usuario. 

### Causa Raíz

El código PHP en `UserRepository.php` intentaba insertar datos en columnas que **no existían en la tabla `usuarios`** de la base de datos:

**Columnas faltantes:**
- `apellidos` - Para almacenar los apellidos del usuario
- `departamento` - Para el departamento al que pertenece el usuario
- `fecha_creacion` - Para la fecha de creación del usuario
- `fecha_ultimo_acceso` - Para registrar el último acceso del usuario

**Error SQL generado:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'apellidos' in 'field list'
```

## Solución Implementada

### 1. Migración de Base de Datos

Se creó y ejecutó el archivo `migration_fix_usuarios_columns.sql` que agrega las columnas faltantes:

```sql
-- Agregar columna apellidos
ALTER TABLE usuarios 
ADD COLUMN apellidos VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER nombre;

-- Agregar columna departamento
ALTER TABLE usuarios 
ADD COLUMN departamento VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER rol;

-- Agregar columnas de fecha
ALTER TABLE usuarios 
ADD COLUMN fecha_creacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP 
AFTER activo;

ALTER TABLE usuarios 
ADD COLUMN fecha_ultimo_acceso TIMESTAMP NULL DEFAULT NULL 
AFTER fecha_creacion;

-- Copiar datos de las columnas antiguas a las nuevas
UPDATE usuarios 
SET fecha_creacion = fecha_registro,
    fecha_ultimo_acceso = ultimo_acceso;
```

### 2. Estructura Actualizada de la Tabla

**Antes:**
- id
- nombre
- email
- password
- rol
- cuota_almacenamiento
- almacenamiento_usado
- activo
- fecha_registro
- ultimo_acceso

**Después:**
- id
- nombre
- **apellidos** (NUEVO)
- email
- password
- rol
- **departamento** (NUEVO)
- cuota_almacenamiento
- almacenamiento_usado
- activo
- **fecha_creacion** (NUEVO)
- **fecha_ultimo_acceso** (NUEVO)
- fecha_registro (mantenido para compatibilidad)
- ultimo_acceso (mantenido para compatibilidad)

## Archivos Modificados

### 1. `migration_fix_usuarios_columns.sql`
- **Nuevo archivo**: Script SQL para agregar las columnas faltantes
- **Ubicación**: `/var/www/html/biblioteca/` (servidor)

### 2. Archivos del Código que ya usaban estas columnas
- `app/Repositories/UserRepository.php` - Método `createUser()` y `map()`
- `app/Controllers/AdminUsersController.php` - Métodos `createUser()` y `updateUser()`
- `app/Views/admin/users.php` - Formulario de creación/edición de usuarios

## Despliegue

La migración se ejecutó en el servidor de producción:

```bash
# Copiar migración al servidor
scp -i orion.pem migration_fix_usuarios_columns.sql ubuntu@orion.rinorisk.com:/tmp/

# Ejecutar migración
ssh -i orion.pem ubuntu@orion.rinorisk.com "sudo mysql biblioteca_digital < /tmp/migration_fix_usuarios_columns.sql"
```

## Verificación

Se verificó el funcionamiento correcto mediante:

1. **Script de prueba directa**: Creó un usuario de prueba exitosamente
2. **Verificación de estructura**: Confirmó que las columnas existen en la tabla
3. **Prueba manual**: El formulario de administración ahora permite crear usuarios sin errores

## Resultado

✅ **La inserción de usuarios ahora funciona correctamente**

Los usuarios pueden ser creados desde el panel de administración con todos los campos:
- Email (obligatorio)
- Nombre (obligatorio)
- Apellidos (opcional)
- Rol (obligatorio)
- Departamento (opcional)
- Cuota de almacenamiento (obligatorio)
- Contraseña (obligatorio para nuevos usuarios)

## Notas Adicionales

- Las columnas antiguas `fecha_registro` y `ultimo_acceso` se mantuvieron para preservar la compatibilidad con código existente
- Los datos de las columnas antiguas se copiaron a las nuevas columnas durante la migración
- La solución es retrocompatible y no afecta a usuarios existentes

## Fecha de Implementación

**15 de octubre de 2025**

---

**Estado**: ✅ Completado y verificado en producción


