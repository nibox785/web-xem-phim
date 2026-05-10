<?php
$title = 'Quản lý người dùng - Admin';
include APP_PATH . '/Views/shared/admin_header.php';
?>

<div class="admin-container">
    <h1>Quản lý người dùng</h1>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
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
            <?php if (!empty($users) && count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['roles'] ?? 'user'); ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <form method="POST" action="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_delete_user" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Các bình luận của họ cũng sẽ bị xóa.');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="delete_user_id" value="<?php echo (int)$user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">(Tài khoản hiện tại)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Không có người dùng nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include APP_PATH . '/Views/shared/admin_footer.php'; ?>
