<?php

// Fetch movies by genre
function getMoviesByGenre($conn, $genreId, $limit = 15, $offset = 0) {
    if (!($conn instanceof mysqli)) {
        throw new InvalidArgumentException('Invalid mysqli connection passed to getMoviesByGenre');
    }

    $sql = "SELECT m.* 
            FROM movies m
            JOIN movie_genres mg ON m.id = mg.movie_id
            WHERE mg.genre_id = ?
            ORDER BY m.release_year DESC
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $genreId, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    return $movies;
}

// Fetch movie details with universe and genres
function getMovieDetails($conn, $movieId) {
    if (!($conn instanceof mysqli)) {
        throw new InvalidArgumentException('Invalid mysqli connection passed to getMovieDetails');
    }

    $sql = "SELECT m.*, u.name AS universe_name 
            FROM movies m
            LEFT JOIN universes u ON m.universe_id = u.id
            WHERE m.id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Prepare failed in getMovieDetails: ' . $conn->error);
    }
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = $stmt->get_result();

    $movie = $result->fetch_assoc();

    if (!$movie) {
        return null;
    }

    $sql = "SELECT g.* 
            FROM genres g
            JOIN movie_genres mg ON g.id = mg.genre_id
            WHERE mg.movie_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Prepare failed in getMovieDetails (genres): ' . $conn->error);
    }
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = $stmt->get_result();

    $genres = [];
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }

    $movie['genres'] = $genres;

    return $movie;
}

// Fetch all genres
function getAllGenres($conn) {
    if (!($conn instanceof mysqli)) {
        throw new InvalidArgumentException('Invalid mysqli connection passed to getAllGenres');
    }

    $sql = "SELECT * FROM genres ORDER BY name";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Prepare failed in getAllGenres: ' . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $genres = [];
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }

    return $genres;
}

// Fetch all movies (optionally filtered by universe_id)
function getAllMovies($conn, $limit = 10, $offset = 0, $universe = null) {
    if (!($conn instanceof mysqli)) {
        throw new InvalidArgumentException('Invalid mysqli connection passed to getAllMovies');
    }

    $sql = "SELECT * FROM movies";
    $params = [];
    $types = '';

    if ($universe !== null) {
        $sql .= " WHERE universe_id = ?";
        $params[] = $universe;
        $types .= 'i';
    }

    $sql .= " ORDER BY release_year DESC LIMIT ? OFFSET ?";
    $types .= 'ii';
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Prepare failed in getAllMovies: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }

    return $movies;
}

// Function get Featured Movies
function getFeaturedMovies($conn, $limit = 15, $offset = 0) {
    if (!($conn instanceof mysqli)) {
        throw new InvalidArgumentException('Invalid mysqli connection passed to getFeaturedMovies');
    }

    $sql = "SELECT * FROM movies WHERE featured = 1 ORDER BY RAND() LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException('Prepare failed in getFeaturedMovies: ' . $conn->error);
    }

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch movie by ID
function getMovieById($conn, $id) {
    if (!($conn instanceof mysqli)) {
        throw new InvalidArgumentException('Invalid mysqli connection passed to getMovieById');
    }

    $sql = "SELECT * FROM movies WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Prepare failed in getMovieById: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

?>