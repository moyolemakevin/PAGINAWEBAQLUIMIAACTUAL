<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (isPublicAuthDisabled()) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow', true);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Acceso con Google no disponible en este dominio.');
}

if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    die('Google OAuth no esta configurado. Define GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET.');
}

$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state,
    'access_type' => 'online',
    'include_granted_scopes' => 'true',
    'prompt' => 'consent',
];

$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $authUrl);
exit;
