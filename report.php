<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "staff") {
    header("Location: login.php");
    exit;
}
require "config.php";

// Total revenue
$revenue = $pdo->query("SELECT SUM(total) AS revenue FROM orders")->fetch()["revenue"] ?? 0;

// Total orders
$total_orders = $pdo->query("SELECT COUNT(*) AS cnt FROM orders")->fetch()["cnt"] ?? 0;

// Total products
$total_products = $pdo->query("SELECT COUNT(*) AS cnt FROM products")->fetch()["cnt"] ?? 0;

// Low stock (5 or fewer)
$low_stock = $pdo->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC")->fetchAll();

// Recent orders
$recent_orders = $pdo->query(
    "SELECT o.*, u.name AS customer_name
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC
     LIMIT 10"
)->fetchAll();

// Top selling products
$top_products = $pdo->query(
    "SELECT p.name, SUM(oi.quantity) AS total_sold
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     GROUP BY oi.product_id
     ORDER BY total_sold DESC
     LIMIT 5"
)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 950px; margin: auto; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { border: 1px solid lightgray; padding: 20px; border-radius: 5px; flex: 1; text-align: center; }
        .stat-box h3 { margin: 0; color: gray; font-size: 0.9em; }
        .stat-box p { font-size: 2em; font-weight: bold; margin: 10px 0 0 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid lightgray; padding: 8px; text-align: left; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .low-stock { color: orange; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    <p>
        <a href="staff.php">Staff Dashboard</a> |
        <a href="products.php">View Store</a> |
        <a href="logout.php">Logout</a>
    </p>

    <!-- Summary stats -->
    <div class="stats">
        <div class="stat-box">
            <h3>Total Revenue</h3>
            <p>$<?= number_format($revenue, 2) ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Orders</h3>
            <p><?= $total_orders ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Products</h3>
            <p><?= $total_products ?></p>
        </div>
    </div>

    <!-- Low stock warning -->
    <?php if (!empty($low_stock)): ?>
        <div class="warning">
            <strong>⚠ Low Stock Alert</strong> - the following products have 5 or fewer units left:
        </div>
        <table>
            <tr><th>Product</th><th>Stock Remaining</th></tr>
            <?php foreach ($low_stock as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["name"]) ?></td>
                    <td class="low-stock"><?= $item["stock"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <!-- Top selling -->
    <?php if (!empty($top_products)): ?>
        <h2>Top Selling Products</h2>
        <table>
            <tr><th>Product</th><th>Units Sold</th></tr>
            <?php foreach ($top_products as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["name"]) ?></td>
                    <td><?= $item["total_sold"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <!-- Recent orders -->
    <h2>Recent Orders</h2>
    <?php if (empty($recent_orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td>#<?= $order["id"] ?></td>
                    <td><?= htmlspecialchars($order["customer_name"]) ?></td>
                    <td>$<?= number_format($order["total"], 2) ?></td>
                    <td><?= $order["status"] ?></td>
                    <td><?= date("M j, Y", strtotime($order["created_at"])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
