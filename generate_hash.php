<?php
// Change this password to whatever you want to hash
$password = 'admin@123';

// Using password_hash (recommended)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Using MD5 (not recommended)
// $hashed_password = md5($password);

echo "Password: $password\n";
echo "Hashed Password (password_hash): $hashed_password\n";

// Uncomment below to see MD5 hash instead:
// echo "Hashed Password (MD5): " . md5($password) . "\n";
?>
