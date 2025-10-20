# Claves JWT

Este directorio contiene las claves públicas necesarias para verificar los tokens JWT.

## Archivo orion.pem

Coloca aquí el archivo de clave pública RSA `orion.pem` proporcionado por el sistema Orion.

El archivo debe tener el siguiente formato:

```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
...
-----END PUBLIC KEY-----
```

## Configuración

La ruta a la clave pública se configura en `config/config.php`:

```php
'jwt' => [
    'public_key' => __DIR__ . '/../config/keys/orion.pem',
    'verify_signature' => false, // Cambiar a true para verificar firma
    'auto_create_users' => true,
],
```

## Notas de Seguridad

- Este archivo NO debe estar en control de versiones (ya está en .gitignore)
- Protege estos archivos con permisos 600 o 400
- Solo el usuario del servidor web debe tener acceso

