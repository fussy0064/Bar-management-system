<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';

class Product
{
    private PDO $db;
    private Logger $logger;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new Logger();
    }

    public function create(string $name, int $categoryId, float $costPrice, float $sellingPrice, int $stock, string $unit): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (product_name, category_id, cost_price, selling_price, stock_quantity, unit, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())'
        );
        $result = $stmt->execute([$name, $categoryId, $costPrice, $sellingPrice, $stock, $unit]);

        if ($result) {
            $this->logger->log($_SESSION['user_id'] ?? null, 'ADD_PRODUCT', "Added product: {$name}");
        }

        return $result;
    }

    public function update(int $id, string $name, int $categoryId, float $costPrice, float $sellingPrice, int $stock, string $unit): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET product_name = ?, category_id = ?, cost_price = ?, selling_price = ?, stock_quantity = ?, unit = ?, updated_at = NOW()
             WHERE id = ?'
        );
        $result = $stmt->execute([$name, $categoryId, $costPrice, $sellingPrice, $stock, $unit, $id]);

        if ($result) {
            $this->logger->log($_SESSION['user_id'] ?? null, 'UPDATE_PRODUCT', "Updated product ID: {$id}");
        }

        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $result = $stmt->execute([$id]);

        if ($result) {
            $this->logger->log($_SESSION['user_id'] ?? null, 'DELETE_PRODUCT', "Deleted product ID: {$id}");
        }

        return $result;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT p.*, c.category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             ORDER BY p.product_name'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getCategories(): array
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY category_name');
        return $stmt->fetchAll();
    }

    public function reduceStock(int $id, int $quantity): bool
    {
        $stmt = $this->db->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?');
        return $stmt->execute([$quantity, $id]);
    }

    public function getLowStock(int $threshold = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.stock_quantity <= ?
             ORDER BY p.stock_quantity ASC'
        );
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function getSalesPerformance(): array
    {
        $stmt = $this->db->query(
            'SELECT p.product_name, SUM(oi.quantity) AS total_sold, SUM(oi.subtotal) AS total_revenue
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             JOIN orders o ON oi.order_id = o.id
             WHERE o.status = "completed"
             GROUP BY p.id, p.product_name
             ORDER BY total_revenue DESC'
        );
        return $stmt->fetchAll();
    }
}
