<?php
// includes/db.php
require_once 'config.php';

// Tạo kết nối
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3307);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die('Kết nối cơ sở dữ liệu thất bại: ' . $conn->connect_error);
}

// Đặt charset
$conn->set_charset("utf8mb4");

// Định nghĩa base_url từ SITE_URL đã định nghĩa động trong config.php
$base_url = defined('SITE_URL') ? SITE_URL : '/';

// Hoặc xử lý đường dẫn tương đối (không có protocol và host)
$relative_base_url = parse_url($base_url, PHP_URL_PATH);
if (!$relative_base_url) {
    $relative_base_url = '/';
}
?>