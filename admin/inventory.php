<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$allowedRoles = ['admin'];
require_once __DIR__ . '/../includes/auth_check.php';

require_once __DIR__ . '/../classes/Product.php';

$productModel = new Product();

$editItem = null;
if (isset($_GET['edit'])) {
    $editItem = $productModel->getById((int) $_GET['edit']);
}

$search = trim($_GET['q'] ?? '');
$products = $search !== '' ? $productModel->search($search) : $productModel->getAll();
$categories = $productModel->getCategories();

$message = $_SESSION['inventory_message'] ?? '';
unset($_SESSION['inventory_message']);

$pageTitle = 'Inventory';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Inventory Management</h1>

<?php if ($message): ?>
    <div class="success"><?= e($message) ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="page-title"><?= $editItem ? 'Edit Product' : 'Add Product' ?></h2>
    <form action="<?= BASE_URL ?>/admin/process_inventory.php" method="POST">
        <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
        <?php if ($editItem): ?>
            <input type="hidden" name="id" value="<?= (int) $editItem['id'] ?>">
        <?php endif; ?>

        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" required
               value="<?= e($editItem['product_name'] ?? '') ?>">

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"
                    <?= ($editItem && (int) $editItem['category_id'] === (int) $category['id']) ? 'selected' : '' ?>>
                    <?= e($category['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="cost_price">Cost Price (TSh)</label>
        <input type="number" id="cost_price" name="cost_price" step="0.01" min="0" required
               value="<?= e($editItem['cost_price'] ?? '') ?>">

        <label for="selling_price">Selling Price (TSh)</label>
        <input type="number" id="selling_price" name="selling_price" step="0.01" min="0" required
               value="<?= e($editItem['selling_price'] ?? '') ?>">

        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" id="stock_quantity" name="stock_quantity" min="0" required
               value="<?= e($editItem['stock_quantity'] ?? '') ?>">

        <label for="unit">Unit</label>
        <input type="text" id="unit" name="unit" placeholder="bottle, crate, plate" required
               value="<?= e($editItem['unit'] ?? '') ?>">

        <button type="submit" class="btn-primary"><?= $editItem ? 'Update Product' : 'Add Product' ?></button>
        <?php if ($editItem): ?>
            <a href="<?= BASE_URL ?>/admin/inventory.php" class="btn">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h2 class="page-title">Product Catalog</h2>
    <form action="<?= BASE_URL ?>/admin/inventory.php" method="GET" style="margin-bottom:1rem;">
        <input type="text" name="q" placeholder="Search by product or category" value="<?= e($search) ?>">
        <button type="submit" class="btn">Search</button>
        <?php if ($search !== ''): ?>
            <a href="<?= BASE_URL ?>/admin/inventory.php" class="btn">Clear</a>
        <?php endif; ?>
    </form>
    <table>
        <tr>
            <th>Product</th><th>Category</th><th>Cost</th><th>Price</th><th>Stock</th><th>Unit</th><th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= e($product['product_name']) ?></td>
                <td><?= e($product['category_name']) ?></td>
                <td><?= formatCurrency($product['cost_price']) ?></td>
                <td><?= formatCurrency($product['selling_price']) ?></td>
                <td><?= (int) $product['stock_quantity'] ?></td>
                <td><?= e($product['unit']) ?></td>
                <td class="actions">
                    <a href="<?= BASE_URL ?>/admin/inventory.php?edit=<?= (int) $product['id'] ?>" class="btn">Edit</a>
                    <form action="<?= BASE_URL ?>/admin/process_inventory.php" method="POST" onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
