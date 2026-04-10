<?php
// Run this file once in your browser: http://localhost/clothing-store/generate-passwords.php
// Copy the hashes into database.sql to replace the placeholders, then delete this file.

$staff_hash    = password_hash("admin123", PASSWORD_DEFAULT);
$customer_hash = password_hash("customer123", PASSWORD_DEFAULT);

echo "<p><strong>Staff hash (admin123):</strong><br>" . $staff_hash . "</p>";
echo "<p><strong>Customer hash (customer123):</strong><br>" . $customer_hash . "</p>";
echo "<p style='color:red;'>Delete this file after use!</p>";

// Or just run this to insert directly into the DB:
require "config.php";
$pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@test.com'")->execute([$staff_hash]);
$pdo->prepare("UPDATE users SET password = ? WHERE email = 'customer@test.com'")->execute([$customer_hash]);
echo "<p style='color:green;'>Passwords updated in database. You can delete this file now.</p>";
?>