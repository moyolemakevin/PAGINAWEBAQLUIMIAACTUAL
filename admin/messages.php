<?php
require_once __DIR__ . '/../config/config.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Simular mensajes
$messages = [
    [
        'id' => 1,
        'name' => 'María González',
        'email' => 'maria@ejemplo.com',
        'phone' => '+593 983015307',
        'subject' => 'Consulta sobre software personalizado',
        'message' => 'Hola, me interesa mucho el software personalizado que ofrecen. ¿Podrían darme más información sobre los precios y tiempos de entrega?',
        'status' => 'unread',
        'created_at' => '2025-01-15 10:30:00'
    ],
    [
        'id' => 2,
        'name' => 'Carlos Rodríguez',
        'email' => 'carlos@ejemplo.com',
        'phone' => '+593 987654321',
        'subject' => 'Aceites esenciales',
        'message' => 'Buenos días, quisiera saber si tienen aceites esenciales de lavanda y eucalipto. ¿Cuál es el precio por unidad?',
        'status' => 'read',
        'created_at' => '2025-01-14 15:45:00'
    ],
    [
        'id' => 3,
        'name' => 'Ana Martínez',
        'email' => 'ana@ejemplo.com',
        'phone' => '+593 912345678',
        'subject' => 'Figuras de yeso artesanales',
        'message' => 'Me encantan las figuras de yeso que tienen. ¿Hacen figuras personalizadas por encargo?',
        'status' => 'replied',
        'created_at' => '2025-01-13 09:20:00'
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Mensajes - Admin <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-flask"></i>
                    Admin Panel
                </h3>
            </div>
            
            <ul class="list-unstyled components">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        Productos
                    </a>
                </li>
                <li>
                    <a href="categories.php">
                        <i class="fas fa-tags"></i>
                        Categorías
                    </a>
                </li>
                <li>
                    <a href="orders.php">
                        <i class="fas fa-shopping-cart"></i>
                        Pedidos
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        Usuarios
                    </a>
                </li>
                <li class="active">
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i>
                        Mensajes
                    </a>
                </li>
                <li>
                    <a href="team.php">
                        <i class="fas fa-users-cog"></i>
                        Equipo
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        Configuración
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="../index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-eye me-2"></i>Ver Sitio
                </a>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Salir
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                <?php echo $_SESSION['user_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h3 mb-0">Gestionar Mensajes</h1>
                            <div>
                                <button class="btn btn-outline-success me-2" onclick="markAllAsRead()">
                                    <i class="fas fa-check-double me-2"></i>Marcar Todo como Leído
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteAllMessages()">
                                    <i class="fas fa-trash me-2"></i>Eliminar Todo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Mensajes</div>
                                        <div class="h5 mb-0"><?php echo count($messages); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">No Leídos</div>
                                        <div class="h5 mb-0">1</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope-open fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Respondidos</div>
                                        <div class="h5 mb-0">1</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-reply fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Hoy</div>
                                        <div class="h5 mb-0">1</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-day fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages List -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-inbox me-2"></i>
                                    Bandeja de Entrada
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($messages as $msg): ?>
                                        <div class="list-group-item list-group-item-action <?php echo $msg['status'] == 'unread' ? 'fw-bold' : ''; ?>" 
                                             onclick="viewMessage(<?php echo $msg['id']; ?>)">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($msg['name']); ?></h6>
                                                <small><?php echo date('d/m H:i', strtotime($msg['created_at'])); ?></small>
                                            </div>
                                            <p class="mb-1 text-truncate"><?php echo htmlspecialchars($msg['subject']); ?></p>
                                            <small class="text-muted"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></small>
                                            <?php if ($msg['status'] == 'unread'): ?>
                                                <span class="badge bg-warning float-end">Nuevo</span>
                                            <?php elseif ($msg['status'] == 'replied'): ?>
                                                <span class="badge bg-success float-end">Respondido</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-envelope-open me-2"></i>
                                    Detalle del Mensaje
                                </h5>
                            </div>
                            <div class="card-body" id="messageDetails">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-envelope fa-3x mb-3"></i>
                                    <p>Selecciona un mensaje para ver su contenido</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reply Modal -->
    <div class="modal fade" id="replyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Responder Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="replyForm">
                        <input type="hidden" id="replyToEmail" name="to_email">
                        <input type="hidden" id="replyToName" name="to_name">
                        
                        <div class="mb-3">
                            <label class="form-label">Para:</label>
                            <input type="text" class="form-control" id="replyTo" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Asunto:</label>
                            <input type="text" class="form-control" name="subject" id="replySubject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mensaje:</label>
                            <textarea class="form-control" name="message" id="replyMessage" rows="8" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendCopy" name="send_copy">
                                <label class="form-check-label" for="sendCopy">
                                    Enviar copia a mi email
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="sendReply()">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Respuesta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        // Message management functions
        function viewMessage(messageId) {
            const messages = <?php echo json_encode($messages); ?>;
            const message = messages.find(m => m.id == messageId);
            
            if (message) {
                document.getElementById('messageDetails').innerHTML = `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6>${message.name}</h6>
                                <p class="text-muted mb-1">${message.email}</p>
                                <p class="text-muted mb-0">${message.phone}</p>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">${new Date(message.created_at).toLocaleString()}</small>
                                <br>
                                ${message.status === 'unread' ? '<span class="badge bg-warning">Nuevo</span>' : 
                                  message.status === 'replied' ? '<span class="badge bg-success">Respondido</span>' : 
                                  '<span class="badge bg-secondary">Leído</span>'}
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6>Asunto:</h6>
                        <p class="fw-bold">${message.subject}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Mensaje:</h6>
                        <div class="border rounded p-3 bg-light">
                            ${message.message.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="replyToMessage('${message.email}', '${message.name}', '${message.subject}')">
                            <i class="fas fa-reply me-2"></i>Responder
                        </button>
                        <button class="btn btn-outline-success" onclick="markAsRead(${message.id})">
                            <i class="fas fa-check me-2"></i>Marcar como Leído
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteMessage(${message.id})">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                    </div>
                `;
                
                // Mark as read if unread
                if (message.status === 'unread') {
                    markAsRead(messageId);
                }
            }
        }
        
        function replyToMessage(email, name, subject) {
            document.getElementById('replyTo').value = `${name} <${email}>`;
            document.getElementById('replyToEmail').value = email;
            document.getElementById('replyToName').value = name;
            document.getElementById('replySubject').value = `Re: ${subject}`;
            document.getElementById('replyMessage').value = `Hola ${name},\n\nGracias por contactarnos.\n\nSaludos cordiales,\nEquipo de AlquimiaTechnologic`;
            
            const modal = new bootstrap.Modal(document.getElementById('replyModal'));
            modal.show();
        }
        
        function sendReply() {
            // Simulate sending reply
            alert('Respuesta enviada correctamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('replyModal'));
            modal.hide();
            location.reload();
        }
        
        function markAsRead(messageId) {
            // Simulate marking as read
            console.log('Message marked as read:', messageId);
        }
        
        function deleteMessage(messageId) {
            if (confirm('¿Estás seguro de que quieres eliminar este mensaje?')) {
                // Simulate deleting message
                alert('Mensaje eliminado correctamente');
                location.reload();
            }
        }
        
        function markAllAsRead() {
            if (confirm('¿Marcar todos los mensajes como leídos?')) {
                // Simulate marking all as read
                alert('Todos los mensajes marcados como leídos');
                location.reload();
            }
        }
        
        function deleteAllMessages() {
            if (confirm('¿Estás seguro de que quieres eliminar todos los mensajes? Esta acción no se puede deshacer.')) {
                // Simulate deleting all messages
                alert('Todos los mensajes eliminados');
                location.reload();
            }
        }
    </script>
</body>
</html> 