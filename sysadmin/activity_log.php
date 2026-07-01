<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['sysadmin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Logger.php';

$logger = new Logger();
$logs = $logger->getRecent(150);

$pageTitle = 'Activity Log';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">System Activity Log</h1>

<div class="card">
    <table>
        <tr><th>Date/Time</th><th>User</th><th>Action</th><th>Description</th></tr>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= e($log['created_at']) ?></td>
                <td><?= e($log['full_name'] ?? 'System') ?></td>
                <td><span class="badge"><?= e($log['action']) ?></span></td>
                <td><?= e($log['description']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
