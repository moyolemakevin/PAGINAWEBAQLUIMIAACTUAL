<?php
require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$teamFile = __DIR__ . '/../data/team.json';
$team = [];
if (file_exists($teamFile)) {
    $data = json_decode(file_get_contents($teamFile), true);
    if (is_array($data)) {
        $team = $data;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTeam = [];
    $count = count($_POST['name']);
    for ($i = 0; $i < $count; $i++) {
        $name = cleanInput($_POST['name'][$i] ?? '');
        $role = cleanInput($_POST['role'][$i] ?? '');
        $desc = cleanInput($_POST['description'][$i] ?? '');
        $image = $team[$i]['image'] ?? 'assets/images/placeholder.jpg';
        if (isset($_FILES['image']['tmp_name'][$i]) && $_FILES['image']['tmp_name'][$i]) {
            $uploadDir = __DIR__ . '/../assets/images/team/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid() . '_' . basename($_FILES['image']['name'][$i]);
            move_uploaded_file($_FILES['image']['tmp_name'][$i], $uploadDir . $filename);
            $image = 'assets/images/team/' . $filename;
        }
        $newTeam[] = [
            'name' => $name,
            'role' => $role,
            'description' => $desc,
            'image' => $image
        ];
    }
    file_put_contents($teamFile, json_encode($newTeam, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $team = $newTeam;
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo - Admin <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-flask"></i> Admin Panel</h3>
        </div>
        <ul class="list-unstyled components">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i>Productos</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i>Categorías</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i>Pedidos</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i>Usuarios</a></li>
            <li><a href="messages.php"><i class="fas fa-envelope"></i>Mensajes</a></li>
            <li class="active"><a href="team.php"><i class="fas fa-users-cog"></i>Equipo</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i>Configuración</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-eye me-2"></i>Ver Sitio</a>
            <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt me-2"></i>Salir</a>
        </div>
    </nav>
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary"><i class="fas fa-bars"></i></button>
                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo $_SESSION['user_name']; ?>
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
        <div class="container-fluid">
            <h1 class="h3 mb-4">Nuestro Equipo</h1>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">Equipo actualizado correctamente</div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <?php foreach ($team as $i => $member): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3 text-center">
                                    <img src="<?php echo htmlspecialchars($member['image']); ?>" class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover;">
                                    <input type="file" name="image[]" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name[]" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cargo</label>
                                    <input type="text" name="role[]" class="form-control" value="<?php echo htmlspecialchars($member['role']); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description[]" class="form-control" rows="3" required><?php echo htmlspecialchars($member['description']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>
