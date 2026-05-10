<?php

namespace App\Repositories;

use mysqli;

class UserRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Đếm tất cả người dùng
     */
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }

    /**
     * Lấy tất cả người dùng với quyền
     */
    public function getAllWithRoles(): array
    {
        $sql = "SELECT u.id, u.username, u.email, GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') AS roles 
                FROM users u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                GROUP BY u.id, u.username, u.email 
                ORDER BY u.id DESC";

        $result = $this->conn->query($sql);

        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        return $users;
    }

    /**
     * Lấy người dùng theo ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Xóa người dùng
     */
    public function delete(int $id): bool
    {
        $this->conn->begin_transaction();

        try {
            // Xóa comments
            $sql = "DELETE FROM comments WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa ratings
            $sql = "DELETE FROM ratings WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa user_roles
            $sql = "DELETE FROM user_roles WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa user_tokens
            $sql = "DELETE FROM user_tokens WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Xóa người dùng
            $sql = "DELETE FROM users WHERE id = ?";
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
