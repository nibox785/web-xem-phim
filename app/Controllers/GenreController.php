<?php

namespace App\Controllers;

use App\Services\GenreService;

class GenreController
{
    private GenreService $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
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

        $data = $this->genreService->getMoviesByGenre(
            genreId: $genreId,
            page: $page,
            perPage: 15
        );

        return $this->view('movies/by_genre', $data);
    }

    protected function view($view, $data = [])
    {
        extract($data);
        include APP_PATH . "/Views/{$view}.php";
    }
}
