<?php
// Ngăn truy cập trực tiếp
if (!defined('INCLUDED_VIA_INDEX')) {
    header('Location: ../index.php');
    exit;
}

// require_once '../include/functions.php';

// session_start();

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Tạo CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Kiểm tra các trường
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = 'Vui lòng nhập đầy đủ thông tin';
        } elseif (strlen($username) > 50 || strlen($email) > 255) {
            $error = 'Tên người dùng hoặc email quá dài';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = 'Tên người dùng chỉ được chứa chữ cái, số và dấu gạch dưới';
        } elseif ($password !== $confirm_password) {
            $error = 'Xác nhận mật khẩu không khớp';
        } elseif (strlen($password) < 6) {
            $error = 'Mật khẩu phải có ít nhất 6 ký tự';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email không hợp lệ';
        } else {
            // Kiểm tra email đã tồn tại
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = 'Email đã được sử dụng';
            } else {
                // Kiểm tra username đã tồn tại
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error = 'Tên người dùng đã tồn tại';
                } else {
                    // Mã hóa mật khẩu
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Thêm người dùng vào database
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $username, $email, $hashed_password);

                    if ($stmt->execute()) {
                        $success = "Đăng ký thành công! Giờ bạn có thể <a href=\"" . htmlspecialchars($base_url . 'index.php?page=login') . "\">đăng nhập</a>.";
                    } else {
                        $error = 'Có lỗi xảy ra: ' . $stmt->error;
                    }
                }
            }
            $stmt->close();
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-form">
            <h2>Đăng ký tài khoản</h2>
            <?php if (!empty($error)): ?>
                <div class="auth-error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="auth-success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="auth-form-group">
                    <label for="username">Tên người dùng</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
                <div class="auth-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <div class="auth-form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="auth-form-group">
                    <label for="confirm_password">Xác nhận mật khẩu</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="auth-form-group">
                    <button type="submit" class="auth-button">Đăng ký</button>
                </div>
                <div class="auth-links">
                    <p>Đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a></p>
                </div>
            </form>
        </div>
    </div>
</div>  