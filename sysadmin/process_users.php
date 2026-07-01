<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['sysadmin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('/sysadmin/users.php');
}

$userModel = new User();
$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $contact = trim($_POST['contact'] ?? '');
    $userModel->create(
        trim($_POST['username']),
        $_POST['password'],
        trim($_POST['full_name']),
        (int) $_POST['role_id'],
        $contact !== '' ? $contact : null
    );
    $_SESSION['users_message'] = 'User created successfully';
} elseif ($action === 'reset_password') {
    $userModel->resetPassword((int) $_POST['id'], $_POST['new_password']);
    $_SESSION['users_message'] = 'Password reset successfully';
} elseif ($action === 'delete') {
    $userModel->delete((int) $_POST['id']);
    $_SESSION['users_message'] = 'User deactivated successfully';
}

redirectTo('/sysadmin/users.php');
