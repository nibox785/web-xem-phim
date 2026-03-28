<?php
// Số phim mỗi trang
$per_page = 15;

// Lấy trang hiện tại từ query string, mặc định là 1
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $per_page;

// Lấy tổng số phim ngoài Marvel/DC
$sql_total = "SELECT COUNT(*) as total FROM movies WHERE universe_id = 3";
$result_total = mysqli_query($conn, $sql_total);
$total_movies = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_movies / $per_page);

// Lấy danh sách phim cho trang hiện tại
$sql_marvel = "SELECT id, title, thumbnail, release_year FROM movies WHERE universe_id = 3 ORDER BY release_year DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql_marvel);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$result_marvel = $stmt->get_result();
?>

<main class="container">
    <h1 class="page-title">NGOÀI VŨ TRỤ MARVEL/DC</h1>

    <div class="movie-grid" id="movie-list">
        <?php if ($result_marvel->num_rows > 0): ?>
            <?php while ($row_marvel = $result_marvel->fetch_assoc()): ?>
                <div class="movie-card">
                    <div class="movie-poster">
                        <img
                            src="<?php echo htmlspecialchars($row_marvel['thumbnail'] ?: 'img/default.jpg'); ?>"
                            alt="<?php echo htmlspecialchars($row_marvel['title']); ?>"
                        />
                        <a href="index.php?page=watch&id=<?php echo htmlspecialchars($row_marvel['id']); ?>" class="play-button">
                            <span class="play-icon"></span>
                        </a>
                        <div class="quality-label">HD</div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?php echo htmlspecialchars($row_marvel['title']); ?></h3>
                        <div class="movie-meta">
                            <span><?php echo htmlspecialchars($row_marvel['release_year']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Không có phim nào trong danh mục này.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=marvel&p=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>
