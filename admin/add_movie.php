<?php
require_once __DIR__ . '/../include/db.php';
require_once __DIR__ . '/check_admin.php';
requireAdmin();

$genres = $conn->query("SELECT * FROM genres ORDER BY name");
$universes = $conn->query("SELECT * FROM universes ORDER BY name");

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $release_year = (int)$_POST['release_year'];
    $description = trim($_POST['description']);
    $universe_id = (int)$_POST['universe_id'] ;
    $universe_id = $universe_id > 0 ? $universe_id : NULL;
    $thumbnail = isset($_POST['thumbnail']) && !empty($_POST['thumbnail']) ? trim($_POST['thumbnail']) : NULL;
    $video_url = isset($_POST['video_url']) && !empty($_POST['video_url']) ? trim($_POST['video_url']) : NULL;
    $genre_ids = isset($_POST['genres']) ? $_POST['genres'] : [];
    
    if (empty($title)) {
        $errors[] = "Tên phim không được để trống";
    }
    
    if ($release_year < 1888 || $release_year > date("Y") + 5) {
        $errors[] = "Năm phát hành không hợp lệ";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO movies (title, release_year, description, thumbnail, video_url, universe_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssi", $title, $release_year, $description, $thumbnail, $video_url, $universe_id);
        
        if ($stmt->execute()) {
            $movie_id = $conn->insert_id;
            
            if (!empty($genre_ids)) {
                $genre_stmt = $conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                foreach ($genre_ids as $genre_id) {
                    $genre_stmt->bind_param("ii", $movie_id, $genre_id);
                    $genre_stmt->execute();
                }
                $genre_stmt->close();
            }
            
            $success = true;
        } else {
            $errors[] = "Không thể thêm phim: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm phim mới - Admin</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url . 'assets/admin.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Thêm phim mới</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Phim đã được thêm thành công! <a href="<?php echo htmlspecialchars($base_url . 'admin/movies.php'); ?>">Xem danh sách phim</a>
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
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="release_year">Năm phát hành *</label>
                    <input type="number" id="release_year" name="release_year" min="1888" max="<?php echo date("Y") + 5; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="universe_id">Vũ trụ điện ảnh</label>
                <select id="universe_id" name="universe_id">
                    <option value="0">Không thuộc vũ trụ nào</option>
                    <?php while ($universe = $universes->fetch_assoc()): ?>
                        <option value="<?php echo $universe['id']; ?>">
                            <?php echo htmlspecialchars($universe['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Thể loại</label>
                <div class="checkbox-group">
                    <?php while ($genre = $genres->fetch_assoc()): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="genre_<?php echo $genre['id']; ?>" name="genres[]" value="<?php echo $genre['id']; ?>">
                            <label for="genre_<?php echo $genre['id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>
            
            <div class="form-group">
                <label for="thumbnail">URL hình ảnh thumbnail</label>
                <input type="url" id="thumbnail" name="thumbnail">
            </div>
            
            <div class="form-group">
                <label for="video_url">URL video</label>
                <input type="url" id="video_url" name="video_url">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Thêm phim</button>
                <a href="<?php echo htmlspecialchars($base_url . 'admin/movies.php'); ?>" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
    
    <?php include __DIR__ . '/admin_footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?php echo htmlspecialchars($base_url . 'js/admin.js'); ?>"></script>
</body>
</html>