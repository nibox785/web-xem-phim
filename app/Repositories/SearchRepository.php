<?php

namespace App\Repositories;

use mysqli;

class SearchRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Tìm kiếm phim
     */
    public function search(string $query, int $limit, int $offset): array
    {
        $searchParam = "%{$query}%";
        $sql = "SELECT id, title, poster_url, release_year, rating 
                FROM movies 
                WHERE title LIKE ? OR original_title LIKE ? OR description LIKE ? 
                ORDER BY release_year DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        $stmt->close();

        return $movies;
    }

    /**
     * Đếm kết quả tìm kiếm
     */
    public function countSearch(string $query): int
    {
        $searchParam = "%{$query}%";
        $sql = "SELECT COUNT(*) as total 
                FROM movies 
                WHERE title LIKE ? OR original_title LIKE ? OR description LIKE ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }
}
