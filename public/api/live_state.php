<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/settings.php';
header('Content-Type: application/json');

// Email the current sermon notes to a viewer.
// The email is built from the notes stored on the server; the viewer only supplies
// their address and the plain-text answers they typed into the blanks.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'email_notes') {
    require_once __DIR__ . '/../../includes/csrf.php';

    $fail = function (int $code, string $message): void {
        http_response_code($code);
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit;
    };

    $token = (string) ($_POST['csrf_token'] ?? '');
    if ($token === '' || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
        $fail(403, 'Your session expired. Please reload the page and try again.');
    }

    $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $fail(422, 'Please enter a valid email address.');
    }

    // Rate limit: 3 per IP per 10 minutes, 3 per recipient per hour.
    $limitFile = sys_get_temp_dir() . '/bmi_notes_ratelimit.json';
    $now = time();
    $fh = fopen($limitFile, 'c+');
    flock($fh, LOCK_EX);
    $log = json_decode(stream_get_contents($fh) ?: '[]', true) ?: [];
    $log = array_values(array_filter($log, fn ($e) => $e['t'] > $now - 3600));
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $byIp = count(array_filter($log, fn ($e) => $e['ip'] === $ip && $e['t'] > $now - 600));
    $byTo = count(array_filter($log, fn ($e) => $e['to'] === strtolower($email)));
    if ($byIp >= 3 || $byTo >= 3) {
        flock($fh, LOCK_UN);
        fclose($fh);
        $fail(429, 'Too many requests. Please try again later.');
    }
    $log[] = ['t' => $now, 'ip' => $ip, 'to' => strtolower($email)];
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($log));
    flock($fh, LOCK_UN);
    fclose($fh);

    try {
        $stmt = db_connect()->prepare('SELECT setting_value FROM live_state WHERE setting_key = :k');
        $stmt->execute([':k' => 'current_notes_html']);
        $notesHtml = (string) $stmt->fetchColumn();
    } catch (Throwable $e) {
        log_exception($e, 'live_state');
        log_exception($e, 'live_state');
        $fail(500, 'Notes are not available right now.');
    }
    if (trim($notesHtml) === '') {
        $fail(422, 'There are no notes to send yet.');
    }

    // Fill each blank with the viewer's answer as plain text, in order.
    $answers = $_POST['answers'] ?? [];
    $answers = is_array($answers) ? array_values($answers) : [];
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="notes-root">' . $notesHtml . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);
    $hasClass = fn (string $c) => 'contains(concat(" ", normalize-space(@class), " "), " ' . $c . ' ")';
    $i = 0;
    foreach (iterator_to_array($xpath->query('//*[' . $hasClass('notes-input') . ' or ' . $hasClass('notes-textarea') . ']')) as $blank) {
        $answer = trim(mb_substr((string) ($answers[$i++] ?? ''), 0, 2000));
        $filled = $doc->createElement('strong');
        $filled->appendChild($doc->createTextNode($answer !== '' ? $answer : '________________'));
        $blank->parentNode->replaceChild($filled, $blank);
    }
    // Strip anything executable from the stored notes before mailing.
    foreach (iterator_to_array($xpath->query('//script|//style|//iframe|//object|//embed|//form')) as $node) {
        $node->parentNode->removeChild($node);
    }
    foreach (iterator_to_array($xpath->query('//@*[starts-with(name(), "on")]')) as $attr) {
        $attr->ownerElement->removeAttributeNode($attr);
    }
    $body = '';
    foreach ($xpath->query('//div[@id="notes-root"]')->item(0)->childNodes as $child) {
        $body .= $doc->saveHTML($child);
    }

    $siteTitle = setting('site.title', 'Bridge Ministries International');
    $subject = 'Your Sermon Notes - ' . $siteTitle;
    $message = '<html><body style="font-family:sans-serif;padding:20px;color:#333;line-height:1.6">'
        . '<h2 style="color:#000">Your Sermon Notes</h2>' . $body
        . '<p style="color:#888;font-size:12px">Sent from ' . htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8')
        . ' because this address was entered on the livestream page.</p>'
        . '</body></html>';

    require_once __DIR__ . '/../../includes/mailer.php';
    if (send_mail($email, $subject, $message, ['html' => true])) {
        echo json_encode(['status' => 'success']);
    } else {
        $fail(500, 'Failed to send email.');
    }
    exit;
}

// Every viewer polls this during a service, so the response is cached for a few seconds:
// in a file on the server (one database read per 10 seconds, not one per viewer) and by
// browsers/CDN via Cache-Control. Saving in admin Live Control clears the file immediately.
$cacheFile = live_state_cache_file();
$cacheSeconds = 10;
header('Cache-Control: public, max-age=5, s-maxage=' . $cacheSeconds);
if (is_file($cacheFile) && (time() - (int) @filemtime($cacheFile)) < $cacheSeconds && ($cached = @file_get_contents($cacheFile)) !== false) {
    echo $cached;
    exit;
}

try {
    $pdo = db_connect();

    $stmt = $pdo->query("SELECT setting_key, setting_value FROM live_state");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $json = json_encode([
        'status' => 'success',
        'current_prompt_html' => safe_html($settings['current_prompt_html'] ?? '', 'notes'),
        'current_notes_html' => safe_html($settings['current_notes_html'] ?? '', 'notes')
    ]);
    @file_put_contents($cacheFile, $json, LOCK_EX);
    echo $json;
} catch (Throwable $e) {
    header('Cache-Control: no-store');
    log_exception($e, 'live_state');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch live state'
    ]);
}
