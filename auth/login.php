<?php
require_once __DIR__ . '/../config/bootstrap.php';
sendNoCacheHeaders();

$user = null;
$loginSystemAvailable = true;
$error = $_SESSION['auth_login_error'] ?? '';
$success = '';
$allowRegistration = getSiteSetting('allow_registration', true);
$authDisabled = isPublicAuthDisabled();
$pageTitle = $authDisabled ? 'Acceso no disponible' : 'Login';
$pageSubtitle = $authDisabled
    ? 'El acceso con cuenta esta temporalmente deshabilitado en este dominio.'
    : 'Iniciar Sesión';
$requestedRedirect = trim((string) ($_GET['redirect'] ?? $_POST['redirect'] ?? ''));

unset($_SESSION['auth_login_error']);

try {
    $user = new User();
} catch (Throwable $e) {
    $loginSystemAvailable = false;
    $error = 'No se pudo iniciar el sistema de acceso. Revisa la conexion de la base de datos en el hosting.';
    error_log('Login bootstrap failed for ' . getNormalizedRequestHost() . ': ' . $e->getMessage());
}

if (isSafeInternalRedirectPath($requestedRedirect)) {
    $_SESSION['post_login_redirect'] = ltrim($requestedRedirect, '/');
}

if ($authDisabled && !headers_sent()) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow', true);
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$authDisabled && $loginSystemAvailable) {
    $now = time();
    $windowSeconds = 10 * 60;
    $maxAttempts = 5;
    $attempts = $_SESSION['login_attempts'] ?? [];
    $attempts = array_values(array_filter($attempts, function ($ts) use ($now, $windowSeconds) {
        return is_int($ts) && $ts > ($now - $windowSeconds);
    }));

    if (count($attempts) >= $maxAttempts) {
        $error = 'Demasiados intentos. Intenta nuevamente en unos minutos.';
    } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['auth_login_error'] = 'El formulario vencio o la pagina se recargo. Intenta otra vez.';
        redirect(SITE_URL . '/auth/login.php');
    } elseif (isset($_POST['login'])) {
        $username = cleanInput($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = 'Por favor complete todos los campos.';
        } else {
            $loginOk = false;

            try {
                $loginOk = $user !== null && $user->login($username, $password);
            } catch (Throwable $e) {
                $error = 'No se pudo completar el inicio de sesion. Revisa la configuracion del servidor.';
                error_log('Login execution failed for ' . getNormalizedRequestHost() . ': ' . $e->getMessage());
            }

            if ($loginOk) {
                unset($_SESSION['login_attempts']);
                redirect(getPostLoginRedirectUrl());
            } elseif ($error === '') {
                $error = 'Credenciales incorrectas.';
                $attempts[] = $now;
                $_SESSION['login_attempts'] = $attempts;
            }
        }
    }
}

// Si ya está logueado, redirigir
if ($loginSystemAvailable && isLoggedIn()) {
    redirect(getPostLoginRedirectUrl());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@600&display=swap');
        :root {
            --login-bg: radial-gradient(circle at 20% 20%, #e0f2ff 0%, #f5f7ff 45%, #eef2ff 100%);
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #556070;
            --border-subtle: #e5e7eb;
            --input-bg: #f8fafc;
            --primary: #2563eb;
            --primary-2: #0ea5e9;
            --neon: #22d3ee;
        }
        body {
            background: var(--login-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-main);
        }
        .login-container {
            background: var(--card-bg);
            border-radius: 18px;
            border: 1px solid rgba(34, 211, 238, 0.55);
            box-shadow:
                0 0 0 1px rgba(34, 211, 238, 0.25),
                0 0 28px rgba(34, 211, 238, 0.22),
                0 18px 42px rgba(15, 23, 42, 0.14);
            overflow: hidden;
            max-width: 460px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-2) 100%);
            color: white;
            padding: 1.6rem;
            text-align: center;
        }
        .login-header h2 {
            font-family: 'Space Grotesk', 'Inter', system-ui, sans-serif;
            letter-spacing: -0.02em;
        }
        .login-header small { opacity: 0.92; }
        .login-body {
            padding: 1.9rem;
            background: #ffffff;
            color: var(--text-main);
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid var(--border-subtle);
            background: var(--input-bg);
            color: var(--text-main);
            transition: all 0.15s ease;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.16rem rgba(37, 99, 235, 0.18);
            background: #fff;
        }
        .btn-login {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-2) 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: white;
            font-weight: 700;
            transition: all 0.15s ease;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.28);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 36px rgba(37, 99, 235, 0.35);
        }
        .btn-google {
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            background: #ffffff;
            color: var(--text-main);
            font-weight: 600;
            transition: all 0.15s ease;
        }
        .btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        }
        .btn-google i { color: #ea4335; }
        .input-group-text {
            background: var(--input-bg);
            border: 1px solid var(--border-subtle);
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: var(--text-muted);
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        .input-group .toggle-pass {
            border: 1px solid var(--border-subtle);
            border-left: none;
            background: var(--input-bg);
            color: var(--text-muted);
            border-radius: 0 12px 12px 0;
            cursor: pointer;
        }
        .alert {
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
        }
        .text-muted a, .text-center a { color: var(--primary); text-decoration: none; }
        .text-muted a:hover, .text-center a:hover { text-decoration: underline; }
        .form-check-label { color: var(--text-muted); }
        .form-text { color: #ef4444; }
        @media (max-width: 768px) {
            .login-container {
                box-shadow:
                    0 0 0 1px rgba(34, 211, 238, 0.18),
                    0 12px 26px rgba(15, 23, 42, 0.16);
                border: 1px solid rgba(34, 211, 238, 0.35);
            }
            .login-body { padding: 1.35rem; }
            .btn-login, .btn-google {
                box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2 class="mb-0">
                <i class="fas <?php echo $authDisabled ? 'fa-shield-alt' : 'fa-flask'; ?> me-2"></i>
                <?php echo $authDisabled ? 'Acceso no disponible' : 'Moyoalquimia'; ?>
            </h2>
            <p class="mb-0 mt-2"><?php echo htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($authDisabled): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-shield-alt me-2"></i>
                    El acceso con cuenta está deshabilitado temporalmente en este dominio por seguridad.
                </div>
                <div class="text-center">
                    <p class="mb-2">Usa un dominio verificado o contacta por WhatsApp / formulario de contacto.</p>
                    <a href="../contact.php" class="btn btn-login w-100 mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        Ir a Contacto
                    </a>
                    <a href="../index.php" class="text-decoration-none">Volver al inicio</a>
                </div>
            <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES); ?>">
                <?php if ($requestedRedirect !== ''): ?>
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($requestedRedirect, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" name="username" id="login-username" placeholder="Usuario o Email" required>
                    </div>
                    <div class="form-text text-danger small d-none" id="user-error">Ingresa usuario o email.</div>
                </div>
                
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" id="login-password" placeholder="Contraseña" required>
                        <button class="toggle-pass px-3" type="button" tabindex="-1" aria-label="Mostrar/ocultar contraseña" data-target="login-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text text-danger small d-none" id="pass-error">Ingresa tu contraseña.</div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="rememberEmail">
                    <label class="form-check-label" for="rememberEmail">
                        Recordar email/usuario
                    </label>
                </div>
                
                <button type="submit" name="login" class="btn btn-login w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar Sesión
                </button>

                <?php if (!empty(GOOGLE_CLIENT_ID)): ?>
                <a class="btn btn-google w-100 mb-3" href="google_login.php">
                    <i class="fab fa-google me-2"></i>Continuar con Google
                </a>
                <?php endif; ?>

                <div class="text-center mb-3">
                    <a href="forgot.php" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <?php if ($allowRegistration): ?>
                        <p class="mb-0">¿No tienes cuenta? <a href="register.php" class="text-decoration-none">Registrarse</a></p>
                    <?php endif; ?>
                    <p class="mb-0 mt-2"><a href="../index.php" class="text-decoration-none">Volver al inicio</a></p>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const userInput = document.getElementById('login-username');
            const passInput = document.getElementById('login-password');
            const userError = document.getElementById('user-error');
            const passError = document.getElementById('pass-error');
            const rememberCb = document.getElementById('rememberEmail');
            const form = document.querySelector('form');
            const toggleBtns = document.querySelectorAll('.toggle-pass');

            // Cargar email recordado
            try {
                const saved = localStorage.getItem('remember_email');
                if (saved && userInput) {
                    userInput.value = saved;
                    if (rememberCb) rememberCb.checked = true;
                }
            } catch (e) {}

            function validateField(input, errorEl) {
                const ok = input && input.value.trim() !== '';
                if (ok) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    if (errorEl) errorEl.classList.add('d-none');
                } else {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                    if (errorEl) errorEl.classList.remove('d-none');
                }
                return ok;
            }

            if (userInput) {
                userInput.addEventListener('input', () => validateField(userInput, userError));
            }
            if (passInput) {
                passInput.addEventListener('input', () => validateField(passInput, passError));
            }

            if (form) {
                form.addEventListener('submit', (e) => {
                    const okUser = validateField(userInput, userError);
                    const okPass = validateField(passInput, passError);
                    if (!okUser || !okPass) {
                        e.preventDefault();
                        return;
                    }
                    // Guardar email si está marcado
                    try {
                        if (rememberCb && rememberCb.checked && userInput) {
                            localStorage.setItem('remember_email', userInput.value.trim());
                        } else {
                            localStorage.removeItem('remember_email');
                        }
                    } catch (err) {}
                });
            }

            toggleBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    const isPass = input.type === 'password';
                    input.type = isPass ? 'text' : 'password';
                    btn.innerHTML = `<i class="fas ${isPass ? 'fa-eye-slash' : 'fa-eye'}"></i>`;
                });
            });
        })();
    </script>
</body>
</html>
