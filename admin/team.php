<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$teamFile = __DIR__ . '/../data/team.json';
$team = [];
if (file_exists($teamFile)) {
    $data = json_decode(file_get_contents($teamFile), true);
    if (is_array($data)) {
        $team = array_map(function($member) {
            $member['visible'] = isset($member['visible']) ? (bool) $member['visible'] : true;
            $member['image'] = getSafeImagePath($member['image'] ?? '', 'assets/images/placeholder.jpg');
            return $member;
        }, $data);
    }
}

$success = false;
$errors = [];
$csrfToken = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token CSRF invalido';
    } else {
        $names = $_POST['name'] ?? [];
        $roles = $_POST['role'] ?? [];
        $descriptions = $_POST['description'] ?? [];
        $count = is_array($names) ? count($names) : 0;
        $newTeam = [];
        $uploadDir = __DIR__ . '/../assets/images/' . sanitizeUploadFolder('team') . '/';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $errors[] = 'No se pudo preparar la carpeta de imagenes';
        } else {
            for ($i = 0; $i < $count; $i++) {
                $name = cleanInput($names[$i] ?? '');
                $role = cleanInput($roles[$i] ?? '');
                $desc = cleanInput($descriptions[$i] ?? '');
                $visible = isset($_POST['visible'][$i]) && $_POST['visible'][$i] == '1';
                $image = getSafeImagePath($team[$i]['image'] ?? '', 'assets/images/placeholder.jpg');

                if ($name === '' || $role === '' || $desc === '') {
                    $errors[] = 'Todos los miembros del equipo deben tener nombre, cargo y descripcion';
                    break;
                }

                $tmpName = $_FILES['image']['tmp_name'][$i] ?? '';
                $fileError = (int) ($_FILES['image']['error'][$i] ?? UPLOAD_ERR_NO_FILE);
                $fileSize = (int) ($_FILES['image']['size'][$i] ?? 0);
                $originalName = (string) ($_FILES['image']['name'][$i] ?? 'imagen');

                if ($fileError !== UPLOAD_ERR_NO_FILE && $fileError !== UPLOAD_ERR_OK) {
                    $errors[] = "Error al subir la imagen de '$name'";
                    break;
                }

                if ($fileError === UPLOAD_ERR_OK) {
                    if ($tmpName === '' || !is_file($tmpName)) {
                        $errors[] = "No se pudo leer la imagen de '$name'";
                        break;
                    }

                    if ($fileSize <= 0 || $fileSize > (5 * 1024 * 1024)) {
                        $errors[] = "La imagen de '$name' excede el tamano permitido";
                        break;
                    }

                    $imageInfo = detectImageFileInfo($tmpName);
                    if (!$imageInfo) {
                        $errors[] = "La imagen de '$name' no es valida";
                        break;
                    }

                    $fileName = generateSafeImageFileName($originalName, $imageInfo['extension'], $uploadDir);
                    $destination = $uploadDir . $fileName;

                    $moveSuccess = move_uploaded_file($tmpName, $destination);
                    if (!$moveSuccess && !is_uploaded_file($tmpName)) {
                        $moveSuccess = @rename($tmpName, $destination);
                    }

                    if (!$moveSuccess) {
                        $errors[] = "No se pudo guardar la imagen de '$name'";
                        break;
                    }

                    @chmod($destination, 0644);
                    $image = 'assets/images/team/' . $fileName;
                }

                $newTeam[] = [
                    'name' => $name,
                    'role' => $role,
                    'description' => $desc,
                    'image' => $image,
                    'visible' => $visible
                ];
            }
        }

        if (empty($errors)) {
            file_put_contents($teamFile, json_encode($newTeam, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $team = $newTeam;
            $success = true;
        }
    }
}

$pageTitle = 'Equipo';
$activePage = 'team';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div id="content">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
        <div class="container-fluid">
            <h1 class="h3 mb-4">Nuestro Equipo</h1>
            <?php if ($success): ?>
                <div class="alert alert-success">Equipo actualizado correctamente</div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars(implode(' ', $errors)); ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <?php foreach ($team as $i => $member): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3 text-center">
                                    <img src="<?php echo htmlspecialchars(getSafeImagePath($member['image'] ?? '', 'assets/images/placeholder.jpg')); ?>" class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover;" alt="<?php echo htmlspecialchars($member['name'] ?? 'Miembro del equipo'); ?>">
                                    <input type="file" name="image[]" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
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
                                    <label class="form-label">Descripcion</label>
                                    <textarea name="description[]" class="form-control" rows="3" required><?php echo htmlspecialchars($member['description']); ?></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Visibilidad</label>
                                    <input type="hidden" name="visible[<?php echo $i; ?>]" value="0">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" name="visible[<?php echo $i; ?>]" value="1" id="visible-<?php echo $i; ?>" <?php echo !empty($member['visible']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="visible-<?php echo $i; ?>">Mostrar en la pagina</label>
                                    </div>
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
<?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
