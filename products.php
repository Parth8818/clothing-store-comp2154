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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Store | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Custom styles for the product grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .product-card {
            border: 1px solid WhiteSmoke;
            padding: 20px;
            border-radius: 10px;
            background: White;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-card:hover { transform: translateY(-5px); }
        .product-card h3 { text-align: left; margin: 10px 0; color: MidnightBlue; }
        .price-tag { font-size: 1.2rem; color: ForestGreen; font-weight: bold; margin: 10px 0; }
        .search-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Our Collection</h1>

        <nav class="text-center" style="margin-bottom: 20px;">
            <a href="index.php">Home</a> |
            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="cart.php">My Cart</a> |
                <a href="orders.php">My Orders</a> |
                <a href="logout.php" style="color: FireBrick;">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> |
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>

        <form method="GET" class="search-container">
            <input type="text" name="search" placeholder="Search for clothes..." 
                   value="<?= htmlspecialchars($search) ?>" style="flex: 1; max-width: 400px;">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search): ?>
                <a href="products.php" class="btn" style="background: Silver; color: white;">Clear</a>
            <?php endif; ?>
        </form>

        <p class="text-center" style="color: gray;">Showing <?= count($products) ?> available items</p>

        <?php if (empty($products)): ?>
            <div class="text-center" style="padding: 50px;">
                <p>We couldn't find any products matching your search.</p>
                <a href="products.php">See all products</a>
            </div>
        <?php endif; ?>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <div>
                        <h3><?= htmlspecialchars($p["name"]) ?></h3>
                        <p style="font-size: 0.9rem; color: #666;">
                            <?= htmlspecialchars($p["description"] ?? "High quality clothing item.") ?>
                        </p>
                    </div>
                    
                    <div>
                        <p class="price-tag">$<?= number_format($p["price"], 2) ?></p>
                        <p style="font-size: 0.8rem; color: Silver;">Stock: <?= $p["stock"] ?> units</p>
                        
                        <?php if (isset($_SESSION["user_id"])): ?>
                            <form method="POST" action="add-to-cart.php">
                                <input type="hidden" name="product_id" value="<?= $p["id"] ?>">
                                <div style="display: flex; gap: 5px; margin-top: 10px;">
                                    <input type="number" name="quantity" value="1" min="1" 
                                           max="<?= $p["stock"] ?>" style="width: 70px;">
                                    <button type="submit" class="btn btn-success" style="padding: 10px;">Add</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary" style="display: block; margin-top: 10px;">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
