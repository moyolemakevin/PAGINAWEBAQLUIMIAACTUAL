<?php
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

// Verificar token CSRF
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['error' => 'Token CSRF inválido']);
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
            $role = cleanInput($_POST['role'] ?? 'customer');
            
            // Validaciones
            if (empty($name) || empty($email) || empty($password)) {
                throw new Exception('Todos los campos obligatorios deben estar completos');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }
            
            if (strlen($password) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            // Verificar si el email ya existe
            if ($user->getUserByEmail($email)) {
                throw new Exception('El email ya está registrado');
            }
            
            $userId = $user->createUser($name, $email, $password, $phone, $role);
            
            if ($userId) {
                $response = [
                    'success' => true,
                    'message' => 'Usuario creado correctamente',
                    'user_id' => $userId
                ];
            } else {
                throw new Exception('Error al crear el usuario');
            }
            break;
            
        case 'update':
            $userId = (int)($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $email = cleanInput($_POST['email'] ?? '');
            $phone = cleanInput($_POST['phone'] ?? '');
            $role = cleanInput($_POST['role'] ?? 'customer');
            
            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido');
            }
            
            if (empty($name) || empty($email)) {
                throw new Exception('Nombre y email son obligatorios');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }
            
            // Verificar si el email ya existe en otro usuario
            $existingUser = $user->getUserByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                throw new Exception('El email ya está registrado por otro usuario');
            }
            
            $result = $user->updateAdminUser($userId, $name, $email, $phone, $role);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Usuario actualizado correctamente'
                ];
            } else {
                throw new Exception('Error al actualizar el usuario');
            }
            break;
            
        case 'delete':
            $userId = (int)($_POST['id'] ?? 0);
            
            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido');
            }
            
            // No permitir eliminar el propio usuario
            if ($userId == $_SESSION['user_id']) {
                throw new Exception('No puedes eliminar tu propia cuenta');
            }
            
            $result = $user->deleteUser($userId);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Usuario eliminado correctamente'
                ];
            } else {
                throw new Exception('Error al eliminar el usuario');
            }
            break;
            
        case 'reset_password':
            $userId = (int)($_POST['id'] ?? 0);
            
            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido');
            }
            
            $newPassword = bin2hex(random_bytes(4));
            $result = $user->updatePassword($userId, $newPassword);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Contraseña reseteada correctamente. Nueva contraseña: ' . $newPassword
                ];
            } else {
                throw new Exception('Error al resetear la contraseña');
            }
            break;
            
        case 'get_user':
            $userId = (int)($_POST['id'] ?? 0);
            
            if ($userId <= 0) {
                throw new Exception('ID de usuario inválido');
            }
            
            $userData = $user->getUserById($userId);

            if ($userData) {
                unset($userData['password']);
                $response = [
                    'success' => true,
                    'user' => $userData
                ];
            } else {
                throw new Exception('Usuario no encontrado');
            }
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?> 
