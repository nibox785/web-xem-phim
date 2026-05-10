<?php
/**
 * Search Results View
 * 
 * Hiển thị kết quả tìm kiếm phim
 */
?>
<main class="container">
    <h1 class="page-title">
        KẾT QUẢ TÌM KIẾM
        <?php if (!empty($query)): ?>
            CHO: "<?= htmlspecialchars($query) ?>"
        <?php endif; ?>
    </h1>

    <?php if (!empty($total)): ?>
        <p class="search-summary">Tìm thấy <strong><?= $total ?></strong> kết quả</p>
    <?php endif; ?>

    <div class="movie-grid" id="movie-list">
        <?php if (!empty($movies)): ?>
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <div class="movie-poster">
                        <img 
                            src="<?= htmlspecialchars($movie['poster_url']) ?>" 
                            alt="<?= htmlspecialchars($movie['title']) ?>"
                            loading="lazy"
                        />
                        <a href="<?= htmlspecialchars($movie['watch_link']) ?>" class="play-button">
                            <span class="play-icon"></span>
                        </a>
                        <div class="quality-label">HD</div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                        <div class="movie-meta">
                            <span><?= htmlspecialchars($movie['release_year']) ?></span>
                            <span class="rating">⭐ <?= number_format($movie['rating'], 1) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-message">
                <?php if (!empty($query)): ?>
                    Không tìm thấy phim nào khớp với "<strong><?= htmlspecialchars($query) ?></strong>". 
                    <br />Hãy thử lại với từ khóa khác.
                <?php else: ?>
                    Vui lòng nhập từ khóa tìm kiếm.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['has_prev']): ?>
                <a href="?page=search&q=<?= urlencode($query) ?>&p=<?= $pagination['prev_page'] ?>" class="pagination-nav">
                    &laquo; Trước
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <a href="?page=search&q=<?= urlencode($query) ?>&p=<?= $i ?>" 
                   class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=search&q=<?= urlencode($query) ?>&p=<?= $pagination['next_page'] ?>" class="pagination-nav">
                    Sau &raquo;
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<link rel="stylesheet" href="assets/responsive.css">
