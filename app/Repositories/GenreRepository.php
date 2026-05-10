<?php

namespace App\Repositories;

use mysqli;

class GenreRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
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

    /**
     * Đếm tất cả thể loại
     */
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) as total FROM genres";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }

    /**
     * Lấy tất cả thể loại
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM genres ORDER BY name";
        $result = $this->conn->query($sql);

        $genres = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $genres[] = $row;
            }
        }

        return $genres;
    }

    /**
     * Tạo thể loại mới
     */
    public function create(string $name): int
    {
        $sql = "INSERT INTO genres (name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();

        $id = $this->conn->insert_id;
        $stmt->close();

        return $id;
    }

    /**
     * Xóa thể loại
     */
    public function delete(int $id): bool
    {
        $this->conn->begin_transaction();

        try {
            // Xóa movie_genres associations
            $sql = "DELETE FROM movie_genres WHERE genre_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa thể loại
            $sql = "DELETE FROM genres WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return $result;
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
}
