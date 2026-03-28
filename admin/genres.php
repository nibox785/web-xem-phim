<?php
// Start session and include database connection
session_start();
require_once '../include/db.php';
require_once './check_admin.php';

// Require admin privileges
requireAdmin();

// Handle adding genre
$errors = [];
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_genre'])) {
    $name = trim($_POST['name']);
    
    // Validate genre name
    if (empty($name)) {
        $errors[] = "Tên thể loại không được để trống";
    } else {
        // Insert genre into database
        $stmt = $conn->prepare("INSERT INTO genres (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $success_message = "Thêm thể loại thành công!";
        } else {
            $errors[] = "Lỗi khi thêm thể loại: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle deleting genre
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $genre_id = (int)$_GET['delete'];
    
    // Delete movie_genres associations first
    $stmt = $conn->prepare("DELETE FROM movie_genres WHERE genre_id = ?");
    $stmt->bind_param("i", $genre_id);
    $stmt->execute();
    
    // Delete genre
    $stmt = $conn->prepare("DELETE FROM genres WHERE id = ?");
    $stmt->bind_param("i", $genre_id);
    if ($stmt->execute()) {
        $success_message = "Xóa thể loại thành công!";
    } else {
        $errors[] = "Lỗi khi xóa thể loại!";
    }
    $stmt->close();
}

// Fetch genre list
$result = $conn->query("SELECT * FROM genres ORDER BY name");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thể loại - Admin</title>
    <!-- Use correct CSS path -->
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <!-- Include header -->
    <?php include './admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Quản lý thể loại</h1>
        
        <!-- Display success message -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <!-- Display errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Add genre form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Tên thể loại *</label>
                <input type="text" id="name" name="name" required>
            </div>
            <button type="submit" name="add_genre" class="btn btn-primary">Thêm thể loại</button>
        </form>
        
        <!-- Genres table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên thể loại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($genre = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $genre['id']; ?></td>
                            <td><?php echo htmlspecialchars($genre['name']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Fixed delete link -->
                                    <a href="genres.php?delete=<?php echo $genre['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa thể loại này?');">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Không có thể loại nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php include './admin_footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>