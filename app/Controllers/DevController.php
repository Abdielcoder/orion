<?php

namespace App\Controllers;

use App\Services\Database;
use App\Services\Config;
use App\Services\Storage;
use App\Repositories\UserSettingsRepository;
use App\Helpers\Response;

class DevController
{
    public function setPlainPassword()
    {
        if (Config::get('env') !== 'local') {
            return Response::json(['error' => 'Solo disponible en local'], 403);
        }
        $userId = (int)($_POST['user_id'] ?? 0);
        $password = (string)($_POST['password'] ?? '');
        if ($userId <= 0 || $password === '') {
            return Response::json(['error' => 'Parámetros inválidos'], 422);
        }
        $db = Database::connection();
        $stmt = $db->prepare('UPDATE usuarios SET password = :pwd WHERE id = :id');
        $stmt->execute(['pwd' => $password, 'id' => $userId]);
        return Response::json(['ok' => true]);
    }

    public function forcePlainDemo()
    {
        if (Config::get('env') !== 'local') {
            return Response::json(['error' => 'Solo disponible en local'], 403);
        }
        $db = Database::connection();
        // Fuerza contraseña en texto plano 'demo1234' para el usuario de prueba
        $stmt = $db->prepare("UPDATE usuarios SET password = 'demo1234' WHERE email = 'usuario@biblioteca.com'");
        $stmt->execute();
        return Response::json(['ok' => true, 'email' => 'usuario@biblioteca.com', 'password' => 'demo1234']);
    }

    public function testUpload()
    {
        if (Config::get('env') !== 'local') {
            return Response::json(['error' => 'Solo disponible en local'], 403);
        }
        
        return Response::json([
            'ok' => true,
            'session_user' => $_SESSION['user_id'] ?? null,
            'post_data' => $_POST,
            'files_data' => $_FILES,
            'storage_path' => Storage::basePath(),
            'user_path' => Storage::userPath(2),
        ]);
    }
    
    public function testUserSettings()
    {
        if (Config::get('env') !== 'local') {
            return Response::json(['error' => 'Solo disponible en local'], 403);
        }

        try {
            $userSettings = new UserSettingsRepository();
            
            // Probar obtener configuraciones del usuario 1
            $settings = $userSettings->getBackgroundSettings(1);
            
            // Probar establecer un color
            $userSettings->setBackgroundColor(1, '#ff0000');
            
            // Obtener configuraciones actualizadas
            $updatedSettings = $userSettings->getBackgroundSettings(1);
            
            return Response::json([
                'ok' => true,
                'initial_settings' => $settings,
                'updated_settings' => $updatedSettings
            ]);
            
        } catch (\Exception $e) {
            return Response::json([
                'error' => 'Error en UserSettingsRepository',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    public function testSession()
    {
        session_start();
        return Response::json([
            'session_id' => session_id(),
            'session_data' => $_SESSION,
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
    }
}


