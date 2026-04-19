<?php
session_start();
require "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Hero Section Styling */
        .hero {
            background: GhostWhite;
            padding: 60px 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid WhiteSmoke;
        }
        .hero h2 { 
            font-size: 2.5rem; 
            color: MidnightBlue; 
            margin-bottom: 10px;
        }
        .hero p { 
            font-size: 1.1rem; 
            color: SlateGray; 
            margin-bottom: 30px; 
        }
        .nav-menu {
            margin-bottom: 30px;
            padding: 15px;
            background: WhiteSmoke;
            border-radius: 8px;
        }
        .nav-menu a {
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
            color: SteelBlue;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Clothing Store Inventory System</h1>
        
        <nav class="nav-menu text-center">
            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="products.php">Shop Products</a>
                <a href="cart.php">My Cart</a>
                <?php if ($_SESSION["role"] == "staff"): ?>
                    <a href="staff.php" style="color: DarkOrange;">Staff Dashboard</a>
                    <a href="report.php" style="color: ForestGreen;">Sales Report</a>
                <?php endif; ?>
                <a href="logout.php" style="color: FireBrick;">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="products.php">Guest View</a>
            <?php endif; ?>
        </nav>

        <div class="hero">
            <h2>Style Meets System</h2>
            <p>Our inventory system ensures you always get the latest trends with real-time stock updates.</p>
            
            <?php if (!isset($_SESSION["user_id"])): ?>
                <div class="mt-20">
                    <a href="register.php" class="btn btn-primary">Create Your Account</a>
                    <p style="margin-top: 15px; font-size: 0.9rem;">
                        Already a member? <a href="login.php">Sign In</a>
                    </p>
                </div>
            <?php else: ?>
                <div class="mt-20">
                    <a href="products.php" class="btn btn-success" style="width: auto; padding: 15px 40px;">Enter Storefront</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-20" style="color: Silver; font-size: 0.8rem;">
            <p>&copy; 2026 Clothing Store Inventory System - Technical Lab Project</p>
        </div>
    </div>
</body>
</html>
