<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Logger.php';

class User
{
    private PDO $db;
    private Logger $logger;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new Logger();
    }

    public function login(string $username, string $password): bool
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.role_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.username = ? AND u.status = "active"'
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && Security::verifyPassword($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role_name'];
            $this->logger->log((int) $user['id'], 'LOGIN', 'User logged in');
            return true;
        }

        return false;
    }

    public function create(string $username, string $password, string $fullName, int $roleId, ?string $contact = null): bool
    {
        $contactEncrypted = $contact ? Security::encrypt($contact) : null;

        $stmt = $this->db->prepare(
            'INSERT INTO users (username, password_hash, full_name, contact_encrypted, role_id, status, created_at)
             VALUES (?, ?, ?, ?, ?, "active", NOW())'
        );
        $result = $stmt->execute([
            $username,
            Security::hashPassword($password),
            $fullName,
            $contactEncrypted,
            $roleId,
        ]);

        if ($result) {
            $this->logger->log($_SESSION['user_id'] ?? null, 'CREATE_USER', "Created user: {$username}");
        }

        return $result;
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET status = "inactive" WHERE id = ?');
        $result = $stmt->execute([$userId]);

        if ($result) {
            $this->logger->log($_SESSION['user_id'] ?? null, 'DELETE_USER', "Deactivated user ID: {$userId}");
        }

        return $result;
    }

    public function resetPassword(int $userId, string $newPassword): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $result = $stmt->execute([Security::hashPassword($newPassword), $userId]);

        if ($result) {
            $this->logger->log($_SESSION['user_id'] ?? null, 'RESET_PASSWORD', "Password reset for user ID: {$userId}");
        }

        return $result;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT u.id, u.username, u.full_name, u.contact_encrypted, r.role_name, u.status, u.created_at
             FROM users u
             JOIN roles r ON u.role_id = r.id
             ORDER BY u.created_at DESC'
        );
        $users = $stmt->fetchAll();

        foreach ($users as &$user) {
            $user['contact'] = Security::decrypt($user['contact_encrypted']);
        }

        return $users;
    }

    public function getRoles(): array
    {
        $stmt = $this->db->query('SELECT * FROM roles ORDER BY id');
        return $stmt->fetchAll();
    }
}
