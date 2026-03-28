<?php
// File: create_admin_password.php
$password = '123123123'; 
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Mật khẩu băm cho admin1: " . $hashed_password;
?>
