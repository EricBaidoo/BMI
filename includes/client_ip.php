<?php
/**
 * The visitor's real IP address and country.
 *
 * Behind Cloudflare, REMOTE_ADDR is a Cloudflare server shared by many visitors, so login
 * lockouts and rate limits would block everyone at once. Set TRUST_CLOUDFLARE=true in .env
 * only when the site really sits behind Cloudflare (and the host only accepts traffic from it);
 * otherwise the header could be forged.
 */
require_once __DIR__ . '/env.php';

if (!function_exists('client_ip')) {
    function client_ip(): string
    {
        $remote = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        if (env('TRUST_CLOUDFLARE', false) === true) {
            $cf = trim((string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? ''));
            if ($cf !== '' && filter_var($cf, FILTER_VALIDATE_IP)) {
                return substr($cf, 0, 45);
            }
        }
        return substr($remote, 0, 45);
    }

    /**
     * Two-letter country code from Cloudflare (e.g. GH, US), or '' when unknown.
     * Only used to pick sensible defaults; visitors can always change them.
     */
    function client_country(): string
    {
        if (env('TRUST_CLOUDFLARE', false) !== true) {
            return '';
        }
        $c = strtoupper(trim((string) ($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '')));
        return preg_match('/^[A-Z]{2}$/', $c) && $c !== 'XX' ? $c : '';
    }
}
