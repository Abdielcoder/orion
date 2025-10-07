# 🎯 INSTALACIÓN - ELIGE TU MÉTODO

## ⚡ Método 1: Terminal - El Más Rápido

### 1. Abre Terminal (`Cmd + Espacio` → "Terminal")

### 2. Navega a la carpeta:
```bash
cd /Applications/MAMP/htdocs/biblioteca
```

### 3. Ejecuta UNO de estos comandos:

#### Opción A: Con contraseña vacía
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock < install_simple.sql
```

#### Opción B: Con contraseña "root"
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock < install_simple.sql
```

#### Opción C: Te pedirá la contraseña
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -p --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock < install_simple.sql
```
(Ingresa la contraseña cuando la pida)

### 4. Verificar instalación:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -p --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock -e "USE biblioteca_digital; SELECT * FROM usuarios WHERE id=1;"
```

---

## 🌐 Método 2: Desde el Navegador (phpMyAdmin)

### 1. Abre phpMyAdmin:
```
http://localhost:8888/phpMyAdmin
```

o

```
http://localhost/phpmyadmin
```

### 2. Inicia sesión:
- **Usuario**: `root`
- **Contraseña**: Déjala vacía o prueba `root`

### 3. Importar archivo:
1. Haz clic en **"Importar"** (pestaña superior)
2. Haz clic en **"Seleccionar archivo"**
3. Elige: `install_simple.sql`
4. Desplázate abajo y haz clic en **"Continuar"**
5. Espera el mensaje de éxito ✅

### 4. Verificar:
1. En el panel izquierdo, haz clic en `biblioteca_digital`
2. Deberías ver 7 tablas
3. Haz clic en `usuarios`
4. Verifica que existe el usuario administrador

---

## 💻 Método 3: Terminal Interactivo

### 1. Conectar a MySQL:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -p --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock
```

### 2. Una vez dentro (verás `mysql>`), pega estos comandos:

```sql
CREATE DATABASE IF NOT EXISTS biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_digital;
source install_simple.sql
```

### 3. Verificar:
```sql
SHOW TABLES;
SELECT * FROM usuarios WHERE id = 1;
```

### 4. Salir:
```sql
exit
```

---

## 🔑 ¿No Sabes la Contraseña de MySQL?

### Ver contraseña en MAMP:

1. Abre **MAMP**
2. En la ventana de inicio, verás:
   - **MySQL port**: 8889 (u otro)
   - **MySQL user**: root
   - **MySQL password**: (aquí estará la contraseña)

### O intenta estas contraseñas comunes:
- Vacía (sin contraseña)
- `root`
- `mamp`
- `MAMP`

---

## 📊 Tabla de Comandos Según tu MAMP

| Versión MAMP | Comando |
|--------------|---------|
| MAMP 6+ | `/Applications/MAMP/Library/bin/mysql80/bin/mysql` |
| MAMP 5 | `/Applications/MAMP/Library/bin/mysql/bin/mysql` |
| MAMP 4 | `/Applications/MAMP/Library/bin/mysql` |

---

## ✅ Verificar que Funciona

### Desde Terminal:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -p --port=8889 -e "
USE biblioteca_digital;
SELECT 'Tablas:' as Info, COUNT(*) as Total FROM information_schema.tables WHERE table_schema='biblioteca_digital'
UNION ALL
SELECT 'Usuario Admin:', COUNT(*) FROM usuarios WHERE id=1;
"
```

Deberías ver:
- **Tablas**: 7
- **Usuario Admin**: 1

### Desde Navegador:
```
http://localhost:8888/biblioteca/public/index.php/auth/login
```

**Credenciales:**
- Email: `admin@biblioteca.com`
- Password: `admin123`

---

## 🐛 Errores Comunes

### "Access denied"
**Solución**: Verifica la contraseña de MySQL en la ventana de inicio de MAMP

### "Can't connect to MySQL server"
**Solución**: 
1. Abre MAMP
2. Haz clic en "Start Servers"
3. Espera a que ambos círculos estén verdes

### "Command not found"
**Solución**: Usa la ruta completa del comando como se muestra arriba

### "No such file or directory"
**Solución**: 
```bash
cd /Applications/MAMP/htdocs/biblioteca
pwd  # Verifica que estás en la carpeta correcta
ls install_simple.sql  # Verifica que el archivo existe
```

---

## 🎉 ¡Listo!

Una vez instalado:

1. **Accede**: `http://localhost:8888/biblioteca/public/index.php/auth/login`
2. **Inicia sesión**: 
   - Email: `admin@biblioteca.com`
   - Password: `admin123`
3. **IMPORTANTE**: Cambia el password inmediatamente

---

## 📞 ¿Necesitas Ayuda?

Si ningún método funcionó, ejecuta esto y envía el resultado:

```bash
echo "=== Información del Sistema ==="
echo "MAMP corriendo:"
ps aux | grep -i mysql | grep -v grep
echo ""
echo "Puerto MySQL:"
lsof -i :8889 2>/dev/null || lsof -i :3306 2>/dev/null
echo ""
echo "Archivos SQL disponibles:"
ls -lh *.sql
echo ""
echo "MySQL version:"
/Applications/MAMP/Library/bin/mysql80/bin/mysql --version 2>/dev/null || echo "MySQL no encontrado en esta ruta"
```

Con esta información podré ayudarte mejor! 🚀
