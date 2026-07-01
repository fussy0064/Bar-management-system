<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['cashier', 'admin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Order.php';

$orderId = (int) ($_GET['id'] ?? 0);

$orderModel = new Order();
$order = $orderModel->getById($orderId);

if (!$order) {
    redirectTo('/cashier/pos.php');
}

$items = $orderModel->getItems($orderId);

$pageTitle = 'Billing Slip';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="slip">
    <h2>Bundara Bar</h2>
    <div class="slip-sub">Dar es Salaam, Tanzania</div>

    <p>
        Order No: <?= e($order['order_number']) ?><br>
        Date: <?= e($order['created_at']) ?><br>
        Cashier: <?= e($order['cashier_name']) ?><br>
        Payment: <?= e(ucfirst(str_replace('_', ' ', $order['payment_method']))) ?>
    </p>

    <table>
        <tr><th>Item</th><th>Qty</th><th class="text-right">Subtotal</th></tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['product_name']) ?></td>
                <td><?= (int) $item['quantity'] ?></td>
                <td class="text-right"><?= formatCurrency($item['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="cart-total">
        <span>Total</span>
        <span><?= formatCurrency($order['total_amount']) ?></span>
    </div>

    <p style="text-align:center; color:#666666; font-size:13px;">Thank you for your visit</p>
</div>

<div class="no-print" style="text-align:center; margin-top:20px;">
    <button type="button" class="btn-primary" onclick="window.print()">Print Slip</button>
    <a href="<?= BASE_URL ?>/cashier/pos.php" class="btn">New Order</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
