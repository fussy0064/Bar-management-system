<?php

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Security.php';

class User extends Model
{
    public function getTableName(): string
    {
        return 'users';
    }

    public function validate(array $data): array
    {
        $errors = [];
        Validator::required($data['username'] ?? '', 'Username', $errors);
        Validator::required($data['full_name'] ?? '', 'Full name', $errors);
        if (!empty($data['password'])) {
            Validator::minLength($data['password'], 6, 'Password', $errors);
        }
        if (!empty($data['contact'])) {
            Validator::minLength($data['contact'], 7, 'Contact number', $errors);
        }
        return $errors;
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
            $this->logChange('LOGIN', 'User logged in');
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
            $this->logChange('CREATE_USER', "Created user: {$username}");
        }

        return $result;
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET status = "inactive" WHERE id = ?');
        $result = $stmt->execute([$userId]);

        if ($result) {
            $this->logChange('DELETE_USER', "Deactivated user ID: {$userId}");
        }

        return $result;
    }

    public function resetPassword(int $userId, string $newPassword): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $result = $stmt->execute([Security::hashPassword($newPassword), $userId]);

        if ($result) {
            $this->logChange('RESET_PASSWORD', "Password reset for user ID: {$userId}");
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
        return $this->decryptContact($stmt->fetchAll());
    }

    // Search by username or full name (search functionality requirement)
    public function search(string $keyword): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.username, u.full_name, u.contact_encrypted, r.role_name, u.status, u.created_at
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.username LIKE ? OR u.full_name LIKE ?
             ORDER BY u.created_at DESC'
        );
        $like = '%' . $keyword . '%';
        $stmt->execute([$like, $like]);
        return $this->decryptContact($stmt->fetchAll());
    }

    public function getRoles(): array
    {
        $stmt = $this->db->query('SELECT * FROM roles ORDER BY id');
        return $stmt->fetchAll();
    }

    private function decryptContact(array $users): array
    {
        foreach ($users as &$user) {
            $user['contact'] = Security::decrypt($user['contact_encrypted']);
        }
        return $users;
    }
}
