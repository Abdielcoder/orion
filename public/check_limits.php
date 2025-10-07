<?php
echo "<h2>Límites actuales de PHP:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><td><b>upload_max_filesize:</b></td><td style='color: " . (ini_get('upload_max_filesize') >= '256M' ? 'green' : 'red') . "'>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td><b>post_max_size:</b></td><td style='color: " . (ini_get('post_max_size') >= '256M' ? 'green' : 'red') . "'>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td><b>max_execution_time:</b></td><td>" . ini_get('max_execution_time') . " segundos</td></tr>";
echo "<tr><td><b>max_input_time:</b></td><td>" . ini_get('max_input_time') . " segundos</td></tr>";
echo "<tr><td><b>memory_limit:</b></td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td><b>max_file_uploads:</b></td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "</table>";

echo "<h3>Estado:</h3>";
if (ini_get('post_max_size') == '8M' || ini_get('upload_max_filesize') == '8M') {
    echo "<p style='color:red'>❌ Los límites siguen en 8M. Necesitas editar php.ini y reiniciar MAMP.</p>";
} else {
    echo "<p style='color:green'>✅ Los límites han sido actualizados. Deberías poder subir archivos grandes.</p>";
}

echo "<p><small>Elimina este archivo después de verificar por seguridad.</small></p>";
?>
