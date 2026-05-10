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
     * Đếm tất cả phim
     */
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) as total FROM movies";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }

    /**
     * Lấy phim gần đây
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT id, title, release_year FROM movies ORDER BY id DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
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
     * Lấy tất cả phim với thể loại
     */
    public function getAllWithGenres(int $limit, int $offset): array
    {
        $sql = "SELECT m.*, s.name AS studio_name,
                GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
                FROM movies m
                LEFT JOIN studios s ON m.studio_id = s.id
                LEFT JOIN movie_genres mg ON m.id = mg.movie_id
                LEFT JOIN genres g ON mg.genre_id = g.id
                GROUP BY m.id
                ORDER BY m.id DESC
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
     * Lấy phim theo ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM movies WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
        $stmt->close();

        return $movie;
    }

    /**
     * Tạo phim mới
     */
    public function create(array $data): int
    {
        $title = $data['title'] ?? '';
        $release_year = (int)($data['release_year'] ?? 0);
        $description = $data['description'] ?? '';
        $poster_url = $data['poster_url'] ?? null;
        $trailer_url = $data['trailer_url'] ?? null;
        $play_url = $trailer_url;
        $studio_id = (int)($data['studio_id'] ?? 0);
        $studio_id = $studio_id > 0 ? $studio_id : null;

        $slug = $this->generateSlug($title . '-' . $release_year);

        $sql = "INSERT INTO movies (title, slug, type, status, release_year, description, poster_url, trailer_url, play_url, studio_id, featured)
                VALUES (?, ?, 'movie', 'released', ?, ?, ?, ?, ?, ?, 0)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssissssi", $title, $slug, $release_year, $description, $poster_url, $trailer_url, $play_url, $studio_id);
        $stmt->execute();

        $id = $this->conn->insert_id;
        $stmt->close();

        return $id;
    }

    /**
     * Cập nhật phim
     */
    public function update(int $id, array $data): bool
    {
        $title = $data['title'] ?? '';
        $release_year = (int)($data['release_year'] ?? 0);
        $description = $data['description'] ?? '';
        $poster_url = $data['poster_url'] ?? null;
        $trailer_url = $data['trailer_url'] ?? null;
        $play_url = $trailer_url;
        $studio_id = (int)($data['studio_id'] ?? 0);
        $studio_id = $studio_id > 0 ? $studio_id : null;

        $slug = $this->generateSlug($title . '-' . $release_year . '-' . $id);

        $sql = "UPDATE movies SET title = ?, slug = ?, release_year = ?, description = ?, poster_url = ?, trailer_url = ?, play_url = ?, studio_id = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssisssii", $title, $slug, $release_year, $description, $poster_url, $trailer_url, $play_url, $studio_id, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Xóa phim
     */
    public function delete(int $id): bool
    {
        $this->conn->begin_transaction();

        try {
            // Xóa genres
            $sql = "DELETE FROM movie_genres WHERE movie_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa comments
            $sql = "DELETE FROM comments WHERE movie_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa ratings
            $sql = "DELETE FROM ratings WHERE movie_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa phim
            $sql = "DELETE FROM movies WHERE id = ?";
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

    /**
     * Lấy thể loại của phim
     */
    public function getGenres(int $movieId): array
    {
        $sql = "SELECT genre_id FROM movie_genres WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();

        $result = $stmt->get_result();
        $genres = [];
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
        $stmt->close();

        return $genres;
    }

    /**
     * Thêm thể loại cho phim
     */
    public function addGenre(int $movieId, int $genreId): bool
    {
        $sql = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $movieId, $genreId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Xóa tất cả thể loại của phim
     */
    public function removeAllGenres(int $movieId): bool
    {
        $sql = "DELETE FROM movie_genres WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Sinh slug từ title
     */
    private function generateSlug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        return trim($text, '-');
    }
}