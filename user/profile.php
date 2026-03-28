<?php
// Tính toán base_url động
$base_path = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$base_url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path) . '/';

// Ngăn truy cập trực tiếp
if (!defined('INCLUDED_VIA_INDEX')) {
    header('Location: ' . $base_url . 'index.php');
    exit;
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . 'index.php?page=login');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT id, username, email, profile_image, password_hash FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$errors = [];
$success = false;

// Định nghĩa thư mục lưu ảnh
$image_dir = __DIR__ . '/../images/';
if (!is_dir($image_dir)) {
    if (!mkdir($image_dir, 0755, true)) {
        $errors[] = 'Không thể tạo thư mục lưu ảnh';
        error_log('Failed to create directory: ' . $image_dir);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý form thông tin cá nhân
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Kiểm tra username
        if (empty($username)) {
            $errors[] = 'Tên đăng nhập không được để trống';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
        }

        // Kiểm tra nếu username đã tồn tại (trừ username hiện tại)
        if ($username !== $user['username']) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->bind_param("si", $username, $user_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $errors[] = 'Tên đăng nhập đã được sử dụng';
            }
        }

        // Kiểm tra email
        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        // Kiểm tra nếu email đã tồn tại (trừ email hiện tại)
        if ($email !== $user['email']) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $errors[] = 'Email đã được sử dụng bởi tài khoản khác';
            }
        }

        // Kiểm tra nếu người dùng muốn đổi mật khẩu
        if (!empty($current_password)) {
            if (!password_verify($current_password, $user['password_hash'])) {
                $errors[] = 'Mật khẩu hiện tại không chính xác';
            } elseif (empty($new_password)) {
                $errors[] = 'Mật khẩu mới không được để trống';
            } elseif (strlen($new_password) < 6) {
                $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            } elseif ($new_password !== $confirm_password) {
                $errors[] = 'Mật khẩu xác nhận không khớp';
            }
        }

        // Cập nhật thông tin nếu không có lỗi
        if (empty($errors)) {
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password_hash = ? WHERE id = ?");
                $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $username, $email, $user_id);
            }

            if ($stmt->execute()) {
                $success = true;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                // Cập nhật thông tin người dùng
                $stmt = $conn->prepare("SELECT id, username, email, profile_image, password_hash FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            } else {
                $errors[] = 'Không thể cập nhật thông tin: ' . $stmt->error;
            }
        }
    }

    // Xử lý tải lên ảnh đại diện
    if (isset($_POST['upload_image'])) {
        $profile_image = $user['profile_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        // Tải lên từ file
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK && !empty($_POST['image_source']) && $_POST['image_source'] === 'file') {
            $file = $_FILES['profile_image'];
            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = 'Chỉ hỗ trợ file JPEG, PNG hoặc GIF';
            } elseif ($file['size'] > $max_size) {
                $errors[] = 'File ảnh phải nhỏ hơn 2MB';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
                $upload_path = $image_dir . $filename;

                if (!is_writable($image_dir)) {
                    $errors[] = 'Thư mục lưu ảnh không có quyền ghi';
                    error_log('Directory not writable: ' . $image_dir);
                } elseif (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $profile_image = $filename;
                    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                    $stmt->bind_param("si", $profile_image, $user_id);
                    if ($stmt->execute()) {
                        $success = true;
                        $_SESSION['profile_image'] = $profile_image;
                        $user['profile_image'] = $profile_image;
                    } else {
                        $errors[] = 'Không thể cập nhật ảnh đại diện: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = 'Không thể tải lên ảnh: Lỗi di chuyển file';
                    error_log('Failed to move uploaded file to: ' . $upload_path);
                }
            }
        }
        // Tải lên từ URL
        elseif (isset($_POST['image_url']) && !empty($_POST['image_url']) && !empty($_POST['image_source']) && $_POST['image_source'] === 'url') {
            $image_url = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);
            if (!$image_url) {
                $errors[] = 'URL ảnh không hợp lệ';
            } else {
                // Tải ảnh từ URL
                $ch = curl_init($image_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                $response = curl_exec($ch);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $file_size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code !== 200) {
                    $errors[] = 'Không thể tải ảnh từ URL (mã lỗi: ' . $http_code . ')';
                } elseif (!in_array($content_type, $allowed_types)) {
                    $errors[] = 'Ảnh từ URL phải là JPEG, PNG hoặc GIF';
                } elseif ($file_size > $max_size || $file_size <= 0) {
                    $errors[] = 'Ảnh từ URL phải nhỏ hơn 2MB';
                } else {
                    $image_data = substr($response, $header_size);
                    $ext = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (!in_array('image/' . $ext, $allowed_types)) {
                        $ext = ($content_type === 'image/jpeg') ? 'jpg' : (($content_type === 'image/png') ? 'png' : 'gif');
                    }
                    $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
                    $upload_path = $image_dir . $filename;

                    if (!is_writable($image_dir)) {
                        $errors[] = 'Thư mục lưu ảnh không có quyền ghi';
                        error_log('Directory not writable: ' . $image_dir);
                    } elseif (file_put_contents($upload_path, $image_data)) {
                        $profile_image = $filename;
                        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                        $stmt->bind_param("si", $profile_image, $user_id);
                        if ($stmt->execute()) {
                            $success = true;
                            $_SESSION['profile_image'] = $profile_image;
                            $user['profile_image'] = $profile_image;
                        } else {
                            $errors[] = 'Không thể cập nhật ảnh đại diện: ' . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $errors[] = 'Không thể lưu ảnh từ URL';
                        error_log('Failed to save URL image to: ' . $upload_path);
                    }
                }
            }
        } else {
            $errors[] = 'Vui lòng chọn file ảnh hoặc nhập URL hợp lệ';
        }
    }
}
?>

<div class="container">
    <?php if ($success): ?>
        <div class="auth-success-message">
            Thông tin đã được cập nhật thành công!
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="auth-error-message">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="outer-container">
        <div class="profile-page-container">
            <div class="profile-user-image">
                <img src="<?php echo htmlspecialchars($base_url . 'images/' . ($user['profile_image'] ?? 'default.jpg')); ?>" alt="Profile Image">
                <form method="POST" enctype="multipart/form-data" class="profile-image-upload-form">
                    <div class="auth-form-group">
                        <label>
                            <input type="radio" name="image_source" value="file" checked> Tải lên từ máy tính
                        </label>
                        <label>
                            <input type="radio" name="image_source" value="url"> Nhập URL ảnh
                        </label>
                    </div>
                    <div class="auth-form-group">
                        <input type="file" name="profile_image" accept="image/*" id="profile_image_input">
                        <input type="url" name="image_url" placeholder="Nhập URL ảnh (VD: https://example.com/image.jpg)" id="image_url_input" style="display: none;">
                    </div>
                    <input type="hidden" name="upload_image" value="1">
                    <button type="submit" class="profile-btn profile-btn-secondary">Tải lên ảnh</button>
                </form>
                <script>
                    // Chuyển đổi giữa file input và URL input
                    document.querySelectorAll('input[name="image_source"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            document.getElementById('profile_image_input').style.display = this.value === 'file' ? 'block' : 'none';
                            document.getElementById('image_url_input').style.display = this.value === 'url' ? 'block' : 'none';
                        });
                    });
                </script>
            </div>
            
            <form method="POST" class="profile-edit-form">
                <input type="hidden" name="update_profile" value="1">
                
                <h2 class="profile-heading">Thông tin cá nhân</h2>
                
                <table class="profile-data-table">
                    <tr>
                        <td><label for="username">Tên đăng nhập</label></td>
                        <td>
                            <div class="auth-form-group">
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="email">Email</label></td>
                        <td>
                            <div class="auth-form-group">
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <h2 class="profile-heading">Đổi mật khẩu</h2>
                
                <table class="profile-data-table">
                    <tr>
                        <td><label for="current_password">Mật khẩu hiện tại</label></td>
                        <td>
                            <div class="auth-form-group">
                                <input type="password" id="current_password" name="current_password">
                                <small>Nhập mật khẩu hiện tại để đổi mật khẩu mới</small>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="new_password">Mật khẩu mới</label></td>
                        <td>
                            <div class="auth-form-group">
                                <input type="password" id="new_password" name="new_password">
                                <small>Mật khẩu mới phải có ít nhất 6 ký tự</small>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="confirm_password">Xác nhận mật khẩu mới</label></td>
                        <td>
                            <div class="auth-form-group">
                                <input type="password" id="confirm_password" name="confirm_password">
                            </div>
                        </td>
                    </tr>
                </table>
                
                <button type="submit" class="profile-btn profile-btn-primary">Cập nhật thông tin</button>
            </form>
        </div>
    </div>
</div>