<?php

namespace App\Controllers;

use App\Services\SearchService;

class SearchController
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
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

        $data = $this->searchService->searchMovies(
            query: $query,
            page: $page,
            perPage: 15
        );

        return $this->view('movies/search_results', $data);
    }

    protected function view($view, $data = [])
    {
        extract($data);
        include APP_PATH . "/Views/{$view}.php";
    }
}
