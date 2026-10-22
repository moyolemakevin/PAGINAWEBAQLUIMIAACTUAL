<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalido']);
    exit();
}

$response = ['success' => false, 'message' => 'Accion invalida'];
$messageService = new Message();

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_message':
            $messageId = (int)($_POST['id'] ?? 0);
            if ($messageId <= 0) {
                throw new Exception('ID invalido');
            }

            $message = $messageService->getMessageById($messageId);
            if (!$message) {
                throw new Exception('Mensaje no encontrado');
            }

            $response = ['success' => true, 'message' => $message];
            break;

        case 'send_reply':
            $messageId = (int)($_POST['id'] ?? 0);
            $toEmail = trim($_POST['to_email'] ?? '');
            $toName = trim($_POST['to_name'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $body = trim($_POST['message'] ?? '');
            $sendCopy = ($_POST['send_copy'] ?? '') === '1';

            if ($messageId <= 0) {
                throw new Exception('ID invalido');
            }

            if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email de destinatario invalido');
            }

            if ($subject === '' || $body === '') {
                throw new Exception('Asunto y mensaje son obligatorios');
            }

            $message = $messageService->getMessageById($messageId);
            if (!$message) {
                throw new Exception('Mensaje no encontrado');
            }

            $fromEmail = getSiteSetting('contact_email', ADMIN_EMAIL);
            $fromName = getSiteSetting('site_name', SITE_NAME);
            $bcc = $sendCopy ? $fromEmail : null;

            if (!sendEmail($toEmail, $subject, $body, $fromEmail, $fromName, $bcc)) {
                throw new Exception('No se pudo enviar el correo. Revisa la configuracion de correo del servidor.');
            }

            if (!$messageService->updateStatus($messageId, 'replied')) {
                throw new Exception('Respuesta enviada, pero no se pudo actualizar el estado');
            }

            $response = ['success' => true, 'message' => 'Respuesta enviada'];
            break;

        case 'update_status':
            $messageId = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            if ($messageId <= 0) {
                throw new Exception('ID invalido');
            }

            if (!$messageService->updateStatus($messageId, $status)) {
                throw new Exception('No se pudo actualizar el estado');
            }

            $response = ['success' => true, 'message' => 'Estado actualizado', 'status' => $status];
            break;

        case 'delete_message':
            $messageId = (int)($_POST['id'] ?? 0);
            if ($messageId <= 0) {
                throw new Exception('ID invalido');
            }

            if (!$messageService->deleteMessage($messageId)) {
                throw new Exception('No se pudo eliminar el mensaje');
            }

            $response = ['success' => true, 'message' => 'Mensaje eliminado'];
            break;

        case 'mark_all_read':
            if (!$messageService->markAllRead()) {
                throw new Exception('No se pudieron actualizar los mensajes');
            }

            $response = ['success' => true, 'message' => 'Mensajes actualizados'];
            break;

        case 'delete_all':
            if (!$messageService->deleteAllMessages()) {
                throw new Exception('No se pudieron eliminar los mensajes');
            }

            $response = ['success' => true, 'message' => 'Mensajes eliminados'];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
