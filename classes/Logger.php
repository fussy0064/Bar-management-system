<?php

require_once __DIR__ . '/../config/Database.php';

class Logger
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function log(?int $userId, string $action, string $description = ''): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO activity_logs (user_id, action, description, created_at) VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$userId, $action, $description]);
    }

    public function getRecent(int $limit = 100): array
    {
        $stmt = $this->db->prepare(
            'SELECT al.*, u.full_name
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
