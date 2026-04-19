<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION["cart"];
$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Review Your Order</title>
    <style>
        body { font-family: sans-serif; padding: 40px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .btn-confirm { background: #28a745; color: white; padding: 15px; width: 100%; border: none; cursor: pointer; font-size: 18px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Review Your Order</h1>
    <p>Please check your items before confirming.</p>

    <table>
        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach ($cart as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item["name"]) ?></td>
                <td><?= $item["quantity"] ?></td>
                <td>$<?= number_format($item["price"], 2) ?></td>
                <td>$<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>$<?= number_format($total, 2) ?></strong></td>
        </tr>
    </table>

    <form action="process_order.php" method="POST">
        <button type="submit" class="btn-confirm">Confirm & Place Order</button>
    </form>
    <p style="text-align:center;"><a href="cart.php">Back to Cart</a></p>
</body>
</html>
