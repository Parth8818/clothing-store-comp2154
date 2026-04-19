<?php
session_start();
require "config.php";

// 1. Security Check: Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// 2. Cart Check: Must have items to checkout
if (empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

$error = "";

// 3. Handle the "Confirm Order" button click
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $cart    = $_SESSION["cart"];

    // Calculate total price
    $total = 0;
    foreach ($cart as $item) {
        $total += $item["price"] * $item["quantity"];
    }

    try {
        // START TRANSACTION (The "Atomic" Part)
        $pdo->beginTransaction();

        // A. Create the main order record
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'completed', NOW())");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // B. Loop through cart to add items and reduce stock
        foreach ($cart as $product_id => $item) {
            $qty   = $item["quantity"];
            $price = $item["price"];

            // Insert into order_items table
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $qty, $price]);

            // Reduce stock ONLY if enough is available (Security check)
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmt->execute([$qty, $product_id, $qty]);

            // If the stock didn't change, it means someone bought the last one while you were looking!
            if ($stmt->rowCount() === 0) {
                throw new Exception("Sorry, " . htmlspecialchars($item["name"]) . " is now out of stock.");
            }
        }

        // COMMIT (Save everything to the database forever)
        $pdo->commit();

        // CLEAR CART
        $_SESSION["cart"] = [];

        // REDIRECT (This stops the "stuck" feeling)
        header("Location: order_success.php");
        exit();

    } catch (Exception $e) {
        // ROLLBACK (If anything failed, undo everything so we don't have errors)
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Prepare data for the page view
$cart  = $_SESSION["cart"] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Clothing Store</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; max-width: 800px; margin: auto; line-height: 1.6; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .summary-table th, .summary-table td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
        .error-msg { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .btn-confirm { background-color: #28a745; color: white; border: none; padding: 15px 30px; font-size: 18px; cursor: pointer; border-radius: 5px; width: 100%; }
        .btn-confirm:hover { background-color: #218838; }
        .back-link { display: block; margin-top: 20px; text-align: center; color: #666; text-decoration: none; }
    </style>
</head>
<body>

    <h1>Finalize Your Order</h1>

    <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <h2>Order Summary</h2>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["name"]) ?></td>
                    <td><?= $item["quantity"] ?></td>
                    <td>$<?= number_format($item["price"], 2) ?></td>
                    <td>$<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right"><strong>Total Amount:</strong></td>
                <td><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <form method="POST">
        <button type="submit" class="btn-confirm">Place Order & Pay</button>
    </form>
    
    <a href="cart.php" class="back-link">← Return to Cart</a>

</body>
</html>
