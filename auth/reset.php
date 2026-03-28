<?php
// auth/reset.php
// require_once '../include/db.php';
// require_once '../include/functions.php';

// session_start();

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

// Xử lý yêu cầu đặt lại mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Vui lòng nhập email của bạn';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';
    } else {
        // Kiểm tra email tồn tại
        $email = $conn->real_escape_string($email);
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows == 0) {
            $error = 'Email không tồn tại trong hệ thống';
        } else {
            // Trong môi trường thực tế, tại đây bạn sẽ:
            // 1. Tạo mã đặt lại mật khẩu và lưu vào database
            // 2. Gửi email với link đặt lại mật khẩu
            
            // Hiện tại chỉ hiển thị thông báo thành công
            $success = 'Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư đến.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title>Quên mật khẩu - <?php echo SITE_NAME; ?></title>
</head>
<body class="auth-page">
    
    <div class="auth-container">
        <div class="auth-form">
            <h2>Quên mật khẩu</h2>
            <?php if (!empty($error)): ?>
                <div class="auth-error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="auth-success-message"><?php echo $success; ?></div>
            <?php else: ?>
                <p style="color: #ccc; text-align: center; margin-bottom: 20px;">
                    Nhập email đã đăng ký để nhận hướng dẫn đặt lại mật khẩu
                </p>
                
                <form method="POST" action="">
                    <div class="auth-form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="auth-form-group">
                        <button type="submit" class="auth-button">Gửi yêu cầu</button>
                    </div>
                    
                    <div class="auth-links">
                        <p><a href="index.php?page=login">Quay lại đăng nhập</a></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>