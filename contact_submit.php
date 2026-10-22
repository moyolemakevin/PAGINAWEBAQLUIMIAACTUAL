<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo no permitido']);
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalido']);
    exit();
}

$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud invalida']);
    exit();
}

$formTime = (int)($_POST['form_time'] ?? 0);
if ($formTime > 0 && (time() - $formTime) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud invalida']);
    exit();
}

$now = time();
$windowSeconds = 15 * 60;
$maxAttempts = 5;
$attempts = $_SESSION['contact_rate'] ?? [];
$attempts = array_values(array_filter($attempts, function ($ts) use ($now, $windowSeconds) {
    return is_int($ts) && $ts > ($now - $windowSeconds);
}));

if (count($attempts) >= $maxAttempts) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Demasiadas solicitudes, intenta mas tarde']);
    exit();
}

$_SESSION['contact_rate'] = array_merge($attempts, [$now]);

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$body = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '') {
    $errors[] = 'El nombre es obligatorio';
} elseif (strlen($name) > 100) {
    $errors[] = 'El nombre supera el limite permitido';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email invalido';
} elseif (strlen($email) > 100) {
    $errors[] = 'El email supera el limite permitido';
}

if ($phone !== '') {
    if (strlen($phone) > 30) {
        $errors[] = 'El telefono supera el limite permitido';
    } elseif (!preg_match('/^[0-9+()\\s-]+$/', $phone)) {
        $errors[] = 'El telefono tiene un formato invalido';
    }
}

if ($subject !== '' && strlen($subject) > 200) {
    $errors[] = 'El asunto supera el limite permitido';
}

if ($body === '') {
    $errors[] = 'El mensaje es obligatorio';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $errors[0], 'errors' => $errors]);
    exit();
}

$messageService = new Message();
$messageId = $messageService->createMessage([
    'name' => $name,
    'email' => $email,
    'phone' => $phone !== '' ? $phone : null,
    'subject' => $subject !== '' ? $subject : null,
    'message' => $body,
    'status' => 'unread'
]);

if (!$messageId) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar el mensaje']);
    exit();
}

echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente', 'id' => $messageId]);
?>
