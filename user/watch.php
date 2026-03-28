<?php
// Bật báo cáo lỗi để phát hiện vấn đề
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tính toán base_url động
$base_path = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$base_url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path) . '/';

// Chỉ gọi functions.php, không gọi header.php ở đây
require_once __DIR__ . '/../include/functions.php';

// Kiểm tra và lấy ID phim
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../movies.php');
    exit;
}

$movie_id = (int)$_GET['id'];

// Xử lý gửi bình luận
$comment_message = '';
if (isset($_SESSION['user_id']) && isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment']);
    
    if (!empty($comment_text)) {
        $comment_text = $conn->real_escape_string($comment_text);
        $insert_comment_sql = "INSERT INTO comments (user_id, movie_id, comment_text, created_at, is_approved) 
                              VALUES (?, ?, ?, NOW(), 1)";
        $insert_stmt = $conn->prepare($insert_comment_sql);
        if (!$insert_stmt) {
            die("Lỗi chuẩn bị truy vấn bình luận: " . $conn->error);
        }
        $insert_stmt->bind_param("iis", $user_id, $movie_id, $comment_text);
        if ($insert_stmt->execute()) {
            $comment_message = 'Bình luận đã được gửi!';
            // Cập nhật lại danh sách bình luận
            $comments_sql = "SELECT c.comment_text, c.created_at, u.username, u.profile_image 
                             FROM comments c 
                             JOIN users u ON c.user_id = u.id 
                             WHERE c.movie_id = ? AND c.is_approved = 1 
                             ORDER BY c.created_at DESC 
                             LIMIT 5";
            $comments_stmt = $conn->prepare($comments_sql);
            if (!$comments_stmt) {
                die("Lỗi chuẩn bị truy vấn bình luận: " . $conn->error);
            }
            $comments_stmt->bind_param("i", $movie_id);
            $comments_stmt->execute();
            $comments_result = $comments_stmt->get_result();
            $comments = $comments_result->fetch_all(MYSQLI_ASSOC);
            $comments_stmt->close();
        }
        $insert_stmt->close();
        header("Location: {$base_url}index.php?page=watch&id=$movie_id#comment");
        exit;
    }
}

// Xử lý gửi đánh giá
$rating_message = '';
if (isset($_SESSION['user_id']) && isset($_POST['rating'])) {
    $user_id = $_SESSION['user_id'];
    $rating = (int)$_POST['rating'];
    
    if ($rating >= 1 && $rating <= 5) {
        $check_sql = "SELECT id FROM ratings WHERE user_id = ? AND movie_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            die("Lỗi chuẩn bị truy vấn kiểm tra đánh giá: " . $conn->error);
        }
        $check_stmt->bind_param("ii", $user_id, $movie_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $update_sql = "UPDATE ratings SET rating = ?, created_at = NOW() WHERE user_id = ? AND movie_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                die("Lỗi chuẩn bị truy vấn cập nhật đánh giá: " . $conn->error);
            }
            $update_stmt->bind_param("iii", $rating, $user_id, $movie_id);
            if ($update_stmt->execute()) {
                $rating_message = 'Cập nhật đánh giá thành công!';
            }
        } else {
            $insert_sql = "INSERT INTO ratings (user_id, movie_id, rating, created_at) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                die("Lỗi chuẩn bị truy vấn thêm đánh giá: " . $conn->error);
            }
            $insert_stmt->bind_param("iii", $user_id, $movie_id, $rating);
            if ($insert_stmt->execute()) {
                $rating_message = 'Đánh giá thành công!';
            }
        }
        header("Location: {$base_url}index.php?page=watch&id=$movie_id#review");
        exit;
    }
}

// Lấy profile_image cho người dùng hiện tại (dùng trong comment-form)
$current_user_image = $base_url . 'img/profile.jpg';
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $sql = "SELECT profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['profile_image'])) {
                $current_user_image = $base_url . 'images/' . htmlspecialchars($row['profile_image']);
            }
        }
        $stmt->close();
    }
}

// Lấy bình luận
$comments_sql = "SELECT c.comment_text, c.created_at, u.username, u.profile_image 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.movie_id = ? AND c.is_approved = 1 
                 ORDER BY c.created_at DESC 
                 LIMIT 5";
$comments_stmt = $conn->prepare($comments_sql);
if (!$comments_stmt) {
    die("Lỗi chuẩn bị truy vấn bình luận: " . $conn->error);
}
$comments_stmt->bind_param("i", $movie_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
$comments = $comments_result->fetch_all(MYSQLI_ASSOC);

// Lấy dữ liệu phim
$movie = getMovieDetails($conn, $movie_id);
if (!$movie) {
    error_log("Không tìm thấy phim với ID: $movie_id");
    header('Location: ../movies.php');
    exit;
}

// Debug video_url
if (empty($movie['video_url'])) {
    error_log("video_url trống cho phim ID: $movie_id");
}

// Lấy diễn viên
$cast_sql = "SELECT a.name, ma.role 
             FROM actors a 
             JOIN movie_actors ma ON a.id = ma.actor_id 
             WHERE ma.movie_id = ?
             ORDER BY ma.actor_id"; 
$cast_stmt = $conn->prepare($cast_sql);
if (!$cast_stmt) {
    die("Lỗi chuẩn bị truy vấn diễn viên: " . $conn->error);
}
$cast_stmt->bind_param("i", $movie_id);
$cast_stmt->execute();
$cast_result = $cast_stmt->get_result();
$cast = $cast_result->fetch_all(MYSQLI_ASSOC);

// Lấy đánh giá trung bình
$avg_rating_sql = "SELECT AVG(rating) as avg_rating, COUNT(rating) as rating_count 
                   FROM ratings 
                   WHERE movie_id = ?";
$avg_rating_stmt = $conn->prepare($avg_rating_sql);
if (!$avg_rating_stmt) {
    die("Lỗi chuẩn bị truy vấn đánh giá: " . $conn->error);
}
$avg_rating_stmt->bind_param("i", $movie_id);
$avg_rating_stmt->execute();
$avg_rating_result = $avg_rating_stmt->get_result();
$rating_data = $avg_rating_result->fetch_assoc();

// Xử lý giá trị avg_rating
$avg_rating = $rating_data['avg_rating'] ? number_format($rating_data['avg_rating'], 1) : '0.0';
$rating_count = $rating_data['rating_count'] ?? 0;
$stars = is_numeric($avg_rating) ? floor($avg_rating) : 0;

// Lấy phim liên quan (ưu tiên cùng diễn viên, sau đó cùng thể loại)
$related_sql = " SELECT DISTINCT m.*, 
           (SELECT COUNT(*) 
            FROM movie_actors ma1 
            JOIN movie_actors ma2 ON ma1.actor_id = ma2.actor_id 
            WHERE ma1.movie_id = ? AND ma2.movie_id = m.id) AS shared_actors
    FROM movies m
    WHERE m.id != ?
    AND (
        m.id IN (
            SELECT ma.movie_id 
            FROM movie_actors ma 
            WHERE ma.actor_id IN (
                SELECT actor_id 
                FROM movie_actors 
                WHERE movie_id = ?
            )
        )
        OR m.id IN (
            SELECT mg2.movie_id 
            FROM movie_genres mg1 
            JOIN movie_genres mg2 ON mg1.genre_id = mg2.genre_id 
            WHERE mg1.movie_id = ?
        )
    )
    ORDER BY shared_actors DESC, m.release_year DESC
    LIMIT 8";

$related_stmt = $conn->prepare($related_sql);
if (!$related_stmt) {
    die("Lỗi chuẩn bị truy vấn phim liên quan: " . $conn->error);
}
$related_stmt->bind_param("iiii", $movie_id, $movie_id, $movie_id, $movie_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_movies = $related_result->fetch_all(MYSQLI_ASSOC);

// Hàm chuyển đổi URL YouTube
function convertYouTubeUrl($url) {
    $url = trim($url);
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
        return "https://www.youtube.com/embed/$video_id?enablejsapi=1&rel=0";
    } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
        return "https://www.youtube.com/embed/$video_id?enablejsapi=1&rel=0";
    }
    error_log("URL YouTube không hợp lệ: $url");
    return null;
}

$embed_url = convertYouTubeUrl($movie['video_url']);

// Chỉ gọi header.php sau khi xử lý tất cả logic có thể gọi header()
require_once __DIR__ . '/../include/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem <?php echo htmlspecialchars($movie['title']); ?> - Marvel DC Movies</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url . 'assets/responsive.css'); ?>">
    <script async src="https://www.youtube.com/iframe_api"></script>
</head>
<body>
    <div class="container movie-container">
        <!-- Video Player -->
        <div class="player-container">
            <div class="video-wrapper" id="video-wrapper">
                <?php if ($embed_url && filter_var($embed_url, FILTER_VALIDATE_URL)): ?>
                    <iframe id="youtube-player" src="<?php echo htmlspecialchars($embed_url); ?>" 
                            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>
                <?php else: ?>
                    <p style="color: red; text-align: center; padding: 20px;">
                        Không thể phát video. URL không hợp lệ hoặc video không khả dụng. 
                        URL: <?php echo htmlspecialchars($movie['video_url'] ?? 'Không có URL'); ?>
                    </p>
                <?php endif; ?>
                <div class="video-placeholder"></div>
            </div>
            <div class="player-controls">
                <div class="control-left">
                    <button class="control-btn" onclick="seekVideo(-5)">⏮ Lùi 5s</button>
                    <button class="control-btn" onclick="seekVideo(5)">⏭ Tiến 5s</button>
                    <button class="control-btn" onclick="togglePlay()">⏸ Tạm dừng</button>
                </div>
            </div>
        </div>

        <!-- Main Content and Sidebar -->
        <div class="movie-page-container">
            <!-- Movie Content -->
            <div class="content-section">
                <div class="movie-header">
                    <div class="movie-poster-watch">
                        <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" />
                    </div>
                    <div class="movie-info">
                        <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?> (<?php echo $movie['release_year']; ?>)</h1>

                        <div class="categories">
                            <?php foreach ($movie['genres'] as $genre): ?>
                                <?php if (isset($genre['id']) && is_numeric($genre['id'])): ?>
                                    <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=genres&genre_id=' . $genre['id']); ?>" class="category-tag">
                                        <?php echo htmlspecialchars($genre['name']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="category-tag"><?php echo htmlspecialchars($genre['name']); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="movie-meta-watch">
                            <span>Năm sản xuất: <?php echo $movie['release_year']; ?></span>
                            <span>Vũ trụ: <?php echo htmlspecialchars($movie['universe_name']); ?></span>
                        </div>

                        <div class="movie-rating">
                            <?php if ($rating_count > 0): ?>
                                <span class="star"><?php echo str_repeat('★', $stars); ?></span>
                                <span><?php echo $avg_rating; ?>/5 (<?php echo $rating_count; ?> đánh giá)</span>
                            <?php else: ?>
                                <span>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="movie-description">
                    <h3 class="description-header">Mô tả phim</h3>
                    <div class="description-content">
                        <p><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
                    </div>
                </div>

                <div class="movie-review" id="review">
                    <h2>Đánh giá phim</h2>
                    <p>Hãy chia sẻ cảm nghĩ và đánh giá của bạn về bộ phim này.</p>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST">
                            <div class="rating-select">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="rating-star" data-value="<?php echo $i; ?>">★</span>
                                <?php endfor; ?>
                                <input type="hidden" name="rating" id="rating-value">
                            </div>
                            <button type="submit">Gửi đánh giá</button>
                        </form>
                        <?php if (!empty($rating_message)): ?>
                            <div id="review-message" style="margin-top: 10px; color: green;">
                                <?php echo $rating_message; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Vui lòng <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=login'); ?>">đăng nhập</a> để đánh giá.</p>
                    <?php endif; ?>
                </div>

                <!-- Comments Section -->
                <div class="comments-section" id="comments">
                    <h3 class="description-header">Bình luận về phim (<?php echo count($comments); ?>)</h3>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="comment-form">
                            <div class="user-avatar">
                                <img src="<?php echo htmlspecialchars($current_user_image); ?>" alt="User Avatar" />
                            </div>
                            <div class="comment-input-container">
                                <form method="POST">
                                    <textarea class="comment-input" name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
                                    <button class="comment-submit" type="submit">Gửi bình luận</button>
                                </form>
                                <?php if (!empty($comment_message)): ?>
                                    <div style="margin-top: 10px; color: green;">
                                        <?php echo $comment_message; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Vui lòng <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=login'); ?>">đăng nhập</a> để bình luận.</p>
                    <?php endif; ?>

                    <div class="comment-list">
                        <?php if (count($comments) > 0): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="user-avatar">
                                        <img src="<?php echo $comment['profile_image'] ? ($base_url . 'images/' . htmlspecialchars($comment['profile_image'])) : ($base_url . 'images/default.jpg'); ?>" alt="<?php echo htmlspecialchars($comment['username']); ?>" />
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-user"><?php echo htmlspecialchars($comment['username']); ?></div>
                                        <div class="comment-date"><?php echo date('d/m/Y, H:i', strtotime($comment['created_at'])); ?></div>
                                        <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Related Movies Sidebar -->
            <div class="related-movies-sidebar">
                <h3>Phim liên quan</h3>
                <?php if (count($related_movies) > 0): ?>
                    <?php foreach ($related_movies as $related): ?>
                        <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=watch&id=' . $related['id']); ?>" class="related-movie-item">
                            <img src="<?php echo htmlspecialchars($related['thumbnail']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" />
                            <div>
                                <div class="related-movie-title"><?php echo htmlspecialchars($related['title']); ?></div>
                                <div class="related-movie-year"><?php echo htmlspecialchars($related['release_year']); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có phim liên quan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?php echo htmlspecialchars($base_url . 'js/watch.js'); ?>"></script>
</body>
</html>