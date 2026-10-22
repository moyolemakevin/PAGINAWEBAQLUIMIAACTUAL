<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = trim((string)($data['email'] ?? ''));
$name  = trim((string)($data['name'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

$record = [
    'email' => $email,
    'name'  => $name,
    'subscribed_at' => date('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'ua' => $_SERVER['HTTP_USER_AGENT'] ?? ''
];

$file = __DIR__ . '/data/newsletter.json';
$list = [];
if (file_exists($file)) {
    $existing = json_decode(file_get_contents($file), true);
    if (is_array($existing)) {
        $list = $existing;
    }
}

// Evitar duplicados por email
$list = array_values(array_filter($list, function($item) use ($email) {
    return isset($item['email']) && strcasecmp($item['email'], $email) !== 0;
}));
$list[] = $record;

if (file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar suscripción']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Suscripción registrada']);
