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

    $portsToTry = [];
    if (!empty($dbPort)) {
        $portsToTry[] = (int) $dbPort;
    }
    $portsToTry[] = 3306;
    $portsToTry[] = 3307;
    $portsToTry = array_values(array_unique(array_filter($portsToTry, static function ($port) {
        return $port > 0;
    })));

    $hostsToTry = array_values(array_unique(array_filter([
        (string) $dbHost,
        '127.0.0.1',
        'localhost',
    ])));

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $lastException = null;

    foreach ($hostsToTry as $host) {
        foreach ($portsToTry as $port) {
            try {
                $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $dbName);
                $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
                return $pdo;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }
    }

    throw new RuntimeException(
        'Database connection failed. Start MySQL in XAMPP and confirm the database "' . $dbName . '" exists. ' .
        'Checked hosts: ' . implode(', ', $hostsToTry) . ' on ports: ' . implode(', ', $portsToTry) . '.',
        0,
        $lastException
    );
}
