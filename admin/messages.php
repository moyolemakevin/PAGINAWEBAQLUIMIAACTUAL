<?php
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Mensajes desde base de datos
$messageService = new Message();
$messages = $messageService->getAllMessages();
$stats = $messageService->getMessageStats();
$csrfToken = generateCSRFToken();
$pageTitle = 'Gestionar Mensajes';
$activePage = 'messages';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/partials/topbar.php'; ?>

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
                                        <div class="h5 mb-0" id="statTotal"><?php echo (int)($stats['total'] ?? 0); ?></div>
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
                                        <div class="h5 mb-0" id="statUnread"><?php echo (int)($stats['unread'] ?? 0); ?></div>
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
                                        <div class="h5 mb-0" id="statReplied"><?php echo (int)($stats['replied'] ?? 0); ?></div>
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
                                        <div class="h5 mb-0" id="statToday"><?php echo (int)($stats['today'] ?? 0); ?></div>
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
                                <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-inbox me-2"></i>
                                        Bandeja de Entrada
                                    </h5>
                                    <div class="d-flex gap-2">
                                        <select id="messageStatusFilter" class="form-select form-select-sm">
                                            <option value="all">Todos</option>
                                            <option value="unread">No leidos</option>
                                            <option value="read">Leidos</option>
                                            <option value="replied">Respondidos</option>
                                        </select>
                                        <input type="text" id="messageSearch" class="form-control form-control-sm" placeholder="Buscar">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush" id="messageList">
                                    <?php if (empty($messages)): ?>
                                        <div class="list-group-item text-center text-muted py-4 message-empty">
                                            No hay mensajes.
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($messages as $msg): ?>
                                            <?php
                                                $status = $msg['status'] ?? 'unread';
                                                $subject = $msg['subject'] ?? '';
                                                $subjectLabel = $subject !== '' ? $subject : 'Sin asunto';
                                                $messageBody = $msg['message'] ?? '';
                                                $snippet = substr($messageBody, 0, 50);
                                                if (strlen($messageBody) > 50) {
                                                    $snippet .= '...';
                                                }
                                                $createdAt = $msg['created_at'] ?? '';
                                                $createdLabel = $createdAt ? date('d/m H:i', strtotime($createdAt)) : '';
                                            ?>
                                            <div class="list-group-item list-group-item-action message-item <?php echo $status === 'unread' ? 'fw-bold' : ''; ?>"
                                                 data-message-id="<?php echo (int)$msg['id']; ?>"
                                                 data-status="<?php echo htmlspecialchars($status); ?>"
                                                 data-created-at="<?php echo htmlspecialchars($createdAt); ?>">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($msg['name'] ?? ''); ?></h6>
                                                    <small><?php echo htmlspecialchars($createdLabel); ?></small>
                                                </div>
                                                <p class="mb-1 text-truncate"><?php echo htmlspecialchars($subjectLabel); ?></p>
                                                <small class="text-muted"><?php echo htmlspecialchars($snippet); ?></small>
                                                <?php if ($status === 'unread'): ?>
                                                    <span class="badge bg-warning float-end">Nuevo</span>
                                                <?php elseif ($status === 'replied'): ?>
                                                    <span class="badge bg-success float-end">Respondido</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
                        <input type="hidden" id="replyMessageId" name="message_id">
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
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
        <script>
        const csrfToken = '<?php echo $csrfToken; ?>';
        const siteName = <?php echo json_encode(SITE_NAME); ?>;
        const messageList = document.getElementById('messageList');
        const messageDetails = document.getElementById('messageDetails');
        const statusFilter = document.getElementById('messageStatusFilter');
        const searchInput = document.getElementById('messageSearch');
        let currentMessage = null;

        function notify(message, type) {
            if (typeof showNotification === 'function') {
                showNotification(message, type);
            } else {
                alert(message);
            }
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        function formatDate(value) {
            if (!value) return '';
            const normalized = value.replace(' ', 'T');
            const date = new Date(normalized);
            if (Number.isNaN(date.getTime())) {
                return value;
            }
            return date.toLocaleString();
        }

        function isToday(value) {
            if (!value) return false;
            const datePart = value.split(' ')[0];
            const now = new Date();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const today = `${now.getFullYear()}-${month}-${day}`;
            return datePart === today;
        }

        function getStatValue(id) {
            const el = document.getElementById(id);
            if (!el) return 0;
            const value = parseInt(el.textContent, 10);
            return Number.isNaN(value) ? 0 : value;
        }

        function setStatValue(id, value) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = value;
            }
        }

        function adjustStat(id, delta) {
            const value = getStatValue(id);
            const nextValue = Math.max(0, value + delta);
            setStatValue(id, nextValue);
        }

        function updateStatsForStatusChange(oldStatus, newStatus) {
            if (oldStatus === newStatus) return;
            if (oldStatus === 'unread') adjustStat('statUnread', -1);
            if (newStatus === 'unread') adjustStat('statUnread', 1);
            if (oldStatus === 'replied') adjustStat('statReplied', -1);
            if (newStatus === 'replied') adjustStat('statReplied', 1);
        }

        function updateListItemStatus(messageId, status) {
            if (!messageList) return;
            const item = messageList.querySelector(`[data-message-id="${messageId}"]`);
            if (!item) return;
            item.dataset.status = status;
            item.classList.toggle('fw-bold', status === 'unread');

            const existingBadge = item.querySelector('.badge');
            if (existingBadge) {
                existingBadge.remove();
            }

            if (status === 'unread' || status === 'replied') {
                const badge = document.createElement('span');
                badge.className = `badge ${status === 'unread' ? 'bg-warning' : 'bg-success'} float-end`;
                badge.textContent = status === 'unread' ? 'Nuevo' : 'Respondido';
                item.appendChild(badge);
            }

            applyMessageFilters();
        }

        function applyMessageFilters() {
            if (!messageList) return;
            const statusValue = statusFilter ? statusFilter.value : 'all';
            const term = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let visibleCount = 0;

            const items = messageList.querySelectorAll('.message-item');
            if (items.length === 0) {
                return;
            }

            items.forEach(item => {
                const itemStatus = item.dataset.status || 'unread';
                const matchesStatus = statusValue === 'all' || itemStatus === statusValue;
                const matchesSearch = term === '' || item.textContent.toLowerCase().includes(term);
                const shouldShow = matchesStatus && matchesSearch;
                item.style.display = shouldShow ? '' : 'none';
                if (shouldShow) {
                    visibleCount += 1;
                }
            });

            const existingEmpty = messageList.querySelector('.message-empty');
            if (existingEmpty) {
                existingEmpty.remove();
            }

            if (visibleCount === 0) {
                const empty = document.createElement('div');
                empty.className = 'list-group-item text-center text-muted py-4 message-empty';
                empty.textContent = 'No hay resultados.';
                messageList.appendChild(empty);
            }
        }

        function setMessageDetails(message) {
            if (!messageDetails) return;
            const subject = message.subject ? message.subject : 'Sin asunto';
            const statusLabel = message.status === 'unread' ? 'Nuevo' : message.status === 'replied' ? 'Respondido' : 'Leido';
            const statusClass = message.status === 'unread' ? 'bg-warning' : message.status === 'replied' ? 'bg-success' : 'bg-secondary';
            const showMarkRead = message.status === 'unread';
            const phoneValue = message.phone ? String(message.phone) : '';
            const phoneLine = phoneValue ? `<p class="text-muted mb-1"><i class="fas fa-phone me-2"></i>${escapeHtml(phoneValue)}</p>` : '';

            messageDetails.innerHTML = `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6>${escapeHtml(message.name || '')}</h6>
                            <p class="text-muted mb-1">${escapeHtml(message.email || '')}</p>
                            ${phoneLine}
                        </div>
                        <div class="text-end">
                            <small class="text-muted">${escapeHtml(formatDate(message.created_at || ''))}</small>
                            <br>
                            <span id="messageStatusBadge" class="badge ${statusClass}">${statusLabel}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <h6>Asunto:</h6>
                    <p class="fw-bold">${escapeHtml(subject)}</p>
                </div>
                <div class="mb-3">
                    <h6>Mensaje:</h6>
                    <div class="border rounded p-3 bg-light">
                        ${escapeHtml(message.message || '').replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="openReplyModal()">
                        <i class="fas fa-reply me-2"></i>Responder
                    </button>
                    <button class="btn btn-outline-success" onclick="markAsRead(${message.id})" ${showMarkRead ? '' : 'disabled'}>
                        <i class="fas fa-check me-2"></i>Marcar como Leido
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteMessage(${message.id})">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </div>
            `;
        }

        function messageRequest(action, payload) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('csrf_token', csrfToken);
            Object.keys(payload || {}).forEach(key => {
                formData.append(key, payload[key]);
            });
            return fetch('process_message.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json());
        }

        function viewMessage(messageId) {
            messageRequest('get_message', { id: messageId })
                .then(data => {
                    if (!data.success) {
                        notify(data.message || 'No se pudo cargar el mensaje', 'error');
                        return;
                    }

                    currentMessage = data.message;
                    setMessageDetails(currentMessage);

                    if (currentMessage.status === 'unread') {
                        markAsRead(messageId);
                    }
                })
                .catch(error => {
                    notify('Error al cargar el mensaje: ' + error.message, 'error');
                });
        }

        function openReplyModal() {
            if (!currentMessage) {
                notify('No se encontro el mensaje', 'error');
                return;
            }
            const replyTo = document.getElementById('replyTo');
            const replyToEmail = document.getElementById('replyToEmail');
            const replyToName = document.getElementById('replyToName');
            const replySubject = document.getElementById('replySubject');
            const replyMessage = document.getElementById('replyMessage');
            const replyMessageId = document.getElementById('replyMessageId');

            if (replyTo) replyTo.value = `${currentMessage.name || ''} <${currentMessage.email || ''}>`;
            if (replyToEmail) replyToEmail.value = currentMessage.email || '';
            if (replyToName) replyToName.value = currentMessage.name || '';
            if (replySubject) replySubject.value = `Re: ${currentMessage.subject || ''}`;
            if (replyMessage) replyMessage.value = `Hola ${currentMessage.name || ''},

Gracias por contactarnos.

Saludos cordiales,
Equipo de ${siteName}`;
            if (replyMessageId) replyMessageId.value = currentMessage.id;

            const modal = new bootstrap.Modal(document.getElementById('replyModal'));
            modal.show();
        }

        function sendReply() {
            const replyMessageId = document.getElementById('replyMessageId');
            const replySubject = document.getElementById('replySubject');
            const replyMessage = document.getElementById('replyMessage');
            const replyToEmail = document.getElementById('replyToEmail');
            const replyToName = document.getElementById('replyToName');
            const sendCopy = document.getElementById('sendCopy');
            const sendButton = document.querySelector('#replyModal .btn.btn-primary');
            const messageId = replyMessageId ? parseInt(replyMessageId.value, 10) : 0;

            if (!messageId) {
                notify('No se encontro el mensaje', 'error');
                return;
            }
            if (!replySubject || !replySubject.value.trim() || !replyMessage || !replyMessage.value.trim()) {
                notify('Completa el asunto y el mensaje antes de enviar', 'error');
                return;
            }
            if (!replyToEmail || !replyToEmail.value.trim()) {
                notify('No se encontro el email del destinatario', 'error');
                return;
            }

            if (sendButton) {
                sendButton.disabled = true;
                sendButton.dataset.originalText = sendButton.innerHTML;
                sendButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviando...';
            }

            const oldStatus = currentMessage ? currentMessage.status : '';
            messageRequest('send_reply', {
                id: messageId,
                to_email: replyToEmail.value.trim(),
                to_name: replyToName ? replyToName.value.trim() : '',
                subject: replySubject.value.trim(),
                message: replyMessage.value.trim(),
                send_copy: sendCopy && sendCopy.checked ? '1' : '0'
            })
                .then(data => {
                    if (!data.success) {
                        notify(data.message || 'No se pudo enviar el mensaje', 'error');
                        return;
                    }

                    updateStatsForStatusChange(oldStatus, 'replied');
                    updateListItemStatus(messageId, 'replied');
                    if (currentMessage && currentMessage.id === messageId) {
                        currentMessage.status = 'replied';
                        setMessageDetails(currentMessage);
                    }
                    notify('Respuesta enviada correctamente', 'success');

                    const modal = bootstrap.Modal.getInstance(document.getElementById('replyModal'));
                    if (modal) modal.hide();
                })
                .catch(error => {
                    notify('Error al responder: ' + error.message, 'error');
                })
                .finally(() => {
                    if (sendButton) {
                        sendButton.disabled = false;
                        sendButton.innerHTML = sendButton.dataset.originalText || sendButton.innerHTML;
                    }
                });
        }

        function markAsRead(messageId) {
            if (!messageId) return;
            const oldStatus = currentMessage && currentMessage.id === messageId ? currentMessage.status : 'unread';
            if (oldStatus !== 'unread') return;
            messageRequest('update_status', { id: messageId, status: 'read' })
                .then(data => {
                    if (!data.success) return;

                    updateStatsForStatusChange(oldStatus, 'read');
                    updateListItemStatus(messageId, 'read');
                    if (currentMessage && currentMessage.id === messageId) {
                        currentMessage.status = 'read';
                        setMessageDetails(currentMessage);
                    }
                })
                .catch(error => {
                    notify('Error al actualizar el estado: ' + error.message, 'error');
                });
        }

        function removeMessageFromList(messageId) {
            if (!messageList) return;
            const item = messageList.querySelector(`[data-message-id="${messageId}"]`);
            if (item) {
                item.remove();
            }

            const remaining = messageList.querySelectorAll('.message-item').length;
            if (remaining === 0) {
                messageList.innerHTML = '<div class="list-group-item text-center text-muted py-4 message-empty">No hay mensajes.</div>';
                return;
            }

            applyMessageFilters();
        }

        function deleteMessage(messageId) {
            if (!confirm('Estas seguro de que quieres eliminar este mensaje?')) {
                return;
            }

            const deletedMessage = currentMessage && currentMessage.id === messageId ? currentMessage : null;
            messageRequest('delete_message', { id: messageId })
                .then(data => {
                    if (!data.success) {
                        notify(data.message || 'No se pudo eliminar el mensaje', 'error');
                        return;
                    }

                    removeMessageFromList(messageId);
                    adjustStat('statTotal', -1);
                    if (deletedMessage) {
                        if (deletedMessage.status === 'unread') adjustStat('statUnread', -1);
                        if (deletedMessage.status === 'replied') adjustStat('statReplied', -1);
                        if (isToday(deletedMessage.created_at)) adjustStat('statToday', -1);
                    }

                    if (currentMessage && currentMessage.id === messageId && messageDetails) {
                        messageDetails.innerHTML = `
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-envelope fa-3x mb-3"></i>
                                <p>Selecciona un mensaje para ver su contenido</p>
                            </div>
                        `;
                        currentMessage = null;
                    }

                    notify('Mensaje eliminado', 'success');
                })
                .catch(error => {
                    notify('Error al eliminar el mensaje: ' + error.message, 'error');
                });
        }

        function markAllAsRead() {
            if (!confirm('Marcar todos los mensajes como leidos?')) {
                return;
            }

            messageRequest('mark_all_read', {})
                .then(data => {
                    if (!data.success) {
                        notify(data.message || 'No se pudieron actualizar los mensajes', 'error');
                        return;
                    }

                    if (messageList) {
                        const items = messageList.querySelectorAll('[data-status="unread"]');
                        items.forEach(item => {
                            updateListItemStatus(item.dataset.messageId, 'read');
                        });
                    }
                    setStatValue('statUnread', 0);
                    if (currentMessage && currentMessage.status === 'unread') {
                        currentMessage.status = 'read';
                        setMessageDetails(currentMessage);
                    }
                    notify('Mensajes marcados como leidos', 'success');
                })
                .catch(error => {
                    notify('Error al actualizar mensajes: ' + error.message, 'error');
                });
        }

        function deleteAllMessages() {
            if (!confirm('Estas seguro de que quieres eliminar todos los mensajes? Esta accion no se puede deshacer.')) {
                return;
            }

            messageRequest('delete_all', {})
                .then(data => {
                    if (!data.success) {
                        notify(data.message || 'No se pudieron eliminar los mensajes', 'error');
                        return;
                    }

                    if (messageList) {
                    messageList.innerHTML = '<div class="list-group-item text-center text-muted py-4 message-empty">No hay mensajes.</div>';
                    }
                    setStatValue('statTotal', 0);
                    setStatValue('statUnread', 0);
                    setStatValue('statReplied', 0);
                    setStatValue('statToday', 0);

                    if (messageDetails) {
                        messageDetails.innerHTML = `
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-envelope fa-3x mb-3"></i>
                                <p>Selecciona un mensaje para ver su contenido</p>
                            </div>
                        `;
                    }
                    currentMessage = null;
                    notify('Mensajes eliminados', 'success');
                })
                .catch(error => {
                    notify('Error al eliminar mensajes: ' + error.message, 'error');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (!messageList) return;
            const items = messageList.querySelectorAll('.message-item');
            items.forEach(item => {
                item.addEventListener('click', function() {
                    viewMessage(this.dataset.messageId);
                });
            });

            if (statusFilter) {
                statusFilter.addEventListener('change', applyMessageFilters);
            }
            if (searchInput) {
                searchInput.addEventListener('input', applyMessageFilters);
            }
            applyMessageFilters();
        });
    </script>

</body>
</html> 

