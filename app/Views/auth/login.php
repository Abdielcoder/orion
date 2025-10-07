<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORION - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(dirname($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars(dirname($baseUrl ?? ''), ENT_QUOTES, 'UTF-8') ?>/assets/css/theme.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            background: #ffffff !important;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff !important;
            background-color: #ffffff !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background particles */
        .background-particles {
            display: none !important; /* Ocultar partículas completamente */
        }

        .particle {
            display: none !important; /* Ocultar partículas completamente */
        }

        .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-delay: -2s; }
        .particle:nth-child(3) { width: 40px; height: 40px; left: 30%; animation-delay: -4s; }
        .particle:nth-child(4) { width: 100px; height: 100px; left: 40%; animation-delay: -1s; }
        .particle:nth-child(5) { width: 50px; height: 50px; left: 60%; animation-delay: -3s; }
        .particle:nth-child(6) { width: 70px; height: 70px; left: 80%; animation-delay: -5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-100px) rotate(180deg); opacity: 0.3; }
        }

        .login-container {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            border: 2px solid #e2e8f0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(45, 55, 72, 0.3);
        }

        .logo i {
            font-size: 40px;
            color: white;
        }

        .app-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .app-subtitle {
            font-size: 16px;
            color: #718096;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .input-container {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 18px; /* Espaciado interno estándar sin iconos */
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }

        .form-input:focus {
            outline: none;
            border-color: #4a5568;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.1);
        }

        

        .login-button {
            width: 100%;
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            box-shadow: 0 10px 30px rgba(45, 55, 72, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .button-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .message {
            margin-top: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .message.show {
            opacity: 1;
            transform: translateY(0);
        }

        .message.success {
            background: #f0fff4;
            color: #38a169;
            border: 1px solid #9ae6b4;
        }

        .message.error {
            background: #fed7d7;
            color: #e53e3e;
            border: 1px solid #feb2b2;
        }

        .message.info {
            background: #ebf8ff;
            color: #3182ce;
            border: 1px solid #90cdf4;
        }


        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            font-size: 12px;
            color: #a0aec0;
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 25px;
            }

            .app-title {
                font-size: 24px;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .logo i {
                font-size: 35px;
            }
        }

        /* Forzar tema claro siempre */
        @media (prefers-color-scheme: dark) {
            html {
                background: #ffffff !important;
            }
            
            body {
                background: #ffffff !important;
                background-color: #ffffff !important;
            }

            .login-container {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #2d3748;
            }
        }
    </style>
</head>
<body>
    <div class="background-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-hdd"></i>
            </div>
            <h1 class="app-title">ORION</h1>
            <p class="app-subtitle">Organiza • Registra • Informa • Optimiza • Notifica</p>
        </div>

        <form id="loginForm">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-container">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        required 
                        autocomplete="username"
                        placeholder="usuario@orion.com"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-container">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            <button type="submit" class="login-button" id="loginButton">
                <div class="button-text">
                    <div class="loading-spinner" id="loadingSpinner"></div>
                    <span id="buttonText">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </span>
                </div>
            </button>
        </form>

        <div class="message" id="message" role="alert" aria-live="polite"></div>


                    <div class="footer">
            <p class="footer-text">
                © 2024 ORION. Todos los derechos reservados.
            </p>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const message = document.getElementById('message');
        const loginButton = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        // Get base URL from server
        const baseUrl = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8') ?>';
        
        function showMessage(text, type = 'info') {
            message.textContent = text;
            message.className = `message ${type} show`;
            
            if (type === 'success') {
                setTimeout(() => {
                    message.classList.remove('show');
                }, 2000);
            }
        }

        function hideMessage() {
            message.classList.remove('show');
        }

        function setLoading(loading) {
            loginButton.disabled = loading;
            if (loading) {
                loadingSpinner.style.display = 'block';
                buttonText.innerHTML = 'Iniciando sesión...';
            } else {
                loadingSpinner.style.display = 'none';
                buttonText.innerHTML = '<i class="fas fa-sign-in-alt"></i> Iniciar Sesión';
            }
        }


        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideMessage();
            setLoading(true);

            try {
                const formData = new FormData(form);
                const response = await fetch(`${baseUrl}/auth/login`, {
                    method: 'POST',
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.ok) {
                    showMessage('¡Inicio de sesión exitoso! Redirigiendo...', 'success');
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        // Try to redirect to drive, fallback to root
                        window.location.href = `${baseUrl}/drive` || `${baseUrl}/`;
                    }, 1500);
                } else {
                    showMessage(data.error || 'Error al iniciar sesión', 'error');
                    setLoading(false);
                }
            } catch (error) {
                console.error('Login error:', error);
                showMessage('Error de conexión. Por favor, intente nuevamente.', 'error');
                setLoading(false);
            }
        });

        // Auto-focus first input
        emailInput.focus();

        // Add enter key support for credential buttons
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                hideMessage();
            }
        });

        // Input validation feedback
        emailInput.addEventListener('input', () => {
            if (emailInput.validity.valid) {
                emailInput.style.borderColor = '#48bb78';
            } else if (emailInput.value) {
                emailInput.style.borderColor = '#f56565';
            } else {
                emailInput.style.borderColor = '#e2e8f0';
            }
        });

        passwordInput.addEventListener('input', () => {
            if (passwordInput.value.length >= 6) {
                passwordInput.style.borderColor = '#48bb78';
            } else if (passwordInput.value) {
                passwordInput.style.borderColor = '#f56565';
            } else {
                passwordInput.style.borderColor = '#e2e8f0';
            }
        });

        console.log('🚀 Biblioteca Digital Login System Ready');
        console.log('Base URL:', baseUrl);
    </script>
</body>
</html>


