#!/bin/bash

echo "🔧 Configurando Git globalmente..."
echo "=================================="

# Información personal
echo "👤 Configurando información personal..."
git config --global user.name "Abdiel Carrasco"
git config --global user.email "abdiel@apexlabs.com"

# Configuración de comportamiento
echo "⚙️ Configurando comportamiento..."
git config --global init.defaultBranch main
git config --global pull.rebase false
git config --global push.default simple
git config --global push.followTags true

# Configuración de colores
echo "🎨 Habilitando colores..."
git config --global color.ui auto
git config --global color.branch auto
git config --global color.diff auto
git config --global color.status auto

# Aliases útiles
echo "🔗 Configurando aliases..."
git config --global alias.st status
git config --global alias.co checkout
git config --global alias.br branch
git config --global alias.ci commit
git config --global alias.unstage 'reset HEAD --'
git config --global alias.last 'log -1 HEAD'
git config --global alias.lg "log --oneline --decorate --all --graph"
git config --global alias.tree "log --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit"

# Configuración del editor
echo "📝 Configurando editor..."
if command -v code &> /dev/null; then
    git config --global core.editor "code --wait"
    echo "✅ Editor configurado: VS Code"
elif command -v nano &> /dev/null; then
    git config --global core.editor "nano"
    echo "✅ Editor configurado: Nano"
else
    git config --global core.editor "vim"
    echo "✅ Editor configurado: Vim"
fi

# Configuración de sistema
echo "🖥️ Configurando sistema..."
git config --global core.autocrlf input
git config --global core.ignorecase false
git config --global credential.helper store

# Configuración de merge y diff
git config --global merge.tool vimdiff
git config --global diff.tool vimdiff

echo ""
echo "✅ ¡Git configurado exitosamente!"
echo "=================================="
echo ""
echo "📋 Configuración aplicada:"
git config --global --list | grep -E "user\.|alias\.|color\.|core\.|push\.|pull\."
echo ""
echo "🚀 Comandos útiles disponibles:"
echo "git st       - Status"
echo "git co       - Checkout"
echo "git br       - Branch"
echo "git ci       - Commit"
echo "git lg       - Log gráfico"
echo "git tree     - Log con árbol bonito"
echo "git unstage  - Quitar del staging"
echo "git last     - Último commit"
