<?php
require_once __DIR__ . '/config.php';

/**
 * Return a shared PDO connection.
 */
function db_connect(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;

    $host = !empty($dbHost) ? (string) $dbHost : '127.0.0.1';
    $port = !empty($dbPort) ? (int) $dbPort : 3306;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 2, // 2 second timeout instead of hanging
    ];

    try {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $dbName);
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        return $pdo;
    } catch (PDOException $e) {
        throw new RuntimeException(
            'Database connection failed on ' . $host . ':' . $port . '. Confirm XAMPP MySQL is running and "' . $dbName . '" exists. Error: ' . $e->getMessage(),
            0,
            $e
        );
    }
}
