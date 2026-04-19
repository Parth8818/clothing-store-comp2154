<?php
session_start();
require "config.php";

$error = "";
if ($_POST) {
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"]    = $user["name"];
        $_SESSION["role"]    = $user["role"];
        header("Location: products.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 400px;"> 
        <h1>Login</h1>
        
        <p><a href="index.php" style="text-decoration: none;">&larr; Home</a></p>

        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <div class="text-center mt-20">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><small style="color: gray;">Staff: admin@test.com / admin123</small></p>
        </div>
    </div>
</body>
</html>
