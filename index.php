<?php
session_start();
require "config.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clothing Store</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        nav a { margin-right: 10px; }
        .welcome-box { border: 1px solid lightgray; padding: 20px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Clothing Store Inventory System</h1>
    <nav>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="products.php">Products</a> |
            <a href="cart.php">Cart</a> |
            <?php if ($_SESSION["role"] == "staff"): ?>
                <a href="staff.php">Staff Dashboard</a> |
                <a href="orders.php">Orders</a> |
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> |
            <a href="register.php">Register</a> |
            <a href="products.php">Products</a>
        <?php endif; ?>
    </nav>

    <div class="welcome-box">
        <h2>Welcome</h2>
        <p>Browse our clothing products or log in to manage your cart and orders.</p>
        <?php if (!isset($_SESSION["user_id"])): ?>
            <p><a href="register.php">Create an account</a> to start shopping, or
               <a href="login.php">log in</a> if you already have one.</p>
        <?php else: ?>
            <p>Welcome back! <a href="products.php">Browse products</a> or
               <a href="cart.php">view your cart</a>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
