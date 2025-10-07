# Configuración para Archivos Grandes

## Cambios Realizados

Se han eliminado todas las restricciones de extensiones y tamaño de archivos para permitir la subida de archivos grandes como .dmg de 200MB o más.

### Archivos Modificados:

1. **`.htaccess` (raíz y public/)**
   - Configuración de límites PHP para archivos de hasta 2GB
   - Tiempo de ejecución extendido a 300 segundos
   - Memoria aumentada a 512MB

2. **`config/php_limits.php`**
   - Configuración programática de límites PHP
   - Se incluye automáticamente en todos los scripts

3. **`app/Controllers/DriveController.php`**
   - Inclusión de configuración de límites
   - Logging mejorado para archivos grandes
   - Sin restricciones de extensiones

4. **`app/Views/drive/dashboard.php`**
   - Soporte para iconos de archivos .dmg, .iso, .exe, etc.
   - Indicadores de progreso mejorados

### Extensiones Soportadas:

- **Archivos de disco**: .dmg, .iso, .img
- **Ejecutables**: .exe, .msi, .deb, .rpm, .pkg, .app
- **Comprimidos**: .zip, .rar, .7z, .tar, .gz, .bz2
- **Y todas las demás extensiones** (sin restricciones)

### Límites Configurados:

- **Tamaño máximo de archivo**: 2GB
- **Tamaño máximo POST**: 2GB
- **Tiempo de ejecución**: 300 segundos
- **Memoria**: 512MB
- **Archivos simultáneos**: 20

### Notas Importantes:

1. **Reiniciar servidor**: Es posible que necesites reiniciar MAMP para que los cambios de .htaccess tomen efecto.

2. **Configuración del servidor**: Si los límites persisten, verifica la configuración de PHP en MAMP:
   - Ve a MAMP > Preferences > PHP
   - Asegúrate de que los límites estén configurados correctamente

3. **Logs**: Los archivos grandes se registran en los logs para debugging.

4. **Cuota de usuario**: El sistema sigue respetando la cuota de almacenamiento del usuario.

### Verificación:

Para verificar que los cambios funcionan:
1. Intenta subir un archivo .dmg de 200MB
2. Verifica que aparezca el indicador de progreso
3. Confirma que el archivo se suba correctamente

Si persisten problemas, revisa los logs de error de PHP y MAMP.
