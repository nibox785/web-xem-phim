<?php

namespace App\Services;

use App\Repositories\GenreRepository;

class GenreService
{
    private GenreRepository $repository;
    private PaginationService $paginationService;

    public function __construct(GenreRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
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
