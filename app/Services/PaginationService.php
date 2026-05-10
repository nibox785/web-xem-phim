<?php

namespace App\Services;

class PaginationService
{
    /**
     * Tính toán thông tin phân trang
     */
    public function calculate(int $total, int $page, int $perPage): array
    {
        $totalPages = ceil($total / $perPage);
        $page = max(1, min($page, $totalPages)); // Clamp page

        return [
            'page' => $page,
            'total' => $total,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'offset' => ($page - 1) * $perPage,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
            'prev_page' => $page - 1,
            'next_page' => $page + 1
        ];
    }
}
