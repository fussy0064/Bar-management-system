<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="brand">Bundara Bar</div>
    <div class="nav-links">
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/inventory.php">Inventory</a>
            <a href="<?= BASE_URL ?>/admin/sales_report.php">Sales Report</a>
            <a href="<?= BASE_URL ?>/cashier/pos.php">New Order</a>
        <?php elseif (($_SESSION['role'] ?? '') === 'cashier'): ?>
            <a href="<?= BASE_URL ?>/cashier/pos.php">New Order</a>
        <?php elseif (($_SESSION['role'] ?? '') === 'sysadmin'): ?>
            <a href="<?= BASE_URL ?>/sysadmin/users.php">Users</a>
            <a href="<?= BASE_URL ?>/sysadmin/activity_log.php">Activity Log</a>
        <?php endif; ?>
        <span class="nav-user"><?= e($_SESSION['full_name'] ?? '') ?></span>
        <a href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
    </div>
</nav>
<main>
