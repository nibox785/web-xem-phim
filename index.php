<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/include/db.php';
define('INCLUDED_VIA_INDEX', true);
require_once __DIR__ . '/include/functions.php';

// Kiểm tra role và điều hướng
if (isset($_SESSION['user_id']) && !isset($_GET['page'])) {
    $user_id = (int)$_SESSION['user_id'];
    $sql = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn role: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['role'] === 'admin') {
            header("Location: ./admin/dashboard.php");
            exit;
        }
    } else {
        session_unset();
        session_destroy();
        header("Location: ./index.php?page=login");
        exit;
    }
    $stmt->close();
}
?>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('SITE_NAME') ? SITE_NAME : 'Movie Website'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="./assets/style.css">
    <?php
    if (isset($_GET['page']) && in_array($_GET['page'], ['marvel', 'dcu', 'others', 'search'])) {
        echo '<link rel="stylesheet" href="./assets/responsive.css">';
    }
    if (isset($_GET['page']) && in_array($_GET['page'], ['contact', 'introduce', 'TermsOfUse', 'policy'])) {
        echo '<link rel="stylesheet" href="./contact/contact.css">';
    }
    if (isset($_GET['page']) && in_array($_GET['page'], ['register', 'login', 'profile', 'reset'])) {
        echo '<link rel="stylesheet" href="./assets/auth.css">';
    }
    ?>
</head>
<body>
    <?php
    include __DIR__ . '/include/header.php';

    if (isset($_GET['page'])) {
        $chuyen = $_GET['page'];
    } else {
        $chuyen = '';
    }

    if ($chuyen == 'home') {
        include __DIR__ . '/include/slider.php';
    } elseif ($chuyen == 'marvel') {
        include __DIR__ . '/include/marvel.php';
    } elseif ($chuyen == 'dcu') {
        include __DIR__ . '/include/dcu.php';
    } elseif ($chuyen == 'others') {
        include __DIR__ . '/include/other.php';
    } elseif ($chuyen == 'featured') {
        include __DIR__ . '/include/featured.php';
    } elseif (isset($_GET['page']) && $_GET['page'] === 'watch' && isset($_GET['id'])) {
        require_once __DIR__ . '/user/watch.php';
    } elseif ($chuyen == 'genres') {
        include __DIR__ . '/include/genres.php';
    } elseif ($chuyen == 'search') {
        include __DIR__ . '/include/search.php';
    } elseif ($chuyen == 'register') {
        include __DIR__ . '/auth/register.php';
    } elseif ($chuyen == 'login') {
        include __DIR__ . '/auth/login.php';
    } elseif ($chuyen == 'reset') {
        include __DIR__ . '/auth/reset.php';
    } elseif ($chuyen == 'profile') {
        include __DIR__ . '/user/profile.php';
    } elseif ($chuyen == 'contact') {
        include __DIR__ . '/contact/lienhe.php';
    } elseif ($chuyen == 'policy') {
        include __DIR__ . '/contact/chinh-sach.php';
    } elseif ($chuyen == 'TermsOfUse') {
        include __DIR__ . '/contact/dieu-khoan.php';
    } elseif ($chuyen == 'introduce') {
        include __DIR__ . '/contact/gioithieu.php';
    } else {
        include __DIR__ . '/include/slider.php';
    }

    include __DIR__ . '/include/footer.php';
    ?>
    <script src="./js/app.js"></script>
    <script src="./js/hamburger.js"></script>
</body>
</html>
<?php
ob_end_flush();
?>