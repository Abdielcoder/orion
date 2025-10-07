-- Migration: User Settings Table
-- Tabla para almacenar configuraciones personalizadas por usuario

CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_setting_unique` (`user_id`, `setting_key`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuraciones por defecto si no existen
INSERT IGNORE INTO `user_settings` (`user_id`, `setting_key`, `setting_value`)
SELECT `id`, 'background_type', 'default' FROM `usuarios` WHERE `activo` = 1;

INSERT IGNORE INTO `user_settings` (`user_id`, `setting_key`, `setting_value`)
SELECT `id`, 'background_color', NULL FROM `usuarios` WHERE `activo` = 1;

INSERT IGNORE INTO `user_settings` (`user_id`, `setting_key`, `setting_value`)
SELECT `id`, 'background_image', NULL FROM `usuarios` WHERE `activo` = 1;
