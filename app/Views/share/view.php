<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($file->nombre ?? 'Archivo compartido') ?> - ORION</title>
    <link rel="stylesheet" href="/biblioteca/public/assets/css/fontawesome.min.css">
    <!-- No cargar theme.css oscuro para esta vista -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .header .logo {
            font-size: 20px;
            font-weight: 600;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header .divider {
            width: 1px;
            height: 24px;
            background: #e0e0e0;
        }
        
        .header .breadcrumb {
            color: #666;
            font-size: 14px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }
        
        .file-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .file-header {
            padding: 24px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .file-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 24px;
            color: #fff;
        }
        
        .file-icon.pdf { background: #dc3545; }
        .file-icon.doc { background: #0d6efd; }
        .file-icon.xls { background: #198754; }
        .file-icon.ppt { background: #fd7e14; }
        .file-icon.img { background: #6f42c1; }
        .file-icon.video { background: #e91e63; }
        .file-icon.audio { background: #ff9800; }
        .file-icon.zip { background: #6c757d; }
        .file-icon.txt { background: #495057; }
        .file-icon.default { background: #6c757d; }
        
        .file-details h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #202124;
        }
        
        .file-meta {
            display: flex;
            gap: 16px;
            color: #666;
            font-size: 14px;
        }
        
        .file-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(74, 144, 226, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border: 1px solid rgba(74, 144, 226, 0.3);
        }
        
        .btn-secondary:hover {
            background: #fff;
            transform: translateY(-1px);
            border-color: rgba(74, 144, 226, 0.5);
        }
        
        .preview-section {
            padding: 24px;
        }
        
        .preview-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .preview-icon {
            font-size: 64px;
            color: #666;
            margin-bottom: 16px;
        }
        
        .preview-text {
            color: #666;
            font-size: 16px;
            margin-bottom: 16px;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.9);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid rgba(74, 144, 226, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .info-item h3 {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item p {
            font-size: 16px;
            color: #333;
        }
        
        .footer {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        
        .security-notice {
            background: linear-gradient(135deg, rgba(74, 144, 226, 0.1), rgba(53, 122, 189, 0.1));
            border: 1px solid rgba(74, 144, 226, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(10px);
        }
        
        .security-notice .icon {
            color: #4a90e2;
            font-size: 24px;
        }
        
        .security-notice .text {
            color: #4a5568;
            font-size: 15px;
            font-weight: 500;
        }
        
        /* Estilos para inputs */
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            color: #4a5568 !important; /* Gris oscuro */
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid #d1d5db !important;
            padding: 12px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            transition: all 0.2s !important;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none !important;
            border-color: #4a90e2 !important;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1) !important;
        }
        
        input::placeholder {
            color: #9ca3af !important; /* Gris más claro para placeholder */
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .file-info {
                flex-direction: column;
                text-align: center;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-book"></i>
            ORION
        </div>
        <div class="divider"></div>
        <div class="breadcrumb">
            Archivo compartido
        </div>
    </div>

    <div class="container">
        <?php if (isset($requiresPassword) && $requiresPassword): ?>
            <div class="security-notice">
                <i class="fas fa-lock icon"></i>
                <div class="text">
                    <?php if (isset($hasAccessCode) && $hasAccessCode): ?>
                        Este archivo está protegido con un código de acceso. Ingresa el código de 6 dígitos para continuar.
                    <?php else: ?>
                        Este archivo está protegido con contraseña. Ingresa la contraseña para continuar.
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST" style="max-width: 400px; margin: 0 auto;">
                <?php if (isset($hasAccessCode) && $hasAccessCode): ?>
                    <div style="margin-bottom: 16px;">
                        <label for="access_code" style="display: block; margin-bottom: 8px; font-weight: 500;">Código de acceso:</label>
                        <input type="text" id="access_code" name="access_code" required maxlength="6" 
                               placeholder="Ej: ABC123"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 18px; text-align: center; text-transform: uppercase; letter-spacing: 4px; font-family: monospace; color: #4a5568;"
                               oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                               autocomplete="off">
                    </div>
                <?php else: ?>
                    <div style="margin-bottom: 16px;">
                        <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">Contraseña:</label>
                        <input type="password" id="password" name="password" required 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; color: #4a5568;">
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-unlock"></i>
                    Acceder al archivo
                </button>
            </form>
        <?php elseif (isset($file) && $file !== null): ?>
            <div class="file-card">
                <div class="file-header">
                    <div class="file-info">
                        <div class="file-icon <?= $iconClass ?>">
                            <i class="<?= $iconName ?>"></i>
                        </div>
                        <div class="file-details">
                            <h1><?= htmlspecialchars($file->nombre ?? 'Archivo sin nombre') ?></h1>
                            <div class="file-meta">
                                <span>
                                    <i class="fas fa-file"></i>
                                    <?= strtoupper($file->extension ?? 'Archivo') ?>
                                </span>
                                <?php if (isset($file->tamaño) && $file->tamaño > 0): ?>
                                <span>
                                    <i class="fas fa-weight-hanging"></i>
                                    <?php
                                        $bytes = $file->tamaño;
                                        if ($bytes == 0) {
                                            echo '0 Bytes';
                                        } else {
                                            $k = 1024;
                                            $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                                            $i = floor(log($bytes) / log($k));
                                            echo round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
                                        }
                                    ?>
                                </span>
                                <?php endif; ?>
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    Compartido <?= date('d/m/Y', strtotime($link['fecha_creacion'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="actions">
                        <?php if ($permissions['can_download']): ?>
                            <a href="?action=download" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Descargar
                            </a>
                        <?php endif; ?>
                        
                        <?php if (in_array($file->extension ?? '', ['jpg', 'jpeg', 'png', 'gif', 'pdf'])): ?>
                            <button onclick="togglePreview()" class="btn btn-secondary" id="previewBtn">
                                <i class="fas fa-eye"></i>
                                Vista previa
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (in_array($file->extension ?? '', ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <div class="preview-section" id="previewSection" style="display: none;">
                    <div class="preview-container">
                        <img src="?action=preview" alt="<?= htmlspecialchars($file->nombre ?? 'Imagen') ?>" class="image-preview">
                    </div>
                </div>
                <?php elseif (($file->extension ?? '') === 'pdf'): ?>
                <div class="preview-section" id="previewSection" style="display: none;">
                    <div class="preview-container" style="background: #fff; padding: 0; border-radius: 8px; overflow: hidden;">
                        <div style="background: #f5f5f5; padding: 8px 16px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #666;">Vista previa del PDF</span>
                            <a href="?action=preview" target="_blank" style="font-size: 12px; color: #1a73e8; text-decoration: none;">
                                <i class="fas fa-external-link-alt"></i> Abrir en nueva pestaña
                            </a>
                        </div>
                        <object data="?action=preview" type="application/pdf" width="100%" height="600px">
                            <iframe src="?action=preview" width="100%" height="600px" style="border: none;">
                                <div style="padding: 40px; text-align: center;">
                                    <i class="fas fa-file-pdf" style="font-size: 48px; color: #dc3545; margin-bottom: 16px;"></i>
                                    <p style="margin-bottom: 16px;">No se pudo mostrar la vista previa del PDF en este navegador.</p>
                                    <a href="?action=preview" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt"></i>
                                        Abrir PDF en nueva pestaña
                                    </a>
                                </div>
                            </iframe>
                        </object>
                    </div>
                </div>
                <?php else: ?>
                <div class="preview-section" id="previewSection" style="display: none;">
                    <div class="preview-container">
                        <div class="preview-icon">
                            <i class="<?= $iconName ?>"></i>
                        </div>
                        <div class="preview-text">
                            Vista previa no disponible para este tipo de archivo
                        </div>
                        <?php if ($permissions['can_download']): ?>
                            <a href="?action=download" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Descargar para ver el contenido
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <h3>Tipo de archivo</h3>
                    <p><?= $file->tipo_mime ?? 'Desconocido' ?></p>
                </div>
                
                <div class="info-item">
                    <h3>Permisos</h3>
                    <p><?= ucfirst($link['permiso'] ?? 'Lector') ?></p>
                </div>
                
                <?php if ($link['fecha_expiracion']): ?>
                <div class="info-item">
                    <h3>Expira</h3>
                    <p><?= date('d/m/Y H:i', strtotime($link['fecha_expiracion'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <h3>Accesos</h3>
                    <p><?= $link['contador_accesos'] ?> visualizaciones</p>
                </div>
            </div>
        <?php else: ?>
            <!-- Caso de error -->
            <?php if (isset($errorMessage)): ?>
                <div class="security-notice" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(185, 28, 28, 0.1)); border-color: rgba(239, 68, 68, 0.3);">
                    <i class="fas fa-exclamation-triangle icon" style="color: #ef4444;"></i>
                    <div class="text" style="color: #991b1b;">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="footer">
            <p>Compartido desde ORION</p>
        </div>
    </div>

    <script>
        function togglePreview() {
            const previewSection = document.getElementById('previewSection');
            const previewBtn = document.getElementById('previewBtn');
            
            if (previewSection.style.display === 'none') {
                previewSection.style.display = 'block';
                previewBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar vista previa';
            } else {
                previewSection.style.display = 'none';
                previewBtn.innerHTML = '<i class="fas fa-eye"></i> Vista previa';
            }
        }
        
        // Auto-hide preview on mobile when scrolling
        if (window.innerWidth <= 768) {
            let lastScrollTop = 0;
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop + 50) {
                    const previewSection = document.getElementById('previewSection');
                    if (previewSection && previewSection.style.display === 'block') {
                        togglePreview();
                    }
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            });
        }
    </script>
</body>
</html>
