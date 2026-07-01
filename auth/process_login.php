<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('/auth/login.php');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Please enter username and password';
    redirectTo('/auth/login.php');
}

$userModel = new User();

if ($userModel->login($username, $password)) {
    redirectTo('/index.php');
} else {
    $_SESSION['login_error'] = 'Invalid username or password';
    redirectTo('/auth/login.php');
}
