<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (isPublicAuthDisabled()) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow', true);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Acceso con Google no disponible en este dominio.');
}

if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    die('Google OAuth no esta configurado.');
}

$state = $_GET['state'] ?? '';
if (!$state || !isset($_SESSION['google_oauth_state']) || $state !== $_SESSION['google_oauth_state']) {
    die('Estado invalido. Intenta de nuevo.');
}
unset($_SESSION['google_oauth_state']);

$code = $_GET['code'] ?? '';
if (!$code) {
    die('No se recibio codigo de autorizacion.');
}

$tokenData = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code',
];

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$tokenResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$tokenResponse) {
    die('No se pudo obtener el token de Google.');
}

$tokenJson = json_decode($tokenResponse, true);
$accessToken = $tokenJson['access_token'] ?? '';

if (!$accessToken) {
    die('Respuesta de token invalida.');
}

$profileResponse = file_get_contents(
    'https://www.googleapis.com/oauth2/v3/userinfo',
    false,
    stream_context_create([
        'http' => [
            'header' => 'Authorization: Bearer ' . $accessToken,
        ],
    ])
);
$profileJson = $profileResponse ? json_decode($profileResponse, true) : null;

if (!$profileJson) {
    die('No se pudo obtener el perfil de Google.');
}

$email = $profileJson['email'] ?? '';
$name = $profileJson['name'] ?? trim(($profileJson['given_name'] ?? '') . ' ' . ($profileJson['family_name'] ?? ''));

if (!$email) {
    die('Google no devolvio un email verificable.');
}

$userModel = new User();
$existing = $userModel->getUserByEmail($email);

if (!$existing) {
    $password = bin2hex(random_bytes(8));
    $created = $userModel->createUser($name ?: $email, $email, $password, '', 'customer');
    if (!$created) {
        die('No se pudo crear el usuario con Google.');
    }
    $existing = $userModel->getUserByEmail($email);
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

$_SESSION['user_id'] = $existing['id'];
$_SESSION['user_role'] = normalizeUserRole($existing['role'] ?? '');
$_SESSION['user_name'] = trim(($existing['first_name'] ?? '') . ' ' . ($existing['last_name'] ?? '')) ?: ($existing['name'] ?? $email);

header('Location: ' . getPostLoginRedirectUrl());
exit;
