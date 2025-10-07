# 📖 Instalación Manual - Paso a Paso

## Para cuando phpMyAdmin no funciona

---

## ✅ Opción 1: Terminal (Más Fácil)

### Paso 1: Abrir Terminal

1. Presiona `Cmd + Espacio`
2. Escribe `Terminal`
3. Presiona `Enter`

### Paso 2: Ir a la carpeta del proyecto

```bash
cd /Applications/MAMP/htdocs/biblioteca
```

### Paso 3: Conectarse a MySQL

Prueba primero con **puerto 8889**:

```bash
/Applications/MAMP/Library/bin/mysql -u root -proot --port=8889
```

Si no funciona, prueba con **puerto 3306**:

```bash
/Applications/MAMP/Library/bin/mysql -u root -proot --port=3306
```

Si aún no funciona, prueba **sin especificar puerto**:

```bash
/Applications/MAMP/Library/bin/mysql -u root -proot
```

### Paso 4: Ejecutar el script SQL

Una vez conectado a MySQL (verás `mysql>`), ejecuta:

```sql
source install_simple.sql
```

### Paso 5: Verificar

```sql
USE biblioteca_digital;
SHOW TABLES;
SELECT * FROM usuarios WHERE id = 1;
```

Deberías ver 7 tablas y el usuario administrador.

### Paso 6: Salir

```sql
exit
```

---

## ✅ Opción 2: Instalar Manualmente (Si nada funciona)

### Paso 1: Crear base de datos

Abre Terminal y conéctate a MySQL (prueba los comandos del Opción 1).

```sql
CREATE DATABASE biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_digital;
```

### Paso 2: Copiar y pegar cada tabla

Abre el archivo `install_simple.sql` en un editor de texto y copia cada comando `CREATE TABLE` uno por uno y pégalo en el terminal de MySQL.

**Orden de las tablas:**

1. `usuarios`
2. `carpetas`
3. `archivos`
4. `permisos_recursos`
5. `enlaces_compartidos`
6. `actividad`
7. `user_settings`

### Paso 3: Insertar usuario administrador

```sql
INSERT INTO usuarios (nombre, email, password, rol, cuota_almacenamiento, almacenamiento_usado, activo) 
VALUES ('Administrador', 'admin@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 107374182400, 0, 1);
```

### Paso 4: Verificar

```sql
SELECT * FROM usuarios WHERE id = 1;
```

---

## ✅ Opción 3: Usando Sequel Pro o TablePlus

Si tienes alguna de estas herramientas:

### Paso 1: Conectar

- **Host**: `localhost` o `127.0.0.1`
- **Usuario**: `root`
- **Password**: `root`
- **Puerto**: `8889` o `3306`
- **Socket**: `/Applications/MAMP/tmp/mysql/mysql.sock`

### Paso 2: Importar SQL

1. Selecciona "Ejecutar Query" o "Query"
2. Abre el archivo `install_simple.sql`
3. Copia todo el contenido
4. Pégalo en la ventana de query
5. Ejecuta (⌘ + R o botón "Run")

---

## ✅ Opción 4: Configurar phpMyAdmin de MAMP

Si quieres que phpMyAdmin funcione:

### Paso 1: Editar configuración

```bash
open -a TextEdit /Applications/MAMP/bin/phpMyAdmin/config.inc.php
```

### Paso 2: Buscar y modificar estas líneas

Busca la sección `$cfg['Servers'][$i]` y asegúrate que diga:

```php
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['port'] = '8889';  // o 3306, verifica en MAMP
$cfg['Servers'][$i]['socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'root';
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
```

### Paso 3: Guardar y reiniciar MAMP

1. Guarda el archivo (⌘ + S)
2. Cierra MAMP completamente
3. Vuelve a abrir MAMP
4. Inicia los servidores
5. Abre phpMyAdmin: `http://localhost:8888/phpMyAdmin`

---

## 🔍 Encontrar el Puerto de MySQL

### Opción A: Desde MAMP

1. Abre **MAMP**
2. Haz clic en **Preferencias**
3. Ve a la pestaña **Ports**
4. Mira el **MySQL Port** (usualmente 8889 o 3306)

### Opción B: Desde Terminal

```bash
ps aux | grep mysql
```

Busca algo como `--port=8889` o `--port=3306`

---

## 🎯 Comando Definitivo (Copia y Pega)

Este comando intenta todos los puertos comunes:

```bash
# Ir a la carpeta
cd /Applications/MAMP/htdocs/biblioteca

# Intentar con puerto 8889
/Applications/MAMP/Library/bin/mysql -u root -proot --port=8889 < install_simple.sql 2>/dev/null

# Si no funcionó, intentar con puerto 3306
/Applications/MAMP/Library/bin/mysql -u root -proot --port=3306 < install_simple.sql 2>/dev/null

# Si no funcionó, intentar sin puerto
/Applications/MAMP/Library/bin/mysql -u root -proot < install_simple.sql

# Verificar
/Applications/MAMP/Library/bin/mysql -u root -proot -e "USE biblioteca_digital; SELECT COUNT(*) as tablas FROM information_schema.tables WHERE table_schema='biblioteca_digital';"
```

---

## ✅ Verificación Final

Para verificar que todo está instalado:

```bash
/Applications/MAMP/Library/bin/mysql -u root -proot -e "
USE biblioteca_digital;
SELECT 'Tablas creadas:' as info, COUNT(*) as total FROM information_schema.tables WHERE table_schema='biblioteca_digital';
SELECT 'Usuario admin:' as info, COUNT(*) as existe FROM usuarios WHERE id=1;
"
```

Deberías ver:
- **Tablas creadas**: 7
- **Usuario admin existe**: 1

---

## 🎉 ¡Listo!

Una vez instalado, accede a:

```
http://localhost:8888/biblioteca/public/index.php/auth/login
```

**Credenciales:**
- Email: `admin@biblioteca.com`
- Password: `admin123`

---

## 📞 Si Nada Funciona

Envía esta información:

```bash
# Versión de MAMP
ls -la /Applications/MAMP/

# Estado de MySQL
ps aux | grep mysql

# Puerto de MySQL
lsof -i :3306
lsof -i :8889

# Intentar conectar
/Applications/MAMP/Library/bin/mysql -u root -proot --version
```

Con esta información podré ayudarte mejor.
