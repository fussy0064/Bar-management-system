<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Product.php';

class Order
{
    private PDO $db;
    private Logger $logger;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->logger = new Logger();
    }

    // $items is an array of ['product_id' => int, 'quantity' => int, 'unit_price' => float]
    public function create(int $cashierId, array $items, string $paymentMethod): int|false
    {
        if (empty($items)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $productStmt = $this->db->prepare('SELECT product_name, stock_quantity, selling_price FROM products WHERE id = ? FOR UPDATE');
            $role = $_SESSION['role'] ?? 'cashier';

            $total = 0.0;
            $validatedItems = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                $productStmt->execute([$productId]);
                $product = $productStmt->fetch();

                if (!$product) {
                    throw new Exception("Product not found: ID {$productId}");
                }

                if ($role !== 'admin') {
                    // Cashier validation
                    if ($product['stock_quantity'] < $quantity) {
                        throw new Exception("Not enough stock for " . $product['product_name']);
                    }
                    $price = (float) $product['selling_price'];
                } else {
                    // Admin can override price and quantity
                    $price = (float) $item['unit_price'];
                }

                $subtotal = $price * $quantity;
                $total += $subtotal;

                $validatedItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $subtotal
                ];
            }

            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $stmt = $this->db->prepare(
                'INSERT INTO orders (order_number, cashier_id, total_amount, payment_method, status, created_at)
                 VALUES (?, ?, ?, ?, "completed", NOW())'
            );
            $stmt->execute([$orderNumber, $cashierId, $total, $paymentMethod]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?)'
            );

            $productModel = new Product();
            foreach ($validatedItems as $vItem) {
                $itemStmt->execute([$orderId, $vItem['product_id'], $vItem['quantity'], $vItem['unit_price'], $vItem['subtotal']]);
                $productModel->reduceStock((int) $vItem['product_id'], (int) $vItem['quantity']);
            }

            $this->db->commit();
            $this->logger->log($cashierId, 'CREATE_ORDER', "Order {$orderNumber} created, total TZS " . number_format($total, 0));

            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.full_name AS cashier_name
             FROM orders o
             JOIN users u ON o.cashier_id = u.id
             WHERE o.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getItems(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT oi.*, p.product_name
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function getRecent(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.full_name AS cashier_name
             FROM orders o
             JOIN users u ON o.cashier_id = u.id
             ORDER BY o.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDailySales(): array
    {
        $stmt = $this->db->query(
            'SELECT DATE(created_at) AS sale_date, COUNT(*) AS order_count, SUM(total_amount) AS daily_total
             FROM orders
             WHERE status = "completed"
             GROUP BY DATE(created_at)
             ORDER BY sale_date DESC
             LIMIT 30'
        );
        return $stmt->fetchAll();
    }

    public function getTodayTotal(): float
    {
        $stmt = $this->db->query(
            'SELECT COALESCE(SUM(total_amount), 0) AS total
             FROM orders
             WHERE status = "completed" AND DATE(created_at) = CURDATE()'
        );
        return (float) $stmt->fetch()['total'];
    }
}
