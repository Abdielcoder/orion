-- ============================================
-- INSTALACIÓN SIMPLE - BIBLIOTECA DIGITAL
-- Versión: 1.0 - Compatibilidad Máxima
-- ============================================

-- PASO 1: Crear y usar la base de datos
CREATE DATABASE IF NOT EXISTS biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_digital;

-- PASO 2: Eliminar tablas si existen (instalación limpia)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS actividad;
DROP TABLE IF EXISTS user_settings;
DROP TABLE IF EXISTS enlaces_compartidos;
DROP TABLE IF EXISTS permisos_recursos;
DROP TABLE IF EXISTS archivos;
DROP TABLE IF EXISTS carpetas;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- TABLA: usuarios
-- ============================================
CREATE TABLE usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  rol ENUM('administrador','editor','colaborador','viewer') DEFAULT 'viewer',
  cuota_almacenamiento BIGINT DEFAULT 5368709120 COMMENT '5GB en bytes',
  almacenamiento_usado BIGINT DEFAULT 0,
  activo TINYINT(1) DEFAULT 1,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ultimo_acceso TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: carpetas
-- ============================================
CREATE TABLE carpetas (
  id INT NOT NULL AUTO_INCREMENT,
  google_folder_id VARCHAR(100) DEFAULT NULL,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  padre_id INT DEFAULT NULL,
  nivel INT DEFAULT 0,
  ruta_completa VARCHAR(1000) DEFAULT NULL,
  propietario_id INT NOT NULL,
  departamento VARCHAR(100) DEFAULT NULL,
  etiqueta VARCHAR(100) DEFAULT NULL,
  color_etiqueta VARCHAR(7) DEFAULT NULL,
  icono_personalizado VARCHAR(50) DEFAULT NULL,
  activa TINYINT(1) DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_propietario (propietario_id),
  KEY idx_padre (padre_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: archivos
-- ============================================
CREATE TABLE archivos (
  id INT NOT NULL AUTO_INCREMENT,
  google_file_id VARCHAR(100) DEFAULT NULL,
  nombre VARCHAR(255) NOT NULL,
  nombre_original VARCHAR(255) DEFAULT NULL,
  descripcion TEXT,
  tipo_mime VARCHAR(100) DEFAULT NULL,
  extension VARCHAR(10) DEFAULT NULL,
  tamaño BIGINT DEFAULT NULL,
  carpeta_id INT DEFAULT NULL,
  propietario_id INT NOT NULL,
  version INT DEFAULT 1,
  url_descarga VARCHAR(500) DEFAULT NULL,
  url_vista VARCHAR(500) DEFAULT NULL,
  thumbnail VARCHAR(500) DEFAULT NULL,
  tags JSON DEFAULT NULL,
  metadata JSON DEFAULT NULL,
  ruta_local VARCHAR(500) NOT NULL DEFAULT '',
  activo TINYINT(1) DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_propietario (propietario_id),
  KEY idx_carpeta (carpeta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: permisos_recursos
-- ============================================
CREATE TABLE permisos_recursos (
  id INT NOT NULL AUTO_INCREMENT,
  recurso_tipo ENUM('archivo','carpeta') NOT NULL,
  recurso_id INT NOT NULL,
  usuario_id INT NOT NULL,
  nivel_acceso ENUM('viewer','editor','propietario') DEFAULT 'viewer',
  activo TINYINT(1) DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_expiracion TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY unique_recurso_usuario (recurso_tipo, recurso_id, usuario_id),
  KEY idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: enlaces_compartidos
-- ============================================
CREATE TABLE enlaces_compartidos (
  id INT NOT NULL AUTO_INCREMENT,
  token VARCHAR(64) NOT NULL,
  recurso_tipo ENUM('archivo','carpeta') NOT NULL,
  recurso_id INT NOT NULL,
  propietario_id INT NOT NULL,
  nombre_recurso VARCHAR(255) NOT NULL,
  password VARCHAR(255) DEFAULT NULL,
  nivel_acceso ENUM('ver','descargar','editar') DEFAULT 'ver',
  fecha_expiracion DATETIME DEFAULT NULL,
  limite_accesos INT DEFAULT NULL,
  accesos_actuales INT DEFAULT 0,
  activo TINYINT(1) DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY token (token),
  KEY idx_propietario (propietario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: actividad
-- ============================================
CREATE TABLE actividad (
  id INT NOT NULL AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  accion VARCHAR(100) NOT NULL,
  tipo_recurso ENUM('archivo','carpeta','usuario','sistema') NOT NULL,
  recurso_id INT DEFAULT NULL,
  detalles JSON DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent TEXT,
  fecha_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_usuario (usuario_id),
  KEY idx_fecha (fecha_actividad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: user_settings
-- ============================================
CREATE TABLE user_settings (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_user_setting (user_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTAR USUARIO ADMINISTRADOR
-- Email: admin@biblioteca.com
-- Password: admin123
-- ============================================
INSERT INTO usuarios (nombre, email, password, rol, cuota_almacenamiento, almacenamiento_usado, activo) 
VALUES ('Administrador', 'admin@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 107374182400, 0, 1);

-- ============================================
-- VERIFICACIÓN
-- ============================================
SELECT 'Instalación completada exitosamente!' AS mensaje;
SELECT COUNT(*) AS total_tablas FROM information_schema.tables WHERE table_schema = 'biblioteca_digital';
SELECT id, nombre, email, rol FROM usuarios WHERE id = 1;

-- ============================================
-- IMPORTANTE
-- ============================================
-- Credenciales por defecto:
-- Email: admin@biblioteca.com
-- Password: admin123
-- 
-- CAMBIAR EL PASSWORD INMEDIATAMENTE DESPUÉS DE INICIAR SESIÓN
-- ============================================
