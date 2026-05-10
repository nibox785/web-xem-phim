<?php

namespace App\Services;

use App\Repositories\SearchRepository;

class SearchService
{
    private SearchRepository $repository;
    private PaginationService $paginationService;

    public function __construct(SearchRepository $repository, PaginationService $paginationService)
    {
        $this->repository = $repository;
        $this->paginationService = $paginationService;
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
