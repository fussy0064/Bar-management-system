<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['cashier', 'admin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Product.php';

$productModel = new Product();
$products = $productModel->getAll();

$pageTitle = 'New Order';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">New Order</h1>

<div class="pos-layout">
    <div class="card">
        <h2 class="page-title">Products</h2>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <button type="button" class="product-tile"
                    onclick="addToCart(<?= (int) $product['id'] ?>, '<?= e($product['product_name']) ?>', <?= (float) $product['selling_price'] ?>, <?= (int) $product['stock_quantity'] ?>)">
                    <div class="p-name"><?= e($product['product_name']) ?></div>
                    <div class="p-price"><?= formatCurrency($product['selling_price']) ?></div>
                    <div class="p-price">Stock: <?= (int) $product['stock_quantity'] ?></div>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2 class="page-title">Current Order</h2>
        <div id="cartBody"></div>
        <div class="cart-total">
            <span>Total</span>
            <span id="cartTotal">TSh 0</span>
        </div>
        <label for="paymentMethod">Payment Method</label>
        <select id="paymentMethod">
            <option value="cash">Cash</option>
            <option value="mobile_money">Mobile Money</option>
            <option value="card">Card</option>
        </select>
        <button type="button" class="btn-primary" style="width:100%;" onclick="submitOrder()">Complete Order</button>
    </div>
</div>

<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.USER_ROLE = '<?= $_SESSION['role'] ?>';
</script>
<script src="<?= BASE_URL ?>/assets/js/pos.js?v=<?= time() ?>"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
