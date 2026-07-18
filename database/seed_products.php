<?php
/**
 * Seeds the products table with items typically sold in a Dar es Salaam bar.
 * Prices are in TZS. cost_price is encrypted automatically by Product::create().
 * Run once: php database/seed_products.php
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Product.php';

$db = Database::getInstance()->getConnection();

// category_name => id (matches schema.sql seed data)
$catStmt = $db->query('SELECT id, category_name FROM categories');
$categories = [];
foreach ($catStmt->fetchAll() as $row) {
    $categories[$row['category_name']] = (int) $row['id'];
}

// [name, category, cost_price, selling_price, stock, unit]
$products = [
    // Beer
    ['Kilimanjaro Lager 500ml', 'Beer', 2200, 3500, 120, 'bottle'],
    ['Serengeti Lager 500ml', 'Beer', 2200, 3500, 120, 'bottle'],
    ['Safari Lager 500ml', 'Beer', 2000, 3000, 100, 'bottle'],
    ['Tusker Lager 500ml', 'Beer', 2400, 3800, 80, 'bottle'],
    ['Castle Lite 500ml', 'Beer', 2500, 4000, 90, 'bottle'],
    ['Konyagi Ice 300ml', 'Beer', 1800, 3000, 60, 'bottle'],
    ['Guinness Smooth 500ml', 'Beer', 2800, 4500, 70, 'bottle'],
    ['Balimi Cider 330ml', 'Beer', 2300, 3800, 50, 'bottle'],

    // Spirits
    ['Konyagi 250ml', 'Spirits', 4500, 7000, 40, 'bottle'],
    ['Konyagi 750ml', 'Spirits', 12000, 18000, 25, 'bottle'],
    ['Amarula 750ml', 'Spirits', 22000, 32000, 15, 'bottle'],
    ['Chrome Vodka 750ml', 'Spirits', 15000, 23000, 20, 'bottle'],
    ['Smirnoff Vodka 750ml', 'Spirits', 18000, 27000, 20, 'bottle'],
    ['Johnnie Walker Black Label 750ml', 'Spirits', 45000, 65000, 10, 'bottle'],
    ['Jameson Irish Whiskey 750ml', 'Spirits', 40000, 60000, 10, 'bottle'],
    ['Gordon\'s Gin 750ml', 'Spirits', 20000, 30000, 15, 'bottle'],
    ['Captain Morgan Rum 750ml', 'Spirits', 21000, 31000, 12, 'bottle'],

    // Wine
    ['Four Cousins Red 750ml', 'Wine', 9000, 14000, 20, 'bottle'],
    ['Four Cousins Rose 750ml', 'Wine', 9000, 14000, 20, 'bottle'],
    ['Drostdy-Hof Red 750ml', 'Wine', 8000, 12500, 15, 'bottle'],
    ['Robertson Winery Sweet Rose 750ml', 'Wine', 8500, 13000, 12, 'bottle'],

    // Soft Drinks
    ['Coca-Cola 500ml', 'Soft Drinks', 700, 1500, 150, 'bottle'],
    ['Sprite 500ml', 'Soft Drinks', 700, 1500, 150, 'bottle'],
    ['Fanta Orange 500ml', 'Soft Drinks', 700, 1500, 150, 'bottle'],
    ['Stoney Tangawizi 500ml', 'Soft Drinks', 700, 1500, 100, 'bottle'],
    ['Bottled Water 500ml', 'Soft Drinks', 400, 1000, 200, 'bottle'],
    ['Red Bull 250ml', 'Soft Drinks', 2500, 4000, 40, 'can'],
    ['Azam Juice Mango 300ml', 'Soft Drinks', 900, 1800, 60, 'bottle'],

    // Food
    ['Mishkaki (Beef Skewers)', 'Food', 1500, 3000, 50, 'plate'],
    ['Grilled Chicken (Kuku Choma) Quarter', 'Food', 4000, 7000, 30, 'plate'],
    ['Chips Mayai', 'Food', 2000, 4000, 40, 'plate'],
    ['Nyama Choma (Beef) 1kg', 'Food', 12000, 20000, 15, 'kg'],
    ['Samosa (Beef) x3', 'Food', 1000, 2500, 60, 'plate'],
    ['Kachumbari Side Salad', 'Food', 500, 1500, 40, 'plate'],
];

$product = new Product();
$created = 0;
$skipped = 0;

foreach ($products as [$name, $catName, $costPrice, $sellingPrice, $stock, $unit]) {
    if (!isset($categories[$catName])) {
        echo "Skipped '{$name}': category '{$catName}' not found.\n";
        $skipped++;
        continue;
    }

    // Avoid duplicate rows if the script is run more than once
    $check = $db->prepare('SELECT id FROM products WHERE product_name = ?');
    $check->execute([$name]);
    if ($check->fetch()) {
        echo "Skipped '{$name}': already exists.\n";
        $skipped++;
        continue;
    }

    $ok = $product->create($name, $categories[$catName], $costPrice, $sellingPrice, $stock, $unit);
    if ($ok) {
        echo "Added: {$name}\n";
        $created++;
    } else {
        echo "Failed: {$name}\n";
        $skipped++;
    }
}

echo "\nDone. Created: {$created}, Skipped: {$skipped}\n";
