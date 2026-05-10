<?php

namespace App\Controllers;

use App\Services\AdminService;

class AdminController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $stats = $this->adminService->getDashboardStats();
        $stats['base_url'] = $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/';
        return $this->view('admin/dashboard', $stats);
    }

    /**
     * Danh sách phim
     */
    public function movies()
    {
        $page = $_GET['p'] ?? 1;
        $page = is_numeric($page) ? (int)$page : 1;

        $data = $this->adminService->listMovies($page);
        $data['base_url'] = $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/';
        $data['csrf_token'] = $this->generateCsrfToken();

        return $this->view('admin/movies/index', $data);
    }

    /**
     * Form thêm phim
     */
    public function addMovieForm()
    {
        $genres = $this->adminService->getAllGenresForSelect();
        $csrf_token = $this->generateCsrfToken();

        return $this->view('admin/movies/add', [
            'genres' => $genres,
            'csrf_token' => $csrf_token,
            'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
        ]);
    }

    /**
     * Xử lý thêm phim
     */
    public function addMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=admin_movies');
            exit;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->view('admin/movies/add', [
                'errors' => ['CSRF token không hợp lệ'],
                'genres' => $this->adminService->getAllGenresForSelect(),
                'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
            ]);
        }

        $result = $this->adminService->addMovie($_POST);

        if ($result['success']) {
            $_SESSION['success_message'] = 'Phim đã được thêm thành công!';
            header('Location: ?page=admin&action=admin_movies');
            exit;
        } else {
            return $this->view('admin/movies/add', [
                'errors' => $result['errors'] ?? [],
                'genres' => $this->adminService->getAllGenresForSelect(),
                'old_data' => $_POST,
                'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
            ]);
        }
    }

    /**
     * Form sửa phim
     */
    public function editMovieForm()
    {
        $movieId = $_GET['id'] ?? 0;
        $movieId = (int)$movieId;

        if ($movieId <= 0) {
            header('Location: ?page=admin&action=admin_movies');
            exit;
        }

        $movie = $this->adminService->getMovieForEdit($movieId);
        if (!$movie) {
            header('Location: ?page=admin&action=admin_movies');
            exit;
        }

        $genres = $this->adminService->getAllGenresForSelect();
        $selected_genres = $this->adminService->getMovieGenres($movieId);
        $selected_genre_ids = array_map(fn($g) => $g['genre_id'], $selected_genres);

        return $this->view('admin/movies/edit', [
            'movie' => $movie,
            'genres' => $genres,
            'selected_genres' => $selected_genre_ids,
            'csrf_token' => $this->generateCsrfToken(),
            'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
        ]);
    }

    /**
     * Xử lý sửa phim
     */
    public function editMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=admin_movies');
            exit;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->view('admin/movies/edit', [
                'errors' => ['CSRF token không hợp lệ'],
                'movie' => ['id' => $_POST['id'] ?? 0],
                'genres' => $this->adminService->getAllGenresForSelect(),
                'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
            ]);
        }

        $movieId = (int)($_POST['id'] ?? 0);
        $result = $this->adminService->updateMovie($movieId, $_POST);

        if ($result['success']) {
            $_SESSION['success_message'] = 'Phim đã được cập nhật thành công!';
            header('Location: ?page=admin&action=admin_movies');
            exit;
        } else {
            return $this->view('admin/movies/edit', [
                'errors' => $result['errors'] ?? [],
                'movie' => $this->adminService->getMovieForEdit($movieId),
                'genres' => $this->adminService->getAllGenresForSelect(),
                'old_data' => $_POST,
                'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
            ]);
        }
    }

    /**
     * Xử lý xóa phim
     */
    public function deleteMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=admin_movies');
            exit;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['error_message'] = 'CSRF token không hợp lệ';
            header('Location: ?page=admin&action=admin_movies');
            exit;
        }

        $movieId = (int)($_POST['delete_movie_id'] ?? 0);
        $result = $this->adminService->deleteMovie($movieId);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['error'] ?? 'Lỗi không xác định';
        }

        header('Location: ?page=admin&action=admin_movies');
        exit;
    }

    /**
     * Danh sách người dùng
     */
    public function users()
    {
        $users = $this->adminService->listUsers();
        $csrf_token = $this->generateCsrfToken();

        return $this->view('admin/users/index', [
            'users' => $users,
            'csrf_token' => $csrf_token,
            'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
        ]);
    }

    /**
     * Xử lý xóa người dùng
     */
    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=admin_users');
            exit;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['error_message'] = 'CSRF token không hợp lệ';
            header('Location: ?page=admin&action=admin_users');
            exit;
        }

        $userId = (int)($_POST['delete_user_id'] ?? 0);

        // Không cho xóa chính mình
        if ($userId === (int)($_SESSION['user_id'] ?? 0)) {
            $_SESSION['error_message'] = 'Không thể xóa tài khoản đang đăng nhập';
            header('Location: ?page=admin&action=admin_users');
            exit;
        }

        $result = $this->adminService->deleteUser($userId);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['error'] ?? 'Lỗi không xác định';
        }

        header('Location: ?page=admin&action=admin_users');
        exit;
    }

    /**
     * Danh sách thể loại
     */
    public function genres()
    {
        $genres = $this->adminService->listGenres();
        $csrf_token = $this->generateCsrfToken();

        return $this->view('admin/genres/index', [
            'genres' => $genres,
            'csrf_token' => $csrf_token,
            'base_url' => $_SERVER['HTTP_HOST'] === 'localhost' ? '' : '/'
        ]);
    }

    /**
     * Xử lý thêm thể loại
     */
    public function addGenre()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=admin_genres');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $result = $this->adminService->addGenre($name);

        if ($result['success']) {
            $_SESSION['success_message'] = 'Thể loại đã được thêm thành công!';
        } else {
            $_SESSION['error_message'] = $result['error'] ?? 'Lỗi không xác định';
        }

        header('Location: ?page=admin&action=admin_genres');
        exit;
    }

    /**
     * Xử lý xóa thể loại
     */
    public function deleteGenre()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=admin_genres');
            exit;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['error_message'] = 'CSRF token không hợp lệ';
            header('Location: ?page=admin&action=admin_genres');
            exit;
        }

        $genreId = (int)($_POST['delete_genre_id'] ?? 0);
        $result = $this->adminService->deleteGenre($genreId);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['error'] ?? 'Lỗi không xác định';
        }

        header('Location: ?page=admin&action=admin_genres');
        exit;
    }

    /**
     * Load view
     */
    protected function view($view, $data = [])
    {
        extract($data);
        $base_url = $_SERVER['HTTP_HOST'] === 'localhost' ? '/' : '/';
        include APP_PATH . "/Views/{$view}.php";
    }

    /**
     * Generate CSRF token
     */
    private function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    private function verifyCsrfToken(?string $token): bool
    {
        return isset($_SESSION['csrf_token']) && 
               is_string($token) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
}
