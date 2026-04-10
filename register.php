<?php
session_start();
require "config.php";

$error   = "";
$success = "";

if ($_POST) {
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm"];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already taken
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "An account with that email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $hashed]);
            $success = "Account created! You can now log in.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: sans-serif; padding: 40px; max-width: 400px; margin: auto; }
        form input, form button { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
    </style>
</head>
<body>
    <h1>Create an Account</h1>
    <p><a href="index.php">&larr; Home</a></p>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
        <p><a href="login.php">Click here to log in</a></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button>Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>
