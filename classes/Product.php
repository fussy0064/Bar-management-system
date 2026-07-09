<?php

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Security.php';

class Product extends Model
{
    public function getTableName(): string
    {
        return 'products';
    }

    // cost_price is business-sensitive, so it is encrypted before saving.
    // selling_price stays plain so reports/search can use it directly.
    public function validate(array $data): array
    {
        $errors = [];
        Validator::required($data['name'] ?? '', 'Product name', $errors);
        Validator::positive($data['cost_price'] ?? null, 'Cost price', $errors);
        Validator::positive($data['selling_price'] ?? null, 'Selling price', $errors);
        Validator::positive($data['stock'] ?? null, 'Stock quantity', $errors);
        return $errors;
    }

    public function create(string $name, int $categoryId, float $costPrice, float $sellingPrice, int $stock, string $unit): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (product_name, category_id, cost_price, selling_price, stock_quantity, unit, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())'
        );
        $result = $stmt->execute([
            $name,
            $categoryId,
            Security::encrypt((string) $costPrice),
            $sellingPrice,
            $stock,
            $unit,
        ]);

        if ($result) {
            $this->logChange('ADD_PRODUCT', "Added product: {$name}");
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
        $result = $stmt->execute([
            $name,
            $categoryId,
            Security::encrypt((string) $costPrice),
            $sellingPrice,
            $stock,
            $unit,
            $id,
        ]);

        if ($result) {
            $this->logChange('UPDATE_PRODUCT', "Updated product ID: {$id}");
        }

        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $result = $stmt->execute([$id]);

        if ($result) {
            $this->logChange('DELETE_PRODUCT', "Deleted product ID: {$id}");
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
        return $this->decryptCostPrice($stmt->fetchAll());
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            return false;
        }
        $product['cost_price'] = Security::decrypt($product['cost_price']);
        return $product;
    }

    // Search by product name or category (search functionality requirement)
    public function search(string $keyword): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.product_name LIKE ? OR c.category_name LIKE ?
             ORDER BY p.product_name'
        );
        $like = '%' . $keyword . '%';
        $stmt->execute([$like, $like]);
        return $this->decryptCostPrice($stmt->fetchAll());
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
        return $this->decryptCostPrice($stmt->fetchAll());
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

    private function decryptCostPrice(array $rows): array
    {
        foreach ($rows as &$row) {
            if (isset($row['cost_price'])) {
                $row['cost_price'] = Security::decrypt($row['cost_price']);
            }
        }
        return $rows;
    }
}
