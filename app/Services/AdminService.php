<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use App\Repositories\UserRepository;
use App\Repositories\GenreRepository;

class AdminService
{
    private MovieRepository $movieRepository;
    private UserRepository $userRepository;
    private GenreRepository $genreRepository;
    private PaginationService $paginationService;

    public function __construct(
        MovieRepository $movieRepository,
        UserRepository $userRepository,
        GenreRepository $genreRepository,
        PaginationService $paginationService
    ) {
        $this->movieRepository = $movieRepository;
        $this->userRepository = $userRepository;
        $this->genreRepository = $genreRepository;
        $this->paginationService = $paginationService;
    }

    /**
     * Lấy thống kê dashboard
     */
    public function getDashboardStats(): array
    {
        return [
            'total_movies' => $this->movieRepository->countAll(),
            'total_users' => $this->userRepository->countAll(),
            'total_genres' => $this->genreRepository->countAll(),
            'recent_movies' => $this->movieRepository->getRecent(5)
        ];
    }

    /**
     * Lấy danh sách phim với phân trang
     */
    public function listMovies(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $movies = $this->movieRepository->getAllWithGenres($perPage, $offset);
        $total = $this->movieRepository->countAll();
        $pagination = $this->paginationService->calculate($total, $page, $perPage);

        return [
            'movies' => $movies,
            'pagination' => $pagination,
            'total' => $total
        ];
    }

    /**
     * Thêm phim mới
     */
    public function addMovie(array $data): array
    {
        $errors = [];

        // Validate
        if (empty($data['title'])) {
            $errors[] = "Tên phim không được để trống";
        }

        if (empty($data['release_year']) || $data['release_year'] < 1888 || $data['release_year'] > date('Y') + 5) {
            $errors[] = "Năm phát hành không hợp lệ";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Insert
        $movieId = $this->movieRepository->create($data);

        if (!$movieId) {
            return ['success' => false, 'errors' => ['Không thể thêm phim']];
        }

        // Add genres
        if (!empty($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genreId) {
                $this->movieRepository->addGenre($movieId, (int)$genreId);
            }
        }

        return ['success' => true, 'movie_id' => $movieId];
    }

    /**
     * Lấy thông tin phim để sửa
     */
    public function getMovieForEdit(int $movieId): ?array
    {
        return $this->movieRepository->getById($movieId);
    }

    /**
     * Cập nhật phim
     */
    public function updateMovie(int $movieId, array $data): array
    {
        $errors = [];

        // Validate
        if (empty($data['title'])) {
            $errors[] = "Tên phim không được để trống";
        }

        if (empty($data['release_year']) || $data['release_year'] < 1888 || $data['release_year'] > date('Y') + 5) {
            $errors[] = "Năm phát hành không hợp lệ";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update movie
        $result = $this->movieRepository->update($movieId, $data);

        if (!$result) {
            return ['success' => false, 'errors' => ['Không thể cập nhật phim']];
        }

        // Update genres
        $this->movieRepository->removeAllGenres($movieId);
        if (!empty($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genreId) {
                $this->movieRepository->addGenre($movieId, (int)$genreId);
            }
        }

        return ['success' => true, 'movie_id' => $movieId];
    }

    /**
     * Xóa phim
     */
    public function deleteMovie(int $movieId): array
    {
        try {
            $result = $this->movieRepository->delete($movieId);
            if ($result) {
                return ['success' => true, 'message' => 'Phim đã được xóa thành công'];
            } else {
                return ['success' => false, 'error' => 'Không thể xóa phim'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách người dùng
     */
    public function listUsers(): array
    {
        return $this->userRepository->getAllWithRoles();
    }

    /**
     * Xóa người dùng
     */
    public function deleteUser(int $userId): array
    {
        try {
            $result = $this->userRepository->delete($userId);
            if ($result) {
                return ['success' => true, 'message' => 'Người dùng đã được xóa'];
            } else {
                return ['success' => false, 'error' => 'Không thể xóa người dùng'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách thể loại
     */
    public function listGenres(): array
    {
        return $this->genreRepository->getAll();
    }

    /**
     * Thêm thể loại
     */
    public function addGenre(string $name): array
    {
        if (empty($name)) {
            return ['success' => false, 'error' => 'Tên thể loại không được để trống'];
        }

        $genreId = $this->genreRepository->create($name);
        if ($genreId) {
            return ['success' => true, 'genre_id' => $genreId];
        } else {
            return ['success' => false, 'error' => 'Không thể thêm thể loại'];
        }
    }

    /**
     * Xóa thể loại
     */
    public function deleteGenre(int $genreId): array
    {
        try {
            $result = $this->genreRepository->delete($genreId);
            if ($result) {
                return ['success' => true, 'message' => 'Thể loại đã được xóa'];
            } else {
                return ['success' => false, 'error' => 'Không thể xóa thể loại'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy tất cả thể loại (cho select)
     */
    public function getAllGenresForSelect(): array
    {
        return $this->genreRepository->getAll();
    }

    /**
     * Lấy thể loại của phim
     */
    public function getMovieGenres(int $movieId): array
    {
        return $this->movieRepository->getGenres($movieId);
    }
}
