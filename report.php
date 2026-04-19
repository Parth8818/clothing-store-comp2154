<?php
session_start();

// 1. Security Check: Only allow staff to view reports
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "staff") {
    header("Location: login.php");
    exit;
}

// 2. Database Connection
require "config.php";

// 3. FETCH DATA FOR THE REPORT
try {
    // Total revenue from all orders
    $stmt_rev = $pdo->query("SELECT SUM(total) AS revenue FROM orders");
    $revenue = $stmt_rev->fetch()["revenue"] ?? 0;

    // Total count of orders
    $stmt_ord = $pdo->query("SELECT COUNT(*) AS cnt FROM orders");
    $total_orders = $stmt_ord->fetch()["cnt"] ?? 0;

    // Total unique products in the system
    $stmt_prod = $pdo->query("SELECT COUNT(*) AS cnt FROM products");
    $total_products = $stmt_prod->fetch()["cnt"] ?? 0;

    // Low stock items (5 or fewer units)
    $low_stock = $pdo->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC")->fetchAll();

    // Top selling products (requires joining orders with order_items)
    $top_products = $pdo->query(
        "SELECT p.name, SUM(oi.quantity) AS total_sold
         FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         GROUP BY oi.product_id
         ORDER BY total_sold DESC
         LIMIT 5"
    )->fetchAll();

    // Recent orders activity
    $recent_orders = $pdo->query(
        "SELECT o.*, u.name AS customer_name
         FROM orders o
         JOIN users u ON o.user_id = u.id
         ORDER BY o.created_at DESC
         LIMIT 10"
    )->fetchAll();

} catch (PDOException $e) {
    // If the database query fails (e.g., table doesn't exist), show a clean error
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report | Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Dashboard-specific styling */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: White;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid WhiteSmoke;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .stat-card h3 { 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: Silver; 
            margin: 0;
        }
        .stat-card p { 
            font-size: 2.2rem; 
            margin: 10px 0 0 0; 
            color: MidnightBlue;
            font-weight: bold;
        }
        .alert-box {
            background-color: MistyRose;
            border-left: 5px solid FireBrick;
            padding: 15px;
            margin: 20px 0;
            color: FireBrick;
        }
        .badge {
            background: Honeydew;
            color: ForestGreen;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1100px;">
        <h1>Business Insights</h1>
        
        <nav class="text-center" style="margin-bottom: 20px;">
            <a href="staff.php" class="btn" style="background: SteelBlue; color: white;">Staff Dashboard</a>
            <a href="products.php" class="btn" style="background: GhostWhite; color: SteelBlue;">View Store</a>
            <a href="logout.php" class="btn" style="background: FireBrick; color: white;">Logout</a>
        </nav>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p style="color: ForestGreen;">$<?= number_format($revenue, 2) ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p><?= $total_orders ?></p>
            </div>
            <div class="stat-card">
                <h3>Product Count</h3>
                <p><?= $total_products ?></p>
            </div>
        </div>

        <?php if (!empty($low_stock)): ?>
            <div class="alert-box">
                <strong>⚠ Low Stock Alert:</strong> The following items need restocking immediately.
            </div>
            <table>
                <thead>
                    <tr><th>Product Name</th><th>Stock Remaining</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($low_stock as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item["name"]) ?></td>
                            <td style="color: FireBrick; font-weight: bold;"><?= $item["stock"] ?> units</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px;">
            <div>
                <h2>Top Sellers</h2>
                <table>
                    <thead>
                        <tr><th>Product</th><th>Sold</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_products)): ?>
                            <tr><td colspan="2">No items sold yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($top_products as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item["name"]) ?></td>
                                    <td><strong><?= $item["total_sold"] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div>
                <h2>Performance</h2>
                <p style="color: gray;">Your store is currently seeing a steady flow of transactions. Focus on restocking top sellers to maintain revenue.</p>
            </div>
        </div>

        <h2 style="margin-top: 40px;">Recent Activity</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_orders)): ?>
                    <tr><td colspan="5">No recent activity.</td></tr>
                <?php else: ?>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?= $order["id"] ?></td>
                            <td><?= htmlspecialchars($order["customer_name"]) ?></td>
                            <td><strong>$<?= number_format($order["total"], 2) ?></strong></td>
                            <td><span class="badge"><?= strtoupper($order["status"]) ?></span></td>
                            <td><?= date("M j, y", strtotime($order["created_at"])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
