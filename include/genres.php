<?php
// include/genres.php
require_once 'db.php';
require_once 'functions.php';

// Lấy genre_id từ query string, mặc định là 0 nếu không có
$genre_id = isset($_GET['genre_id']) && is_numeric($_GET['genre_id']) ? (int)$_GET['genre_id'] : 0;

// Lấy tên thể loại để hiển thị tiêu đề
$genre_query = "SELECT name FROM genres WHERE id = ?";
$genre_stmt = $conn->prepare($genre_query);
$genre_stmt->bind_param("i", $genre_id);
$genre_stmt->execute();
$genre_result = $genre_stmt->get_result();
$genre = $genre_result->fetch_assoc();
$genre_name = $genre ? htmlspecialchars($genre['name']) : "Thể loại không xác định";

// Số phim mỗi trang
$per_page = 15;

// Lấy trang hiện tại từ query string, mặc định là 1
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $per_page;

// Lấy tổng số phim thuộc thể loại
$sql_total = "SELECT COUNT(DISTINCT m.id) as total FROM movies m INNER JOIN movie_genres mg ON m.id = mg.movie_id WHERE mg.genre_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $genre_id);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_movies = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_movies / $per_page);

// Lấy danh sách phim với phân trang
$movies = getMoviesByGenre($conn, $genre_id, $per_page, $offset);
?>

<main class="container">
    <h1 class="page-title">THỂ LOẠI: <?php echo $genre_name; ?></h1>

    <div class="movie-grid" id="movie-list">
        <?php if (!empty($movies)): ?>
            <?php foreach ($movies as $row_movie): ?>
                <div class="movie-card">
                    <div class="movie-poster">
                        <img
                            src="<?php echo htmlspecialchars($row_movie['thumbnail'] ?: 'img/default.jpg'); ?>"
                            alt="<?php echo htmlspecialchars($row_movie['title']); ?>"
                        />
                        <a href="index.php?page=watch&id=<?php echo htmlspecialchars($row_movie['id']); ?>" class="play-button">
                            <span class="play-icon"></span>
                        </a>
                        <div class="quality-label">HD</div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?php echo htmlspecialchars($row_movie['title']); ?></h3>
                        <div class="movie-meta">
                            <span><?php echo htmlspecialchars($row_movie['release_year']); ?></span>
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
                <a href="?page=genres&genre_id=<?php echo $genre_id; ?>&p=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>
<link rel="stylesheet" href="assets/responsive.css">