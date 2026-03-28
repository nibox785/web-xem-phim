<?php
// include/featured.php
require_once 'db.php';
require_once 'functions.php';

// Số phim mỗi trang
$per_page = 15;

// Lấy trang hiện tại từ query string, mặc định là 1
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $per_page;

// Lấy tổng số phim nổi bật
$sql_total = "SELECT COUNT(*) as total FROM movies WHERE featured = 1";
$result_total = mysqli_query($conn, $sql_total);
$total_movies = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_movies / $per_page);

// Lấy danh sách phim nổi bật sử dụng hàm getFeaturedMovies
$movies = getFeaturedMovies($conn, $per_page, $offset);
?>

<main class="container">
    <h1 class="page-title">PHIM NỔI BẬT</h1>

    <div class="movie-grid" id="movie-list">
        <?php if (!empty($movies)): ?>
            <?php foreach ($movies as $row_marvel): ?>
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
            <?php endforeach; ?>
        <?php else: ?>
            <p>Không có phim nào trong danh mục này.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=featured&p=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>

<link rel="stylesheet" href="assets/responsive.css" />