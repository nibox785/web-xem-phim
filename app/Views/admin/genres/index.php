<?php
$title = 'Quản lý thể loại - Admin';
include APP_PATH . '/Views/shared/admin_header.php';
?>

<div class="admin-container">
    <h1>Quản lý thể loại</h1>
    
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
    
    <form method="POST" action="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_add_genre">
        <div class="form-group">
            <label for="name">Tên thể loại *</label>
            <input type="text" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-primary">Thêm thể loại</button>
    </form>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên thể loại</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($genres) && count($genres) > 0): ?>
                <?php foreach ($genres as $genre): ?>
                    <tr>
                        <td><?php echo $genre['id']; ?></td>
                        <td><?php echo htmlspecialchars($genre['name']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" action="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_delete_genre" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại này?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <input type="hidden" name="delete_genre_id" value="<?php echo (int)$genre['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Không có thể loại nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include APP_PATH . '/Views/shared/admin_footer.php'; ?>
