<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Get all orders for this user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Order History | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Small custom additions for this page */
        .order-nav { background: GhostWhite; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; }
        .order-nav a { margin: 0 15px; text-decoration: none; font-weight: bold; color: SteelBlue; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; background: Honeydew; color: ForestGreen; font-weight: bold; }
        details { cursor: pointer; color: SteelBlue; }
        summary:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Order History</h1>

        <nav class="order-nav">
            <a href="products.php">Shop Products</a>
            <a href="cart.php">My Cart</a>
            <a href="logout.php" style="color: FireBrick;">Logout</a>
        </nav>

        <?php if (empty($orders)): ?>
            <div class="text-center">
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-primary">Start Shopping Now</a>
            </div>
        <?php else: ?>
            <p class="text-center">You have placed <strong><?= count($orders) ?></strong> order(s).</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total Paid</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        // Get items for this order
                        $items_stmt = $pdo->prepare(
                            "SELECT oi.*, p.name FROM order_items oi
                             JOIN products p ON oi.product_id = p.id
                             WHERE oi.order_id = ?"
                        );
                        $items_stmt->execute([$order["id"]]);
                        $items = $items_stmt->fetchAll();
                        ?>
                        <tr>
                            <td><strong>#<?= $order["id"] ?></strong></td>
                            <td><?= date("M j, Y", strtotime($order["created_at"])) ?></td>
                            <td style="color: ForestGreen; font-weight: bold;">$<?= number_format($order["total"], 2) ?></td>
                            <td><span class="status-badge"><?= strtoupper($order["status"]) ?></span></td>
                            <td>
                                <details>
                                    <summary><?= count($items) ?> item(s)</summary>
                                    <ul style="font-size: 0.9rem; padding-left: 20px; margin-top: 10px;">
                                        <?php foreach ($items as $item): ?>
                                            <li>
                                                <?= htmlspecialchars($item["name"]) ?> 
                                                <span style="color: gray;">
                                                    (&times;<?= $item["quantity"] ?>)
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="text-center mt-20">
            <a href="index.php" class="back-link">&larr; Back to Home</a>
        </div>
    </div>
</body>
</html>
