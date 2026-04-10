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
    <title>Login</title>
    <style>
        body { font-family: sans-serif; padding: 40px; max-width: 400px; margin: auto; }
        form input, form button { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
    </style>
</head>
<body>
    <h1>Login</h1>
    <p><a href="index.php">&larr; Home</a></p>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button>Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
    <p><small>Staff login: admin@test.com / admin123</small></p>
</body>
</html>
