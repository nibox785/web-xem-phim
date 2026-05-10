<main class="container">
    <h1 class="page-title">PHIM ĐIỆN ẢNH</h1>

    <div class="movie-grid" id="movie-list">
        <?php if (!empty($movies)): ?>
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <div class="movie-poster">
                        <img 
                            src="<?= $movie['poster_url'] ?>" 
                            alt="<?= $movie['title'] ?>"
                        />
                        <a href="<?= $movie['watch_link'] ?>" class="play-button">
                            <span class="play-icon"></span>
                        </a>
                        <div class="quality-label">HD</div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?= $movie['title'] ?></h3>
                        <div class="movie-meta">
                            <span><?= $movie['release_year'] ?></span>
                            <span class="rating">⭐ <?= number_format($movie['rating'], 1) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Không có phim nào trong danh mục này.</p>
        <?php endif; ?>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <a href="?page=movies&p=<?= $i ?>" 
                   class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>

<link rel="stylesheet" href="assets/responsive.css">