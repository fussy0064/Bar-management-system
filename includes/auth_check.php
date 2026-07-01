<?php

// Include this at the top of any protected page after setting $allowedRoles
// Example: $allowedRoles = ['admin'];

if (!isset($_SESSION['user_id'])) {
    redirectTo('/auth/login.php');
}

if (isset($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles, true)) {
    redirectTo('/index.php');
}
