# Instrucciones de Configuración JWT

## Resumen de Cambios

Se ha implementado un sistema de autenticación mediante JWT que recibe tokens desde un iframe padre. El sistema valida el email del JWT contra la base de datos y autentica automáticamente al usuario.

## Archivos Creados

1. **`app/Helpers/JwtHelper.php`** - Helper para decodificar y validar JWT
2. **`app/Middlewares/JwtAuthMiddleware.php`** - Middleware de autenticación JWT
3. **`config/keys/`** - Directorio para claves públicas
4. **`SISTEMA_AUTENTICACION_JWT.md`** - Documentación completa del sistema
5. **`test_jwt.php`** - Script de prueba

## Archivos Modificados

1. **`public/index.php`** - Rutas actualizadas para usar JwtAuthMiddleware
2. **`config/config.php`** - Agregada configuración JWT
3. **`app/Repositories/UserRepository.php`** - Agregado método updateLastAccess

## Pasos de Configuración

### 1. Copiar la Clave Pública

Si tienes el archivo `orion.pem`, cópialo a:

```bash
cp orion.pem /Applications/MAMP/htdocs/biblioteca/config/keys/orion.pem
```

Si no lo tienes aún, el sistema funcionará sin verificar la firma (solo decodifica el token).

### 2. Ajustar Permisos (Opcional)

```bash
chmod 600 /Applications/MAMP/htdocs/biblioteca/config/keys/orion.pem
```

### 3. Probar el Sistema

#### Opción A: Desde línea de comandos

```bash
cd /Applications/MAMP/htdocs/biblioteca
php test_jwt.php
```

#### Opción B: Desde el navegador

Abre: http://localhost:8888/biblioteca/test_jwt.php

### 4. Probar con cURL

```bash
# Reemplaza el token con uno válido de tu sistema
curl -H "x-token: eyJhbGciOiJSUzI1NiIsInR5c..." \
     http://localhost:8888/biblioteca/public/index.php/drive/list
```

## Configuración Opcional

### Habilitar Verificación de Firma

Edita `config/config.php`:

```php
'jwt' => [
    'verify_signature' => true,  // Cambiar a true
],
```

### Deshabilitar Auto-creación de Usuarios

Si solo quieres permitir usuarios existentes:

```php
'jwt' => [
    'auto_create_users' => false,  // Cambiar a false
],
```

## Verificar que Funciona

### 1. Revisar Logs

Los logs se escriben en el archivo de errores de PHP:

```bash
tail -f /Applications/MAMP/logs/php_error.log | grep "JWT Auth"
```

Deberías ver líneas como:

```
JWT Auth - Token recibido: eyJhbGciOiJSUzI1NiIsInR5c...
JWT Auth - Email extraído del token: usuario@ejemplo.com
JWT Auth - Usuario autenticado: usuario@ejemplo.com (ID: 5, Rol: usuario)
```

### 2. Probar en el Iframe

Cuando tu aplicación esté dentro del iframe de Orion:

1. El sistema Orion enviará automáticamente el header `x-token`
2. Accede a cualquier ruta (ej: `/drive`)
3. Deberías ser autenticado automáticamente
4. Verifica en la base de datos que el usuario se creó (si es nuevo)

### 3. Verificar Usuario en Base de Datos

```sql
SELECT id, email, nombre, apellidos, rol, activo, fecha_ultimo_acceso 
FROM usuarios 
WHERE email = 'desarrollo-general@rinorisk.com';
```

Si el usuario se creó automáticamente desde el JWT, deberías verlo aquí.

## Troubleshooting

### El token no llega al servidor

**Problema:** Error "Token no proporcionado"

**Solución:**
1. Verifica que el iframe padre esté enviando el header
2. Usa las herramientas de desarrollo del navegador (Network tab)
3. Busca el header `x-token` en las peticiones

### Token expirado

**Problema:** El campo `exp` está en el pasado

**Solución:**
- El sistema Orion debe generar un nuevo token
- Verifica que la hora del servidor esté correcta

### Usuario no autorizado

**Problema:** El email no existe en la BD

**Solución:**
- Habilita `auto_create_users` en la configuración
- O crea manualmente el usuario con ese email

### Firma inválida

**Problema:** Si `verify_signature` está activo y falla

**Solución:**
1. Verifica que `orion.pem` sea la clave correcta
2. Verifica que el archivo sea accesible
3. Temporalmente deshabilita la verificación para debugging

## Testing con Token de Ejemplo

El token de ejemplo incluido tiene este payload:

```json
{
  "email": "desarrollo-general@rinorisk.com",
  "name": "Super",
  "namePaternal": "Administrador",
  "nameMaternal": "BETA",
  "role": "SUPER_ADMIN_ROLE"
}
```

**NOTA:** Este token expira el 2025-12-16. Para testing real, usa un token generado por tu sistema Orion.

## Sistema de Login Tradicional

El login tradicional (`/auth/login`) se mantiene como fallback pero ya no es necesario para uso normal en el iframe.

## Soporte

Para más detalles técnicos, consulta:
- `SISTEMA_AUTENTICACION_JWT.md` - Documentación completa
- `config/keys/README.md` - Info sobre las claves

## Checklist de Verificación

- [ ] Archivo `orion.pem` colocado en `config/keys/` (opcional)
- [ ] Ejecutado `test_jwt.php` correctamente
- [ ] Logs de PHP muestran mensajes de "JWT Auth"
- [ ] Usuario de prueba se crea automáticamente
- [ ] Acceso a `/drive` funciona dentro del iframe
- [ ] Headers de seguridad configurados correctamente

## Próximos Pasos

Una vez verificado que todo funciona:

1. Habilitar `verify_signature` si tienes la clave pública
2. Configurar HTTPS en producción
3. Ajustar `cookie_secure` a `true` en producción
4. Revisar política de CORS si es necesario

