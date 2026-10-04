<?php
/**
 * Showing church times in each visitor's own time zone.
 *
 * Staff enter times as text in the church's zone ("Sundays · 8:45 AM", "Wednesdays 6pm").
 * When a weekday and a time can be read from that text, the next occurrence is attached as
 * a timestamp; assets/js/main.js then adds the visitor's local equivalent, e.g.
 * "Sundays · 8:45 AM GMT (4:45 AM EDT your time)". Visitors in the same zone see no change,
 * and text that can't be read (e.g. "First Friday of the month") is shown as written.
 */

if (!function_exists('next_weekly_occurrence')) {
    /**
     * Next start of a weekly time described in $text, in time zone $tz. Null if the text has
     * no recognisable weekday and time.
     */
    function next_weekly_occurrence(string $text, string $tz = ''): ?int
    {
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        if (!preg_match('/\b(sun|mon|tue|tues|wed|thu|thur|thurs|fri|sat)[a-z]*\b/i', $text, $d)) {
            return null;
        }
        $prefix = strtolower(substr($d[1], 0, 3));
        $day = null;
        foreach ($days as $name) {
            if (str_starts_with($name, $prefix)) {
                $day = $name;
                break;
            }
        }
        if (!preg_match('/\b(\d{1,2})(?:[:.](\d{2}))?\s*([ap])\.?\s*m\b\.?/i', $text, $t)) {
            return null;
        }
        $hour = (int) $t[1] % 12 + (strtolower($t[3]) === 'p' ? 12 : 0);
        $minute = (int) $t[2];
        if ($minute > 59 || $day === null) {
            return null;
        }
        try {
            $zone = new DateTimeZone($tz !== '' ? $tz : date_default_timezone_get());
            $now = new DateTimeImmutable('now', $zone);
            $candidate = $now->modify($day === strtolower($now->format('l')) ? 'today' : 'next ' . $day)->setTime($hour, $minute);
            if ($candidate < $now->modify('-2 hours')) {
                $candidate = $candidate->modify('+7 days');
            }
            return $candidate->getTimestamp();
        } catch (Throwable $e) {
            return null;
        }
    }

    /** Placeholder the browser fills with the visitor's local time for timestamp $ts. */
    function local_time_hint(int $ts, string $tz = ''): string
    {
        $zone = $tz !== '' ? $tz : date_default_timezone_get();
        return '<span class="js-local-time text-white/50 font-normal" data-ts="' . $ts . '" data-tz="' . htmlspecialchars($zone, ENT_QUOTES, 'UTF-8') . '"></span>';
    }

    /** Escaped $text plus a local-time hint when a weekly time can be read from it. */
    function time_with_local(string $text, string $tz = ''): string
    {
        $html = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $ts = next_weekly_occurrence($text, $tz);
        return $ts === null ? $html : $html . local_time_hint($ts, $tz);
    }

    /** "+233 26 235 4383" style number for tel: links (Ghana and US numbers written locally). */
    function phone_tel(string $phone, string $country = 'GH'): string
    {
        $plus = str_starts_with(trim($phone), '+');
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return '';
        }
        if ($plus) {
            return '+' . $digits;
        }
        if ($country === 'GH' && str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+233' . substr($digits, 1);
        }
        if ($country === 'US' && strlen($digits) === 10) {
            return '+1' . $digits;
        }
        if ($country === 'US' && strlen($digits) === 11 && $digits[0] === '1') {
            return '+' . $digits;
        }
        return $digits;
    }
}

if (!function_exists('privacy_embed_url')) {
    /** YouTube's privacy-enhanced domain: no tracking cookies until the visitor presses play. */
    function privacy_embed_url(string $url): string
    {
        return (string) preg_replace('#^https?://(www\.)?youtube\.com/embed/#i', 'https://www.youtube-nocookie.com/embed/', $url);
    }
}
