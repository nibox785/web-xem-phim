<?php
// Start session and include database connection
session_start();
require_once '../include/db.php';
require_once './check_admin.php';

// Require admin privileges
requireAdmin();

// Handle deleting user
$success_message = '';
$error_message = '';
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Prevent deleting the current admin account
    if ($user_id === $_SESSION['user_id']) {
        $error_message = "Không thể xóa tài khoản đang đăng nhập!";
    } else {
        // Start a transaction to ensure data consistency
        $conn->begin_transaction();
        try {
            // Delete comments associated with the user
            $stmt = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
            
            // Delete the user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success_message = "Xóa người dùng thành công!";
            } else {
                $error_message = "Không tìm thấy người dùng để xóa!";
            }
            $stmt->close();
            
            // Commit the transaction
            $conn->commit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = "Lỗi khi xóa người dùng: " . $e->getMessage();
        }
    }
}

// Fetch user list
$result = $conn->query("SELECT id, username, email, role FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Admin</title>
    <!-- Use correct CSS path -->
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <!-- Include header -->
    <?php include './admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Quản lý người dùng</h1>
        
        <!-- Display success message -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <!-- Display error message -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Users table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                            <td><?php echo $user['role'] ? 'Người dùng' : 'Admin'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Các bình luận của họ cũng sẽ bị xóa.');">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có người dùng nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    

    <?php include './admin_footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>