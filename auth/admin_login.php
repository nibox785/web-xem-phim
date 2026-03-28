<?php
session_start();
require_once '../include/db.php';

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sử dụng prepared statement để tránh SQL injection
    $stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role'];
            header("Location: ../admin/dashboard.php");
            exit;
        } else {
            $error = "Mật khẩu không đúng.";
        }
    } else {
        $error = "Tên đăng nhập không tồn tại.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">

        <h1>Đăng nhập Admin</h1>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="" autocomplete="off" method="POST">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit" name="login" value="login">Đăng nhập</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" type="text/javascript"></script>
</body>
</html>