<?php
require_once __DIR__ . '/../config/bootstrap.php';
sendNoCacheHeaders();

$error = '';
$success = '';
$authDisabled = isPublicAuthDisabled();
$pageTitle = $authDisabled ? 'Acceso no disponible' : 'Recuperar contraseña';

if ($authDisabled && !headers_sent()) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow', true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$authDisabled) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intenta nuevamente.';
    } else {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
        } else {
            $file = __DIR__ . '/../data/password_reset_requests.json';
            $list = [];
            if (file_exists($file)) {
                $existing = json_decode(file_get_contents($file), true);
                if (is_array($existing)) {
                    $list = $existing;
                }
            }
            $list[] = [
                'email' => $email,
                'requested_at' => date('c'),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ];
            file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $success = 'Si el email existe, enviaremos instrucciones para recuperar la contraseña.';
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
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; }
        .card { border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); max-width: 420px; width: 100%; }
    </style>
</head>
<body>
    <div class="card p-4">
        <h4 class="mb-3 text-center"><i class="fas <?php echo $authDisabled ? 'fa-shield-alt' : 'fa-unlock-alt'; ?> me-2"></i><?php echo $authDisabled ? 'Acceso no disponible' : 'Recuperar contraseña'; ?></h4>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <?php if ($authDisabled): ?>
            <div class="alert alert-warning">
                <i class="fas fa-shield-alt me-2"></i>
                La recuperación de contraseña está deshabilitada temporalmente en este dominio por seguridad.
            </div>
            <div class="text-center mt-3"><a class="text-decoration-none" href="../contact.php">Ir a contacto</a></div>
        <?php else: ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES); ?>">
            <div class="mb-3">
                <label class="form-label">Correo registrado</label>
                <input type="email" class="form-control" name="email" placeholder="tu@email.com" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar instrucciones</button>
            <div class="text-center mt-3"><a class="text-decoration-none" href="login.php">Volver al login</a></div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
