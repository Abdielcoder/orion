# Solución al Error 500 en Enlaces Compartidos

## Problema Identificado

El error HTTP 500 ocurría al intentar abrir enlaces compartidos debido a una **incompatibilidad entre el código PHP y la estructura de la base de datos**.

### Causa Raíz

El archivo `ShareLinkRepository.php` estaba intentando insertar y leer datos usando nombres de columnas que no coincidían con la tabla `enlaces_compartidos` en la base de datos.

**Nombres incorrectos:**
- `contador_accesos` ❌ → Debería ser: `accesos_actuales` ✓
- `limite_descargas` ❌ → Debería ser: `limite_accesos` ✓
- `permisos` (JSON) ❌ → Debería usar columnas individuales ✓
- Faltaban columnas obligatorias: `recurso_tipo`, `propietario_id`, `nombre_recurso`

## Archivos Modificados

### 1. `/app/Repositories/ShareLinkRepository.php`

**Cambios realizados:**
- ✅ Actualizado método `create()` para insertar en todas las columnas requeridas
- ✅ Añadida lógica para obtener el nombre del recurso (archivo o carpeta)
- ✅ Añadida conversión de permisos array a columnas individuales
- ✅ Actualizado método `incrementAccess()` para usar `accesos_actuales`

### 2. `/app/Controllers/ShareController.php`

**Cambios realizados:**
- ✅ Actualizada verificación de límite de accesos para usar `limite_accesos` y `accesos_actuales`
- ✅ Actualizada lógica de autenticación para usar columnas `password` y `contraseña`
- ✅ Mejorado manejo de códigos de acceso y contraseñas hasheadas

### 3. `/app/Views/share/view.php`

**Cambios realizados:**
- ✅ Actualizada visualización de accesos para usar `accesos_actuales`

## Estructura de la Tabla `enlaces_compartidos`

```sql
CREATE TABLE `enlaces_compartidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `tipo` enum('archivo','carpeta') NOT NULL,
  `recurso_tipo` enum('archivo','carpeta') NOT NULL,
  `recurso_id` int NOT NULL,
  `creado_por` int NOT NULL,
  `propietario_id` int NOT NULL,
  `nombre_recurso` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nivel_acceso` enum('ver','descargar','editar') DEFAULT 'ver',
  `fecha_expiracion` datetime DEFAULT NULL,
  `limite_accesos` int DEFAULT NULL,
  `accesos_actuales` int DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `rol_acceso` enum('propietario','editor','comentarista','lector') DEFAULT 'lector',
  `requiere_autenticacion` tinyint(1) DEFAULT '0',
  `dominios_permitidos` text,
  `puede_descargar` tinyint(1) DEFAULT '1',
  `puede_imprimir` tinyint(1) DEFAULT '1',
  `puede_copiar` tinyint(1) DEFAULT '1',
  `notificar_accesos` tinyint(1) DEFAULT '0',
  `contraseña` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
)
```

## Cómo Desplegar la Solución

### Opción 1: Script Automático (Recomendado)

```bash
cd /Applications/MAMP/htdocs/biblioteca
./deploy_fix.sh
```

Este script:
1. ✅ Copia los archivos corregidos al servidor remoto
2. ✅ Establece los permisos correctos
3. ✅ Verifica que todo esté en orden

### Opción 2: Manual con SCP

```bash
# 1. Copiar ShareLinkRepository.php
scp -i orion.pem \
    app/Repositories/ShareLinkRepository.php \
    ubuntu@orion.rinorisk.com:/var/www/html/biblioteca/app/Repositories/

# 2. Copiar ShareController.php
scp -i orion.pem \
    app/Controllers/ShareController.php \
    ubuntu@orion.rinorisk.com:/var/www/html/biblioteca/app/Controllers/

# 3. Copiar view.php
scp -i orion.pem \
    app/Views/share/view.php \
    ubuntu@orion.rinorisk.com:/var/www/html/biblioteca/app/Views/share/

# 4. Establecer permisos
ssh -i orion.pem ubuntu@orion.rinorisk.com
cd /var/www/html/biblioteca
sudo chown -R www-data:www-data app/
sudo chmod -R 644 app/Repositories/*.php
sudo chmod -R 644 app/Controllers/*.php
sudo chmod -R 644 app/Views/**/*.php
exit
```

## Prueba de la Solución

### 1. Probar el enlace existente

El enlace que estaba dando error ahora debería funcionar:

```
http://orion.rinorisk.com/biblioteca/public/index.php/s/0eddd739b9fe0decb2c021fa98ed9e2a5412499036c166589b3ff2dae0b9fe02
```

### 2. Crear un nuevo enlace

1. Inicia sesión en la aplicación
2. Selecciona un archivo o carpeta
3. Haz clic en "Compartir"
4. Crea un enlace público
5. Copia el enlace generado
6. Ábrelo en una ventana de incógnito/privada

### 3. Verificar funcionalidad completa

- ✅ El enlace se abre sin error 500
- ✅ Se muestra la información del archivo
- ✅ El contador de accesos se incrementa
- ✅ Los permisos (descargar, imprimir, copiar) funcionan
- ✅ La vista previa funciona (para imágenes y PDFs)

## Verificación de Logs

Si necesitas revisar los logs del servidor para debugging:

```bash
# Conectarse al servidor
ssh -i orion.pem ubuntu@orion.rinorisk.com

# Ver logs de Apache
sudo tail -f /var/log/apache2/error.log

# Ver logs de PHP (si están configurados)
sudo tail -f /var/log/php/error.log

# Buscar errores relacionados con enlaces compartidos
sudo grep "enlaces_compartidos" /var/log/apache2/error.log
```

## Cambios en el Comportamiento

### Antes ❌
- Error 500 al abrir enlaces compartidos
- No se insertaban enlaces correctamente
- Faltaban datos en la base de datos

### Después ✅
- Los enlaces se abren correctamente
- Se guardan todos los datos necesarios
- Funciona el contador de accesos
- Los permisos se respetan correctamente
- Compatible con contraseñas y códigos de acceso

## Notas Importantes

1. **No se requieren cambios en la base de datos** - La estructura de la tabla ya era correcta
2. **Compatibilidad hacia atrás** - Los enlaces antiguos seguirán funcionando
3. **Seguridad** - El código mantiene todas las verificaciones de seguridad
4. **Sesiones** - La autenticación de enlaces protegidos funciona por 2 horas

## Soporte Adicional

Si encuentras algún problema:

1. Verifica los logs de Apache/PHP en el servidor
2. Revisa la consola del navegador (F12) para errores JavaScript
3. Comprueba que los permisos de archivos sean correctos (644 para PHP)
4. Asegúrate de que Apache tiene permisos de lectura en los archivos

## Resumen de la Solución

✅ **Problema:** Incompatibilidad entre código y base de datos  
✅ **Solución:** Actualizar el código para que coincida con la estructura real de la tabla  
✅ **Archivos modificados:** 3 archivos PHP  
✅ **Tiempo de despliegue:** < 5 minutos  
✅ **Requiere cambios en BD:** No  
✅ **Impacto:** Solo mejoras, sin efectos negativos  

---

**Fecha de implementación:** 15 de octubre de 2025  
**Versión:** 1.0.0


