<?php
// Script de reparación para restaurar acceso de administrador

require_once __DIR__ . '/spl_autoload.php';

use App\Services\Database;

try {
    echo "<h1>Reparación de Login de Administrador</h1>\n";
    
    $db = Database::connection();
    
    // Verificar si el usuario admin existe
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'admin@biblioteca.com' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "<h2>Creando usuario administrador...</h2>\n";
        
        // Crear usuario admin con contraseña "admin123"
        $password = password_hash('admin123', PASSWORD_ARGON2ID);
        
        $stmt = $db->prepare("
            INSERT INTO usuarios (email, nombre, password, rol, activo) 
            VALUES ('admin@biblioteca.com', 'Administrador del Sistema', ?, 'administrador', 1)
        ");
        $stmt->execute([$password]);
        
        echo "✅ Usuario administrador creado<br>";
        echo "📧 Email: admin@biblioteca.com<br>";
        echo "🔑 Contraseña: admin123<br><br>";
        
    } else {
        echo "<h2>Actualizando usuario administrador existente...</h2>\n";
        
        // Actualizar contraseña y asegurar que sea admin
        $password = password_hash('admin123', PASSWORD_ARGON2ID);
        
        $stmt = $db->prepare("
            UPDATE usuarios 
            SET password = ?, rol = 'administrador', activo = 1 
            WHERE email = 'admin@biblioteca.com'
        ");
        $stmt->execute([$password]);
        
        echo "✅ Usuario administrador actualizado<br>";
        echo "📧 Email: admin@biblioteca.com<br>";
        echo "🔑 Contraseña: admin123<br>";
        echo "👤 Rol: admin<br>";
        echo "✅ Estado: Activo<br><br>";
    }
    
    // Verificar que la migración de roles se haya aplicado
    echo "<h2>Verificando roles...</h2>\n";
    
    try {
        // Intentar actualizar la columna rol si aún tiene los valores antiguos
        $stmt = $db->query("SHOW COLUMNS FROM usuarios LIKE 'rol'");
        $rolColumn = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rolColumn) {
            echo "Columna rol actual: {$rolColumn['Type']}<br>";
            
            // Si aún tiene los valores antiguos, actualizarlos
            if (strpos($rolColumn['Type'], 'manager') !== false) {
                echo "Aplicando migración de roles...<br>";
                
                // Actualizar la columna rol
                $db->exec("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin','owner','editor','commenter','viewer') COLLATE utf8mb4_unicode_ci DEFAULT 'viewer'");
                
                // Actualizar roles existentes
                $db->exec("UPDATE usuarios SET rol = 'owner' WHERE rol = 'manager'");
                
                echo "✅ Migración de roles aplicada<br>";
            }
        }
        
        // Verificar roles finales
        $stmt = $db->query("SELECT DISTINCT rol FROM usuarios");
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Roles actuales en la base de datos: " . implode(', ', $roles) . "<br><br>";
        
    } catch (Exception $e) {
        echo "⚠️ Error aplicando migración de roles: " . $e->getMessage() . "<br>";
    }
    
    // Test de login
    echo "<h2>Test de Login</h2>\n";
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'admin@biblioteca.com' AND activo = 1 LIMIT 1");
    $stmt->execute();
    $testUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testUser && password_verify('admin123', $testUser['password'])) {
        echo "✅ Login funcionando correctamente<br>";
        echo "Ahora puedes iniciar sesión con:<br>";
        echo "📧 Email: admin@biblioteca.com<br>";
        echo "🔑 Contraseña: admin123<br><br>";
        
        echo "<h3>🔗 <a href='/biblioteca/public/index.php/auth/login'>Ir al Login</a></h3>";
        
    } else {
        echo "❌ Aún hay problemas con el login<br>";
    }
    
    echo "<hr>";
    echo "<p><strong>Nota:</strong> Después de verificar que el login funciona, elimina este archivo por seguridad:</p>";
    echo "<code>rm /Applications/MAMP/htdocs/biblioteca/fix_admin_login.php</code>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Trace: " . $e->getTraceAsString() . "<br>";
}
?>
