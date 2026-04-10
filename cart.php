<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Handle quantity update
if ($_POST && isset($_POST["update"])) {
    $product_id = (int)$_POST["product_id"];
    $quantity   = (int)$_POST["quantity"];

    if ($quantity <= 0) {
        unset($_SESSION["cart"][$product_id]);
    } else {
        $_SESSION["cart"][$product_id]["quantity"] = $quantity;
    }
    header("Location: cart.php");
    exit;
}

// Handle remove
if (isset($_GET["remove"])) {
    $product_id = (int)$_GET["remove"];
    unset($_SESSION["cart"][$product_id]);
    header("Location: cart.php");
    exit;
}

$cart  = $_SESSION["cart"];
$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid lightgray; padding: 10px; text-align: left; }
        .total { font-weight: bold; font-size: 1.2em; }
        .actions { margin-top: 15px; }
        .actions a, .actions button { padding: 10px 20px; margin-right: 10px; }
    </style>
</head>
<body>
    <h1>Your Cart</h1>
    <nav>
        <a href="products.php">&larr; Continue Shopping</a> |
        <a href="logout.php">Logout</a>
    </nav>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty. <a href="products.php">Browse products</a></p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Remove</th>
            </tr>
            <?php foreach ($cart as $product_id => $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["name"]) ?></td>
                    <td>$<?= number_format($item["price"], 2) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                            <input type="number" name="quantity"
                                   value="<?= $item["quantity"] ?>" min="0" style="width:60px;">
                            <button name="update">Update</button>
                        </form>
                    </td>
                    <td>$<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                    <td><a href="cart.php?remove=<?= $product_id ?>">Remove</a></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="total">Total</td>
                <td colspan="2" class="total">$<?= number_format($total, 2) ?></td>
            </tr>
        </table>

        <div class="actions">
            <form method="POST" action="checkout.php">
                <button type="submit">Proceed to Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
