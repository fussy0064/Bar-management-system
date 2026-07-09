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
    $data = [
        'name' => trim($_POST['product_name'] ?? ''),
        'cost_price' => $_POST['cost_price'] ?? null,
        'selling_price' => $_POST['selling_price'] ?? null,
        'stock' => $_POST['stock_quantity'] ?? null,
    ];
    $errors = $productModel->validate($data);

    if (!empty($errors)) {
        $_SESSION['inventory_message'] = implode(', ', $errors);
    } else {
        $productModel->create(
            $data['name'],
            (int) $_POST['category_id'],
            (float) $data['cost_price'],
            (float) $data['selling_price'],
            (int) $data['stock'],
            trim($_POST['unit'])
        );
        $_SESSION['inventory_message'] = 'Product added successfully';
    }
} elseif ($action === 'update') {
    $data = [
        'name' => trim($_POST['product_name'] ?? ''),
        'cost_price' => $_POST['cost_price'] ?? null,
        'selling_price' => $_POST['selling_price'] ?? null,
        'stock' => $_POST['stock_quantity'] ?? null,
    ];
    $errors = $productModel->validate($data);

    if (!empty($errors)) {
        $_SESSION['inventory_message'] = implode(', ', $errors);
    } else {
        $productModel->update(
            (int) $_POST['id'],
            $data['name'],
            (int) $_POST['category_id'],
            (float) $data['cost_price'],
            (float) $data['selling_price'],
            (int) $data['stock'],
            trim($_POST['unit'])
        );
        $_SESSION['inventory_message'] = 'Product updated successfully';
    }
} elseif ($action === 'delete') {
    $productModel->delete((int) $_POST['id']);
    $_SESSION['inventory_message'] = 'Product deleted successfully';
}

redirectTo('/admin/inventory.php');
