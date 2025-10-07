<?php

namespace App\Repositories;

use App\Services\Database;
use App\Models\UserSetting;
use PDO;

class UserSettingsRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function getSetting(int $userId, string $key): ?string
    {
        $stmt = $this->db->prepare('SELECT setting_value FROM user_settings WHERE user_id = :uid AND setting_key = :key LIMIT 1');
        $stmt->execute(['uid' => $userId, 'key' => $key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : null;
    }

    public function setSetting(int $userId, string $key, ?string $value): void
    {
        $stmt = $this->db->prepare('
            INSERT INTO user_settings (user_id, setting_key, setting_value) 
            VALUES (:uid, :key, :value)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
        ');
        $stmt->execute(['uid' => $userId, 'key' => $key, 'value' => $value]);
    }

    public function getUserSettings(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT setting_key, setting_value FROM user_settings WHERE user_id = :uid');
        $stmt->execute(['uid' => $userId]);
        
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function deleteSetting(int $userId, string $key): void
    {
        $stmt = $this->db->prepare('DELETE FROM user_settings WHERE user_id = :uid AND setting_key = :key');
        $stmt->execute(['uid' => $userId, 'key' => $key]);
    }

    // Métodos específicos para configuraciones de fondo
    public function getBackgroundSettings(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT setting_key, setting_value 
            FROM user_settings 
            WHERE user_id = ? AND setting_key IN (?, ?, ?)
        ');
        $stmt->execute([$userId, 'background_type', 'background_color', 'background_image']);
        
        $settings = [
            'background_type' => 'default',
            'background_color' => null,
            'background_image' => null
        ];
        
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }

    public function setBackgroundColor(int $userId, string $color): void
    {
        $this->setSetting($userId, 'background_type', 'color');
        $this->setSetting($userId, 'background_color', $color);
        $this->setSetting($userId, 'background_image', null);
    }

    public function setBackgroundImage(int $userId, string $imageData): void
    {
        $this->setSetting($userId, 'background_type', 'image');
        $this->setSetting($userId, 'background_image', $imageData);
        $this->setSetting($userId, 'background_color', null);
    }

    public function clearBackground(int $userId): void
    {
        $this->setSetting($userId, 'background_type', 'default');
        $this->setSetting($userId, 'background_color', null);
        $this->setSetting($userId, 'background_image', null);
    }
}
