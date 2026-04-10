<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "staff") {
    header("Location: login.php");
    exit;
}
require "config.php";

$message = "";

// Handle add product
if ($_POST && isset($_POST["add"])) {
    $name        = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price       = floatval($_POST["price"]);
    $stock       = intval($_POST["stock"]);

    if (empty($name) || $price <= 0 || $stock < 0) {
        $message = "Please fill in all fields correctly.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock]);
        $message = "Product added successfully.";
    }
}

// Handle delete
if (isset($_GET["delete"])) {
    $id   = (int)$_GET["delete"];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: staff.php");
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 950px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid lightgray; padding: 8px; text-align: left; }
        form input, form textarea, form button { padding: 8px; margin: 5px; }
        form textarea { width: 250px; }
        .low-stock { color: orange; font-weight: bold; }
        .danger { color: red; }
    </style>
</head>
<body>
    <h1>Staff Dashboard - Inventory Management</h1>
    <p>
        <a href="products.php">View Store</a> |
        <a href="orders.php">View Orders</a> |
        <a href="report.php">Sales Report</a> |
        <a href="logout.php">Logout</a>
    </p>

    <h2>Add New Product</h2>
    <?php if ($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input name="name" placeholder="Product Name" required>
        <textarea name="description" placeholder="Description (optional)" rows="2"></textarea>
        <input name="price" type="number" step="0.01" placeholder="Price" required>
        <input name="stock" type="number" placeholder="Stock Quantity" required>
        <button name="add">Add Product</button>
    </form>

    <h2>All Products (<?= count($products) ?>)</h2>
    <?php if (empty($products)): ?>
        <p>No products yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Added</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p["id"] ?></td>
                    <td><?= htmlspecialchars($p["name"]) ?></td>
                    <td><?= htmlspecialchars($p["description"] ?? "-") ?></td>
                    <td>$<?= number_format($p["price"], 2) ?></td>
                    <td class="<?= $p["stock"] <= 5 ? 'low-stock' : '' ?>">
                        <?= $p["stock"] ?>
                        <?= $p["stock"] <= 5 ? '⚠' : '' ?>
                    </td>
                    <td><?= date("M j, Y", strtotime($p["created_at"])) ?></td>
                    <td>
                        <a href="edit-product.php?id=<?= $p["id"] ?>">Edit</a> |
                        <a href="staff.php?delete=<?= $p["id"] ?>"
                           class="danger"
                           onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
