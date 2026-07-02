<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if sysadmin role exists
    $roleStmt = $db->prepare('SELECT id FROM roles WHERE role_name = "sysadmin"');
    $roleStmt->execute();
    $role = $roleStmt->fetch();
    
    if (!$role) {
        // Insert roles if not present
        $db->exec('INSERT INTO roles (role_name) VALUES ("admin"), ("cashier"), ("sysadmin")');
        // Re-query role ID
        $roleStmt->execute();
        $role = $roleStmt->fetch();
    }
    
    $roleId = (int) $role['id'];
    
    $username = 'happybundara67@gmail.com';
    $password = 'Bundara@67';
    $fullName = 'Super Admin';
    
    // Delete existing user if any to prevent duplicate username constraint failures
    $deleteStmt = $db->prepare('DELETE FROM users WHERE username = ?');
    $deleteStmt->execute([$username]);
    
    // Create the user using User model
    $userModel = new User();
    $result = $userModel->create($username, $password, $fullName, $roleId, '0700000000');
    
    if ($result) {
        echo "SysAdmin seeded successfully!\n";
        echo "Username: {$username}\n";
        echo "Password: {$password}\n";
    } else {
        echo "Failed to seed SysAdmin.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
