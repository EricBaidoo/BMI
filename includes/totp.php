<?php
/**
 * Time-based one-time passwords (RFC 6238), compatible with Google Authenticator,
 * Microsoft Authenticator, Authy, 1Password and similar apps. 6 digits, 30-second steps, SHA-1.
 */

if (!function_exists('totp_generate_secret')) {
    function totp_generate_secret(int $bytes = 20): string
    {
        return base32_encode(random_bytes($bytes));
    }

    function base32_encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= $alphabet[bindec(str_pad($chunk, 5, '0'))];
        }
        return $out;
    }

    function base32_decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $secret));
        $bits = '';
        foreach (str_split($secret) as $char) {
            $bits .= str_pad(decbin(strpos($alphabet, $char)), 5, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr(bindec($byte));
            }
        }
        return $out;
    }

    function totp_code(string $secret, int $step): string
    {
        $hash = hash_hmac('sha1', pack('J', $step), base32_decode($secret), true);
        $offset = ord($hash[19]) & 0x0f;
        $value = ((ord($hash[$offset]) & 0x7f) << 24) | (ord($hash[$offset + 1]) << 16)
            | (ord($hash[$offset + 2]) << 8) | ord($hash[$offset + 3]);
        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Checks a code against the current step and one step either side (clock drift).
     * Returns the matching step, or null. Pass the last used step to block replays.
     */
    function totp_verify(string $secret, string $code, ?int $lastUsedStep = null): ?int
    {
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== 6) {
            return null;
        }
        $now = intdiv(time(), 30);
        foreach ([0, -1, 1] as $drift) {
            $step = $now + $drift;
            if ($lastUsedStep !== null && $step <= $lastUsedStep) {
                continue;
            }
            if (hash_equals(totp_code($secret, $step), $code)) {
                return $step;
            }
        }
        return null;
    }

    function totp_uri(string $secret, string $account, string $issuer): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $account)
            . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer) . '&algorithm=SHA1&digits=6&period=30';
    }

    /** Ten one-time recovery codes like "4F7K-9QX2". Returns [plain codes, hashes to store]. */
    function totp_recovery_codes(int $count = 10): array
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $plain = [];
        for ($i = 0; $i < $count; $i++) {
            $c = '';
            for ($j = 0; $j < 8; $j++) {
                $c .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $plain[] = substr($c, 0, 4) . '-' . substr($c, 4);
        }
        $hashes = array_map(fn ($c) => password_hash(str_replace('-', '', $c), PASSWORD_DEFAULT), $plain);
        return [$plain, $hashes];
    }
}
