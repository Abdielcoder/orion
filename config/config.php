<?php
return [
    'app_name' => 'ORION',
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => (bool)(getenv('APP_DEBUG') ?: true),
    'base_url' => getenv('APP_URL') ?: 'http://localhost:8888/biblioteca/public',
    'secret_key' => getenv('APP_KEY') ?: 'change-this-secret-key-please',
    'auth' => [
        // true = compara contraseñas en texto plano con la base de datos
        // false = usa password_verify (Argon2/bcrypt)
        'plaintext_passwords' => true,
    ],
    'jwt' => [
        // Ruta a la clave pública para verificar JWT (RS256)
        'public_key' => __DIR__ . '/../config/keys/orion.pem',
        // Verificar firma JWT (true = verificar, false = solo decodificar)
        'verify_signature' => false,
        // Permitir auto-creación de usuarios desde JWT
        'auto_create_users' => true,
    ],
    'session' => [
        'name' => 'BIBLIO_SESSID',
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'lifetime' => 7200,
    ],
    'security' => [
        'csrf_token_name' => '_csrf',
        'hsts' => false,
        // Permitimos CDNs para iconos y inline scripts para desarrollo, y objetos para PDFs
        'csp' => "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; font-src 'self' data:; connect-src 'self'; object-src 'self'",
    ],
];


