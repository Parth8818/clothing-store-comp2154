<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_POST && isset($_POST["product_id"])) {
    $product_id = (int)$_POST["product_id"];
    $quantity   = max(1, (int)$_POST["quantity"]);

    // Make sure the product exists and has enough stock
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND stock > 0");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        if (isset($_SESSION["cart"][$product_id])) {
            // Already in cart - add to existing quantity
            $_SESSION["cart"][$product_id]["quantity"] += $quantity;
        } else {
            // New item
            $_SESSION["cart"][$product_id] = [
                "name"     => $product["name"],
                "price"    => $product["price"],
                "quantity" => $quantity
            ];
        }
    }
}

header("Location: cart.php");
exit;
?>