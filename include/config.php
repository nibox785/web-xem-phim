<?php
// includes/config.php

// Hàm xác định đường dẫn cơ sở động
function getBaseUrl() {
    // Lấy đường dẫn gốc của thư mục dự án
    $base_path = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    
    // Lấy đường dẫn tương đối từ document root
    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
    
    // Đảm bảo đường dẫn luôn kết thúc bằng dấu '/'
    $base_url = rtrim($relative_path, '/') . '/';
    
    // Tạo URL đầy đủ
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    return $protocol . $host . $base_url;
}

// Thông tin cơ sở dữ liệu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'marvel_dc_movies');

// Thông tin website
define('SITE_NAME', 'NiBoXMoVie');

// Định nghĩa SITE_URL động thay vì cố định
define('SITE_URL', getBaseUrl());
?>