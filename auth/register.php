<?php
require_once __DIR__ . '/../config/bootstrap.php';
sendNoCacheHeaders();

$user = new User();
$error = '';
$success = '';
$allowRegistration = getSiteSetting('allow_registration', true);
$authDisabled = isPublicAuthDisabled();
$name = '';
$email = '';
$phone = '';
$pageTitle = $authDisabled ? 'Acceso no disponible' : 'Registro';
$pageSubtitle = $authDisabled
    ? 'La creación de cuentas esta deshabilitada en este dominio.'
    : 'Crear Cuenta';

if ($authDisabled && !headers_sent()) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow', true);
}

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(SITE_URL . '/admin/dashboard.php');
    } else {
        redirect(SITE_URL . '/index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $allowRegistration && !$authDisabled) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intenta nuevamente.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            $error = 'Completa todos los campos obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email invalido.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/', $password)) {
            $error = 'La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo.';
        } elseif ($password !== $confirm) {
            $error = 'Las contrasenas no coinciden.';
        } elseif ($user->getUserByEmail($email)) {
            $error = 'El email ya esta registrado.';
        } else {
            $result = $user->createUser($name, $email, $password, $phone, 'customer');
            if ($result) {
                $success = 'Cuenta creada. Ahora puedes iniciar sesion.';
            } else {
                $error = 'No se pudo crear la cuenta.';
            }
        }
    }
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
            --register-bg: radial-gradient(circle at 80% 20%, #e0f7ff 0%, #f5f7ff 50%, #eef2ff 100%);
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
            background: var(--register-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-main);
        }
        .register-container {
            background: var(--card-bg);
            border-radius: 18px;
            border: 1px solid rgba(34, 211, 238, 0.55);
            box-shadow:
                0 0 0 1px rgba(34, 211, 238, 0.25),
                0 0 28px rgba(34, 211, 238, 0.22),
                0 18px 42px rgba(15, 23, 42, 0.14);
            overflow: hidden;
            max-width: 520px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-2) 100%);
            color: white;
            padding: 1.6rem;
            text-align: center;
        }
        .register-header h2 {
            font-family: 'Space Grotesk', 'Inter', system-ui, sans-serif;
            letter-spacing: -0.02em;
        }
        .register-body {
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
        .btn-register {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-2) 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: white;
            font-weight: 700;
            transition: all 0.15s ease;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.28);
        }
        .btn-register:hover {
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
        .form-text { color: #64748b; }
        .text-center a { color: var(--primary); text-decoration: none; }
        .text-center a:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .register-container {
                box-shadow:
                    0 0 0 1px rgba(34, 211, 238, 0.18),
                    0 12px 26px rgba(15, 23, 42, 0.16);
                border: 1px solid rgba(34, 211, 238, 0.35);
            }
            .register-body { padding: 1.35rem; }
            .btn-register, .btn-google {
                box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2 class="mb-0">
                <i class="fas <?php echo $authDisabled ? 'fa-shield-alt' : 'fa-flask'; ?> me-2"></i>
                <?php echo $authDisabled ? 'Acceso no disponible' : 'Moyoalquimia'; ?>
            </h2>
            <p class="mb-0 mt-2"><?php echo htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="register-body">
            <?php if ($authDisabled): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-shield-alt me-2"></i>
                    El registro está deshabilitado temporalmente en este dominio por seguridad.
                </div>
            <?php elseif (!$allowRegistration): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Registro deshabilitado. Contacta al administrador.
                </div>
            <?php endif; ?>

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

            <?php if ($allowRegistration && !$authDisabled): ?>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES); ?>">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" name="name" id="reg-name" placeholder="Nombre completo" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" name="email" id="reg-email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" class="form-control" name="phone" id="reg-phone" placeholder="Telefono (opcional)" value="<?php echo htmlspecialchars($phone); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña (mín 8, Aa1@)" required>
                            <button class="toggle-pass px-3" type="button" tabindex="-1" aria-label="Mostrar/ocultar contraseña" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted" id="password-hint"></div>
                    </div>

                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirmar contraseña" required>
                            <button class="toggle-pass px-3" type="button" tabindex="-1" aria-label="Mostrar/ocultar confirmación" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted" id="confirm-hint">Usa al menos 8 caracteres con mayúscula, minúscula, número y símbolo.</div>
                    </div>

                    <button type="submit" class="btn btn-register w-100">
                        <i class="fas fa-user-plus me-2"></i>
                        Crear Cuenta
                    </button>

                    <?php if (!empty(GOOGLE_CLIENT_ID)): ?>
                    <a class="btn btn-google w-100 mt-3" href="google_login.php">
                        <i class="fab fa-google me-2"></i>Continuar con Google
                    </a>
                    <?php endif; ?>
                </form>
            <?php elseif ($authDisabled): ?>
                <div class="text-center">
                    <p class="mb-3">Usa el formulario de contacto mientras este dominio termina su revisión de seguridad.</p>
                    <a class="btn btn-register w-100" href="../contact.php">
                        <i class="fas fa-envelope me-2"></i>
                        Ir a Contacto
                    </a>
                </div>
            <?php endif; ?>

            <hr class="my-4">

            <div class="text-center">
                <?php if (!$authDisabled): ?>
                    <p class="mb-0">Ya tienes cuenta? <a href="login.php" class="text-decoration-none">Iniciar sesion</a></p>
                <?php endif; ?>
                <p class="mb-0 mt-2"><a href="../index.php" class="text-decoration-none">Volver al inicio</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const form = document.querySelector('form');
        const emailInput = document.getElementById('reg-email');
        const passInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const nameInput = document.getElementById('reg-name');
        const passHint = document.getElementById('password-hint');
        const confirmHint = document.getElementById('confirm-hint');

        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/;

        function setValidity(input, ok, msgEl, msg) {
            if (ok) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                if (msgEl) msgEl.textContent = '';
            } else {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                if (msgEl) msgEl.textContent = msg || '';
            }
        }

        if (emailInput) {
            emailInput.addEventListener('input', () => {
                const ok = emailInput.value.trim().match(/^[^@\s]+@[^@\s]+\.[^@\s]+$/);
                setValidity(emailInput, ok, null, '');
            });
        }

        if (passInput) {
            passInput.addEventListener('input', () => {
                const ok = passRegex.test(passInput.value);
                setValidity(passInput, ok, passHint, ok ? '' : 'Debe incluir mayúscula, minúscula, número y símbolo (8+).');
                if (confirmInput) confirmInput.dispatchEvent(new Event('input'));
            });
        }

        if (confirmInput) {
            confirmInput.addEventListener('input', () => {
                const ok = confirmInput.value === passInput.value && passRegex.test(passInput.value);
                setValidity(confirmInput, ok, confirmHint, ok ? '' : 'Las contraseñas deben coincidir y ser seguras.');
            });
        }

        if (form) {
            form.addEventListener('submit', (e) => {
                if (nameInput && nameInput.value.trim() === '') {
                    nameInput.classList.add('is-invalid');
                    e.preventDefault();
                    return;
                }
                const emailOk = emailInput ? emailInput.classList.contains('is-valid') : true;
                const passOk = passInput ? passRegex.test(passInput.value) : true;
                const confirmOk = confirmInput ? (confirmInput.value === passInput.value) : true;
                if (!emailOk || !passOk || !confirmOk) {
                    e.preventDefault();
                    if (typeof showNotification === 'function') {
                        showNotification('Corrige los campos marcados antes de continuar.', 'warning');
                    } else {
                        alert('Corrige los campos marcados antes de continuar.');
                    }
                }
            });
        }

        const toggleBtns = document.querySelectorAll('.toggle-pass');
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
    </script>
</body>
</html>
