#!/bin/bash
# Script de instalación ultra-simple

echo "🚀 Instalando Biblioteca Digital..."
echo ""

# Probar diferentes métodos
MYSQL_BIN="/Applications/MAMP/Library/bin/mysql"
SQL_FILE="install_simple.sql"

# Método 1: Puerto 8889
echo "Intentando con puerto 8889..."
if $MYSQL_BIN -u root -proot --port=8889 < $SQL_FILE 2>/dev/null; then
    echo "✅ ¡Instalado exitosamente!"
    $MYSQL_BIN -u root -proot --port=8889 -e "USE biblioteca_digital; SELECT * FROM usuarios WHERE id=1;"
    exit 0
fi

# Método 2: Puerto 3306
echo "Intentando con puerto 3306..."
if $MYSQL_BIN -u root -proot --port=3306 < $SQL_FILE 2>/dev/null; then
    echo "✅ ¡Instalado exitosamente!"
    $MYSQL_BIN -u root -proot --port=3306 -e "USE biblioteca_digital; SELECT * FROM usuarios WHERE id=1;"
    exit 0
fi

# Método 3: Sin puerto
echo "Intentando sin especificar puerto..."
if $MYSQL_BIN -u root -proot < $SQL_FILE 2>/dev/null; then
    echo "✅ ¡Instalado exitosamente!"
    $MYSQL_BIN -u root -proot -e "USE biblioteca_digital; SELECT * FROM usuarios WHERE id=1;"
    exit 0
fi

# Si ninguno funcionó
echo "❌ No se pudo instalar automáticamente"
echo ""
echo "Por favor, sigue las instrucciones en:"
echo "INSTALACION_MANUAL_PASO_A_PASO.md"
exit 1
