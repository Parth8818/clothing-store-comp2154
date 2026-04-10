<?php
session_start();
require "config.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE stock > 0 AND name LIKE ? ORDER BY name");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY name");
}
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 900px; margin: auto; }
        .product { border: 1px solid lightgray; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .product h3 { margin: 0 0 5px 0; }
        .price { color: #333; font-weight: bold; }
        .stock { color: gray; font-size: 0.9em; }
        .search-form { margin: 15px 0; }
        .search-form input { padding: 8px; width: 250px; }
        .search-form button, .search-form a { padding: 8px 12px; margin-left: 5px; }
        .out-of-stock { color: red; }
    </style>
</head>
<body>
    <h1>Products</h1>
    <nav>
        <a href="index.php">Home</a> |
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="cart.php">Cart</a> |
            <?php if ($_SESSION["role"] == "staff"): ?>
                <a href="staff.php">Staff Dashboard</a> |
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> |
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>

    <div class="search-form">
        <form method="GET">
            <input type="text" name="search" placeholder="Search products..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <?php if ($search): ?>
                <a href="products.php">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <p><?= count($products) ?> product(s) available</p>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php endif; ?>

    <?php foreach ($products as $p): ?>
        <div class="product">
            <h3><?= htmlspecialchars($p["name"]) ?></h3>
            <?php if (!empty($p["description"])): ?>
                <p><?= htmlspecialchars($p["description"]) ?></p>
            <?php endif; ?>
            <p class="price">$<?= number_format($p["price"], 2) ?></p>
            <p class="stock">Stock: <?= $p["stock"] ?> left</p>
            <?php if (isset($_SESSION["user_id"])): ?>
                <form method="POST" action="add-to-cart.php" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= $p["id"] ?>">
                    <input type="number" name="quantity" value="1" min="1"
                           max="<?= $p["stock"] ?>" style="width:60px; padding:5px;">
                    <button type="submit">Add to Cart</button>
                </form>
            <?php else: ?>
                <a href="login.php">Login to buy</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
