<?php
// Ngăn truy cập trực tiếp
if (!defined('INCLUDED_VIA_INDEX')) {
    header('Location: ' . $base_url . 'index.php');
    exit;
}

// Bật báo cáo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra session, không gọi session_start() vì index.php đã xử lý
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['super_admin', 'moderator'])) {
        header('Location: ' . $base_url . 'admin/dashboard.php');
        error_log("Redirect to admin dashboard for user_id: " . $_SESSION['user_id']);
    } else {
        header('Location: ' . $base_url . 'index.php');
        error_log("Redirect to index for user_id: " . $_SESSION['user_id']);
    }
    exit;
}

// Tạo CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
            $sql = "SELECT id, username, email, role FROM users WHERE id = ? UNION SELECT id, username, NULL AS email, role FROM admins WHERE id = ?";
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
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'] ?? 'user';
                    // Cập nhật thời gian hết hạn của token
                    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $sql = "UPDATE user_tokens SET expires_at = ? WHERE token = ?";
                    $token_stmt = $conn->prepare($sql);
                    if ($token_stmt) {
                        $token_stmt->bind_param("ss", $expires_at, $token);
                        $token_stmt->execute();
                        $token_stmt->close();
                    }
                    if (in_array($user['role'], ['super_admin', 'moderator'])) {
                        header('Location: ' . $base_url . 'admin/dashboard.php');
                    } else {
                        header('Location: ' . $base_url . 'index.php');
                    }
                    exit;
                }
            }
        }
        $stmt->close();
        // Xóa cookie nếu token không hợp lệ
        setcookie('login_token', '', time() - 3600, '/');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
    } else {
        $email_or_username = trim($_POST['email_or_username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

        if (empty($email_or_username) || empty($password)) {
            $error = 'Vui lòng nhập đầy đủ thông tin';
        } elseif (!isset($conn)) {
            $error = 'Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.';
            error_log("Database connection not set in login.php");
        } else {
            // Kiểm tra bảng users
            $stmt = $conn->prepare("SELECT id, username, email, password_hash, role FROM users WHERE email = ? OR username = ?");
            if (!$stmt) {
                $error = 'Lỗi truy vấn cơ sở dữ liệu: ' . $conn->error;
                error_log("Query error in users check: " . $conn->error);
            } else {
                $stmt->bind_param("ss", $email_or_username, $email_or_username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password_hash'])) {
                        // Đăng nhập thành công (người dùng)
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'] ?? 'user';

                        // Xử lý "Ghi nhớ đăng nhập"
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
                            $sql = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
                            $token_stmt = $conn->prepare($sql);
                            if ($token_stmt) {
                                $token_stmt->bind_param("iss", $user['id'], $token, $expires_at);
                                if ($token_stmt->execute()) {
                                    setcookie('login_token', $token, time() + 30 * 24 * 3600, '/', '', false, true);
                                }
                                $token_stmt->close();
                            }
                        }

                        header('Location: ' . $base_url . 'index.php');
                        error_log("User login successful: " . $user['username']);
                        exit;
                    } else {
                        $error = 'Mật khẩu không chính xác';
                    }
                } else {
                    // Kiểm tra bảng admins
                    $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM admins WHERE username = ?");
                    if (!$stmt) {
                        $error = 'Lỗi truy vấn cơ sở dữ liệu: ' . $conn->error;
                        error_log("Query error in admins check: " . $conn->error);
                    } else {
                        $stmt->bind_param("s", $email_or_username);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows == 1) {
                            $admin = $result->fetch_assoc();
                            if (password_verify($password, $admin['password_hash'])) {
                                // Đăng nhập thành công (admin)
                                $_SESSION['user_id'] = $admin['id'];
                                $_SESSION['username'] = $admin['username'];
                                $_SESSION['role'] = $admin['role'];

                                // Xử lý "Ghi nhớ đăng nhập"
                                if ($remember) {
                                    $token = bin2hex(random_bytes(32));
                                    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
                                    $sql = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
                                    $token_stmt = $conn->prepare($sql);
                                    if ($token_stmt) {
                                        $token_stmt->bind_param("iss", $admin['id'], $token, $expires_at);
                                        if ($token_stmt->execute()) {
                                            setcookie('login_token', $token, time() + 30 * 24 * 3600, '/', '', false, true);
                                        }
                                        $token_stmt->close();
                                    }
                                }

                                header('Location: ' . $base_url . 'admin/dashboard.php');
                                error_log("Admin login successful: " . $admin['username']);
                                exit;
                            } else {
                                $error = 'Mật khẩu không chính xác';
                            }
                        } else {
                            $error = 'Tên đăng nhập hoặc email không tồn tại';
                        }
                    }
                }
                $stmt->close();
            }
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-form">
            <h2>Đăng nhập</h2>
            <?php if (!empty($error)): ?>
                <div class="auth-error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="auth-form-group">
                    <label for="email_or_username">Email hoặc Tên đăng nhập</label>
                    <input type="text" id="email_or_username" name="email_or_username" value="<?php echo isset($_POST['email_or_username']) ? htmlspecialchars($_POST['email_or_username']) : ''; ?>" required>
                </div>
                <div class="auth-form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="auth-form-group auth-remember-forgot">
                    <div class="auth-remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <div class="auth-forgot-password">
                        <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=reset'); ?>">Quên mật khẩu?</a>
                    </div>
                </div>
                <div class="auth-form-group">
                    <button type="submit" class="auth-button">Đăng nhập</button>
                </div>
                <div class="auth-links">
                    <p>Chưa có tài khoản? <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=register'); ?>">Đăng ký ngay</a></p>
                </div>
            </form>
        </div>
    </div>
</div>