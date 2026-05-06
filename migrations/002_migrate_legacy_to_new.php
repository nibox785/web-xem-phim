<?php
// migrations/002_migrate_legacy_to_new.php
// Chạy: php migrations/002_migrate_legacy_to_new.php

require_once __DIR__ . "/../include/db.php";

function slugify($text) {
    $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-a-z0-9]+~', '', strtolower($text));
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    if (empty($text)) return 'n-a';
    return $text;
}

function ensure_role($conn, $name, $description = '') {
    $stmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($id);
    if ($stmt->fetch()) {
        $stmt->close();
        return $id;
    }
    $stmt->close();

    $ins = $conn->prepare("INSERT INTO roles (`name`, `description`) VALUES (?, ?)");
    $ins->bind_param('ss', $name, $description);
    $ins->execute();
    $newId = $conn->insert_id;
    $ins->close();
    return $newId;
}

// Hash token (sha256)
function token_hash_value($token) {
    return hash('sha256', $token);
}

echo "Start migration: legacy -> new schema\n";

// Begin transaction -- safer to run in smaller steps in production
$conn->begin_transaction();
try {
    // 1) Ensure roles
    $role_user = ensure_role($conn, 'user', 'Regular user');
    $role_admin = ensure_role($conn, 'admin', 'Administrator');
    $role_super = ensure_role($conn, 'super_admin', 'Super administrator');
    $role_mod = ensure_role($conn, 'moderator', 'Moderator');
    echo "Roles ensured: user=$role_user, admin=$role_admin, super_admin=$role_super, moderator=$role_mod\n";

    // 2) Migrate users (preserve original user IDs)
    $res = $conn->query("SELECT id, username, email, password_hash, created_at, updated_at, profile_image, role FROM users");
    if (!$res) throw new Exception($conn->error);

    $insertUserStmt = $conn->prepare("INSERT INTO users (id, username, email, password_hash, profile_image, created_at, updated_at, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE username=VALUES(username)");

    while ($row = $res->fetch_assoc()) {
        $uid = (int)$row['id'];
        $username = $row['username'];
        $email = $row['email'];
        $password_hash = $row['password_hash'];
        $created_at = $row['created_at'];
        $updated_at = $row['updated_at'];
        $profile_image = $row['profile_image'] ?: 'default.jpg';
        $is_active = 1;

        $insertUserStmt->bind_param('issssssi', $uid, $username, $email, $password_hash, $profile_image, $created_at, $updated_at, $is_active);
        if (!$insertUserStmt->execute()) throw new Exception('Insert user failed: ' . $insertUserStmt->error);

        // map role from old users.role -> user_roles
        $oldRole = $row['role'] ?: 'user';
        switch ($oldRole) {
            case 'admin': $mappedRoleId = $role_admin; break;
            case 'moderator': $mappedRoleId = $role_mod; break;
            default: $mappedRoleId = $role_user; break;
        }
        $conn->query("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES ($uid, $mappedRoleId)");
    }
    $insertUserStmt->close();
    echo "Users migrated (original users table preserved ids).\n";

    // 3) Migrate admins -> users + user_roles
    $resAdmin = $conn->query("SELECT id, username, password_hash, role, created_at, email FROM admins");
    if (!$resAdmin) throw new Exception($conn->error);

    $selectUserByEmail = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $selectUserByEmail->bind_param('s', $emailToCheck);

    $insertAdminAsUser = $conn->prepare("INSERT INTO users (username, email, password_hash, profile_image, created_at, updated_at, is_active) VALUES (?, ?, ?, 'default.jpg', ?, ?, 1)");
    $insertAdminAsUser->bind_param('sssss', $a_username, $a_email, $a_password_hash, $a_created_at, $a_updated_at);

    while ($a = $resAdmin->fetch_assoc()) {
        $a_username = $a['username'];
        $a_email = $a['email'];
        $a_password_hash = $a['password_hash'];
        $a_created_at = $a['created_at'];
        $a_updated_at = $a['created_at'];

        // check existing user by email
        $emailToCheck = $a_email;
        $selectUserByEmail->execute();
        $selectUserByEmail->bind_result($existingUserId);
        if ($selectUserByEmail->fetch()) {
            $selectUserByEmail->free_result();
            $userIdForAdmin = $existingUserId;
        } else {
            // insert new user (admin) — will get new auto-increment ID
            $selectUserByEmail->free_result();
            if (!$insertAdminAsUser->execute()) throw new Exception('Insert admin as user failed: ' . $insertAdminAsUser->error);
            $userIdForAdmin = $conn->insert_id;
        }

        // map admin role
        $adminRole = $a['role'];
        $mapRole = ($adminRole === 'super_admin') ? $role_super : $role_mod;
        $conn->query("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES ($userIdForAdmin, $mapRole)");
    }
    $insertAdminAsUser->close();
    $selectUserByEmail->close();
    echo "Admins merged into users and roles set.\n";

    // 4) Migrate universes -> studios
    $resUniv = $conn->query("SELECT id, name FROM universes");
    $univToStudio = [];
    if ($resUniv) {
        $insStudio = $conn->prepare("INSERT INTO studios (name) VALUES (?)");
        $insStudio->bind_param('s', $studio_name);
        while ($u = $resUniv->fetch_assoc()) {
            $studio_name = $u['name'];
            // insert and map
            // check existing
            $q = $conn->prepare("SELECT id FROM studios WHERE name = ? LIMIT 1");
            $q->bind_param('s', $studio_name);
            $q->execute();
            $q->bind_result($existingStudioId);
            if ($q->fetch()) {
                $q->close();
                $univToStudio[(int)$u['id']] = $existingStudioId;
            } else {
                $q->close();
                if (!$insStudio->execute()) throw new Exception('Insert studio failed: ' . $insStudio->error);
                $univToStudio[(int)$u['id']] = $conn->insert_id;
            }
        }
        $insStudio->close();
    }
    echo "Universes -> studios mapped.\n";

    // 5) Migrate genres
    $resGenres = $conn->query("SELECT id, name FROM genres");
    $genreMap = [];
    if ($resGenres) {
        $insGenre = $conn->prepare("INSERT INTO genres (id, name, slug) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
        $insGenre->bind_param('iss', $gid, $gname, $gslug);
        while ($g = $resGenres->fetch_assoc()) {
            $gid = (int)$g['id'];
            $gname = $g['name'];
            $gslug = slugify($gname);
            if (!$insGenre->execute()) throw new Exception('Insert genre failed: ' . $insGenre->error);
            $genreMap[$gid] = $gid; // preserve id
        }
        $insGenre->close();
    }
    echo "Genres migrated.\n";

    // 6) Migrate actors
    $resActors = $conn->query("SELECT id, name, birth_date FROM actors");
    if ($resActors) {
        $insActor = $conn->prepare("INSERT INTO actors (id, name, birth_date) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
        $insActor->bind_param('iss', $aid, $aname, $abirth);
        while ($a = $resActors->fetch_assoc()) {
            $aid = (int)$a['id'];
            $aname = $a['name'];
            $abirth = $a['birth_date'];
            if (!$insActor->execute()) throw new Exception('Insert actor failed: ' . $insActor->error);
        }
        $insActor->close();
    }
    echo "Actors migrated.\n";

    // 7) Migrate movies (preserve ids)
    $resMovies = $conn->query("SELECT id, title, universe_id, release_year, thumbnail, video_url, featured, description FROM movies");
    if ($resMovies) {
        $insMovie = $conn->prepare("INSERT INTO movies (id, title, slug, type, status, release_year, duration_minutes, description, poster_url, banner_url, trailer_url, play_url, studio_id, featured) VALUES (?, ?, ?, 'movie', 'released', ?, NULL, ?, ?, NULL, ?, NULL, ?, ?)");
        $insMovie->bind_param('ississsisi', $mid, $mtitle, $mslug, $mrelease, $mdesc, $mposter, $mtrailer, $mplay, $mstudio, $mfeatured);
        while ($m = $resMovies->fetch_assoc()) {
            $mid = (int)$m['id'];
            $mtitle = $m['title'];
            $mslug = slugify($mtitle);
            $mrelease = $m['release_year'] ? (int)$m['release_year'] : null;
            $mdesc = $m['description'];
            $mposter = $m['thumbnail'];
            $mtrailer = $m['video_url'];
            $mplay = null;
            $mstudio = isset($univToStudio[(int)$m['universe_id']]) ? $univToStudio[(int)$m['universe_id']] : null;
            $mfeatured = (int)$m['featured'];
            if (!$insMovie->execute()) throw new Exception('Insert movie failed: ' . $insMovie->error . ' SQL: ' . json_encode([$mid,$mtitle]));
        }
        $insMovie->close();
    }
    echo "Movies migrated.\n";

    // 8) Migrate movie_genres
    $resMG = $conn->query("SELECT movie_id, genre_id FROM movie_genres");
    if ($resMG) {
        $insMG = $conn->prepare("INSERT IGNORE INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
        $insMG->bind_param('ii', $mg_movie, $mg_genre);
        while ($r = $resMG->fetch_assoc()) {
            $mg_movie = (int)$r['movie_id'];
            $mg_genre = (int)$r['genre_id'];
            $insMG->execute();
        }
        $insMG->close();
    }
    echo "Movie genres migrated.\n";

    // 9) Migrate movie_actors -> movie_casts
    $resMA = $conn->query("SELECT movie_id, actor_id, role FROM movie_actors");
    if ($resMA) {
        $insCast = $conn->prepare("INSERT IGNORE INTO movie_casts (movie_id, actor_id, character_name, credit_order) VALUES (?, ?, ?, 0)");
        $insCast->bind_param('iis', $mc_movie, $mc_actor, $mc_char);
        while ($r = $resMA->fetch_assoc()) {
            $mc_movie = (int)$r['movie_id'];
            $mc_actor = (int)$r['actor_id'];
            $mc_char = $r['role'];
            $insCast->execute();
        }
        $insCast->close();
    }
    echo "Movie casts migrated.\n";

    // 10) Migrate comments
    $resComments = $conn->query("SELECT id, user_id, movie_id, comment_text, created_at, updated_at, is_approved FROM comments");
    if ($resComments) {
        $insCom = $conn->prepare("INSERT INTO comments (id, user_id, movie_id, comment_text, is_approved, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE comment_text=VALUES(comment_text)");
        $insCom->bind_param('iiisis s', $cid, $cuid, $cmid, $ctext, $capproved, $ccreated, $cupdated);
        // Above binding has a space due to multi-type; to avoid issues we'll use another approach instead below.
        $insCom->close();

        // Use simple escaped queries for comments to preserve timestamps
        while ($c = $resComments->fetch_assoc()) {
            $cid = (int)$c['id'];
            $cuid = $c['user_id'] !== null ? (int)$c['user_id'] : 'NULL';
            $cmid = $c['movie_id'] !== null ? (int)$c['movie_id'] : 'NULL';
            $ctext = $conn->real_escape_string($c['comment_text']);
            $capproved = (int)$c['is_approved'];
            $ccreated = $c['created_at'];
            $cupdated = $c['updated_at'];
            $sql = "INSERT INTO comments (id, user_id, movie_id, comment_text, is_approved, created_at, updated_at) VALUES ($cid, " . ($cuid === 'NULL' ? 'NULL' : $cuid) . ", " . ($cmid === 'NULL' ? 'NULL' : $cmid) . ", '$ctext', $capproved, '$ccreated', '$cupdated') ON DUPLICATE KEY UPDATE comment_text=VALUES(comment_text)";
            if (!$conn->query($sql)) throw new Exception('Insert comment failed: ' . $conn->error . ' SQL: ' . $sql);
        }
    }
    echo "Comments migrated.\n";

    // 11) Migrate ratings (scale 1-5 -> 1-10 by *2)
    $resRatings = $conn->query("SELECT id, user_id, movie_id, rating, created_at FROM ratings");
    if ($resRatings) {
        $insRating = $conn->prepare("INSERT INTO ratings (id, user_id, movie_id, score, created_at) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score=VALUES(score)");
        $insRating->bind_param('iiiis', $rid, $ruid, $rmid, $rscore, $rcreated);
        while ($r = $resRatings->fetch_assoc()) {
            $rid = (int)$r['id'];
            $ruid = $r['user_id'] !== null ? (int)$r['user_id'] : null;
            $rmid = $r['movie_id'] !== null ? (int)$r['movie_id'] : null;
            $rscore = max(1, min(10, ((int)$r['rating']) * 2));
            $rcreated = $r['created_at'];
            if (!$insRating->execute()) throw new Exception('Insert rating failed: ' . $insRating->error);
        }
        $insRating->close();
    }
    echo "Ratings migrated.\n";

    // 12) Migrate user_tokens (hash tokens)
    $resUT = $conn->query("SELECT id, user_id, token, expires_at, created_at FROM user_tokens");
    if ($resUT) {
        $insUT = $conn->prepare("INSERT INTO user_tokens (id, user_id, token_hash, device_info, ip_address, expires_at, created_at) VALUES (?, ?, ?, NULL, NULL, ?, ?) ON DUPLICATE KEY UPDATE token_hash=VALUES(token_hash)");
        $insUT->bind_param('iisss', $utid, $utuid, $uttokenhash, $utexpires, $utcreated);
        while ($u = $resUT->fetch_assoc()) {
            $utid = (int)$u['id'];
            $utuid = (int)$u['user_id'];
            $uttokenhash = token_hash_value($u['token']);
            $utexpires = $u['expires_at'];
            $utcreated = $u['created_at'];
            if (!$insUT->execute()) throw new Exception('Insert user_token failed: ' . $insUT->error . ' token id: ' . $utid);
        }
        $insUT->close();
    }
    echo "User tokens migrated (hashed).\n";

    // Commit
    $conn->commit();
    echo "Migration committed successfully.\n";
} catch (Exception $e) {
    $conn->rollback();
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done.\n";

?>
