<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['admin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Order.php';

$productModel = new Product();
$orderModel = new Order();

$performance = $productModel->getSalesPerformance();
$dailySales = $orderModel->getDailySales();

$pageTitle = 'Sales Report';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Sales Report</h1>

<div class="card">
    <h2 class="page-title">Product Sales Performance</h2>
    <?php if (empty($performance)): ?>
        <p>No sales recorded yet.</p>
    <?php else: ?>
        <table>
            <tr><th>Product</th><th>Units Sold</th><th>Revenue</th></tr>
            <?php foreach ($performance as $row): ?>
                <tr>
                    <td><?= e($row['product_name']) ?></td>
                    <td><?= (int) $row['total_sold'] ?></td>
                    <td><?= formatCurrency($row['total_revenue']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h2 class="page-title">Daily Sales Summary</h2>
    <?php if (empty($dailySales)): ?>
        <p>No sales recorded yet.</p>
    <?php else: ?>
        <table>
            <tr><th>Date</th><th>Orders</th><th>Total</th></tr>
            <?php foreach ($dailySales as $row): ?>
                <tr>
                    <td><?= e($row['sale_date']) ?></td>
                    <td><?= (int) $row['order_count'] ?></td>
                    <td><?= formatCurrency($row['daily_total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
