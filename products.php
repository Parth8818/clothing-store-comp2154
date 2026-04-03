<?php
session_start();
require "config.php";

$products = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Products</title><style>body{font-family:sans-serif;padding:20px;max-width:800px;margin:auto;}
.product{border:1px solid #ddd;padding:15px;margin:10px 0;border-radius:5px;} .price{font-weight:bold;color:green;}
</style></head>
<body>
    <h1>Available Products (<?=count($products)?>)</h1>
    <?php if (isset($_SESSION["role"])): ?>
        <p><a href="staff.php">Staff Dashboard</a> | <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php">Login as Staff</a></p>
    <?php endif; ?>

    <?php foreach ($products as $p): ?>
        <div class="product">
            <h3><?=$p["name"]?></h3>
            <p class="price">$<?=number_format($p["price"], 2)?> (Stock: <?=$p["stock"]?>)</p>
            <form method="POST" action="add-to-cart.php" style="display:inline;">
                <input type="hidden" name="product_id" value="<?=$p["id"]?>">
                <button>Add to Cart</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
