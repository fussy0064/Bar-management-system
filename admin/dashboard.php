<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['admin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Order.php';

$productModel = new Product();
$orderModel = new Order();

$products = $productModel->getAll();
$lowStock = $productModel->getLowStock(10);
$todayTotal = $orderModel->getTodayTotal();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Admin Dashboard</h1>

<div class="grid">
    <div class="stat-box">
        <div class="stat-value"><?= formatCurrency($todayTotal) ?></div>
        <div class="stat-label">Sales Today</div>
    </div>
    <div class="stat-box">
        <div class="stat-value"><?= count($products) ?></div>
        <div class="stat-label">Products in Catalog</div>
    </div>
    <div class="stat-box">
        <div class="stat-value"><?= count($lowStock) ?></div>
        <div class="stat-label">Low Stock Items</div>
    </div>
</div>

<div class="card">
    <h2 class="page-title">Low Stock Alert</h2>
    <?php if (empty($lowStock)): ?>
        <p>All products are sufficiently stocked.</p>
    <?php else: ?>
        <table>
            <tr><th>Product</th><th>Category</th><th>Stock</th><th>Unit</th></tr>
            <?php foreach ($lowStock as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= e($item['category_name']) ?></td>
                    <td><span class="badge badge-low"><?= (int) $item['stock_quantity'] ?></span></td>
                    <td><?= e($item['unit']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
