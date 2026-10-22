<?php
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$user = new User();

// Obtener usuarios
$users = $user->getAllUsers();
$totalUsers = count($users);
$activeUsers = 0;
$adminUsers = 0;
$newToday = 0;
$today = date('Y-m-d');
foreach ($users as $userRow) {
    if (($userRow['status'] ?? 'active') === 'active') {
        $activeUsers++;
    }
    if (($userRow['role'] ?? '') === 'admin') {
        $adminUsers++;
    }
    if (!empty($userRow['created_at']) && strpos($userRow['created_at'], $today) === 0) {
        $newToday++;
    }
}
$pageTitle = 'Gestionar Usuarios';
$activePage = 'users';
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
                            <h1 class="h3 mb-0">Gestionar Usuarios</h1>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                            </button>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Usuarios</div>
                                        <div class="h5 mb-0"><?php echo $totalUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-white-50"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Activos</div>
                                        <div class="h5 mb-0"><?php echo $activeUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-check fa-2x text-white-50"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Nuevos Hoy</div>
                                        <div class="h5 mb-0"><?php echo $newToday; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-plus fa-2x text-white-50"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Administradores</div>
                                        <div class="h5 mb-0"><?php echo $adminUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-shield fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Lista de Usuarios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Avatar</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $userData): ?>
                                        <tr>
                                            <td><?php echo $userData['id']; ?></td>
                                            <td>
                                                <img src="../assets/images/placeholder.jpg"
                                                     alt="<?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?>"
                                                     class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></strong>
                                                <?php if (!empty($userData['phone'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($userData['phone']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($userData['email']); ?></td>
                                            <td>
                                                <?php if (($userData['role'] ?? '') === 'admin'): ?>
                                                    <span class="badge bg-danger">Administrador</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Usuario</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $userData['status'] ?? 'active';
                                                $statusClass = $status === 'active' ? 'success' : 'secondary';
                                                $statusLabel = $status === 'active' ? 'Activo' : 'Inactivo';
                                                ?>
                                                <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                            </td>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($userData['created_at'])); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editUser(<?php echo $userData['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info btn-sm" onclick="viewUser(<?php echo $userData['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm" onclick="resetPassword(<?php echo $userData['id']; ?>)">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteUser(<?php echo $userData['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contrasena</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefono</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role">
                                <option value="customer">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" name="id" id="editUserId">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" id="editUserName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editUserEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefono</label>
                            <input type="tel" class="form-control" name="phone" id="editUserPhone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role" id="editUserRole">
                                <option value="customer">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateUser()">Actualizar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetails">
                    <!-- User details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
    <script>
        // CSRF Token
        const csrfToken = '<?php echo generateCSRFToken(); ?>';

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }
        
        // User management functions
        function editUser(userId) {
            // Load user data via AJAX
            const formData = new FormData();
            formData.append('action', 'get_user');
            formData.append('id', userId);
            formData.append('csrf_token', csrfToken);
            
            fetch('process_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    const fullName = user.name || [user.first_name, user.last_name].filter(Boolean).join(' ');
                    document.getElementById('editUserId').value = user.id;
                    document.getElementById('editUserName').value = fullName;
                    document.getElementById('editUserEmail').value = user.email;
                    document.getElementById('editUserPhone').value = user.phone || '';
                    document.getElementById('editUserRole').value = user.role === 'admin' ? 'admin' : 'customer';
                    
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del usuario');
            });
        }
        
        function viewUser(userId) {
            // Load user data via AJAX
            const formData = new FormData();
            formData.append('action', 'get_user');
            formData.append('id', userId);
            formData.append('csrf_token', csrfToken);
            
            fetch('process_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    const fullName = user.name || [user.first_name, user.last_name].filter(Boolean).join(' ');
                    const roleLabel = user.role === 'admin' ? 'Administrador' : 'Usuario';
                    const roleClass = user.role === 'admin' ? 'danger' : 'primary';
                    const statusLabel = user.status === 'inactive' ? 'Inactivo' : 'Activo';
                    const statusClass = user.status === 'inactive' ? 'secondary' : 'success';
                    const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
                    document.getElementById('userDetails').innerHTML = `
                        <div class="text-center mb-3">
                            <img src="../assets/images/placeholder.jpg"
                                   alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> ${user.id}</p>
                                <p><strong>Nombre:</strong> ${escapeHtml(fullName)}</p>
                                <p><strong>Email:</strong> ${escapeHtml(user.email)}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Telefono:</strong> ${escapeHtml(user.phone || 'No especificado')}</p>
                                <p><strong>Rol:</strong> <span class="badge bg-${roleClass}">${roleLabel}</span></p>
                                <p><strong>Estado:</strong> <span class="badge bg-${statusClass}">${statusLabel}</span></p>
                            </div>
                        </div>
                        <hr>
                        <h6>Estadísticas</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-primary">5</h4>
                                    <small>Pedidos</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-success">$150.000</h4>
                                    <small>Total Gastado</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-info">3</h4>
                                    <small>Productos Favoritos</small>
                                </div>
                            </div>
                        </div>
                    `;
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del usuario');
            });
        }
        
        function saveUser() {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);
            formData.append('action', 'create');
            formData.append('csrf_token', csrfToken);
            
            fetch('process_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    modal.hide();
                    form.reset();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el usuario');
            });
        }
        
        function updateUser() {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);
            formData.append('action', 'update');
            formData.append('csrf_token', csrfToken);
            
            fetch('process_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el usuario');
            });
        }
        
        function resetPassword(userId) {
            if (confirm('Estas seguro de que quieres resetear la contrasena de este usuario?')) {
                const formData = new FormData();
                formData.append('action', 'reset_password');
                formData.append('id', userId);
                formData.append('csrf_token', csrfToken);
                
                fetch('process_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al resetear la contraseña');
                });
            }
        }
        
        function deleteUser(userId) {
            if (confirm('Estas seguro de que quieres eliminar este usuario?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', userId);
                formData.append('csrf_token', csrfToken);
                
                fetch('process_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el usuario');
                });
            }
        }
    </script>
</body>
</html> 

