<?php
require_once '../include/db.php';
require_once '../include/functions.php';

session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = 'Vui lòng nhập đầy đủ thông tin';
        } else {
            // Sử dụng prepared statement
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Kiểm tra mật khẩu
                if (password_verify($password, $user['password_hash'])) {
                    // Đăng nhập thành công
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    // Nếu cột role và profile_pic không tồn tại, bỏ qua
                    // $_SESSION['role'] = $user['role'];
                    // $_SESSION['profile_pic'] = $user['profile_pic'];

                    header('Location: ../index.php');
                    exit;
                } else {
                    $error = 'Mật khẩu không chính xác';
                }
            } else {
                $error = 'Email không tồn tại';
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    <!-- <div class="navbar">
        <div class="navbar-container">
            <div class="logo-container">
                <h1 class="logo"><a href="../index.php"><?php echo SITE_NAME; ?></a></h1>
            </div>
        </div>
    </div> -->

    <div class="container">
        <div class="auth-container">
            <div class="auth-form">
                <h2>Đăng nhập</h2>
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        <div class="forgot-password">
                            <a href="reset.php">Quên mật khẩu?</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="auth-button">Đăng nhập</button>
                    </div>
                    <div class="auth-links">
                        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/app.js"></script>
</body>
</html>