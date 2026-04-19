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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <ul class="steps">
            <li class="step active">1. Cart</li>
            <li class="step">2. Review Order</li>
            <li class="step">3. Confirmation</li>
        </ul>

        <h1>Your Shopping Cart</h1>
        
        <nav class="text-center" style="margin-bottom: 20px;">
            <a href="products.php" style="text-decoration: none;">&larr; Continue Shopping</a>
        </nav>

        <?php if (empty($cart)): ?>
            <div class="text-center" style="padding: 40px;">
                <p>Your cart is currently empty.</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $product_id => $item): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($item["name"]) ?></strong></td>
                            <td>$<?= number_format($item["price"], 2) ?></td>
                            <td>
                                <form method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    <input type="number" name="quantity" 
                                           value="<?= $item["quantity"] ?>" min="0" 
                                           style="width: 60px; padding: 5px;">
                                    <button name="update" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: Gainsboro;">Update</button>
                                </form>
                            </td>
                            <td style="font-weight: bold;">$<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $product_id ?>" style="color: FireBrick; font-size: 0.9rem;">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold; font-size: 1.2rem;">Total Amount:</td>
                        <td colspan="2" style="font-size: 1.5rem; color: ForestGreen; font-weight: bold;">
                            $<?= number_format($total, 2) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-20 text-center">
                <form method="POST" action="checkout.php">
                    <button type="submit" class="btn btn-success" style="padding: 15px 50px; font-size: 1.1rem;">
                        Proceed to Checkout &rarr;
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
