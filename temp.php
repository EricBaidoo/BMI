<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}
require_once __DIR__ . '/database/migrate.php';
