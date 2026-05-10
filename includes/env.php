<?php
/**
 * Minimal .env loader — no composer dependency.
 * Loads `.env` from the project root into $_ENV / getenv() once per request.
 */

if (!function_exists('env')) {
    function env_load(string $path): void
    {
        static $loaded = [];
        if (isset($loaded[$path]) || !is_readable($path)) {
            return;
        }
        $loaded[$path] = true;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $value = trim($value, "\"'");
            }
            if ($key === '' || array_key_exists($key, $_ENV)) {
                continue;
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }
        if (in_array(strtolower((string) $value), ['true', '(true)'], true)) {
            return true;
        }
        if (in_array(strtolower((string) $value), ['false', '(false)'], true)) {
            return false;
        }
        return $value;
    }
}

env_load(dirname(__DIR__) . '/.env');
