<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['sysadmin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/User.php';

$userModel = new User();
$search = trim($_GET['q'] ?? '');
$users = $search !== '' ? $userModel->search($search) : $userModel->getAll();
$roles = $userModel->getRoles();

$message = $_SESSION['users_message'] ?? '';
unset($_SESSION['users_message']);

$pageTitle = 'User Management';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">User Management</h1>

<?php if ($message): ?>
    <div class="success"><?= e($message) ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="page-title">Create User</h2>
    <form action="<?= BASE_URL ?>/sysadmin/process_users.php" method="POST">
        <input type="hidden" name="action" value="create">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <div class="password-wrap">
            <input type="password" id="password" name="password" required>
            <button type="button" class="password-toggle" data-target="password" aria-label="Show password">👁️</button>
        </div>

        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="contact">Contact Number</label>
        <input type="text" id="contact" name="contact" placeholder="0712 345 678">

        <label for="role_id">Role</label>
        <select id="role_id" name="role_id" required>
            <?php foreach ($roles as $role): ?>
                <option value="<?= (int) $role['id'] ?>"><?= e($role['role_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">Create User</button>
    </form>
</div>

<div class="card">
    <h2 class="page-title">System Users</h2>
    <form action="<?= BASE_URL ?>/sysadmin/users.php" method="GET" style="margin-bottom:1rem;">
        <input type="text" name="q" placeholder="Search by username or name" value="<?= e($search) ?>">
        <button type="submit" class="btn">Search</button>
        <?php if ($search !== ''): ?>
            <a href="<?= BASE_URL ?>/sysadmin/users.php" class="btn">Clear</a>
        <?php endif; ?>
    </form>
    <table>
        <tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Contact</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['username']) ?></td>
                <td><?= e($user['full_name']) ?></td>
                <td><?= e($user['contact'] ?? '-') ?></td>
                <td><?= e($user['role_name']) ?></td>
                <td><span class="badge"><?= e($user['status']) ?></span></td>
                <td class="actions">
                    <form action="<?= BASE_URL ?>/sysadmin/process_users.php" method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                        <div class="password-wrap" style="display:inline-block; width:140px;">
                            <input type="password" id="new_password_<?= (int) $user['id'] ?>" name="new_password" placeholder="New password" required style="width:100%; margin-bottom:0;">
                            <button type="button" class="password-toggle" data-target="new_password_<?= (int) $user['id'] ?>" aria-label="Show password">👁️</button>
                        </div>
                        <button type="submit" class="btn">Reset</button>
                    </form>
                    <form action="<?= BASE_URL ?>/sysadmin/process_users.php" method="POST" onsubmit="return confirm('Deactivate this user?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                        <button type="submit" class="btn-danger">Deactivate</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
