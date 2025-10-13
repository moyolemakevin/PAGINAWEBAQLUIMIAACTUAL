<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';

$user = new User();
$user->logout();

redirect(SITE_URL . '/index.php');
?> 