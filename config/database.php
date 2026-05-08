<?php

namespace App\Config;

use mysqli;

class Database
{
    private static ?mysqli $connection = null;

    public static function connect(): mysqli
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASS') ?: '';
        $database = getenv('DB_NAME') ?: 'prj_movie';
        $port = (int)(getenv('DB_PORT') ?: 3307);

        $connection = new mysqli($host, $user, $password, $database, $port);

        if ($connection->connect_error) {
            throw new \Exception('Database connection failed: ' . $connection->connect_error);
        }

        $connection->set_charset('utf8mb4');

        self::$connection = $connection;
        return $connection;
    }

    public static function getInstance(): mysqli
    {
        return self::connect();
    }

    public static function close(): void
    {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
