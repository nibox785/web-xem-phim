<?php
$title = 'Dashboard - Admin';
include APP_PATH . '/Views/shared/admin_header.php';
?>

<div class="admin-container">
    <h1>Dashboard</h1>
    
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
    
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_movies ?? 0; ?></div>
            <div class="stat-label">Phim</div>
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_movies" class="stat-link">Quản lý phim</a>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_users ?? 0; ?></div>
            <div class="stat-label">Người dùng</div>
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_users" class="stat-link">Quản lý người dùng</a>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_genres ?? 0; ?></div>
            <div class="stat-label">Thể loại</div>
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_genres" class="stat-link">Quản lý thể loại</a>
        </div>
    </div>
    
    <div class="recent-section">
        <h2>Phim mới thêm gần đây</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phim</th>
                    <th>Năm phát hành</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_movies) && count($recent_movies) > 0): ?>
                    <?php foreach ($recent_movies as $movie): ?>
                        <tr>
                            <td><?php echo $movie['id']; ?></td>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo $movie['release_year']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_edit_movie&id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-primary" data-confirm="Bạn có chắc muốn sửa phim này không?">Sửa</a>
                                    <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?page=watch&id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-info" target="_blank">Xem</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Không có phim nào trong cơ sở dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="admin-actions">
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_add_movie" class="btn btn-primary">Thêm phim mới</a>
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_movies" class="btn btn-secondary">Xem tất cả phim</a>
        </div>
    </div>
</div>

<?php include APP_PATH . '/Views/shared/admin_footer.php'; ?>
