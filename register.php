<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | Clothing Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 450px;">
        <h1>Create an Account</h1>
        
        <p class="text-center">
            <a href="index.php" style="text-decoration: none;">&larr; Return Home</a>
        </p>

        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: Honeydew; border: 1px solid ForestGreen; padding: 15px; border-radius: 6px; text-align: center;">
                <p style="color: ForestGreen; margin-bottom: 10px;"><?= htmlspecialchars($success) ?></p>
                <a href="login.php" class="btn btn-primary">Log In Now</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Password (min 6 chars)" required>
                </div>

                <div class="form-group">
                    <input type="password" name="confirm" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-20">
            <p>Already have an account? <a href="login.php">Log in here</a></p>
        </div>
    </div>
</body>
</html>
