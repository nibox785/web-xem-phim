<?php
// Admin header - bao gồm nav và mở container
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url ?? '/'); ?>assets/admin.css">
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="admin-header-logo">
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>">Movie Website</a>
        </div>
        <div class="admin-header-title">
            <h1>Trang quản trị</h1>
        </div>
        <div class="admin-header-user">
            <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_logout" class="btn btn-logout">Đăng xuất</a>
        </div>
    </header>

    <nav class="admin-nav">
        <ul>
            <li><a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_dashboard">Dashboard</a></li>
            <li><a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_movies">Quản lý phim</a></li>
            <li><a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_genres">Quản lý thể loại</a></li>
            <li><a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_users">Quản lý người dùng</a></li>
            <li><a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php" target="_blank">Xem trang web</a></li>
        </ul>
    </nav>

    <div class="admin-content">
