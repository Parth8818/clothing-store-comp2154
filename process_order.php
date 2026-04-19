<?php
session_start();
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SESSION["cart"])) {
    $user_id = $_SESSION["user_id"];
    $cart = $_SESSION["cart"];
    $total = 0;
    foreach ($cart as $item) { $total += $item["price"] * $item["quantity"]; }

    try {
        $pdo->beginTransaction();

        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // 2. Items & Stock
        foreach ($cart as $product_id => $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);

            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmt->execute([$item['quantity'], $product_id, $item['quantity']]);
            
            if ($stmt->rowCount() === 0) { throw new Exception("Stock error for " . $item['name']); }
        }

        $pdo->commit();
        $_SESSION["cart"] = [];
        header("Location: order_success.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage()); 
    }
} else {
    header("Location: cart.php");
}
