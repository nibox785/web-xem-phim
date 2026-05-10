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