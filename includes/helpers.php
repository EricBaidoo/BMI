<?php
/**
 * Common helpers used across public and admin pages.
 */

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = preg_replace('~-+~', '-', $text);
        $text = trim($text, '-');
        return $text === '' ? 'post' : $text;
    }
}

/**
 * One-shot session flash message.
 * - flash('key', 'value')  stores the message; returns null.
 * - flash('key')           reads-and-clears the message; returns string|null.
 *
 * Used to implement Post-Redirect-Get (PRG) so a browser refresh after a form
 * submission does not re-submit the form. The page POSTs, sets a flash, then
 * 302-redirects to the same URL; the redirected GET reads-and-clears the flash.
 */
if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $msg = $_SESSION['_flash'][$key] ?? null;
        if ($msg !== null) {
            unset($_SESSION['_flash'][$key]);
        }
        return $msg;
    }
}

if (!function_exists('excerpt')) {
    function excerpt(string $html, int $words = 30): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        $parts = explode(' ', $text);
        if (count($parts) <= $words) {
            return $text;
        }
        return implode(' ', array_slice($parts, 0, $words)) . '…';
    }
}
