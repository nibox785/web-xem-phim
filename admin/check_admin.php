<?php
// Bật báo cáo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../include/db.php';

// Kiểm tra session, không gọi session_start() vì index.php đã xử lý
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Kiểm tra cookie để tự động đăng nhập
if (!isset($_SESSION['user_id']) && isset($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];
    $sql = "SELECT user_id FROM user_tokens WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Lỗi chuẩn bị truy vấn token: " . $conn->error);
        setcookie('login_token', '', time() - 3600, '/');
    } else {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            // Kiểm tra cả bảng users và admins
            $sql = "SELECT id, username, role FROM users WHERE id = ? UNION SELECT id, username, role FROM admins WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                error_log("Lỗi chuẩn bị truy vấn user/admin: " . $conn->error);
            } else {
                $stmt->bind_param("ii", $user_id, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($user = $result->fetch_assoc()) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    // Cập nhật thời gian hết hạn của token
                    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $sql = "UPDATE user_tokens SET expires_at = ? WHERE token = ?";
                    $token_stmt = $conn->prepare($sql);
                    if ($token_stmt) {
                        $token_stmt->bind_param("ss", $expires_at, $token);
                        $token_stmt->execute();
                        $token_stmt->close();
                    }
                } else {
                    setcookie('login_token', '', time() - 3600, '/');
                }
            }
        } else {
            setcookie('login_token', '', time() - 3600, '/');
        }
        $stmt->close();
    }
}

// Kiểm tra đã đăng nhập chưa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kiểm tra là admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && in_array($_SESSION['role'], ['super_admin', 'moderator']);
}

// Chuyển hướng nếu không phải admin
function requireAdmin() {
    global $base_url;
    if (!isLoggedIn()) {
        header("Location: " . $base_url . "index.php?page=login&error=please_login");
        exit;
    }
    if (!isAdmin()) {
        header("Location: " . $base_url . "index.php?error=access_denied");
        exit;
    }
}
?>