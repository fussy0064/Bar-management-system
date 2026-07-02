<?php
// Bundara Bar Management System - configuration
// Update these values to match your server environment

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'bundara_bar');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

// Used by the Security class for AES-256 encryption
// Replace with a long random value in production and keep it secret
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'change-this-to-a-long-random-secret-key');

define('APP_NAME', 'Bundara Bar Management System');
define('APP_CURRENCY', 'TSh');
define('APP_TIMEZONE', 'Africa/Dar_es_Salaam');

date_default_timezone_set(APP_TIMEZONE);

// Dynamically determine the base URL to support hosting in subdirectories (like on localhost)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$dir = dirname($scriptName);
if ($dir === '/' || $dir === '\\') {
    $dir = '';
} else {
    $dir = preg_replace('/(\/(auth|admin|cashier|sysadmin|database|classes|config|includes|assets).*)?$/i', '', $dir);
}
define('BASE_URL', $dir);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
