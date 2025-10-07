# 🔧 Configurar phpMyAdmin en MAMP

## Problema: "Access denied for user 'root'@'localhost'"

---

## Solución Rápida

### Opción 1: Usar las credenciales correctas de MAMP

Las credenciales **por defecto de MAMP** son:
- **Usuario**: `root`
- **Password**: `root`

### Opción 2: Configurar phpMyAdmin de MAMP

1. **Localiza el archivo de configuración de phpMyAdmin:**
```
/Applications/MAMP/bin/phpMyAdmin/config.inc.php
```

2. **Abre el archivo** con un editor de texto

3. **Busca esta línea:**
```php
$cfg['Servers'][$i]['password'] = 'root';
```

4. **Asegúrate que esté así:**
```php
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['port'] = '8889'; // o 3306 dependiendo de tu configuración
$cfg['Servers'][$i]['socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'root';
```

5. **Guarda el archivo**

6. **Reinicia MAMP**

---

## Solución Alternativa: Sin phpMyAdmin

Si phpMyAdmin no funciona, puedes instalar directamente desde la línea de comandos:

### Paso 1: Conectarse a MySQL de MAMP
```bash
/Applications/MAMP/Library/bin/mysql -u root -proot
```

### Paso 2: Ejecutar comandos SQL manualmente
```sql
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_digital;

-- Copiar y pegar el contenido de install_simple.sql aquí
-- O ejecutar:
```

```bash
/Applications/MAMP/Library/bin/mysql -u root -proot < install_simple.sql
```

---

## Verificar Puerto de MySQL

MAMP puede usar diferentes puertos:

### Ver qué puerto usa MAMP:
1. Abre **MAMP**
2. Clic en **Preferencias**
3. Ve a la pestaña **Ports**
4. Verifica el puerto de MySQL (usualmente **8889** o **3306**)

### Si el puerto es 8889:
```bash
/Applications/MAMP/Library/bin/mysql -u root -proot --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock
```

---

## Solución: Crear archivo SQL ejecutable

Voy a crear un script que puedes ejecutar fácilmente:

