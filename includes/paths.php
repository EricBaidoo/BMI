<?php
/**
 * Folder layout. Only PUBLIC_DIR is served by the web server; everything else
 * (includes, templates, database, bin, logs, vendor) lives outside it.
 */
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
    define('PUBLIC_DIR', APP_ROOT . '/public');
    define('ADMIN_TEMPLATES', APP_ROOT . '/templates/admin');
}
