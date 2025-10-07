<?php
// Script para actualizar la sesión con el nuevo rol

require_once __DIR__ . '/spl_autoload.php';

use App\Repositories\UserRepository;
use App\Services\Session;

session_start();

echo "<h1>Actualización de Sesión</h1>";

if (!isset($_SESSION['user_id'])) {
    echo "❌ No hay sesión activa. <a href='/biblioteca/public/index.php/auth/login'>Ir al login</a>";
    exit;
}

$userId = (int)$_SESSION['user_id'];
echo "Usuario ID en sesión: $userId<br>";

$userRepo = new UserRepository();
$user = $userRepo->findById($userId);

if (!$user) {
    echo "❌ Usuario no encontrado en la base de datos<br>";
    exit;
}

echo "Usuario encontrado: {$user->nombre}<br>";
echo "Email: {$user->email}<br>";
echo "Rol actual en BD: {$user->rol}<br>";
echo "Rol en sesión antes: " . ($_SESSION['user_role'] ?? 'no definido') . "<br>";

// Actualizar la sesión con el rol correcto
$_SESSION['user_role'] = $user->rol;

echo "Rol en sesión después: " . $_SESSION['user_role'] . "<br>";

if ($user->rol === 'administrador') {
    echo "✅ El usuario es administrador. Debería ver el menú de administración.<br>";
} else {
    echo "ℹ️ El usuario tiene rol: {$user->rol}<br>";
}

echo "<br><h3>🔗 <a href='/biblioteca/public/index.php/drive'>Ir al Dashboard</a></h3>";
echo "<p>Después de ir al dashboard, deberías ver el botón de engranaje (⚙️) en la esquina superior derecha si eres administrador.</p>";

echo "<hr>";
echo "<p><strong>Información de debug:</strong></p>";
echo "<pre>";
echo "Sesión completa:\n";
print_r($_SESSION);
echo "</pre>";
?>

