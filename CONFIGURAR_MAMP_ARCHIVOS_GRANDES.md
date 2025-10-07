# Configurar MAMP para Archivos Grandes (200MB+)

## ⚠️ IMPORTANTE: Los archivos .htaccess han sido eliminados

Los archivos `.htaccess` con directivas `php_value` causaban error 500. Han sido eliminados.

## Configuración Manual en MAMP

Para permitir archivos grandes como .dmg de 200MB, debes configurar MAMP manualmente:

### Opción 1: Usando la Interfaz de MAMP PRO (Recomendado)

1. Abre **MAMP PRO**
2. Ve a **PHP** en el menú lateral
3. Busca la sección **Limits** o **Límites**
4. Configura:
   - `upload_max_filesize`: **2G**
   - `post_max_size`: **2G**
   - `max_execution_time`: **300**
   - `memory_limit`: **512M**
5. Guarda y reinicia MAMP

### Opción 2: Editando php.ini Directamente

1. Encuentra el archivo `php.ini`:
   - En MAMP: `/Applications/MAMP/bin/php/php[VERSION]/conf/php.ini`
   - Ejemplo: `/Applications/MAMP/bin/php/php8.0.8/conf/php.ini`

2. Abre el archivo con un editor de texto

3. Busca y modifica estas líneas:
```ini
upload_max_filesize = 2G
post_max_size = 2G
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
max_file_uploads = 20
```

4. Guarda el archivo

5. **Reinicia MAMP** completamente

### Verificar la Configuración

1. Crea un archivo `info.php` en `/Applications/MAMP/htdocs/biblioteca/public/`:
```php
<?php phpinfo(); ?>
```

2. Visita: `http://localhost:8888/biblioteca/public/info.php`

3. Busca:
   - `upload_max_filesize`
   - `post_max_size`
   - `memory_limit`

4. **Elimina `info.php` después de verificar** (por seguridad)

### Solución de Problemas

Si aún no puedes subir archivos grandes:

1. **Verifica los logs de MAMP**:
   - `/Applications/MAMP/logs/php_error.log`
   - `/Applications/MAMP/logs/apache_error.log`

2. **Aumenta el timeout de Apache** (si es necesario):
   - En MAMP, edita `httpd.conf`
   - Añade: `Timeout 300`

3. **Verifica el espacio en disco**:
   - Asegúrate de tener suficiente espacio libre

### Cambios en el Código

El código ya está preparado para archivos grandes:
- ✅ Sin restricciones de extensiones
- ✅ Soporte para .dmg, .iso, .exe, etc.
- ✅ Indicadores de progreso individuales
- ✅ Manejo mejorado de errores

### Notas Finales

- Los límites de PHP no se pueden cambiar con `.htaccess` en MAMP con `mod_php`
- Deben configurarse en `php.ini` directamente
- Después de cambiar `php.ini`, **siempre reinicia MAMP**
- El sistema respeta la cuota de almacenamiento del usuario
