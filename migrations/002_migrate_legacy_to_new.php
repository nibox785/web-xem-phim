<?php
require_once __DIR__ . "/../include/db.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

// ======================
// HELPER
// ======================
function table_exists($conn, $table) {
    $res = $conn->query("SHOW TABLES LIKE '$table'");
    return $res && $res->num_rows > 0;
}

function log_msg($msg) {
    echo "[" . date("H:i:s") . "] $msg\n";
}

// ======================
// START MIGRATION
// ======================
$conn->begin_transaction();

try {

    // ======================
    // 1. ROLES
    // ======================
    log_msg("Migrating roles...");
    $roles = ['admin', 'user'];

    foreach ($roles as $role) {
        $stmt = $conn->prepare("INSERT IGNORE INTO roles(name) VALUES (?)");
        $stmt->bind_param("s", $role);
        $stmt->execute();
    }

    // ======================
    // 2. USERS
    // ======================
    if (table_exists($conn, 'users')) {
        log_msg("Migrating users...");

        $res = $conn->query("SELECT id, username, password, email, role FROM users");

        while ($row = $res->fetch_assoc()) {

            // insert user
            $stmt = $conn->prepare("
                INSERT IGNORE INTO users(id, username, password, email)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("isss",
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email']
            );
            $stmt->execute();

            // map role
            $stmtRole = $conn->prepare("SELECT id FROM roles WHERE name=?");
            $stmtRole->bind_param("s", $row['role']);
            $stmtRole->execute();
            $roleRes = $stmtRole->get_result()->fetch_assoc();

            if ($roleRes) {
                $stmtMap = $conn->prepare("
                    INSERT IGNORE INTO user_roles(user_id, role_id)
                    VALUES (?, ?)
                ");
                $stmtMap->bind_param("ii", $row['id'], $roleRes['id']);
                $stmtMap->execute();
            }
        }

    } else {
        log_msg("WARN: users table not found");
    }

    // ======================
    // 3. UNIVERSes -> STUDIOS
    // ======================
    if (table_exists($conn, 'universes')) {
        log_msg("Migrating universes -> studios...");

        $res = $conn->query("SELECT id, name FROM universes");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO studios(id, name)
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $row['id'], $row['name']);
            $stmt->execute();
        }

    } else {
        log_msg("WARN: universes table not found");
    }

    // ======================
    // 4. MOVIES
    // ======================
    if (table_exists($conn, 'movies')) {
        log_msg("Migrating movies...");

        $res = $conn->query("
            SELECT id, title, description, release_year, universe_id
            FROM movies
        ");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO movies(id, title, description, release_year, studio_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issii",
                $row['id'],
                $row['title'],
                $row['description'],
                $row['release_year'],
                $row['universe_id']
            );
            $stmt->execute();
        }
    }

    // ======================
    // 5. GENRES
    // ======================
    if (table_exists($conn, 'genres')) {
        log_msg("Migrating genres...");

        $res = $conn->query("SELECT id, name FROM genres");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO genres(id, name)
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $row['id'], $row['name']);
            $stmt->execute();
        }
    }

    // ======================
    // 6. MOVIE_GENRES
    // ======================
    if (table_exists($conn, 'movie_genres')) {
        log_msg("Migrating movie_genres...");

        $res = $conn->query("SELECT movie_id, genre_id FROM movie_genres");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO movie_genres(movie_id, genre_id)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii",
                $row['movie_id'],
                $row['genre_id']
            );
            $stmt->execute();
        }
    }

    // ======================
    // 7. ACTORS
    // ======================
    if (table_exists($conn, 'actors')) {
        log_msg("Migrating actors...");

        $res = $conn->query("SELECT id, name FROM actors");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO actors(id, name)
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $row['id'], $row['name']);
            $stmt->execute();
        }
    }

    // ======================
    // 8. MOVIE_ACTORS -> MOVIE_CASTS
    // ======================
    if (table_exists($conn, 'movie_actors')) {
        log_msg("Migrating movie_actors -> movie_casts...");

        $res = $conn->query("
            SELECT movie_id, actor_id, role
            FROM movie_actors
        ");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO movie_casts(movie_id, actor_id, role)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis",
                $row['movie_id'],
                $row['actor_id'],
                $row['role']
            );
            $stmt->execute();
        }

    } else {
        log_msg("WARN: movie_actors table not found");
    }

    // ======================
    // 9. COMMENTS
    // ======================
    if (table_exists($conn, 'comments')) {
        log_msg("Migrating comments...");

        $res = $conn->query("
            SELECT id, user_id, movie_id, content
            FROM comments
        ");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO comments(id, user_id, movie_id, content)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiis",
                $row['id'],
                $row['user_id'],
                $row['movie_id'],
                $row['content']
            );
            $stmt->execute();
        }
    }

    // ======================
    // 10. RATINGS
    // ======================
    if (table_exists($conn, 'ratings')) {
        log_msg("Migrating ratings...");

        $res = $conn->query("
            SELECT id, user_id, movie_id, rating
            FROM ratings
        ");

        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT IGNORE INTO ratings(id, user_id, movie_id, rating)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiii",
                $row['id'],
                $row['user_id'],
                $row['movie_id'],
                $row['rating']
            );
            $stmt->execute();
        }
    }

    $conn->commit();
    log_msg("Migration SUCCESS!");

} catch (Exception $e) {
    $conn->rollback();
    log_msg("ERROR: " . $e->getMessage());
}
?>