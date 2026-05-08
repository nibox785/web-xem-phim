<?php

/**
 * Xem Phim - Main Router
 * 
 * Entry point cho ứng dụng
 * Điều hướng requests đến các controller tương ứng
 */

// Define constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load environment variables
require_once ROOT_PATH . '/include/config.php';
load_env_file(ROOT_PATH . '/.env');

// Load autoloader
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
} else {
    // Fallback: Simple PSR-4 autoloader nếu chưa chạy composer
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        if (strpos($class, $prefix) === 0) {
            $relative_class = substr($class, strlen($prefix));
            $file = APP_PATH . '/' . str_replace('\\', '/', $relative_class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}

// Load database config
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/app.php';

use App\Config\Database;
use App\Controllers\MovieController;
use App\Services\MovieService;
use App\Services\PaginationService;
use App\Repositories\MovieRepository;

try {
    // Initialize database connection
    $conn = Database::getInstance();

    // Get request page
    $page = $_GET['page'] ?? 'movies';
    $page = preg_replace('/[^a-z_]/', '', $page);

    // Route handling
    switch ($page) {
        // ============ MOVIES ============
        case 'movies':
            $repository = new MovieRepository($conn);
            $paginationService = new PaginationService();
            $movieService = new MovieService($repository, $paginationService);
            $controller = new MovieController($movieService);
            $controller->listMovies();
            break;

        case 'featured':
            $repository = new MovieRepository($conn);
            $paginationService = new PaginationService();
            $movieService = new MovieService($repository, $paginationService);
            $controller = new MovieController($movieService);
            $controller->listFeatured();
            break;

        case 'genre':
            $repository = new MovieRepository($conn);
            $paginationService = new PaginationService();
            $movieService = new MovieService($repository, $paginationService);
            $controller = new MovieController($movieService);
            $controller->showByGenre();
            break;

        case 'search':
            $repository = new MovieRepository($conn);
            $paginationService = new PaginationService();
            $movieService = new MovieService($repository, $paginationService);
            $controller = new MovieController($movieService);
            $controller->search();
            break;

        default:
            header('HTTP/1.0 404 Not Found');
            echo "404 - Page not found";
            break;
    }

    // Close database connection
    Database::close();

} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo "Error: " . ($e->getMessage());
    if (getenv('APP_DEBUG')) {
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
