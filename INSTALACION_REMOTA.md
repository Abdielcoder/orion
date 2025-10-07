# 🚀 Guía de Instalación en Servidor Remoto

## Biblioteca Digital - Sistema de Gestión de Archivos en la Nube

---

## 📋 Requisitos Previos

### Servidor
- **PHP**: 8.0 o superior
- **MySQL**: 8.0 o superior (o MariaDB 10.5+)
- **Servidor Web**: Apache o Nginx
- **Espacio en disco**: Mínimo 10GB (dependiendo del uso esperado)
- **Memoria RAM**: Mínimo 512MB

### Extensiones PHP Requeridas
```bash
php-mysqli
php-pdo
php-pdo_mysql
php-json
php-mbstring
php-fileinfo
php-session
```

---

## 🔧 Instalación Paso a Paso

### 1. Preparar el Servidor

#### A. Verificar versión de PHP
```bash
php -v
```
Debe ser 8.0 o superior.

#### B. Verificar extensiones de PHP
```bash
php -m | grep -E 'mysqli|pdo|json|mbstring|fileinfo'
```

### 2. Crear Base de Datos

#### A. Acceder a MySQL
```bash
mysql -u root -p
```

#### B. Ejecutar el script de instalación
```sql
SOURCE /ruta/a/install_remote.sql;
```

O desde phpMyAdmin:
1. Crear base de datos: `biblioteca_digital`
2. Importar archivo: `install_remote.sql`

### 3. Subir Archivos al Servidor

#### A. Estructura de archivos a subir:
```
biblioteca/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   ├── Helpers/
│   ├── Middlewares/
│   ├── Repositories/
│   └── Services/
├── config/
│   ├── config.php
│   └── database.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   └── fonts/
│   └── index.php
├── storage/
│   └── files/
└── spl_autoload.php
```

#### B. Subir vía FTP, SFTP o Git
```bash
# Ejemplo con Git
git clone tu-repositorio.git
cd biblioteca
```

### 4. Configurar Permisos

```bash
# Permisos para carpeta storage
chmod -R 755 storage/
chmod -R 775 storage/files/

# Owner (si es necesario)
chown -R www-data:www-data storage/
```

### 5. Configurar Base de Datos

Editar `config/database.php`:

```php
<?php
return [
    'host' => 'localhost',           // Tu host MySQL
    'dbname' => 'biblioteca_digital', // Nombre de tu BD
    'user' => 'tu_usuario',          // Usuario MySQL
    'password' => 'tu_password',      // Password MySQL
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

### 6. Configurar Aplicación

Editar `config/config.php`:

```php
<?php
return [
    'app_name' => 'Biblioteca Digital',
    'base_url' => 'https://tudominio.com',
    'session' => [
        'name' => 'biblioteca_session',
        'lifetime' => 3600,
        'secure' => true,  // true si usas HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ],
    'storage_path' => __DIR__ . '/../storage/files',
];
```

### 7. Configurar Servidor Web

#### Apache (.htaccess en public/)

Ya incluido, pero verificar que `mod_rewrite` esté habilitado:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx

```nginx
server {
    listen 80;
    server_name tudominio.com;
    root /ruta/a/biblioteca/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Configuración para archivos grandes
    client_max_body_size 256M;
    
    location ~ /\.ht {
        deny all;
    }
}
```

### 8. Configurar PHP para Archivos Grandes

Editar `php.ini`:

```ini
upload_max_filesize = 256M
post_max_size = 256M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
max_file_uploads = 20
```

Reiniciar PHP-FPM:
```bash
sudo systemctl restart php8.0-fpm
```

---

## 🔐 Configuración de Seguridad

### 1. Cambiar Credenciales por Defecto

**IMPORTANTE**: Cambiar el password del administrador inmediatamente.

Acceder a: `https://tudominio.com/index.php/auth/login`

- **Email**: `admin@biblioteca.com`
- **Password**: `admin123`

Ir a configuración y cambiar el password.

### 2. Configurar SSL/HTTPS

```bash
# Con Let's Encrypt
sudo certbot --apache -d tudominio.com
```

### 3. Configurar Firewall

```bash
# UFW (Ubuntu)
sudo ufw allow 'Apache Full'
sudo ufw allow 22
sudo ufw enable
```

### 4. Crear Usuario MySQL Específico

```sql
CREATE USER 'biblioteca_app'@'localhost' IDENTIFIED BY 'password_seguro_aqui';
GRANT SELECT, INSERT, UPDATE, DELETE ON biblioteca_digital.* TO 'biblioteca_app'@'localhost';
GRANT EXECUTE ON PROCEDURE biblioteca_digital.sp_actualizar_almacenamiento_usuario TO 'biblioteca_app'@'localhost';
GRANT EXECUTE ON PROCEDURE biblioteca_digital.sp_eliminar_archivo TO 'biblioteca_app'@'localhost';
FLUSH PRIVILEGES;
```

---

## ✅ Verificación de Instalación

### 1. Verificar Base de Datos

```sql
USE biblioteca_digital;
SHOW TABLES;
SELECT * FROM usuarios WHERE id = 1;
```

### 2. Verificar Acceso Web

Abrir navegador:
```
https://tudominio.com/index.php/auth/login
```

### 3. Verificar Permisos de Escritura

```bash
touch storage/files/test.txt
rm storage/files/test.txt
```

### 4. Verificar PHP

Crear `info.php` en public/:
```php
<?php phpinfo(); ?>
```

Visitar: `https://tudominio.com/info.php`

**IMPORTANTE**: Eliminar después de verificar.

---

## 🔄 Mantenimiento

### Backups Automáticos

#### Script de Backup (backup.sh)

```bash
#!/bin/bash
FECHA=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/ruta/a/backups"

# Backup Base de Datos
mysqldump -u usuario -p'password' biblioteca_digital > "$BACKUP_DIR/db_$FECHA.sql"

# Backup Archivos
tar -czf "$BACKUP_DIR/files_$FECHA.tar.gz" /ruta/a/biblioteca/storage/files/

# Limpiar backups antiguos (más de 7 días)
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completado: $FECHA"
```

#### Configurar Cron

```bash
crontab -e

# Backup diario a las 2 AM
0 2 * * * /ruta/a/backup.sh
```

### Monitoreo

```bash
# Ver logs de errores
tail -f /var/log/apache2/error.log

# Ver logs de acceso
tail -f /var/log/apache2/access.log

# Espacio en disco
df -h
```

---

## 🐛 Resolución de Problemas

### Error: "No se puede conectar a la base de datos"

1. Verificar credenciales en `config/database.php`
2. Verificar que MySQL esté corriendo: `sudo systemctl status mysql`
3. Verificar permisos de usuario MySQL

### Error: "Permission denied" al subir archivos

```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

### Error: "Maximum upload size exceeded"

1. Aumentar límites en `php.ini`
2. Reiniciar PHP-FPM
3. Verificar límites de Nginx/Apache

### Error 500 - Internal Server Error

1. Verificar logs: `tail -f /var/log/apache2/error.log`
2. Verificar permisos de archivos
3. Verificar sintaxis de `.htaccess`

---

## 📞 Soporte

Para más información, consultar:
- `MANUAL_SISTEMA_BIBLIOTECA.md` - Manual completo del sistema
- `SISTEMA_ROLES_COMPARTICION.md` - Sistema de permisos
- Logs del sistema en: `/var/log/`

---

## 📝 Checklist de Instalación

- [ ] PHP 8.0+ instalado
- [ ] MySQL 8.0+ instalado
- [ ] Base de datos creada
- [ ] Script SQL ejecutado
- [ ] Archivos subidos al servidor
- [ ] Permisos configurados (storage/)
- [ ] database.php configurado
- [ ] config.php configurado
- [ ] Servidor web configurado
- [ ] PHP.ini ajustado para archivos grandes
- [ ] SSL/HTTPS configurado
- [ ] Password de admin cambiado
- [ ] Usuario MySQL específico creado
- [ ] Firewall configurado
- [ ] Backup automático configurado
- [ ] Logs verificados
- [ ] Pruebas de subida de archivos

---

## 🎉 ¡Instalación Completa!

Tu sistema Biblioteca Digital está listo para usarse.

**Credenciales por defecto** (CAMBIAR INMEDIATAMENTE):
- Email: `admin@biblioteca.com`
- Password: `admin123`

**URL de acceso**: `https://tudominio.com/index.php/auth/login`

---

**Versión**: 1.0  
**Última actualización**: 2025
