# 📚 Manual del Sistema de Biblioteca Digital

## 🎯 Índice

1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Gestión de Usuarios](#gestión-de-usuarios)
4. [Sistema de Archivos](#sistema-de-archivos)
5. [Sistema de Compartición](#sistema-de-compartición)
6. [Gestión de Grupos](#gestión-de-grupos)
7. [Control de Almacenamiento](#control-de-almacenamiento)
8. [Vista Previa de Archivos](#vista-previa-de-archivos)
9. [Seguridad](#seguridad)
10. [Interfaz de Usuario](#interfaz-de-usuario)
11. [API y Endpoints](#api-y-endpoints)
12. [Instalación y Configuración](#instalación-y-configuración)

---

## 🌟 Introducción

El Sistema de Biblioteca Digital es una aplicación web completa desarrollada en PHP que permite la gestión, almacenamiento y compartición de documentos digitales. Inspirado en Google Drive, ofrece funcionalidades avanzadas de colaboración, control de acceso y gestión de archivos.

### ✨ Características Principales

- **🔐 Sistema de autenticación** con roles diferenciados
- **📁 Gestión completa de archivos** con vista previa
- **👥 Compartición avanzada** (usuarios, grupos, enlaces públicos)
- **💾 Control de cuotas** de almacenamiento
- **🎨 Interfaz moderna** con temas claro/oscuro
- **📱 Diseño responsivo** para todos los dispositivos
- **🔒 Seguridad robusta** con middlewares y validaciones

---

## 🏗️ Arquitectura del Sistema

### 📋 Estructura MVC

```
biblioteca/
├── app/
│   ├── Controllers/     # Controladores de lógica de negocio
│   ├── Models/         # Modelos de datos
│   ├── Views/          # Vistas y templates
│   ├── Repositories/   # Acceso a datos
│   ├── Services/       # Servicios del sistema
│   ├── Middlewares/    # Middlewares de seguridad
│   ├── Policies/       # Políticas de autorización
│   └── Helpers/        # Utilidades y helpers
├── public/             # Archivos públicos
├── storage/            # Almacenamiento de archivos
└── config/             # Configuraciones
```

### 🔧 Tecnologías Utilizadas

- **Backend**: PHP 8.1+ con arquitectura MVC
- **Base de Datos**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Estilos**: CSS Grid/Flexbox, Font Awesome
- **Seguridad**: Middlewares personalizados, CSRF protection
- **Servidor**: Apache/Nginx con MAMP

---

## 👥 Gestión de Usuarios

### 🎭 Sistema de Roles

#### **Roles del Sistema**
1. **👑 Administrador**
   - Acceso completo al sistema
   - Gestión de usuarios y grupos
   - Configuración de cuotas
   - Auditoría del sistema

2. **✏️ Usuario Editor**
   - Control de su propio drive
   - Compartición de documentos
   - Gestión de carpetas personales

#### **Roles de Compartición**
1. **👑 Propietario** - Control total del archivo/carpeta
2. **✏️ Editor** - Puede ver y modificar contenido
3. **💬 Comentarista** - Puede ver y comentar
4. **👁️ Lector** - Solo visualización

### 🛠️ Funcionalidades de Administración

#### **Crear Usuario**
```php
// Endpoint: POST /admin/users/create
$userData = [
    'email' => 'usuario@ejemplo.com',
    'nombre' => 'Juan',
    'apellidos' => 'Pérez',
    'rol' => 'usuario_editor',
    'departamento' => 'Desarrollo',
    'cuota_almacenamiento' => 2147483648, // 2GB en bytes
    'password' => 'contraseña_segura'
];
```

#### **Gestión de Usuarios**
- ✅ **Crear** nuevos usuarios con roles específicos
- ✏️ **Editar** información personal y permisos
- 🔄 **Activar/Desactivar** cuentas de usuario
- 🗑️ **Eliminar** usuarios (con confirmación)
- 📊 **Auditoría** de cambios de roles

### 🔍 Búsqueda de Usuarios
- **Búsqueda en tiempo real** por nombre o email
- **Filtros** por rol y estado
- **Paginación** para grandes volúmenes de datos

---

## 📁 Sistema de Archivos

### 📤 Subida de Archivos

#### **Características**
- **Múltiples archivos** simultáneos
- **Validación de cuota** antes de subir
- **Detección automática** de tipo MIME
- **Generación de hash** único para almacenamiento
- **Actualización en tiempo real** del uso de almacenamiento

#### **Proceso de Subida**
```javascript
// Frontend: Subida con validación
const formData = new FormData();
formData.append('files[]', file);
formData.append('folder_id', currentFolderId);
formData.append('_csrf', csrfToken);

fetch('/drive/upload', {
    method: 'POST',
    body: formData,
    credentials: 'include'
});
```

### 📋 Gestión de Carpetas

#### **Operaciones Disponibles**
- **➕ Crear** carpetas con nombres personalizados
- **✏️ Renombrar** carpetas existentes
- **🎨 Personalizar** con etiquetas de color
- **🔄 Mover** carpetas entre ubicaciones
- **🗑️ Eliminar** con confirmación

#### **Navegación**
- **🍞 Breadcrumb** dinámico para ubicación
- **🌳 Vista de árbol** en sidebar
- **📊 Vista en columnas** tipo Miller
- **⚡ Navegación rápida** con atajos de teclado

### 🔍 Tipos de Vista

#### **1. 🔲 Vista de Cuadrícula**
```css
.grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
}
```
- Miniaturas de archivos
- Íconos específicos por tipo
- Información básica visible

#### **2. 📋 Vista de Lista**
```css
.list-view {
    display: grid;
    grid-template-columns: 1fr 150px 120px 100px 80px;
}
```
- Información detallada
- Ordenamiento por columnas
- Acciones rápidas

#### **3. 📊 Vista en Columnas**
- Navegación tipo Finder
- Vista jerárquica
- Selección y movimiento intuitivo

### 🎨 Íconos de Archivos

#### **Tipos Soportados**
```javascript
const fileIcons = {
    // Microsoft Office
    'word': 'fas fa-file-word',     // 🔵 Azul
    'excel': 'fas fa-file-excel',   // 🟢 Verde
    'powerpoint': 'fas fa-file-powerpoint', // 🔴 Rojo
    
    // Documentos
    'pdf': 'fas fa-file-pdf',       // 🔴 Rojo
    'txt': 'fas fa-file-alt',       // ⚫ Gris
    'csv': 'fas fa-file-csv',       // 🟢 Verde
    
    // Multimedia
    'image': 'fas fa-file-image',   // 🟣 Púrpura
    'video': 'fas fa-file-video',   // 🟠 Naranja
    'audio': 'fas fa-file-audio',   // 🟢 Verde claro
    
    // Otros
    'archive': 'fas fa-file-archive', // 🟡 Amarillo
    'code': 'fas fa-file-code',     // ⚫ Gris
};
```

---

## 🤝 Sistema de Compartición

### 🎯 Tipos de Compartición

#### **1. 👤 Compartir con Usuarios Específicos**
```php
// Compartir archivo con usuario por email
POST /sharing/share-with-users
{
    "resource_id": 123,
    "resource_type": "archivo",
    "emails": ["usuario1@ejemplo.com", "usuario2@ejemplo.com"],
    "role": "editor",
    "expiration_date": "2024-12-31"
}
```

**Características:**
- ✉️ **Búsqueda por email** en tiempo real
- 🎭 **Roles específicos** por usuario
- ⏰ **Fecha de expiración** opcional
- 📧 **Notificaciones automáticas**

#### **2. 👥 Compartir con Grupos**
```php
// Compartir con grupo completo
POST /sharing/share-with-group
{
    "resource_id": 123,
    "resource_type": "carpeta",
    "group_id": 5,
    "role": "lector"
}
```

**Ventajas:**
- 🚀 **Compartición masiva** eficiente
- 🔄 **Gestión centralizada** de permisos
- ➕ **Nuevos miembros** heredan permisos automáticamente

#### **3. 🔗 Enlaces Públicos**
```php
// Generar enlace público
POST /sharing/create-link
{
    "archivo_id": 123,
    "expiracion": "2024-06-30 23:59:59",
    "limite_descargas": 100,
    "requiere_password": true,
    "password": "mi_password_seguro"
}
```

**Opciones Avanzadas:**
- 🔒 **Protección con contraseña**
- 📊 **Límite de descargas**
- ⏰ **Expiración automática**
- 📈 **Contador de accesos**

### 🔐 Control de Acceso

#### **Validación de Permisos**
```php
public function canUserAccessResource($userId, $resourceId, $resourceType) {
    // 1. Verificar si es propietario
    if ($this->isOwner($userId, $resourceId, $resourceType)) {
        return true;
    }
    
    // 2. Verificar permisos directos
    if ($this->hasDirectPermission($userId, $resourceId, $resourceType)) {
        return true;
    }
    
    // 3. Verificar permisos por grupo
    if ($this->hasGroupPermission($userId, $resourceId, $resourceType)) {
        return true;
    }
    
    return false;
}
```

### 📤 Compartido Conmigo

#### **Vista Especializada**
- 📋 **Lista de archivos compartidos** por otros usuarios
- 👤 **Información del propietario** visible
- 📅 **Fecha de compartición** y último acceso
- 🔍 **Filtros** por tipo y propietario
- 🎨 **Todas las vistas** disponibles (cuadrícula, lista, columnas)

#### **Indicadores Visuales**
```css
.shared-indicator {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #007acc;
    border-radius: 50%;
    width: 16px;
    height: 16px;
}
```

---

## 👥 Gestión de Grupos

### ➕ Creación de Grupos

#### **Formulario de Grupo**
```html
<form id="groupForm">
    <input name="nombre" placeholder="Nombre del grupo" required>
    <textarea name="descripcion" placeholder="Descripción opcional"></textarea>
    <div id="membersContainer">
        <!-- Miembros seleccionados -->
    </div>
</form>
```

#### **Selección de Miembros**
- 🔍 **Búsqueda en tiempo real** de usuarios
- ➕ **Agregar miembros** con un clic
- ❌ **Remover miembros** fácilmente
- 👥 **Vista previa** de miembros seleccionados

### 🛠️ Administración de Grupos

#### **Operaciones CRUD**
```php
// Crear grupo
POST /admin/groups/create
{
    "nombre": "Equipo Desarrollo",
    "descripcion": "Desarrolladores del proyecto",
    "members": [1, 2, 3, 4]
}

// Actualizar grupo
POST /admin/groups/update
{
    "id": 5,
    "nombre": "Nuevo nombre",
    "descripcion": "Nueva descripción"
}

// Gestión de miembros
POST /admin/groups/add-member
POST /admin/groups/remove-member
```

### 📊 Información de Grupos
- 👥 **Contador de miembros** en tiempo real
- 📅 **Fecha de creación** y última modificación
- 👤 **Lista de miembros** con roles
- 📈 **Estadísticas de uso**

---

## 💾 Control de Almacenamiento

### 📊 Sistema de Cuotas

#### **Asignación de Cuotas**
```php
// Configuración por usuario
$quotaConfig = [
    'cuota_almacenamiento' => 2147483648, // 2GB
    'almacenamiento_usado' => 0,
    'fecha_asignacion' => date('Y-m-d H:i:s')
];
```

#### **Unidades Soportadas**
- **MB** (Megabytes) - 1,048,576 bytes
- **GB** (Gigabytes) - 1,073,741,824 bytes  
- **TB** (Terabytes) - 1,099,511,627,776 bytes

### 📈 Monitoreo de Uso

#### **Indicador Visual**
```html
<div class="storage-quota-sidebar">
    <div class="quota-header-sidebar">
        <i class="fas fa-chart-pie"></i>
        <span>Almacenamiento</span>
    </div>
    <div class="quota-bar-sidebar">
        <div class="quota-fill-sidebar normal" style="width: 45%"></div>
    </div>
    <div class="quota-text-sidebar">
        <div>450.2 MB / 1 GB</div>
        <div>45% usado</div>
        <div>573.8 MB libre</div>
    </div>
</div>
```

#### **Estados de Alerta**
- 🟢 **Normal (0-79%)** - Verde
- 🟡 **Advertencia (80-94%)** - Amarillo
- 🔴 **Crítico (95-100%)** - Rojo con animación

#### **Notificaciones Automáticas**
```javascript
function showQuotaWarning(percentage, availableFormatted) {
    if (percentage >= 90 && !sessionStorage.getItem('quota-warning-shown')) {
        // Mostrar notificación una vez por sesión
        displayWarningNotification();
        sessionStorage.setItem('quota-warning-shown', 'true');
    }
}
```

### 🔄 Recálculo Automático

#### **Sincronización de Datos**
```php
public function recalcularUsoAlmacenamiento($userId) {
    // Sumar tamaños reales de archivos
    $stmt = $this->db->prepare('
        SELECT COALESCE(SUM(tamaño), 0) as uso_real 
        FROM archivos 
        WHERE propietario_id = ? AND tamaño IS NOT NULL
    ');
    
    $stmt->execute([$userId]);
    $usoReal = $stmt->fetch()['uso_real'];
    
    // Actualizar si hay diferencia
    if ($usoActual !== $usoReal) {
        $this->updateStorageUsage($userId, $usoReal);
    }
}
```

### 🚫 Validación de Subidas

#### **Control de Cuota**
```php
public function validateUpload($userId, $fileSize) {
    $user = $this->getUserStorageInfo($userId);
    
    if (($user['almacenamiento_usado'] + $fileSize) > $user['cuota_almacenamiento']) {
        return [
            'error' => 'Cuota de almacenamiento excedida',
            'available' => $user['cuota_almacenamiento'] - $user['almacenamiento_usado'],
            'required' => $fileSize
        ];
    }
    
    return ['success' => true];
}
```

---

## 👁️ Vista Previa de Archivos

### 🖼️ Panel de Vista Previa

#### **Activación**
- **👆 Un clic** en cualquier archivo abre vista previa
- **👆👆 Doble clic** descarga el archivo
- **⌨️ Escape** cierra la vista previa

#### **Contenido Soportado**
```javascript
function generatePreviewContent(file) {
    const mimeType = file.tipo_mime || '';
    
    if (mimeType.startsWith('image/')) {
        return `<img src="/drive/preview?id=${file.id}" alt="${file.nombre}">`;
    }
    
    if (mimeType === 'application/pdf') {
        return `<iframe src="/drive/preview?id=${file.id}#toolbar=0"></iframe>`;
    }
    
    if (mimeType.startsWith('text/')) {
        return `<iframe src="/drive/preview?id=${file.id}"></iframe>`;
    }
    
    return generateFileInfo(file);
}
```

### 🔍 Modal Expandido

#### **Funcionalidades**
- **🖼️ Imágenes** en tamaño completo
- **📄 PDFs** con navegación completa
- **📝 Archivos de texto** con formato preservado
- **ℹ️ Información detallada** para otros tipos

#### **Controles**
```html
<div class="preview-modal-footer">
    <button class="btn btn-secondary" onclick="closePreviewModal()">
        <i class="fas fa-times"></i> Cerrar
    </button>
    <button class="btn btn-primary" onclick="downloadFromModal()">
        <i class="fas fa-download"></i> Descargar
    </button>
</div>
```

### 🛠️ Sistema de Fallback

#### **Para PDFs Problemáticos**
```javascript
function showPDFError() {
    return `
        <div class="file-info-large">
            <h3>Vista previa de PDF no disponible</h3>
            <div class="fallback-options">
                <button onclick="window.open('${pdfUrl}', '_blank')">
                    Abrir en nueva ventana
                </button>
                <button onclick="downloadFile(${file.id}, '${file.nombre}')">
                    Descargar PDF
                </button>
            </div>
        </div>
    `;
}
```

#### **Timeout y Error Handling**
- ⏱️ **Timeout de 5 segundos** para PDFs
- 🔄 **Fallback automático** si no carga
- 📊 **Logs de debug** para diagnóstico

---

## 🔒 Seguridad

### 🛡️ Middlewares de Seguridad

#### **1. Autenticación (AuthMiddleware)**
```php
public function handle(Closure $next) {
    if (!Session::has('user_id')) {
        return Response::json(['error' => 'No autorizado'], 401);
    }
    return $next();
}
```

#### **2. Protección CSRF (CsrfMiddleware)**
```php
public function handle(Closure $next) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            return Response::json(['error' => 'Token CSRF inválido'], 403);
        }
    }
    return $next();
}
```

#### **3. Rate Limiting (RateLimitMiddleware)**
```php
public function handle(Closure $next) {
    $key = $this->getUserKey();
    $attempts = $this->getAttempts($key);
    
    if ($attempts > $this->maxAttempts) {
        return Response::json(['error' => 'Demasiados intentos'], 429);
    }
    
    return $next();
}
```

#### **4. Headers de Seguridad (SecurityHeadersMiddleware)**
```php
public function handle(Closure $next) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    return $next();
}
```

### 🔐 Gestión de Contraseñas

#### **Hashing Seguro**
```php
// Usando ARGON2ID para máxima seguridad
$hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

// Verificación
if (password_verify($inputPassword, $hashedPassword)) {
    // Contraseña válida
}
```

### 🔍 Validación de Acceso

#### **Verificación de Permisos**
```php
public function checkFileAccess($userId, $fileId) {
    // 1. Verificar propiedad
    if ($this->isFileOwner($userId, $fileId)) {
        return true;
    }
    
    // 2. Verificar permisos compartidos
    if ($this->hasSharedAccess($userId, $fileId)) {
        return true;
    }
    
    // 3. Verificar enlaces públicos válidos
    if ($this->hasPublicLinkAccess($fileId)) {
        return true;
    }
    
    return false;
}
```

---

## 🎨 Interfaz de Usuario

### 🌓 Sistema de Temas

#### **Tema Oscuro (Predeterminado)**
```css
:root {
    --bg-primary: #1e1e1e;
    --bg-secondary: #2d2d30;
    --text-primary: #ffffff;
    --text-secondary: #cccccc;
    --accent: #007acc;
}
```

#### **Tema Claro**
```css
body.light-theme {
    --bg-primary: #ffffff;
    --bg-secondary: #f7f7f9;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --accent: #0e7490;
}
```

### 📱 Diseño Responsivo

#### **Breakpoints**
```css
/* Mobile */
@media (max-width: 768px) {
    .main { flex-direction: column; }
    .sidebar { width: 100%; }
}

/* Tablet */
@media (max-width: 1024px) {
    .grid-view { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
}

/* Desktop */
@media (min-width: 1025px) {
    .grid-view { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
}
```

### ⌨️ Atajos de Teclado

#### **Navegación**
- **Escape** - Cerrar modales/paneles
- **Ctrl/Cmd + A** - Seleccionar todo
- **Delete** - Eliminar seleccionados
- **F2** - Renombrar elemento
- **Ctrl/Cmd + C** - Copiar
- **Ctrl/Cmd + V** - Pegar

### 🔔 Sistema de Notificaciones

#### **Tipos de Notificaciones**
```javascript
function showAlert(message, type) {
    const alertTypes = {
        'success': { icon: 'fas fa-check', color: '#28a745' },
        'error': { icon: 'fas fa-exclamation-triangle', color: '#dc3545' },
        'warning': { icon: 'fas fa-exclamation', color: '#ffc107' },
        'info': { icon: 'fas fa-info', color: '#17a2b8' }
    };
    
    // Mostrar notificación temporal
}
```

---

## 🔌 API y Endpoints

### 👤 Gestión de Usuarios

#### **Autenticación**
```http
POST /auth/login
Content-Type: application/x-www-form-urlencoded

email=usuario@ejemplo.com&password=mi_password&_csrf=token
```

```http
POST /auth/logout
```

#### **Administración de Usuarios**
```http
GET /admin/users/api
POST /admin/users/create
POST /admin/users/update
POST /admin/users/delete
POST /admin/users/toggle-status
GET /admin/users/search?q=nombre
```

### 📁 Gestión de Archivos

#### **Operaciones de Archivos**
```http
GET /drive/api/list?folder=0
POST /drive/upload
GET /drive/download?id=123
GET /drive/preview?id=123
GET /drive/file-info?id=123
```

#### **Operaciones de Carpetas**
```http
POST /drive/create-folder
POST /drive/rename-folder
POST /drive/move-items
DELETE /drive/delete-items
```

### 🤝 Sistema de Compartición

#### **Compartir Recursos**
```http
POST /sharing/share-with-users
POST /sharing/share-with-group
POST /sharing/create-link
GET /sharing/shared-with-me
```

#### **Enlaces Públicos**
```http
GET /s/{token}
POST /s/{token} (para enlaces protegidos con contraseña)
```

### 👥 Gestión de Grupos

#### **CRUD de Grupos**
```http
GET /admin/groups/api
POST /admin/groups/create
POST /admin/groups/update
POST /admin/groups/delete
GET /admin/groups/members?id=5
POST /admin/groups/add-member
POST /admin/groups/remove-member
```

### 💾 Control de Almacenamiento

#### **Información de Cuota**
```http
GET /drive/storage-quota
```

**Respuesta:**
```json
{
    "quota": 2147483648,
    "used": 1073741824,
    "available": 1073741824,
    "percentage": 50,
    "quota_formatted": "2 GB",
    "used_formatted": "1 GB",
    "available_formatted": "1 GB"
}
```

---

## ⚙️ Instalación y Configuración

### 📋 Requisitos del Sistema

#### **Servidor**
- **PHP**: 8.1 o superior
- **MySQL**: 8.0 o superior
- **Apache/Nginx**: Con mod_rewrite habilitado
- **Extensiones PHP**:
  - `pdo_mysql`
  - `fileinfo`
  - `mbstring`
  - `json`
  - `session`

#### **Cliente**
- **Navegador moderno** con soporte para ES6+
- **JavaScript habilitado**
- **Cookies habilitadas**

### 🛠️ Proceso de Instalación

#### **1. Clonar Repositorio**
```bash
git clone https://github.com/tu-usuario/biblioteca-digital.git
cd biblioteca-digital
```

#### **2. Configurar Base de Datos**
```sql
-- Crear base de datos
CREATE DATABASE biblioteca_digital;

-- Ejecutar migraciones
mysql -u root -p biblioteca_digital < biblioteca_digital.sql
mysql -u root -p biblioteca_digital < migration_labels.sql
mysql -u root -p biblioteca_digital < migration_user_settings.sql
mysql -u root -p biblioteca_digital < migration_sharing_tables.sql
```

#### **3. Configurar Aplicación**
```php
// config/database.php
return [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'biblioteca_digital',
    'username' => 'tu_usuario',
    'password' => 'tu_password'
];
```

#### **4. Configurar Permisos**
```bash
# Permisos de escritura para storage
chmod -R 755 storage/
chown -R www-data:www-data storage/

# Permisos de lectura para public
chmod -R 644 public/
```

#### **5. Configurar Servidor Web**

**Apache (.htaccess)**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php [QSA,L]
```

**Nginx**
```nginx
location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### 🔧 Configuración Avanzada

#### **Variables de Entorno**
```php
// config/config.php
return [
    'env' => 'production', // local, development, production
    'debug' => false,
    'max_upload_size' => '100M',
    'session_lifetime' => 120, // minutos
    'default_quota' => 1073741824, // 1GB
    'auth' => [
        'plaintext_passwords' => false // Solo para desarrollo
    ]
];
```

#### **Configuración de Seguridad**
```php
// Configurar headers de seguridad
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'self\'');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
```

### 📊 Monitoreo y Logs

#### **Logs del Sistema**
```php
// Configurar logs
error_log("Error en archivo: " . $e->getMessage());
error_log("Usuario {$userId} subió archivo: {$fileName}");
```

#### **Métricas Importantes**
- 📈 **Uso de almacenamiento** por usuario
- 👥 **Usuarios activos** por día/mes
- 📁 **Archivos subidos** por período
- 🔗 **Enlaces compartidos** activos
- ⚡ **Tiempo de respuesta** promedio

---

## 🎯 Casos de Uso Comunes

### 📚 Biblioteca Académica

#### **Configuración Recomendada**
- **Roles**: Administrador, Profesor, Estudiante
- **Cuotas**: 
  - Profesores: 5GB
  - Estudiantes: 1GB
- **Grupos**: Por materia/curso
- **Compartición**: Principalmente lectura para estudiantes

#### **Flujo Típico**
1. **Profesor** sube material de clase
2. **Comparte** con grupo de estudiantes (rol: lector)
3. **Estudiantes** acceden y descargan material
4. **Administrador** monitorea uso y gestiona cuotas

### 🏢 Empresa Corporativa

#### **Configuración Recomendada**
- **Roles**: Admin, Gerente, Empleado
- **Cuotas**: Según departamento
- **Grupos**: Por proyecto/departamento
- **Compartición**: Colaborativa con roles de editor

#### **Flujo Típico**
1. **Empleados** suben documentos de proyecto
2. **Comparten** con equipo (rol: editor)
3. **Colaboran** en tiempo real
4. **Gerente** supervisa y aprueba

### 🏥 Centro Médico

#### **Configuración Recomendada**
- **Roles**: Admin, Doctor, Enfermero, Recepcionista
- **Cuotas**: Altas para imágenes médicas
- **Grupos**: Por especialidad
- **Compartición**: Controlada y con expiración

#### **Flujo Típico**
1. **Recepcionista** digitaliza documentos
2. **Comparte** con doctor asignado
3. **Doctor** revisa y comparte con especialista
4. **Sistema** expira automáticamente accesos

---

## 🔮 Funcionalidades Futuras

### 🚀 Roadmap de Desarrollo

#### **Versión 2.0**
- 🔄 **Sincronización offline** con PWA
- 🎥 **Videoconferencias** integradas
- 🤖 **OCR automático** para documentos escaneados
- 📱 **App móvil** nativa

#### **Versión 3.0**
- 🧠 **IA para clasificación** automática
- 🔍 **Búsqueda semántica** avanzada
- 🌐 **Integración con APIs** externas
- 📊 **Analytics avanzados**

### 🔧 Mejoras Técnicas

#### **Performance**
- ⚡ **Cache Redis** para sesiones
- 🗜️ **Compresión** de archivos automática
- 📦 **CDN** para archivos estáticos
- 🔄 **Lazy loading** para listas grandes

#### **Seguridad**
- 🔐 **2FA** obligatorio para admins
- 🛡️ **Encriptación** de archivos sensibles
- 📋 **Auditoría** completa de acciones
- 🔒 **Backup automático** cifrado

---

## 📞 Soporte y Mantenimiento

### 🆘 Resolución de Problemas

#### **Problemas Comunes**

1. **Archivos no suben**
   - ✅ Verificar cuota disponible
   - ✅ Revisar permisos de carpeta storage/
   - ✅ Comprobar tamaño máximo de PHP

2. **Vista previa no funciona**
   - ✅ Verificar extensión fileinfo
   - ✅ Revisar permisos de archivo
   - ✅ Comprobar MIME type

3. **Usuarios no pueden acceder**
   - ✅ Verificar estado activo
   - ✅ Revisar permisos compartidos
   - ✅ Comprobar expiración de enlaces

### 📧 Contacto y Soporte

- **📧 Email**: soporte@biblioteca-digital.com
- **🐛 Issues**: GitHub Issues
- **📖 Documentación**: Wiki del proyecto
- **💬 Chat**: Discord/Slack del equipo

---

## 📄 Licencia y Créditos

### ⚖️ Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo `LICENSE` para más detalles.

### 🙏 Agradecimientos

- **Font Awesome** - Íconos del sistema
- **PHP Community** - Frameworks y librerías
- **MySQL** - Sistema de base de datos
- **Apache/Nginx** - Servidores web

### 👥 Contribuidores

- **Desarrollador Principal**: [Tu Nombre]
- **Diseño UI/UX**: [Diseñador]
- **Testing**: [Tester]
- **Documentación**: [Technical Writer]

---

**📅 Última actualización**: Diciembre 2024  
**🔢 Versión del documento**: 1.0  
**📝 Estado**: Completo y actualizado

---

*Este manual está en constante evolución. Para sugerencias o correcciones, por favor abre un issue en el repositorio del proyecto.*
