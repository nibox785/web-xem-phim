<?php
// Bắt đầu session để kiểm tra quyền admin
session_start();

// Yêu cầu kiểm tra quyền admin
require_once 'check_admin.php';
requireAdmin();

// Chuyển hướng đến trang dashboard
header('Location: dashboard.php');
exit;
?>