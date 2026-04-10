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

$error   = "";
$success = false;

if ($_POST) {
    $user_id = $_SESSION["user_id"];
    $cart    = $_SESSION["cart"];

    // Calculate total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item["price"] * $item["quantity"];
    }

    // Use a transaction - order + stock updates all happen together or not at all
    try {
        $pdo->beginTransaction();

        // Create the order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // Add each item and reduce stock
        foreach ($cart as $product_id => $item) {
            $qty   = $item["quantity"];
            $price = $item["price"];

            // Insert order item
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $qty, $price]);

            // Reduce stock - only if enough is available
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmt->execute([$qty, $product_id, $qty]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Not enough stock for: " . htmlspecialchars($item["name"]));
            }
        }

        $pdo->commit();

        // Clear the cart after successful order
        $_SESSION["cart"] = [];
        $success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$cart  = $_SESSION["cart"] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 700px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid lightgray; padding: 10px; }
        .success-box { background: #e6ffe6; border: 1px solid green; padding: 20px; border-radius: 5px; }
        button { padding: 10px 25px; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <?php if ($success): ?>
        <div class="success-box">
            <h2>Order Placed!</h2>
            <p>Your order was successful. Stock has been updated.</p>
            <a href="products.php">Continue Shopping</a> |
            <a href="orders.php">View My Orders</a>
        </div>

    <?php else: ?>
        <?php if ($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <h2>Order Summary</h2>
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

        <form method="POST">
            <button type="submit">Confirm Order</button>
        </form>
        <p><a href="cart.php">Back to Cart</a></p>
    <?php endif; ?>
</body>
</html>
