<?php

namespace App\Services;

use App\Repositories\MovieRepository;

class MovieService
{
    private MovieRepository $repository;
    private PaginationService $paginationService;

    public function __construct(MovieRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
    }

    /**
     * Lấy phim theo loại
     */
    public function getMoviesByType(string $type, string $status, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        // Lấy dữ liệu
        $movies = $this->repository->getByType($type, $status, $perPage, $offset);
        $total = $this->repository->countByType($type, $status);

        // Tính toán phân trang
        $pagination = $this->paginationService->calculate($total, $page, $perPage);

        return [
            'movies' => $this->formatMovies($movies),
            'pagination' => $pagination,
            'page' => $page,
            'total' => $total
        ];
    }

    /**
     * Lấy phim theo thể loại
     */
    public function getMoviesByGenre(int $genreId, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        // Kiểm tra thể loại tồn tại
        $genre = $this->repository->getGenre($genreId);
        if (!$genre) {
            throw new \Exception('Thể loại không tồn tại');
        }

        // Lấy dữ liệu
        $movies = $this->repository->getByGenre($genreId, $perPage, $offset);
        $total = $this->repository->countByGenre($genreId);

        // Tính toán phân trang
        $pagination = $this->paginationService->calculate($total, $page, $perPage);

        return [
            'movies' => $this->formatMovies($movies),
            'genre' => $genre,
            'pagination' => $pagination,
            'page' => $page,
            'total' => $total
        ];
    }

    /**
     * Tìm kiếm phim
     */
    public function searchMovies(string $query, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        // Lấy dữ liệu
        $movies = $this->repository->search($query, $perPage, $offset);
        $total = $this->repository->countSearch($query);

        // Tính toán phân trang
        $pagination = $this->paginationService->calculate($total, $page, $perPage);

        return [
            'movies' => $this->formatMovies($movies),
            'query' => $query,
            'pagination' => $pagination,
            'page' => $page,
            'total' => $total
        ];
    }

    /**
     * Lấy phim nổi bật
     */
    public function getFeaturedMovies(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        // Lấy dữ liệu
        $movies = $this->repository->getFeatured($perPage, $offset);
        $total = $this->repository->countFeatured();

        // Tính toán phân trang
        $pagination = $this->paginationService->calculate($total, $page, $perPage);

        return [
            'movies' => $this->formatMovies($movies),
            'pagination' => $pagination,
            'page' => $page,
            'total' => $total
        ];
    }

    /**
     * Format danh sách phim để hiển thị
     */
    private function formatMovies($movies): array
    {
        return array_map(fn($movie) => [
            'id' => $movie['id'],
            'title' => htmlspecialchars($movie['title']),
            'poster_url' => $movie['poster_url'] ?: 'img/default.jpg',
            'release_year' => $movie['release_year'],
            'rating' => $movie['rating'] ?? 0,
            'watch_link' => "index.php?page=watch&id=" . $movie['id']
        ], $movies);
    }
}