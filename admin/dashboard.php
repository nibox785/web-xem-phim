<?php
// Bật báo cáo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../include/db.php';
require_once __DIR__ . '/check_admin.php';
requireAdmin();

try {
    $movie_count = $conn->query("SELECT COUNT(*) AS total FROM movies")->fetch_assoc()['total'];
    $user_count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
    $genre_count = $conn->query("SELECT COUNT(*) AS total FROM genres")->fetch_assoc()['total'];
    $recent_movies = $conn->query("SELECT * FROM movies ORDER BY id DESC LIMIT 5");
} catch (Exception $e) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(  '../assets/admin.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Dashboard</h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo $movie_count; ?></div>
                <div class="stat-label">Phim</div>
                <a href="<?php echo htmlspecialchars($base_url . 'admin/movies.php'); ?>" class="stat-link">Quản lý phim</a>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $user_count; ?></div>
                <div class="stat-label">Người dùng</div>
                <a href="<?php echo htmlspecialchars($base_url . 'admin/users.php'); ?>" class="stat-link">Quản lý người dùng</a>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $genre_count; ?></div>
                <div class="stat-label">Thể loại</div>
                <a href="<?php echo htmlspecialchars($base_url . 'admin/genres.php'); ?>" class="stat-link">Quản lý thể loại</a>
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
                    <?php if ($recent_movies->num_rows > 0): ?>
                        <?php while ($movie = $recent_movies->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $movie['id']; ?></td>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo $movie['release_year']; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo htmlspecialchars($base_url . 'admin/edit_movie.php?id=' . $movie['id']); ?>" class="btn btn-sm btn-primary">Sửa</a>
                                        <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=watch&id=' . $movie['id']); ?>" class="btn btn-sm btn-info" target="_blank">Xem</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không có phim nào trong cơ sở dữ liệu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="admin-actions">
                <a href="<?php echo htmlspecialchars($base_url . 'admin/add_movie.php'); ?>" class="btn btn-primary">Thêm phim mới</a>
                <a href="<?php echo htmlspecialchars($base_url . 'admin/movies.php'); ?>" class="btn btn-secondary">Xem tất cả phim</a>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/admin_footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?php echo htmlspecialchars($base_url . 'js/admin.js'); ?>"></script>
</body>
</html>