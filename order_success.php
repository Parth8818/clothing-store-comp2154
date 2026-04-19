<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Successful!</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        .card { border: 1px solid #ccc; padding: 30px; display: inline-block; border-radius: 10px; background: #f9f9f9; }
        .green-check { color: green; font-size: 50px; }
        a { text-decoration: none; color: blue; margin: 0 10px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="green-check">✔</div>
        <h1>Thank You!</h1>
        <p>Your order has been placed successfully.</p>
        <p>The inventory has been updated in our database.</p>
        <hr>
        <a href="products.php">Back to Shop</a>
        <a href="orders.php">View My Orders</a>
    </div>
</body>
</html>
