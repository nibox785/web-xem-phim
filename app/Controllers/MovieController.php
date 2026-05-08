<?php

namespace App\Controllers;

use App\Services\MovieService;

class MovieController
{
    private MovieService $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Hiển thị danh sách phim điện ảnh
     */
    public function listMovies()
    {
        $page = $_GET['p'] ?? 1;
        
        $data = $this->movieService->getMoviesByType(
            type: 'movie',
            status: 'released',
            page: $page,
            perPage: 15
        );

        return $this->view('movies/index', $data);
    }

    /**
     * Hiển thị phim theo thể loại
     */
    public function showByGenre()
    {
        $genreId = $_GET['genre_id'] ?? 0;
        $page = $_GET['p'] ?? 1;

        if (!$genreId) {
            return $this->view('errors/404', ['message' => 'Thể loại không tồn tại']);
        }

        $data = $this->movieService->getMoviesByGenre(
            genreId: $genreId,
            page: $page,
            perPage: 15
        );

        return $this->view('movies/by_genre', $data);
    }

    /**
     * Tìm kiếm phim
     */
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $page = $_GET['p'] ?? 1;

        if (strlen($query) < 2) {
            return $this->view('errors/400', ['message' => 'Vui lòng nhập từ khóa hợp lệ']);
        }

        $data = $this->movieService->searchMovies(
            query: $query,
            page: $page,
            perPage: 15
        );

        return $this->view('movies/search_results', $data);
    }

    /**
     * Hiển thị phim nổi bật
     */
    public function listFeatured()
    {
        $page = $_GET['p'] ?? 1;

        $data = $this->movieService->getFeaturedMovies(
            page: $page,
            perPage: 15
        );

        return $this->view('movies/featured', $data);
    }

    protected function view($view, $data = [])
    {
        extract($data);
        include APP_PATH . "/Views/{$view}.php";
    }
}