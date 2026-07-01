<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Order.php';

$allowedRoles = ['cashier', 'admin'];
require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$items = $input['items'] ?? [];
$paymentMethod = $input['payment_method'] ?? 'cash';

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'No items in order']);
    exit;
}

$validMethods = ['cash', 'mobile_money', 'card'];
if (!in_array($paymentMethod, $validMethods, true)) {
    $paymentMethod = 'cash';
}

$cleanItems = [];
$role = $_SESSION['role'] ?? 'cashier';

if ($role === 'admin') {
    foreach ($items as $item) {
        $cleanItems[] = [
            'product_id' => (int) $item['product_id'],
            'quantity' => (int) $item['quantity'],
            'unit_price' => (float) $item['unit_price'],
        ];
    }
} else {
    require_once __DIR__ . '/../classes/Product.php';
    $productModel = new Product();
    
    foreach ($items as $item) {
        $productId = (int) $item['product_id'];
        $quantity = (int) $item['quantity'];
        
        $product = $productModel->getById($productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock for ' . $product['product_name']]);
            exit;
        }
        
        $cleanItems[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => (float) $product['selling_price'],
        ];
    }
}

$orderModel = new Order();
$orderId = $orderModel->create((int) $_SESSION['user_id'], $cleanItems, $paymentMethod);

if ($orderId) {
    echo json_encode(['success' => true, 'order_id' => $orderId]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not process the order']);
}
