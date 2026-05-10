<?php
$title = 'Quản lý phim - Admin';
include APP_PATH . '/Views/shared/admin_header.php';
?>

<div class="admin-container">
    <h1>Quản lý phim</h1>
    
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
    
    <div class="admin-controls">
        <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_add_movie" class="btn btn-primary">Thêm phim mới</a>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Tên phim</th>
                <th>Năm phát hành</th>
                <th>Thể loại</th>
                <th>Studio</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($movies) && count($movies) > 0): ?>
                <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?php echo $movie['id']; ?></td>
                        <td>
                            <?php if (!empty($movie['poster_url'])): ?>
                                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" width="50">
                            <?php else: ?>
                                <span class="no-image">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($movie['title']); ?></td>
                        <td><?php echo $movie['release_year']; ?></td>
                        <td><?php echo htmlspecialchars($movie['genres'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($movie['studio_name'] ?? 'N/A'); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_edit_movie&id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-primary" data-confirm="Bạn có chắc muốn sửa phim này không?">Sửa</a>
                                <form method="POST" action="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_delete_movie" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phim này? Các bình luận và đánh giá liên quan cũng sẽ bị xóa.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <input type="hidden" name="delete_movie_id" value="<?php echo (int)$movie['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                                <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?page=watch&id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-info" target="_blank">Xem</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Không có phim nào trong cơ sở dữ liệu</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_movies&p=<?php echo $i; ?>" 
                   class="<?php echo $i === $pagination['page'] ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php include APP_PATH . '/Views/shared/admin_footer.php'; ?>
