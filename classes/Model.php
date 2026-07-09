<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';

/**
 * Abstract base class for all data models.
 * Demonstrates: abstraction, inheritance, polymorphism.
 */
abstract class Model
{
    protected PDO $db;
    protected Logger $logger;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new Logger();
    }

    // Each child class must say which table it owns (abstraction)
    abstract public function getTableName(): string;

    // Each child class must define its own validation rules (abstraction)
    // Returns an array of error messages. Empty array = valid.
    abstract public function validate(array $data): array;

    // Shared method, but behaves differently per child because
    // getTableName() and validate() are overridden (polymorphism)
    public function logChange(string $action, string $description): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $this->logger->log($userId, $action, "[" . $this->getTableName() . "] " . $description);
    }
}
