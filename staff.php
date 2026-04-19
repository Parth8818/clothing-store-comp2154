<?php
session_start();


if (!isset($_SESSION["role"]) || $_SESSION["role"] != "staff") {
    header("Location: login.php");
    exit;
}


require "config.php";

$message = "";


if ($_POST && isset($_POST["add"])) {
    $name        = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price       = floatval($_POST["price"]);
    $stock       = intval($_POST["stock"]);

    if (empty($name) || $price <= 0 || $stock < 0) {
        $message = "Please fill in all fields correctly.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $stock]);
            $message = "Product added successfully.";
        } catch (PDOException $e) {
            $message = "Error adding product: " . $e->getMessage();
        }
    }
}


if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: staff.php");
    exit;
}


$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard | Inventory</title>
    <link rel="stylesheet" href="style.css">
    <style>
     
        .add-product-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background: WhiteSmoke;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .full-width { grid-column: span 2; }
        .stock-warning { 
            background: MistyRose; 
            color: FireBrick; 
            font-weight: bold; 
            padding: 2px 6px; 
            border-radius: 4px; 
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1100px;">
        <h1>Inventory Management</h1>
        
        <nav class="text-center" style="margin-bottom: 30px;">
            <a href="products.php" class="btn btn-primary" style="background: SteelBlue;">View Store</a>
            <a href="orders.php" class="btn btn-primary" style="background: SlateGray;">Customer Orders</a>
            <a href="report.php" class="btn btn-primary" style="background: ForestGreen;">Sales Report</a>
            <a href="logout.php" class="btn btn-primary" style="background: FireBrick;">Logout</a>
        </nav>

        <section>
            <h2>Add New Product</h2>
            <?php if ($message): ?>
                <p class="success-msg" style="background: Honeydew; color: ForestGreen; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <form method="POST" class="add-product-form">
                <div class="form-group">
                    <label>Product Name</label>
                    <input name="name" placeholder="e.g. Blue Denim Jacket" required>
                </div>
                <div class="form-group">
                    <label>Price ($)</label>
                    <input name="price" type="number" step="0.01" placeholder="0.00" required>
                </div>
                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" placeholder="Brief details about the item..." rows="2" style="width:100%; padding:10px; border-radius:6px; border:1px solid Silver;"></textarea>
                </div>
                <div class="form-group">
                    <label>Initial Stock</label>
                    <input name="stock" type="number" placeholder="Quantity" required>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button name="add" class="btn btn-success" style="width: 100%;">Save Product</button>
                </div>
            </form>
        </section>

        <section>
            <h2>Current Inventory (<?= count($products) ?> items)</h2>
            
            <?php if (empty($products)): ?>
                <div class="text-center" style="padding: 40px; border: 1px dashed Silver; border-radius: 8px;">
                    <p>Your warehouse is currently empty.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Stock Level</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><small>#<?= $p["id"] ?></small></td>
                                <td><strong><?= htmlspecialchars($p["name"]) ?></strong></td>
                                <td style="color: ForestGreen; font-weight: bold;">$<?= number_format($p["price"], 2) ?></td>
                                <td>
                                    <?php if ($p["stock"] <= 5): ?>
                                        <span class="stock-warning"><?= $p["stock"] ?> (Low Stock)</span>
                                    <?php else: ?>
                                        <?= $p["stock"] ?>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= date("M j, Y", strtotime($p["created_at"])) ?></small></td>
                                <td>
                                    <a href="edit-product.php?id=<?= $p["id"] ?>" style="color: SteelBlue; text-decoration: none; font-weight: bold;">Edit</a> | 
                                    <a href="staff.php?delete=<?= $p["id"] ?>" 
                                       style="color: FireBrick; text-decoration: none;"
                                       onclick="return confirm('Delete this product permanently?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
