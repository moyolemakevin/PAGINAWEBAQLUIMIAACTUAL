<?php
$activePage = $activePage ?? '';
$unreadMessages = 0;
try {
    $messageService = new Message();
    $messageStats = $messageService->getMessageStats();
    $unreadMessages = (int)($messageStats['unread'] ?? 0);
} catch (Throwable $e) {
    $unreadMessages = 0;
}
$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'href' => 'dashboard.php'],
    'products' => ['label' => 'Productos', 'icon' => 'fa-box', 'href' => 'products.php'],
    'categories' => ['label' => 'Categorias', 'icon' => 'fa-tags', 'href' => 'categories.php'],
    'orders' => ['label' => 'Pedidos', 'icon' => 'fa-shopping-cart', 'href' => 'orders.php'],
    'users' => ['label' => 'Usuarios', 'icon' => 'fa-users', 'href' => 'users.php'],
    'messages' => ['label' => 'Mensajes', 'icon' => 'fa-envelope', 'href' => 'messages.php'],
    'team' => ['label' => 'Equipo', 'icon' => 'fa-users-cog', 'href' => 'team.php'],
    'settings' => ['label' => 'Configuracion', 'icon' => 'fa-cog', 'href' => 'settings.php'],
];
?>
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-flask"></i>
                    Admin Panel
                </h3>
            </div>

            <ul class="list-unstyled components">
                <?php foreach ($navItems as $key => $item): ?>
                    <?php $isActive = $activePage === $key; ?>
                    <li class="<?php echo $isActive ? 'active' : ''; ?>">
                        <a href="<?php echo $item['href']; ?>" <?php echo $isActive ? 'aria-current="page"' : ''; ?>>
                            <i class="fas <?php echo $item['icon']; ?>"></i>
                            <?php echo $item['label']; ?>
                            <?php if ($key === 'messages' && $unreadMessages > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo $unreadMessages; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
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
