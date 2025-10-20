# 🔧 Solución: Separación de Sesiones de Usuarios

## 🚨 Problema Identificado

El sistema estaba detectando **TODAS** las peticiones como si fueran de un iframe, por lo que siempre usaba el usuario administrador por defecto (`admin@biblioteca.com`) en lugar de respetar el login real.

### Logs que mostraban el problema:
```
DEBUG Auth - Usando usuario administrador por defecto para iframe: admin@biblioteca.com (ID: 1)
```

Esto ocurría en **TODAS** las peticiones, incluso cuando el usuario se autenticaba normalmente con `gerente.mkt@rinorisk.com`.

---

## ✅ Soluciones Implementadas

### 1. **Corregir Detección de Iframes** (`AuthMiddleware.php`)

**Antes (INCORRECTO):**
```php
$isIframe = (strpos($referer, 'localhost') !== false || 
            strpos($referer, '127.0.0.1') !== false ||
            strpos($referer, 'orion.rinorisk.com') !== false || // ❌ Esto detectaba TODO
            isset($_SERVER['HTTP_X_FRAME_OPTIONS']) ||
            !empty($_GET['iframe']) || 
            !empty($_POST['iframe']));
```

**Ahora (CORRECTO):**
```php
// Solo detectar iframe si hay parámetro explícito
$isIframe = (!empty($_GET['iframe']) || !empty($_POST['iframe']));
```

### 2. **Restaurar Configuración de Cookies** (`config/config.php`)

**Antes (Para iframes):**
```php
'cookie_httponly' => false,
'cookie_samesite' => 'None',
```

**Ahora (Para navegación normal):**
```php
'cookie_httponly' => true,
'cookie_samesite' => 'Lax',
```

### 3. **Agregar Logs de Debug** (`AuthMiddleware.php`)

```php
error_log("DEBUG Auth - URL: " . $_SERVER['REQUEST_URI']);
error_log("DEBUG Auth - Session ID: " . session_id());
error_log("DEBUG Auth - User ID from session: " . ($userId ?? 'NULL'));
error_log("DEBUG Auth - User Role from session: " . (Session::get('user_role') ?? 'NULL'));
error_log("DEBUG Auth - Is iframe: " . ($isIframe ? 'YES' : 'NO'));
```

### 4. **Página de Limpieza de Sesión** (`public/clear_session.php`)

Creada para limpiar completamente cookies y caché cuando hay problemas de sesión.

---

## 🧪 Pasos para Probar

### **Paso 1: Limpiar Sesión Actual**

Visita esta URL para limpiar completamente tu sesión:
```
http://orion.rinorisk.com/biblioteca/public/clear_session.php
```

O desde el navegador:
1. Abre las **Herramientas de Desarrollador** (F12)
2. Ve a la pestaña **Application** (Chrome) o **Almacenamiento** (Firefox)
3. Elimina todas las cookies de `orion.rinorisk.com`
4. Limpia **localStorage** y **sessionStorage**
5. Cierra todas las pestañas del sitio

### **Paso 2: Login con Administrador**

1. Ve a: `http://orion.rinorisk.com/biblioteca/public/index.php/auth/login`
2. Inicia sesión con:
   - **Email**: `admin@biblioteca.com`
   - **Password**: `123456` (o la que corresponda)
3. Deberías ver:
   - Panel de administración
   - Tus archivos y carpetas
   - Opción "Usuarios" en el menú

### **Paso 3: Cerrar Sesión**

1. Cierra sesión completamente
2. O visita: `http://orion.rinorisk.com/biblioteca/public/clear_session.php`

### **Paso 4: Login con Usuario Normal**

1. Ve a: `http://orion.rinorisk.com/biblioteca/public/index.php/auth/login`
2. Inicia sesión con:
   - **Email**: `gerente.mkt@rinorisk.com`
   - **Password**: `123456`
3. Deberías ver:
   - **SOLO** tus archivos y carpetas (no los del admin)
   - **SIN** acceso al panel de administración
   - **SIN** opción "Usuarios" en el menú

---

## 📊 Verificar Logs del Servidor

Para verificar que la autenticación funciona correctamente:

```bash
ssh -i orion.pem ubuntu@orion.rinorisk.com 'sudo tail -50 /var/log/apache2/error.log | grep "DEBUG Auth"'
```

**Logs esperados después del login con gerente.mkt@rinorisk.com:**
```
DEBUG Auth - URL: /biblioteca/public/index.php/drive
DEBUG Auth - Session ID: abc123xyz...
DEBUG Auth - User ID from session: 3
DEBUG Auth - User Role from session: usuario_editor
DEBUG Auth - Is iframe: NO
```

**Logs esperados después del login con admin@biblioteca.com:**
```
DEBUG Auth - URL: /biblioteca/public/index.php/drive
DEBUG Auth - Session ID: def456uvw...
DEBUG Auth - User ID from session: 1
DEBUG Auth - User Role from session: administrador
DEBUG Auth - Is iframe: NO
```

---

## 🎯 Uso de Iframes (Opcional)

Si necesitas usar la aplicación en un iframe **Y** quieres especificar qué usuario usar:

### **Iframe con Usuario Administrador:**
```html
<iframe src="http://orion.rinorisk.com/biblioteca/public/index.php/drive?iframe=1&user=admin@biblioteca.com"></iframe>
```

### **Iframe con Usuario Gerente:**
```html
<iframe src="http://orion.rinorisk.com/biblioteca/public/index.php/drive?iframe=1&user=gerente.mkt@rinorisk.com"></iframe>
```

### **Iframe con Usuario por ID:**
```html
<iframe src="http://orion.rinorisk.com/biblioteca/public/index.php/drive?iframe=1&user=3"></iframe>
```

**⚠️ IMPORTANTE:** 
- El parámetro `iframe=1` es **obligatorio** para activar el modo iframe
- Sin `iframe=1`, el sistema requerirá login normal
- Con `iframe=1`, el sistema NO respetará las sesiones normales del navegador

---

## 🔍 Solución de Problemas

### **Problema: Sigo viendo la sesión del administrador**

**Solución:**
1. Visita: `http://orion.rinorisk.com/biblioteca/public/clear_session.php`
2. Cierra **TODAS** las pestañas del sitio
3. Abre el navegador en modo **Incógnito/Privado**
4. Inicia sesión nuevamente

### **Problema: El login no me redirige al dashboard**

**Solución:**
1. Verifica que las cookies estén habilitadas en tu navegador
2. Verifica que no tengas bloqueadores de cookies activos
3. Revisa los logs del servidor para ver errores

### **Problema: Las cookies no se están guardando**

**Solución:**
1. Verifica la configuración de `SameSite` en `config/config.php`
2. Asegúrate de que `cookie_httponly` esté en `true`
3. Verifica que no haya errores en los logs de Apache

---

## 📝 Archivos Modificados

1. ✅ `app/Middlewares/AuthMiddleware.php` - Corregida detección de iframes
2. ✅ `config/config.php` - Restaurada configuración de cookies
3. ✅ `public/clear_session.php` - Nueva página para limpiar sesiones

---

## 🎉 Resultado Esperado

- ✅ **admin@biblioteca.com** ve solo su contenido y tiene acceso completo
- ✅ **gerente.mkt@rinorisk.com** ve solo su contenido y acceso limitado
- ✅ Las sesiones NO se mezclan entre usuarios
- ✅ Cada usuario mantiene su propia sesión independiente
- ✅ El modo iframe funciona cuando se especifica explícitamente

---

## 📞 Soporte

Si el problema persiste:
1. Revisa los logs: `/var/log/apache2/error.log`
2. Verifica la sesión actual con: `public/clear_session.php`
3. Prueba en modo incógnito para descartar problemas de caché


