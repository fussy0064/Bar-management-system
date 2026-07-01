<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Logger.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    $logger = new Logger();
    $logger->log($_SESSION['user_id'], 'LOGOUT', 'User logged out');
}

$_SESSION = [];
session_destroy();
redirectTo('/auth/login.php');
