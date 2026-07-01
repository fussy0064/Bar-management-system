<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['admin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('/admin/inventory.php');
}

$productModel = new Product();
$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $productModel->create(
        trim($_POST['product_name']),
        (int) $_POST['category_id'],
        (float) $_POST['cost_price'],
        (float) $_POST['selling_price'],
        (int) $_POST['stock_quantity'],
        trim($_POST['unit'])
    );
    $_SESSION['inventory_message'] = 'Product added successfully';
} elseif ($action === 'update') {
    $productModel->update(
        (int) $_POST['id'],
        trim($_POST['product_name']),
        (int) $_POST['category_id'],
        (float) $_POST['cost_price'],
        (float) $_POST['selling_price'],
        (int) $_POST['stock_quantity'],
        trim($_POST['unit'])
    );
    $_SESSION['inventory_message'] = 'Product updated successfully';
} elseif ($action === 'delete') {
    $productModel->delete((int) $_POST['id']);
    $_SESSION['inventory_message'] = 'Product deleted successfully';
}

redirectTo('/admin/inventory.php');
