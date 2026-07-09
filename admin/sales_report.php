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

$orderSearch = trim($_GET['order_q'] ?? '');
$orderResults = $orderSearch !== '' ? $orderModel->search($orderSearch) : [];

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
    <h2 class="page-title">Find an Order</h2>
    <form action="<?= BASE_URL ?>/admin/sales_report.php" method="GET" style="margin-bottom:1rem;">
        <input type="text" name="order_q" placeholder="Search by order number" value="<?= e($orderSearch) ?>">
        <button type="submit" class="btn">Search</button>
    </form>
    <?php if ($orderSearch !== ''): ?>
        <?php if (empty($orderResults)): ?>
            <p>No orders found.</p>
        <?php else: ?>
            <table>
                <tr><th>Order #</th><th>Cashier</th><th>Total</th><th>Payment</th><th>Date</th></tr>
                <?php foreach ($orderResults as $order): ?>
                    <tr>
                        <td><?= e($order['order_number']) ?></td>
                        <td><?= e($order['cashier_name']) ?></td>
                        <td><?= formatCurrency($order['total_amount']) ?></td>
                        <td><?= e($order['payment_method']) ?></td>
                        <td><?= e($order['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
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
