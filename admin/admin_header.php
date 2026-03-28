<?php
require_once './check_admin.php';
requireAdmin();
?>
<header class="admin-header">
    <div class="admin-header-logo">
        <a href="../index.php" target="_blank">Movie Website</a>
    </div>
    <div class="admin-header-title">
        <h1>Trang quản trị</h1>
    </div>
    <div class="admin-header-user">
        <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
        <a href="../auth/admin_logout.php" class="btn btn-logout">Đăng xuất</a>
    </div>
</header>

<nav class="admin-nav">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="movies.php">Quản lý phim</a></li>
        <li><a href="genres.php">Quản lý thể loại</a></li>
        <!-- <li><a href="universes.php">Quản lý vũ trụ điện ảnh</a></li> -->
        <li><a href="users.php">Quản lý người dùng</a></li>
        <li><a href="../index.php" target="_blank">Xem trang web</a></li>
    </ul>
</nav>

<div class="admin-content">