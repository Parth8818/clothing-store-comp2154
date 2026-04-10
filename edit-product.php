<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "staff") {
    header("Location: login.php");
    exit;
}
require "config.php";

$id   = (int)$_GET["id"];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}

$error   = "";
$message = "";

if ($_POST && isset($_POST["save"])) {
    $name        = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price       = floatval($_POST["price"]);
    $stock       = intval($_POST["stock"]);

    if (empty($name) || $price <= 0 || $stock < 0) {
        $error = "Please fill in all fields correctly.";
    } else {
        $stmt = $pdo->prepare(
            "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?"
        );
        $stmt->execute([$name, $description, $price, $stock, $id]);
        $message = "Product updated successfully.";

        // Refresh product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 500px; margin: auto; }
        form input, form textarea, form button { display: block; width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        form textarea { height: 80px; }
    </style>
</head>
<body>
    <h1>Edit Product</h1>
    <p><a href="staff.php">&larr; Back to Dashboard</a></p>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product["name"]) ?>" required>

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($product["description"] ?? "") ?></textarea>

        <label>Price ($)</label>
        <input type="number" name="price" step="0.01" value="<?= $product["price"] ?>" required>

        <label>Stock Quantity</label>
        <input type="number" name="stock" value="<?= $product["stock"] ?>" required>

        <button name="save">Save Changes</button>
    </form>
</body>
</html>
