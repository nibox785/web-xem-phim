<?php
// Kết nối CSDL
require_once 'include/db.php';

// Lấy từ khóa tìm kiếm
$search_keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

// Số phim mỗi trang
$per_page = 15;

// Lấy trang hiện tại từ query string, mặc định là 1
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $per_page;

// Tính tổng số phim tìm thấy
$sql_total = "SELECT COUNT(*) as total FROM movies WHERE title LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$search_param = "%$search_keyword%";
$stmt_total->bind_param("s", $search_param);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_movies = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_movies / $per_page);

// Lấy danh sách phim phù hợp với từ khóa tìm kiếm
$sql_search = "SELECT id, title, thumbnail, release_year FROM movies WHERE title LIKE ? ORDER BY release_year DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql_search);
$stmt->bind_param("sii", $search_param, $per_page, $offset);
$stmt->execute();
$result_search = $stmt->get_result();
?>

<main class="container">
    <h1 class="page-title">KẾT QUẢ TÌM KIẾM: "<?php echo htmlspecialchars($search_keyword); ?>" </h1>
    
    <!-- <div class="search-bar search-page">
        <form action="index.php" method="GET">
            <input type="hidden" name="page" value="search">
            <input type="text" name="q" placeholder="Tìm kiếm phim..." 
                   value="<?php echo htmlspecialchars($search_keyword); ?>" />
            <button type="submit">Tìm</button>
        </form>
    </div> -->

    <div class="movie-grid" id="movie-list">
        <?php if ($result_search->num_rows > 0): ?>
            <?php while ($row = $result_search->fetch_assoc()): ?>
                <div class="movie-card">
                    <div class="movie-poster">
                        <img
                            src="<?php echo htmlspecialchars($row['thumbnail'] ?: 'img/default.jpg'); ?>"
                            alt="<?php echo htmlspecialchars($row['title']); ?>"
                        />
                        <a href="index.php?page=watch&id=<?php echo htmlspecialchars($row['id']); ?>" class="play-button">
                            <span class="play-icon"></span>
                        </a>
                        <div class="quality-label">HD</div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="movie-meta">
                            <span><?php echo htmlspecialchars($row['release_year']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <p>Không tìm thấy phim nào phù hợp với từ khóa "<?php echo htmlspecialchars($search_keyword); ?>".</p>
                <p>Vui lòng thử lại với từ khóa khác.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=search&q=<?php echo urlencode($search_keyword); ?>&p=<?php echo $i; ?>" 
                   class="<?php echo $i === $page ? 'active' : ''; ?>" 
                   data-page="<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>