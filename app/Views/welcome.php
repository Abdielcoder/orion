<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORION - Sistema de Gestión Documental</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/assets/css/theme.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.08);
            animation: float 8s ease-in-out infinite;
        }

        .floating-shape:nth-child(1) {
            width: 100px;
            height: 100px;
            left: 10%;
            top: 20%;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            width: 60px;
            height: 60px;
            right: 15%;
            top: 40%;
            animation-delay: -2s;
        }

        .floating-shape:nth-child(3) {
            width: 80px;
            height: 80px;
            left: 20%;
            bottom: 30%;
            animation-delay: -4s;
        }

        .floating-shape:nth-child(4) {
            width: 40px;
            height: 40px;
            right: 25%;
            bottom: 20%;
            animation-delay: -1s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.3;
            }
        }

        .welcome-container {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
            padding: 50px;
            width: 100%;
            max-width: 600px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo-section {
            margin-bottom: 40px;
        }

        .main-logo {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 15px 40px rgba(45, 55, 72, 0.3);
            transition: transform 0.3s ease;
        }

        .main-logo:hover {
            transform: scale(1.05);
        }

        .main-logo i {
            font-size: 60px;
            color: white;
        }

        .welcome-title {
            font-size: 36px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 18px;
            color: #718096;
            margin-bottom: 30px;
            font-weight: 400;
        }

        .status-section {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .status-title {
            font-size: 16px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .status-info {
            font-size: 14px;
            color: #718096;
            line-height: 1.6;
        }

        .status-code {
            background: #edf2f7;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #2d3748;
        }

        .user-section {
            background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%);
            border: 2px solid #9ae6b4;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #38a169 0%, #48bb78 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .user-details h3 {
            font-size: 18px;
            font-weight: 600;
            color: #22543d;
            margin-bottom: 4px;
        }

        .user-details p {
            font-size: 14px;
            color: #38a169;
            margin: 0;
        }

        .logout-form {
            margin-top: 15px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(45, 55, 72, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            box-shadow: 0 12px 35px rgba(45, 55, 72, 0.4);
        }

        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, #38a169 0%, #48bb78 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(56, 161, 105, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(56, 161, 105, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e53e3e 0%, #fc8181 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(229, 62, 62, 0.4);
        }

        .footer-info {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
            font-size: 12px;
            color: #a0aec0;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .welcome-container {
                margin: 20px;
                padding: 30px 25px;
            }

            .welcome-title {
                font-size: 28px;
            }

            .main-logo {
                width: 100px;
                height: 100px;
            }

            .main-logo i {
                font-size: 50px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            }

            .welcome-container {
                background: rgba(45, 55, 72, 0.95);
                color: #e2e8f0;
            }

            .welcome-title {
                color: #e2e8f0;
            }

            .welcome-subtitle {
                color: #a0aec0;
            }

            .status-section {
                background: #2d3748;
                border-color: #4a5568;
            }

            .status-title {
                color: #cbd5e0;
            }

            .status-info {
                color: #a0aec0;
            }

            .status-code {
                background: #374151;
                color: #e2e8f0;
            }

            .user-section {
                background: rgba(45, 55, 72, 0.8);
                border-color: #4a5568;
            }

            .user-details h3 {
                color: #e2e8f0;
            }

            .user-details p {
                color: #a0aec0;
            }

            .btn-secondary {
                background: #374151;
                color: #e2e8f0;
                border-color: #4a5568;
            }

            .btn-secondary:hover {
                background: #4a5568;
                border-color: #6b7280;
            }

            .footer-info {
                border-top-color: #4a5568;
                color: #718096;
            }
        }

        /* Loading animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; font-src 'self' data:; connect-src 'self'">
</head>
<body>
    <div class="background-animation">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <div class="welcome-container">
        <div class="logo-section">
            <div class="main-logo">
                <i class="fas fa-hdd"></i>
            </div>
            <h1 class="welcome-title">
                ORION
            </h1>
            <p class="welcome-subtitle">Organiza • Registra • Informa • Optimiza • Notifica</p>
        </div>

        <div class="status-section">
            <div class="status-title">
                <i class="fas fa-server"></i>
                Estado del Sistema
            </div>
            <div class="status-info">
                Backend operativo. Rutas disponibles: 
                <span class="status-code">/health</span> y 
                <span class="status-code">/auth</span>
                <br>
                Sistema listo para autenticación y gestión de archivos.
            </div>
        </div>

        <?php $uid = $_SESSION['user_id'] ?? null; $csrf = $_SESSION['csrf_token'] ?? ''; ?>
        
        <?php if ($uid): ?>
            <div class="user-section">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <h3>Sesión Activa</h3>
                        <p>Usuario #<?= (int)$uid ?> conectado</p>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/drive" class="action-btn btn-success">
                        <i class="fas fa-folder-open"></i>
                        Acceder al Drive
                    </a>
                    <form id="logoutForm" method="post" action="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/auth/logout" class="logout-form">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="action-btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="status-section">
                <div class="status-title">
                    <i class="fas fa-user-lock"></i>
                    Acceso Requerido
                </div>
                <div class="status-info">
                    Debe iniciar sesión para acceder al sistema de gestión documental.
                </div>
            </div>

            <div class="action-buttons">
                <a href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/auth/login" class="action-btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
                <a href="<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/health" class="action-btn btn-secondary">
                    <i class="fas fa-heartbeat"></i>
                    Estado del Sistema
                </a>
            </div>
        <?php endif; ?>

        <div class="footer-info">
            <p>© 2024 ORION. Sistema de gestión documental empresarial.</p>
            <p>Desarrollado con tecnología PHP 8.1+ y MySQL 8.0+</p>
        </div>
    </div>

    <script>
        // Logout form handler
        const logoutForm = document.getElementById('logoutForm');
        if (logoutForm) {
            logoutForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const submitButton = logoutForm.querySelector('button[type="submit"]');
                const originalContent = submitButton.innerHTML;
                
                // Add loading state
                submitButton.classList.add('loading');
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cerrando sesión...';
                
                try {
                    const response = await fetch('<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>/auth/logout', {
                        method: 'POST',
                        body: new FormData(logoutForm)
                    });
                    
                    const result = await response.json().catch(() => null);
                    
                    if (response.ok) {
                        submitButton.innerHTML = '<i class="fas fa-check"></i> Sesión cerrada';
                        setTimeout(() => {
                            window.location.href = './';
                        }, 1000);
                    } else {
                        throw new Error('Error al cerrar sesión');
                    }
                } catch (error) {
                    console.error('Logout error:', error);
                    submitButton.classList.remove('loading');
                    submitButton.innerHTML = originalContent;
                    if (typeof openSystemModal === 'function') {
                        openSystemModal('Error al cerrar sesión. Por favor, intente nuevamente.', 'error', 'Cerrar sesión');
                    } else {
                        alert('Error al cerrar sesión. Por favor, intente nuevamente.');
                    }
                }
            });
        }

        // Add smooth transitions for buttons
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-2px)';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0)';
            });
        });

        // Add click feedback
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('mousedown', () => {
                button.style.transform = 'translateY(0) scale(0.98)';
            });
            
            button.addEventListener('mouseup', () => {
                button.style.transform = 'translateY(-2px) scale(1)';
            });
        });

        console.log('🚀 ORION Welcome Page Ready');
        console.log('Current session:', <?= $uid ? '"User #' . (int)$uid . '"' : '"Not authenticated"' ?>);
    </script>
</body>
</html>


