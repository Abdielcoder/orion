# 🚀 Configuración Completa en AWS EC2

## Para que funcione: http://98.87.243.120/biblioteca/public/index.php/auth/login

---

## 📦 Paso 1: Subir Archivos

```bash
# Desde tu Mac, comprimir y subir
cd /Applications/MAMP/htdocs/
tar --exclude='biblioteca/storage/files/*' -czf biblioteca.tar.gz biblioteca/
scp -i orion.pem biblioteca.tar.gz ubuntu@98.87.243.120:/home/ubuntu/
```

---

## ⚙️ Paso 2: Configurar en el Servidor

Conectar al servidor:
```bash
ssh -i orion.pem ubuntu@98.87.243.120
```

Descomprimir y configurar:
```bash
cd /home/ubuntu
tar -xzf biblioteca.tar.gz
chmod +x biblioteca/configurar_aws.sh
sudo ./biblioteca/configurar_aws.sh
```

---

## 🗄️ Paso 3: Configurar Base de Datos

### Instalar MySQL:
```bash
sudo apt update
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### Crear usuario y base de datos:
```bash
sudo mysql -u root -p
```

```sql
CREATE USER 'biblioteca_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
CREATE DATABASE biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON biblioteca_digital.* TO 'biblioteca_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Importar estructura:
```bash
cd /var/www/html/biblioteca
mysql -u biblioteca_user -p biblioteca_digital < install_remote.sql
```

---

## 📝 Paso 4: Configurar Aplicación

Editar configuración de base de datos:
```bash
sudo nano /var/www/html/biblioteca/config/database.php
```

Cambiar por:
```php
<?php
return [
    'host' => 'localhost',
    'database' => 'biblioteca_digital',
    'username' => 'biblioteca_user',
    'password' => 'tu_password_seguro',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

---

## 🔧 Archivos .htaccess Creados

### 1. `/var/www/html/.htaccess` (Raíz del servidor)
- Redirecciones principales
- `http://98.87.243.120` → `/biblioteca/public/index.php/auth/login`

### 2. `/var/www/html/biblioteca/.htaccess` (Carpeta de la app)
- Protección de archivos sensibles
- Configuración PHP

### 3. `/var/www/html/biblioteca/public/.htaccess` (Front controller)
- Manejo de rutas
- Seguridad y compresión

---

## 🌐 URLs Resultantes

Después de la configuración:

| URL | Destino |
|-----|---------|
| `http://98.87.243.120` | → Login |
| `http://98.87.243.120/biblioteca` | → Login |
| `http://98.87.243.120/biblioteca/public` | → Login |
| `http://98.87.243.120/biblioteca/public/index.php/auth/login` | Login directo |

---

## ✅ Verificación

### Comprobar que funciona:
```bash
# Estado de Apache
sudo systemctl status apache2

# Logs en tiempo real
sudo tail -f /var/log/apache2/biblioteca_error.log

# Permisos
ls -la /var/www/html/biblioteca/

# Conectividad
curl -I http://98.87.243.120
```

### Probar en navegador:
1. `http://98.87.243.120` → Debería redirigir al login
2. Login con: `admin@biblioteca.com` / `admin123`
3. Cambiar password inmediatamente

---

## 🔐 Seguridad Adicional

### Configurar firewall:
```bash
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### SSL con Let's Encrypt (opcional):
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d tu-dominio.com
```

---

## 🐛 Solución de Problemas

### Error 500:
```bash
sudo tail -f /var/log/apache2/error.log
sudo chmod -R 755 /var/www/html/biblioteca
sudo chown -R www-data:www-data /var/www/html/biblioteca
```

### No redirige:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Base de datos no conecta:
```bash
# Verificar MySQL
sudo systemctl status mysql

# Probar conexión
mysql -u biblioteca_user -p -e "SHOW DATABASES;"
```

---

## 📋 Checklist Final

- [ ] Archivos subidos y descomprimidos
- [ ] Script `configurar_aws.sh` ejecutado
- [ ] MySQL instalado y configurado
- [ ] Base de datos importada
- [ ] `config/database.php` actualizado
- [ ] Apache reiniciado
- [ ] URL principal redirige al login
- [ ] Login funciona correctamente
- [ ] Password de admin cambiado

---

## 🎉 ¡Listo!

Tu aplicación debería estar funcionando en:
**http://98.87.243.120**

Con redirección automática al login de la Biblioteca Digital.
