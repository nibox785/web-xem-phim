<?php
// Bao gồm file kết nối cơ sở dữ liệu
require_once __DIR__ . '/db.php';

// Thiết lập các tiêu đề bảo mật
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Hàm tạo CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Lấy danh sách thể loại từ cơ sở dữ liệu
function getGenres($conn) {
    $sql = "SELECT id, name FROM genres ORDER BY name";
    $result = $conn->query($sql);
    $genres = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}

// Lấy thông tin người dùng nếu đã đăng nhập
$isLoggedIn = isset($_SESSION['user_id']);
$profilePicture = $base_url . 'img/profile.jpg'; // Ảnh mặc định
$profileName = 'Profile';

if ($isLoggedIn) {
    $userId = (int)$_SESSION['user_id'];
    $sql = "SELECT username, profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $profileName = htmlspecialchars($row['username']);
        if (!empty($row['profile_image'])) {
            $profilePicture = $base_url . 'images/' . htmlspecialchars($row['profile_image']);
        }
    }
    $stmt->close();
}

// Lấy danh sách thể loại
$genres = getGenres($conn);
?>
<header>
    <div class="navbar">
        <div class="navbar-container">
            <div class="logo-container">
                <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=home'); ?>">
                    <img src="<?php echo htmlspecialchars($base_url . 'images/slider/OIP.jpg'); ?>" alt="Logo" class="home-logo" />
                </a>
            </div>
            <div class="hamburger">
                <i class="fas fa-bars"></i>
            </div>
            <nav class="menu">
                <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=home'); ?>">Trang chủ</a>
                <!--Dropdown start-->
                <div class="dropdown">
                    <div class="active">Thể loại</div>
                    <ul class="dropdown-content">
                    <?php foreach ($genres as $genre): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=genres&genre_id=' . $genre['id']); ?>">
                            <?php echo htmlspecialchars($genre['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                    </ul>
                </div>
                <!--Dropdown end-->
                <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=marvel'); ?>" class="menu-item">Vũ trụ MARVEL</a>
                <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=dcu'); ?>" class="menu-item">Vũ trụ DCU</a>
                <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=others'); ?>" class="menu-item">Ngoài Marvel/DC</a>
            </nav>

            <div class="search-bar">
                <form action="<?php echo htmlspecialchars($base_url . 'index.php'); ?>" method="GET">
                    <input type="hidden" name="page" value="search">
                    <input type="text" name="q" placeholder="Tìm kiếm phim..." 
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" />
                    <button type="submit">Tìm</button>
                </form>
            </div>

            <!-- Profile -->
            <div class="profile-container">
                <div class="profile-text-container" id="profile-btn">
                    <img class="profile-picture" 
                         src="<?php echo htmlspecialchars($profilePicture); ?>" 
                         alt="Profile Picture" />
                    <span class="profile-text"><?php echo htmlspecialchars($profileName); ?></span>
                    <i class="fas fa-caret-down"></i>
                </div>
                <div class="profile-dropdown-content">
                    <?php if (!$isLoggedIn): ?>
                        <!-- Hiển thị khi chưa đăng nhập -->
                        <div class="guest-options">
                            <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=login'); ?>" class="dropdown-item">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                            <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=register'); ?>" class="dropdown-item">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Hiển thị khi đã đăng nhập -->
                        <div class="logged-in-options">
                            <a href="<?php echo htmlspecialchars($base_url . 'index.php?page=profile'); ?>" class="dropdown-item">
                                <i class="fas fa-user"></i> Thông tin cá nhân
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo htmlspecialchars($base_url . 'auth/user_logout.php'); ?>" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>