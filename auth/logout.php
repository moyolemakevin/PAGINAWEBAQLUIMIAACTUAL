<?php
require_once __DIR__ . '/../config/bootstrap.php';

$user = new User();
$user->logout();

redirect(SITE_URL . '/index.php');
?> 
