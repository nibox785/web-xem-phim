<?php

namespace App\Repositories;

use mysqli;

class MovieRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Lấy phim theo loại
     */
    public function getByType(string $type, string $status, int $limit, int $offset): array
    {
        $sql = "SELECT id, title, poster_url, release_year, rating 
                FROM movies 
                WHERE type = ? AND status = ? 
                ORDER BY release_year DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $type, $status, $limit, $offset);
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
     * Đếm phim theo loại
     */
    public function countByType(string $type, string $status): int
    {
        $sql = "SELECT COUNT(*) as total FROM movies WHERE type = ? AND status = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $type, $status);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }

    /**
     * Lấy phim theo thể loại
     */
    public function getByGenre(int $genreId, int $limit, int $offset): array
    {
        $sql = "SELECT DISTINCT m.id, m.title, m.poster_url, m.release_year, m.rating
                FROM movies m 
                INNER JOIN movie_genres mg ON m.id = mg.movie_id 
                WHERE mg.genre_id = ? 
                ORDER BY m.release_year DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $genreId, $limit, $offset);
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
     * Đếm phim theo thể loại
     */
    public function countByGenre(int $genreId): int
    {
        $sql = "SELECT COUNT(DISTINCT m.id) as total 
                FROM movies m 
                INNER JOIN movie_genres mg ON m.id = mg.movie_id 
                WHERE mg.genre_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $genreId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
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

    /**
     * Lấy phim nổi bật
     */
    public function getFeatured(int $limit, int $offset): array
    {
        $sql = "SELECT id, title, poster_url, release_year, rating 
                FROM movies 
                WHERE featured = 1 
                ORDER BY release_year DESC 
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
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
     * Đếm phim nổi bật
     */
    public function countFeatured(): int
    {
        $sql = "SELECT COUNT(*) as total FROM movies WHERE featured = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }

    /**
     * Lấy thể loại theo ID
     */
    public function getGenre(int $genreId): ?array
    {
        $sql = "SELECT id, name, slug FROM genres WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $genreId);
        $stmt->execute();

        $result = $stmt->get_result();
        $genre = $result->fetch_assoc();
        $stmt->close();

        return $genre;
    }
}