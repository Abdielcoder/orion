# 🚀 Instalación Rápida - 3 Pasos

## Opción 1: Usando phpMyAdmin (RECOMENDADO)

### Paso 1: Importar el archivo SQL
1. Abre **phpMyAdmin** en tu navegador
   - Local: `http://localhost:8888/phpMyAdmin` o `http://localhost/phpmyadmin`
   - Remoto: `https://tudominio.com/phpmyadmin`

2. En el panel izquierdo, haz clic en **"Nueva base de datos"** (o puede que ya exista)

3. Haz clic en la pestaña **"Importar"** en el menú superior

4. Haz clic en **"Seleccionar archivo"** y elige: `install_simple.sql`

5. Desplázate hacia abajo y haz clic en **"Continuar"**

6. Espera a que termine (verás mensaje de éxito)

### Paso 2: Verificar la instalación
1. En phpMyAdmin, selecciona la base de datos `biblioteca_digital`
2. Deberías ver 7 tablas:
   - ✅ actividad
   - ✅ archivos
   - ✅ carpetas
   - ✅ enlaces_compartidos
   - ✅ permisos_recursos
   - ✅ user_settings
   - ✅ usuarios

3. Haz clic en la tabla `usuarios`
4. Verifica que existe el usuario administrador

### Paso 3: Iniciar sesión
1. Abre tu aplicación en el navegador:
   - Local: `http://localhost:8888/biblioteca/public/index.php/auth/login`
   - Remoto: `https://tudominio.com/index.php/auth/login`

2. Ingresa las credenciales:
   - **Email**: `admin@biblioteca.com`
   - **Password**: `admin123`

3. **IMPORTANTE**: Cambia el password inmediatamente después de iniciar sesión

---

## Opción 2: Usando Línea de Comandos

### Para MAMP (Mac)
```bash
cd /Applications/MAMP/htdocs/biblioteca
/Applications/MAMP/Library/bin/mysql -u root -p < install_simple.sql
```

### Para servidor Linux/Unix
```bash
mysql -u tu_usuario -p < install_simple.sql
```

### Para Windows (XAMPP)
```cmd
cd C:\xampp\htdocs\biblioteca
C:\xampp\mysql\bin\mysql.exe -u root -p < install_simple.sql
```

Cuando pida password, ingresa tu password de MySQL.

---

## ⚠️ Si ya existe la base de datos

El script eliminará las tablas existentes y las recreará limpias. Si tienes datos que deseas conservar:

1. **Haz backup primero**:
   ```bash
   mysqldump -u root -p biblioteca_digital > backup.sql
   ```

2. O renombra la base de datos existente en phpMyAdmin

---

## 🐛 Solución de Errores Comunes

### Error: "Access denied"
- Verifica tu usuario y password de MySQL
- En phpMyAdmin verás el usuario actual arriba a la derecha

### Error: "Database exists"
- Es normal, el script usa `CREATE DATABASE IF NOT EXISTS`
- Continuará con la instalación

### Error: "Table 'usuarios' doesn't exist" después de instalar
- El script no se ejecutó correctamente
- Verifica en phpMyAdmin que las tablas se crearon
- Intenta ejecutar el script nuevamente

### Error: "Can't connect to MySQL server"
- Verifica que MySQL/MAMP esté corriendo
- En MAMP: botón "Start Servers"
- En otros: `sudo systemctl start mysql`

---

## ✅ Verificación Final

Ejecuta esta consulta en phpMyAdmin:

```sql
USE biblioteca_digital;
SELECT * FROM usuarios WHERE id = 1;
```

Deberías ver:
- **id**: 1
- **nombre**: Administrador
- **email**: admin@biblioteca.com
- **rol**: administrador

---

## 🎉 ¡Listo!

Tu sistema está instalado y listo para usar.

**Credenciales:**
- Email: `admin@biblioteca.com`
- Password: `admin123` (cambiar inmediatamente)

**Siguiente paso:**
- Iniciar sesión y cambiar password
- Crear más usuarios si es necesario
- Configurar cuotas de almacenamiento

---

## 📝 Archivos Disponibles

1. **`install_simple.sql`** ← Usar este (más compatible)
2. **`install_remote.sql`** - Versión completa con triggers y procedimientos
3. **`INSTALACION_REMOTA.md`** - Guía detallada para servidores

**¿Cuál usar?**
- Empieza con `install_simple.sql`
- Si necesitas características avanzadas, usa `install_remote.sql`

---

## 💡 Consejos

- Usa siempre phpMyAdmin para importar, es más fácil
- Haz backup antes de cualquier cambio importante
- Cambia las credenciales por defecto
- Configura PHP.ini para archivos grandes (ver INSTALACION_REMOTA.md)
