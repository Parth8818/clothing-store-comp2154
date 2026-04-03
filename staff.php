<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "staff") {
    header("Location: login.php");
    exit;
}
require "config.php";

if ($_POST) {
    if (isset($_POST["add"])) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
        $stmt->execute([$_POST["name"], $_POST["price"], $_POST["stock"]]);
        $message = "Product added";
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Staff Dashboard</title><style>body{font-family:sans-serif;padding:20px;max-width:900px;margin:auto;}
table{width:100%;border-collapse:collapse;margin:20px 0;} 
th,td{border:1px solid lightgray;padding:8px;text-align:left;}
form input,form button{padding:8px;margin:5px;}</style></head>
<body>
    <h1>Staff Dashboard - Inventory Management</h1>
    <p><a href="products.php">View Store</a> | <a href="logout.php">Logout</a></p>

    <h2>Add New Product</h2>
    <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
    <form method="POST">
        <input name="name" placeholder="Product Name" required>
        <input name="price" type="number" step="0.01" placeholder="Price" required>
        <input name="stock" type="number" placeholder="Stock Quantity" required>
        <button name="add">Add Product</button>
    </form>

    <h2>Recent Products</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Added</th></tr>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?=$p["id"]?></td>
                <td><?=$p["name"]?></td>
                <td>$<?=number_format($p["price"],2)?></td>
                <td><?=$p["stock"]?></td>
                <td><?=$p["created_at"]?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
