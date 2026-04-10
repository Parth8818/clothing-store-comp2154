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
<html>
<head>
    <title>My Orders</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid lightgray; padding: 10px; text-align: left; }
        .order-items { background: #f9f9f9; }
        details { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>My Orders</h1>
    <nav>
        <a href="products.php">Products</a> |
        <a href="cart.php">Cart</a> |
        <a href="logout.php">Logout</a>
    </nav>

    <?php if (empty($orders)): ?>
        <p>You haven't placed any orders yet. <a href="products.php">Start shopping</a></p>
    <?php else: ?>
        <p><?= count($orders) ?> order(s) total</p>
        <table>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Items</th>
            </tr>
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
                    <td>#<?= $order["id"] ?></td>
                    <td><?= date("M j, Y", strtotime($order["created_at"])) ?></td>
                    <td>$<?= number_format($order["total"], 2) ?></td>
                    <td><?= $order["status"] ?></td>
                    <td>
                        <details>
                            <summary><?= count($items) ?> item(s)</summary>
                            <ul>
                                <?php foreach ($items as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item["name"]) ?>
                                        &times; <?= $item["quantity"] ?>
                                        @ $<?= number_format($item["price"], 2) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
