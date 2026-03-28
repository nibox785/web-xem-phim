<?php
// Bật báo cáo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../include/db.php';
require_once __DIR__ . '/check_admin.php';
requireAdmin();

// Handle movie deletion
$success_message = '';
$error_message = '';
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $movie_id = (int)$_GET['delete'];
    
    if (!$conn) {
        $error_message = "Lỗi kết nối cơ sở dữ liệu.";
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM comments WHERE movie_id = ?");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM ratings WHERE movie_id = ?");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success_message = "Phim đã được xóa thành công!";
            } else {
                $error_message = "Không thể xóa phim!";
            }
            $stmt->close();
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Lỗi khi xóa phim: " . $e->getMessage();
        }
    }
}

// Fetch movie list with genres
$sql = "SELECT m.*, u.name AS universe_name,
        GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
        FROM movies m
        LEFT JOIN universes u ON m.universe_id = u.id
        LEFT JOIN movie_genres mg ON m.id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        GROUP BY m.id
        ORDER BY m.id DESC";
$result = $conn ? $conn->query($sql) : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phim - Admin</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url . 'assets/admin.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Quản lý phim</h1>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="admin-controls">
            <a href="<?php echo htmlspecialchars($base_url . 'admin/add_movie.php'); ?>" class="btn btn-primary">Thêm phim mới</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thumbnail</th>
                    <th>Tên phim</th>
                    <th>Năm phát hành</th>
                    <th>Thể loại</th>
                    <th>Vũ trụ</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($movie = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $movie['id']; ?></td>
                            <td>
                                <?php if (!empty($movie['thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" width="50">
                                <?php else: ?>
                                    <span class="no-image">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo $movie['release_year']; ?></td>
                            <td><?php echo $movie['genres'] ? htmlspecialchars($movie['genres']) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($movie['universe_name'] ?? 'N/A'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo htmlspecialchars($base_url . 'admin/edit_movie.php?id=' . $movie['id']); ?>" class="btn btn-sm btn-primary">Sửa</a>
                                    <a href="<?php echo htmlspecialchars($base_url . 'admin/movies.php?delete=' . $movie['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa phim này? Các bình luận và đánh giá liên quan cũng sẽ bị xóa.');">Xóa</a>
                                    <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=watch&id=' . $movie['id']); ?>" class="btn btn-sm btn-info" target="_blank">Xem</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có phim nào trong cơ sở dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php include __DIR__ . '/admin_footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?php echo htmlspecialchars($base_url . 'js/admin.js'); ?>"></script>
</body>
</html>