<?php
// Bật báo cáo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra session, không gọi session_start() vì index.php đã xử lý
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

require_once __DIR__ . '/../include/db.php';

// Xóa token nếu có
if (isset($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];
    $sql = "DELETE FROM user_tokens WHERE token = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }
    setcookie('login_token', '', time() - 3600, '/');
}

// Xóa session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Chuyển hướng về trang chủ
header('Location: ' . $base_url . 'index.php');
exit;
?>