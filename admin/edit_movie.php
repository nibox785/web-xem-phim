<?php
// Bật báo cáo lỗi và ghi log
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start session and include files
session_start();
require_once __DIR__ . '/../include/db.php';
require_once __DIR__ . '/check_admin.php';

// Kiểm tra quyền admin
requireAdmin();

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    error_log("Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error());
    die("Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.");
}

// Kiểm tra ID phim
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log("ID phim không hợp lệ: " . ($_GET['id'] ?? 'không có'));
    header('Location: ./movies.php');
    exit;
}

$movie_id = (int)$_GET['id'];

// Lấy thông tin phim
$stmt_select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
if (!$stmt_select_movie) {
    error_log("Lỗi chuẩn bị truy vấn phim: " . $conn->error);
    die("Lỗi truy vấn cơ sở dữ liệu.");
}
$stmt_select_movie->bind_param("i", $movie_id);
$stmt_select_movie->execute();
$movie_result = $stmt_select_movie->get_result();

if ($movie_result->num_rows === 0) {
    error_log("Phim không tồn tại: ID $movie_id");
    $stmt_select_movie->close();
    header('Location: ./movies.php');
    exit;
}

$movie = $movie_result->fetch_assoc();
$stmt_select_movie->close();

// Lấy thể loại của phim
$stmt_select_genres = $conn->prepare("SELECT genre_id FROM movie_genres WHERE movie_id = ?");
if (!$stmt_select_genres) {
    error_log("Lỗi chuẩn bị truy vấn thể loại: " . $conn->error);
    die("Lỗi truy vấn cơ sở dữ liệu.");
}
$stmt_select_genres->bind_param("i", $movie_id);
$stmt_select_genres->execute();
$genre_result = $stmt_select_genres->get_result();

$selected_genres = [];
while ($row = $genre_result->fetch_assoc()) {
    $selected_genres[] = $row['genre_id'];
}
$stmt_select_genres->close();

// Lấy danh sách thể loại và vũ trụ điện ảnh
$genres_result = $conn->query("SELECT * FROM genres ORDER BY name");
if (!$genres_result) {
    error_log("Lỗi truy vấn genres: " . $conn->error);
    die("Lỗi truy vấn cơ sở dữ liệu.");
}
$universes_result = $conn->query("SELECT * FROM universes ORDER BY name");
if (!$universes_result) {
    error_log("Lỗi truy vấn universes: " . $conn->error);
    die("Lỗi truy vấn cơ sở dữ liệu.");
}

$errors = [];
$success = false;

// Xử lý form sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $release_year = (int)($_POST['release_year'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $universe_id = (int)($_POST['universe_id'] ?? 0);
    $thumbnail = trim($_POST['thumbnail'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $genre_ids = isset($_POST['genres']) ? array_map('intval', $_POST['genres']) : [];

    // Kiểm tra dữ liệu
    if (empty($title)) {
        $errors[] = "Tên phim không được để trống";
    }
    
    if ($release_year < 1888 || $release_year > date("Y") + 5) {
        $errors[] = "Năm phát hành không hợp lệ";
    }
    
    // Nếu không có lỗi, cập nhật phim
    if (empty($errors)) {
        $stmt_update = $conn->prepare("UPDATE movies SET title = ?, release_year = ?, description = ?, thumbnail = ?, video_url = ?, universe_id = ? WHERE id = ?");
        if (!$stmt_update) {
            error_log("Lỗi chuẩn bị truy vấn UPDATE movies: " . $conn->error);
            $errors[] = "Lỗi chuẩn bị truy vấn cơ sở dữ liệu.";
        } else {
            $stmt_update->bind_param("sissisi", $title, $release_year, $description, $thumbnail, $video_url, $universe_id, $movie_id);
            if ($stmt_update->execute()) {
                // Xóa các thể loại cũ
                $stmt_delete_genres = $conn->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
                if (!$stmt_delete_genres) {
                    error_log("Lỗi chuẩn bị DELETE movie_genres: " . $conn->error);
                    $errors[] = "Lỗi xóa thể loại cũ.";
                } else {
                    $stmt_delete_genres->bind_param("i", $movie_id);
                    $stmt_delete_genres->execute();
                    $stmt_delete_genres->close();
                }
                
                // Thêm các thể loại mới
                if (!empty($genre_ids)) {
                    $stmt_insert_genres = $conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                    if (!$stmt_insert_genres) {
                        error_log("Lỗi chuẩn bị INSERT movie_genres: " . $conn->error);
                        $errors[] = "Lỗi thêm thể loại mới.";
                    } else {
                        foreach ($genre_ids as $genre_id) {
                            $stmt_insert_genres->bind_param("ii", $movie_id, $genre_id);
                            $stmt_insert_genres->execute();
                        }
                        $stmt_insert_genres->close();
                    }
                }
                
                $success = true;
                
                // Lấy lại thông tin phim
                $stmt_select_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
                if (!$stmt_select_movie) {
                    error_log("Lỗi chuẩn bị truy vấn phim sau cập nhật: " . $conn->error);
                    $errors[] = "Lỗi lấy thông tin phim.";
                } else {
                    $stmt_select_movie->bind_param("i", $movie_id);
                    $stmt_select_movie->execute();
                    $movie_result = $stmt_select_movie->get_result();
                    $movie = $movie_result->fetch_assoc();
                    $stmt_select_movie->close();
                }
                
                // Lấy lại thể loại
                $stmt_select_genres = $conn->prepare("SELECT genre_id FROM movie_genres WHERE movie_id = ?");
                if (!$stmt_select_genres) {
                    error_log("Lỗi chuẩn bị truy vấn thể loại sau cập nhật: " . $conn->error);
                    $errors[] = "Lỗi lấy thông tin thể loại.";
                } else {
                    $stmt_select_genres->bind_param("i", $movie_id);
                    $stmt_select_genres->execute();
                    $genre_result = $stmt_select_genres->get_result();
                    $selected_genres = [];
                    while ($row = $genre_result->fetch_assoc()) {
                        $selected_genres[] = $row['genre_id'];
                    }
                    $stmt_select_genres->close();
                }
            } else {
                error_log("Lỗi thực thi UPDATE movies: " . $stmt_update->error);
                $errors[] = "Không thể cập nhật phim: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa phim - Admin</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url . 'assets/admin.css'); ?>">
</head>
<body>
    <?php 
    try {
        include __DIR__ . '/admin_header.php';
    } catch (Exception $e) {
        error_log("Lỗi include admin_header.php: " . $e->getMessage());
        die("Lỗi tải giao diện. Vui lòng kiểm tra log.");
    }
    ?>
    
    <div class="admin-container">
        <h1>Sửa phim: <?php echo htmlspecialchars($movie['title']); ?></h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Phim đã được cập nhật thành công!
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Tên phim *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($movie['title'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="release_year">Năm phát hành *</label>
                <input type="number" id="release_year" name="release_year" min="1888" max="<?php echo date("Y") + 5; ?>" value="<?php echo $movie['release_year'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="universe_id">Vũ trụ điện ảnh</label>
                <select id="universe_id" name="universe_id">
                    <option value="0">Không thuộc vũ trụ nào</option>
                    <?php 
                    $universes_result->data_seek(0);
                    while ($universe = $universes_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $universe['id']; ?>" <?php echo ($movie['universe_id'] == $universe['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($universe['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Thể loại</label>
                <div class="checkbox-group">
                    <?php 
                    $genres_result->data_seek(0);
                    while ($genre = $genres_result->fetch_assoc()): 
                    ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="genre_<?php echo $genre['id']; ?>" name="genres[]" value="<?php echo $genre['id']; ?>" 
                                <?php echo in_array($genre['id'], $selected_genres) ? 'checked' : ''; ?>>
                            <label for="genre_<?php echo $genre['id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="thumbnail">URL hình poster</label>
                <input type="url" id="thumbnail" name="thumbnail" value="<?php echo htmlspecialchars($movie['thumbnail'] ?? ''); ?>">
                <?php if (!empty($movie['thumbnail'])): ?>
                    <div class="poster-preview">
                        <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="Poster Preview" width="100">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="video_url">URL trailer</label>
                <input type="url" id="video_url" name="video_url" value="<?php echo htmlspecialchars($movie['video_url'] ?? ''); ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật phim</button>
                <a href="./movies.php" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
    
    <?php 
    try {
        include __DIR__ . '/admin_footer.php';
    } catch (Exception $e) {
        error_log("Lỗi include admin_footer.php: " . $e->getMessage());
        die("Lỗi tải giao diện. Vui lòng kiểm tra log.");
    }
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>