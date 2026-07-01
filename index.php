<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirectTo('/auth/login.php');
}

switch ($_SESSION['role'] ?? '') {
    case 'admin':
        redirectTo('/admin/dashboard.php');
    case 'cashier':
        redirectTo('/cashier/pos.php');
    case 'sysadmin':
        redirectTo('/sysadmin/users.php');
    default:
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        redirectTo('/auth/login.php');
}
