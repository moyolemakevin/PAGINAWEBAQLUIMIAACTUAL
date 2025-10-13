<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';

// Debe ser administrador autenticado
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Validar CSRF
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit();
}

$user = new User();
$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $name = cleanInput($_POST['name'] ?? '');
            $email = cleanInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $phone = cleanInput($_POST['phone'] ?? '');
            $role = normalizeRole($_POST['role'] ?? 'customer');

            if (!$name || !$email || !$password) {
                throw new Exception('Nombre, email y contraseña son obligatorios.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido.');
            }

            if (strlen($password) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres.');
            }

            if ($user->emailExists($email)) {
                throw new Exception('El email ya está registrado.');
            }

            $userId = $user->createUser([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'role' => $role,
                'status' => 'active'
            ]);

            if (!$userId) {
                throw new Exception('Error al crear el usuario.');
            }

            $response = [
                'success' => true,
                'message' => 'Usuario creado correctamente.',
                'user_id' => $userId
            ];
            break;

        case 'update':
            $userId = (int)($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $email = cleanInput($_POST['email'] ?? '');
            $phone = cleanInput($_POST['phone'] ?? '');
            $role = normalizeRole($_POST['role'] ?? 'customer');

            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido.');
            }

            if (!$name || !$email) {
                throw new Exception('Nombre y email son obligatorios.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido.');
            }

            $existing = $user->getUserByEmail($email);
            if ($existing && (int)$existing['id'] !== $userId) {
                throw new Exception('El email ya está registrado por otro usuario.');
            }

            [$firstName, $lastName] = splitName($name);

            $result = $user->updateUserByAdmin($userId, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
                'status' => 'active'
            ]);

            if (!$result) {
                throw new Exception('Error al actualizar el usuario.');
            }

            $response = [
                'success' => true,
                'message' => 'Usuario actualizado correctamente.'
            ];
            break;

        case 'delete':
            $userId = (int)($_POST['id'] ?? 0);

            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido.');
            }

            if ($userId === (int)($_SESSION['user_id'] ?? 0)) {
                throw new Exception('No puedes eliminar tu propia cuenta.');
            }

            if (!$user->deleteUser($userId)) {
                throw new Exception('Error al eliminar el usuario.');
            }

            $response = [
                'success' => true,
                'message' => 'Usuario desactivado correctamente.'
            ];
            break;

        case 'reset_password':
            $userId = (int)($_POST['id'] ?? 0);

            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido.');
            }

            $newPassword = generateTemporaryPassword();
            if (!$user->updatePassword($userId, $newPassword)) {
                throw new Exception('Error al resetear la contraseña.');
            }

            $response = [
                'success' => true,
                'message' => 'Contraseña reseteada correctamente. Nueva contraseña: ' . $newPassword
            ];
            break;

        case 'get_user':
            $userId = (int)($_POST['id'] ?? 0);

            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido.');
            }

            $userData = $user->getUserById($userId);
            if (!$userData) {
                throw new Exception('Usuario no encontrado.');
            }

            $userData['name'] = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));

            $response = [
                'success' => true,
                'user' => $userData
            ];
            break;

        default:
            throw new Exception('Acción no válida.');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
exit();

/**
 * Normalizar nombre completo.
 */
function splitName(string $name): array {
    $name = trim($name);
    if ($name === '') {
        return ['Usuario', ''];
    }

    $parts = preg_split('/\s+/', $name);
    $firstName = array_shift($parts) ?? 'Usuario';
    $lastName = implode(' ', $parts);

    return [$firstName, $lastName];
}

/**
 * Ajustar rol recibido desde el formulario.
 */
function normalizeRole(string $role): string {
    $role = strtolower(trim($role));
    return $role === 'admin' ? 'admin' : 'customer';
}

/**
 * Generar contraseña temporal segura.
 */
function generateTemporaryPassword(): string {
    return substr(strtoupper(bin2hex(random_bytes(4))), 0, 8);
}
?> 
