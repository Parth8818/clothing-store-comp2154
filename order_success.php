<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <ul class="steps">
            <li class="step complete">1. Cart</li>
            <li class="step complete">2. Review Order</li>
            <li class="step active">3. Confirmation</li>
        </ul>

        <div class="text-center">
            <div style="font-size: 80px; color: ForestGreen;">✔</div>
            <h1>Thank You for Your Order!</h1>
            
            <div style="margin: 30px 0; padding: 20px; background-color: WhiteSmoke; border-radius: 8px;">
                <p>Your transaction was completed successfully.</p>
                <p><strong>Inventory Update:</strong> The stock levels in the <code>products</code> table have been adjusted automatically.</p>
            </div>

            <div class="mt-20">
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                <a href="orders.php" class="btn btn-primary" style="background: SlateGray;">View My Orders</a>
            </div>
            
            <p class="mt-20">
                <a href="index.php" class="back-link">Return to Home Page</a>
            </p>
        </div>
    </div>
</body>
</html>
