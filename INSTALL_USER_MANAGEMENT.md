# Instalación del Sistema de Gestión de Usuarios

Este documento explica cómo instalar y configurar el nuevo sistema de gestión de usuarios con roles basados en Google Drive.

## Pasos de Instalación

### 1. Ejecutar la Migración de Base de Datos

Ejecuta el siguiente script SQL en tu base de datos MySQL:

```bash
mysql -u tu_usuario -p biblioteca_digital < migration_google_drive_roles.sql
```

O importa manualmente el archivo `migration_google_drive_roles.sql` usando phpMyAdmin o tu cliente MySQL preferido.

### 2. Verificar la Migración

Después de ejecutar la migración, verifica que:

- La tabla `usuarios` tenga los nuevos roles: `admin`, `owner`, `editor`, `commenter`, `viewer`
- Se hayan creado las tablas `roles_sistema` y `auditoria_roles`
- Los roles existentes se hayan actualizado correctamente

### 3. Acceder al Sistema de Gestión de Usuarios

1. Inicia sesión como administrador en el sistema
2. En el dashboard, verás un nuevo botón de engranaje (⚙️) en la esquina superior derecha
3. Haz clic en el botón para abrir el menú de administración
4. Selecciona "Gestión de Usuarios" para acceder al panel de administración

## Roles y Permisos

### Roles Disponibles

1. **Administrador** - Control total del sistema
   - Gestionar usuarios, configuraciones y todos los recursos
   - Acceso completo a todas las funcionalidades

2. **Propietario (Owner)** - Control total de archivos/carpetas
   - Ver, comentar, editar, eliminar y transferir propiedad
   - Decidir quién tiene acceso y con qué permisos

3. **Editor** - Modificación de contenido
   - Ver y modificar archivos
   - Añadir, cambiar o borrar contenido
   - Agregar/eliminar archivos en carpetas

4. **Comentarista (Commenter)** - Solo comentarios
   - Ver archivos
   - Agregar comentarios y sugerencias
   - No puede modificar contenido directamente

5. **Lector/Visualizador (Viewer)** - Solo lectura
   - Solo puede ver archivos o carpetas
   - No puede comentar ni editar
   - Nivel de acceso más restrictivo

## Funcionalidades del Panel de Administración

### Gestión de Usuarios

- **Crear usuarios**: Agregar nuevos usuarios con rol específico
- **Editar usuarios**: Modificar información y cambiar roles
- **Activar/Desactivar**: Cambiar el estado de usuarios sin eliminarlos
- **Eliminar usuarios**: Remover usuarios del sistema (con confirmación)
- **Búsqueda y filtros**: Encontrar usuarios por nombre, email, rol o departamento

### Auditoría

- Todos los cambios de roles se registran en la tabla `auditoria_roles`
- Se guarda quién hizo el cambio, cuándo y por qué motivo

## Seguridad

- Solo los usuarios con rol `admin` pueden acceder al panel de gestión
- Se usa middleware `AdminMiddleware` para verificar permisos
- Todas las acciones están protegidas por CSRF tokens
- Las contraseñas se encriptan con Argon2ID

## Estructura de Archivos Creados/Modificados

### Nuevos Archivos
- `app/Controllers/AdminUsersController.php` - Controlador de gestión de usuarios
- `app/Views/admin/users.php` - Vista del panel de administración
- `app/Middlewares/AdminMiddleware.php` - Middleware de verificación de admin
- `migration_google_drive_roles.sql` - Script de migración de base de datos

### Archivos Modificados
- `app/Models/User.php` - Agregados roles y métodos de permisos
- `app/Repositories/UserRepository.php` - Nuevos métodos CRUD
- `app/Controllers/DriveController.php` - Incluye rol en dashboard
- `app/Views/drive/dashboard.php` - Menú de administración
- `public/index.php` - Nuevas rutas de administración

## Solución de Problemas

### Error: "Acceso denegado"
- Verifica que tu usuario tenga el rol `admin` en la base de datos
- Asegúrate de que la sesión esté activa y el rol se esté guardando correctamente

### Error: "Tabla no existe"
- Ejecuta la migración SQL completa
- Verifica que todas las tablas se hayan creado correctamente

### Error: "CSRF token inválido"
- Recarga la página para obtener un nuevo token
- Verifica que las cookies estén habilitadas

## Próximos Pasos

1. Crear usuarios de prueba con diferentes roles
2. Probar las funcionalidades de cada rol
3. Configurar permisos específicos por carpeta/archivo según sea necesario
4. Implementar notificaciones por email para cambios importantes

## Soporte

Si encuentras algún problema durante la instalación o uso del sistema, revisa:

1. Los logs del servidor web
2. Los logs de PHP
3. Los logs de MySQL
4. La consola del navegador para errores JavaScript

---

**Versión:** 1.0.0  
**Fecha:** Enero 2025  
**Compatibilidad:** PHP 8.0+, MySQL 8.0+
