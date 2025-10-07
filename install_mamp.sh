#!/bin/bash

# ============================================
# Script de Instalación Automática para MAMP
# Biblioteca Digital
# ============================================

echo "🚀 Instalación de Biblioteca Digital"
echo "======================================"
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuración MAMP
MYSQL_BIN="/Applications/MAMP/Library/bin/mysql"
MYSQL_USER="root"
MYSQL_PASS="root"
MYSQL_PORT="8889"
MYSQL_SOCKET="/Applications/MAMP/tmp/mysql/mysql.sock"
SQL_FILE="install_simple.sql"

# Verificar que MAMP esté corriendo
echo "🔍 Verificando MAMP..."
if ! ps aux | grep -v grep | grep -q MAMP; then
    echo -e "${RED}❌ ERROR: MAMP no está corriendo${NC}"
    echo "Por favor, inicia MAMP y ejecuta este script de nuevo"
    exit 1
fi
echo -e "${GREEN}✅ MAMP está corriendo${NC}"
echo ""

# Verificar que existe el archivo SQL
if [ ! -f "$SQL_FILE" ]; then
    echo -e "${RED}❌ ERROR: No se encuentra $SQL_FILE${NC}"
    echo "Asegúrate de ejecutar este script desde la carpeta biblioteca/"
    exit 1
fi
echo -e "${GREEN}✅ Archivo SQL encontrado${NC}"
echo ""

# Intentar conectar a MySQL
echo "🔌 Intentando conectar a MySQL..."
if ! $MYSQL_BIN -u$MYSQL_USER -p$MYSQL_PASS --port=$MYSQL_PORT --socket=$MYSQL_SOCKET -e "SELECT 1" > /dev/null 2>&1; then
    echo -e "${RED}❌ ERROR: No se puede conectar a MySQL${NC}"
    echo ""
    echo "Posibles soluciones:"
    echo "1. Verifica que MAMP esté corriendo"
    echo "2. Verifica el puerto de MySQL en MAMP > Preferencias > Ports"
    echo "3. Intenta cambiar el puerto en este script (línea 20)"
    echo "4. Verifica la contraseña de MySQL"
    exit 1
fi
echo -e "${GREEN}✅ Conexión exitosa a MySQL${NC}"
echo ""

# Ejecutar el script SQL
echo "📦 Instalando base de datos..."
if $MYSQL_BIN -u$MYSQL_USER -p$MYSQL_PASS --port=$MYSQL_PORT --socket=$MYSQL_SOCKET < $SQL_FILE; then
    echo -e "${GREEN}✅ Base de datos instalada exitosamente!${NC}"
else
    echo -e "${RED}❌ ERROR al instalar la base de datos${NC}"
    exit 1
fi
echo ""

# Verificar instalación
echo "🔍 Verificando instalación..."
RESULT=$($MYSQL_BIN -u$MYSQL_USER -p$MYSQL_PASS --port=$MYSQL_PORT --socket=$MYSQL_SOCKET -e "USE biblioteca_digital; SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'biblioteca_digital';" -sN 2>/dev/null)

if [ "$RESULT" -eq "7" ]; then
    echo -e "${GREEN}✅ Todas las tablas creadas correctamente (7/7)${NC}"
else
    echo -e "${YELLOW}⚠️  Atención: Se esperaban 7 tablas, pero se encontraron $RESULT${NC}"
fi
echo ""

# Mostrar información del usuario admin
echo "👤 Usuario Administrador creado:"
$MYSQL_BIN -u$MYSQL_USER -p$MYSQL_PASS --port=$MYSQL_PORT --socket=$MYSQL_SOCKET -e "USE biblioteca_digital; SELECT id, nombre, email, rol FROM usuarios WHERE id = 1;" -t
echo ""

# Información final
echo "======================================"
echo -e "${GREEN}🎉 ¡Instalación Completada!${NC}"
echo "======================================"
echo ""
echo "📋 Credenciales de acceso:"
echo "   Email: admin@biblioteca.com"
echo "   Password: admin123"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANTE: Cambia el password inmediatamente después de iniciar sesión${NC}"
echo ""
echo "🌐 Accede a tu aplicación en:"
echo "   http://localhost:8888/biblioteca/public/index.php/auth/login"
echo ""
echo "📚 Base de datos:"
echo "   Nombre: biblioteca_digital"
echo "   Usuario: root"
echo "   Password: root"
echo "   Puerto: $MYSQL_PORT"
echo ""
echo "¡Disfruta de tu Biblioteca Digital! 📖"
