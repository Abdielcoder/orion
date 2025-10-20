# Sistema de Autenticación JWT

Este documento describe el sistema de autenticación mediante JWT implementado en la Biblioteca Digital.

## Resumen

El sistema ahora utiliza autenticación JWT (JSON Web Token) que llega desde un iframe padre. El token se envía en el header HTTP `x-token` y contiene la información del usuario autenticado en el sistema Orion.

## Funcionamiento

### 1. Recepción del Token

La aplicación está diseñada para funcionar dentro de un iframe. El sistema padre (Orion) envía un JWT en el header `x-token` con cada petición HTTP.

**Ejemplo de token:**
```
eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiI2NjRlNTM2MTYyMzZmOGVkMTlkMjJiNWMi...
```

### 2. Estructura del JWT

El JWT tiene tres partes separadas por puntos:

#### Header (decodificado)
```json
{
  "alg": "RS256",
  "typ": "JWT"
}
```

#### Payload (decodificado)
```json
{
  "uid": "664e53616236f8ed19d22b5c",
  "atomId": "USU202408164ZSDP",
  "name": "Super",
  "namePaternal": "Administrador",
  "nameMaternal": "BETA",
  "shortName": "Super Administrador",
  "email": "desarrollo-general@rinorisk.com",
  "avatarUrl": "https://apirino.com/file/usuario/664e53616236f8ed19d22b5c.png",
  "role": "SUPER_ADMIN_ROLE",
  "iat": 1760998347,
  "exp": 1761012747
}
```

#### Signature
Firma RSA256 del token

### 3. Flujo de Autenticación

1. **Usuario accede a la aplicación** (dentro del iframe)
2. **Middleware JwtAuthMiddleware intercepta** la petición
3. **Extrae el token** del header `x-token`
4. **Decodifica el JWT** usando JwtHelper
5. **Valida expiración** del token
6. **Extrae el email** del payload
7. **Busca el usuario** en la base de datos `usuarios`
8. **Si el usuario existe:**
   - Inicia sesión automáticamente
   - Establece la sesión con el rol correspondiente
9. **Si el usuario NO existe:**
   - Crea automáticamente el usuario (si `auto_create_users` está activo)
   - Asigna rol basado en el `role` del JWT
   - Inicia sesión

### 4. Mapeo de Roles

El sistema mapea los roles de Orion a los roles locales:

| Role en JWT | Rol en Biblioteca Digital |
|-------------|---------------------------|
| SUPER_ADMIN_ROLE | administrador |
| ADMIN_* | administrador |
| Otros | usuario |

### 5. Auto-creación de Usuarios

Cuando un usuario nuevo accede con JWT válido, el sistema:

1. Crea un registro en la tabla `usuarios`
2. Asigna nombre y apellidos desde el JWT
3. Genera una contraseña aleatoria (no se usará)
4. Asigna cuota de almacenamiento por defecto (10GB)
5. Marca el usuario como activo

## Configuración

### Archivo: `config/config.php`

```php
'jwt' => [
    // Ruta a la clave pública para verificar JWT (RS256)
    'public_key' => __DIR__ . '/../config/keys/orion.pem',
    
    // Verificar firma JWT (true = verificar, false = solo decodificar)
    'verify_signature' => false,
    
    // Permitir auto-creación de usuarios desde JWT
    'auto_create_users' => true,
],
```

### Clave Pública (orion.pem)

Coloca el archivo de clave pública RSA en: `config/keys/orion.pem`

**Formato esperado:**
```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
-----END PUBLIC KEY-----
```

## Componentes del Sistema

### 1. JwtHelper (`app/Helpers/JwtHelper.php`)

Clase utilitaria para trabajar con JWT:

- `decode(string $token)`: Decodifica el JWT y retorna el payload
- `verify(string $token, string $publicKeyPath)`: Verifica la firma RSA256
- `getEmail(array $payload)`: Extrae el email del payload
- `getUserData(array $payload)`: Extrae toda la info del usuario

### 2. JwtAuthMiddleware (`app/Middlewares/JwtAuthMiddleware.php`)

Middleware que:

- Intercepta todas las peticiones protegidas
- Lee el header `x-token`
- Valida el JWT
- Autentica al usuario automáticamente
- Crea nuevos usuarios si es necesario

### 3. UserRepository (`app/Repositories/UserRepository.php`)

Métodos agregados:

- `updateLastAccess(int $id)`: Actualiza fecha de último acceso
- `createUser(array $data)`: Crea nuevos usuarios desde JWT

## Headers HTTP Soportados

El middleware busca el token en múltiples formatos de header:

- `HTTP_X_TOKEN`
- `HTTP_X-TOKEN`
- `X-Token`
- `X-TOKEN`
- `x-token`

También intenta obtenerlo mediante:
- `getallheaders()`
- `apache_request_headers()`

## Seguridad

### Validaciones Implementadas

1. **Formato del JWT**: Verifica que tenga 3 partes
2. **Expiración**: Valida el campo `exp` del payload
3. **Email obligatorio**: El email debe existir en el payload
4. **Usuario activo**: Solo usuarios con `activo = 1` pueden acceder
5. **Firma RSA256** (opcional): Si está habilitada en config

### Headers de Seguridad

La aplicación mantiene los headers de seguridad estándar:

```php
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');
header('Content-Security-Policy: ...');
```

**Nota:** `X-Frame-Options` está comentado para permitir el uso en iframes.

## Sistema de Login Tradicional

**ELIMINADO COMPLETAMENTE** - El sistema de login tradicional ha sido removido. Solo se mantiene:

- `POST /auth/logout` - Cierra sesión (requiere JWT válido)

Todas las demás rutas de autenticación tradicional han sido eliminadas para usar únicamente JWT.

## Migración de Rutas

Todas las rutas protegidas ahora usan `JwtAuthMiddleware` en lugar de `AuthMiddleware`:

### Antes:
```php
$router->add('GET', '/drive', [DriveController::class, 'dashboard'], 
    [SecurityHeadersMiddleware::class, AuthMiddleware::class]);
```

### Ahora:
```php
$router->add('GET', '/drive', [DriveController::class, 'dashboard'], 
    [SecurityHeadersMiddleware::class, JwtAuthMiddleware::class]);
```

## Logs y Debugging

El sistema genera logs detallados en el error log de PHP:

```
JWT Auth - Token recibido: eyJhbGciOiJSUzI1NiIsInR5c...
JWT Auth - Email extraído del token: usuario@ejemplo.com
JWT Auth - Usuario autenticado: usuario@ejemplo.com (ID: 5, Rol: usuario)
```

Para ver los logs en MAMP:
```bash
tail -f /Applications/MAMP/logs/php_error.log
```

## Testing

### Test Manual con cURL

```bash
# Con token JWT en header
curl -H "x-token: eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9..." \
     http://localhost:8888/biblioteca/public/index.php/drive/list
```

### Token de Ejemplo (para testing)

```
eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiI2NjRlNTM2MTYyMzZmOGVkMTlkMjJiNWMiLCJhdG9tSWQiOiJVU1UyMDI0MDgxNjRaU0RQIiwibmFtZSI6IlN1cGVyIiwibmFtZVBhdGVybmFsIjoiQWRtaW5pc3RyYWRvciIsIm5hbWVNYXRlcm5hbCI6IkJFVEEiLCJzaG9ydE5hbWUiOiJTdXBlciBBZG1pbmlzdHJhZG9yIiwiZW1haWwiOiJkZXNhcnJvbGxvLWdlbmVyYWxAcmlub3Jpc2suY29tIiwiYXZhdGFyVXJsIjoiaHR0cHM6Ly9hcGlyaW5vLmNvbS9maWxlL3VzdWFyaW8vNjY0ZTUzNjE2MjM2ZjhlZDE5ZDIyYjVjLnBuZz8xNzU1NjIzMDc4MDQyIiwicm9sZSI6IlNVUEVSX0FETUlOX1JPTEUiLCJpYXQiOjE3NjA5OTgzNDcsImV4cCI6MTc2MTAxMjc0N30.u5gej0tpKwoq8H8henPt8rVUqXd9mG9B4079CfxcYz9x8bZD8s3Qd9ymBjdTntyMaO3WHQzbwLw5HfIcN6fSEvnHkT0bhSDKLdIJtX66HRjM8J1oCT9IcreMkGiay__F8SMAGG-pZ9WHdj69YNwsHWWMUMZLOlittuxpcvM7xdhK4q26gRFqtMrvxM65NXTilfNl2autzcviQiG7W1fwllhXS-3kyzRw_pKOq04Xrh1_HJpUziimbYFvDwDLtNSmWf2WBmIFMqO-8s_GwNez-32x26X6v1xlQ8nRZmFd4csS89jFwT8wghiZnkvmD8TmulUjH6uSnXxWlq4Ywjqk-g
```

**Payload:**
```json
{
  "email": "desarrollo-general@rinorisk.com",
  "role": "SUPER_ADMIN_ROLE",
  "name": "Super",
  "namePaternal": "Administrador",
  "exp": 1761012747
}
```

## Troubleshooting

### Error: "Token no proporcionado"

**Causa:** El header `x-token` no está llegando al servidor

**Solución:**
1. Verificar que el iframe padre esté enviando el header
2. Verificar configuración de CORS si aplica
3. Revisar logs del servidor web

### Error: "Token inválido"

**Causa:** El JWT no se puede decodificar

**Solución:**
1. Verificar que el token tenga 3 partes separadas por puntos
2. Verificar que no esté corrupto o incompleto
3. Revisar formato base64url

### Error: "Usuario no autorizado"

**Causa:** El email del JWT no existe en la BD y auto-create está desactivado

**Solución:**
1. Activar `auto_create_users` en config
2. O crear manualmente el usuario en la BD

### Error: "Token expirado"

**Causa:** El campo `exp` del JWT está en el pasado

**Solución:**
1. El sistema padre debe generar un nuevo token
2. Verificar sincronización de hora del servidor

## Estructura de Base de Datos

### Tabla: usuarios

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    nombre VARCHAR(255),
    apellidos VARCHAR(255),
    avatar VARCHAR(255),
    password VARCHAR(255),
    rol ENUM('usuario', 'administrador', 'superadmin') DEFAULT 'usuario',
    departamento VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    cuota_almacenamiento BIGINT DEFAULT 1073741824,
    almacenamiento_usado BIGINT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
);
```

## Próximos Pasos

1. **Habilitar verificación de firma:** Cambiar `verify_signature` a `true` una vez que tengas la clave pública
2. **Sincronización de avatares:** Implementar descarga de avatares desde `avatarUrl`
3. **Refresh de tokens:** Implementar lógica para renovar tokens expirados
4. **Audit log:** Registrar accesos y acciones de usuarios JWT

## Soporte

Para más información sobre el sistema Orion y generación de tokens JWT, consulta la documentación de Orion o contacta al equipo de desarrollo.

