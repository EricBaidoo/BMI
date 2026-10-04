<?php
/**
 * Audit log: who changed what, and when. Rows are only ever inserted, never edited.
 * Shown to administrators at admin/audit-log.php.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/logger.php';

if (!function_exists('audit')) {
    /**
     * @param string      $action   e.g. 'create', 'update', 'delete', 'login', 'login_failed'
     * @param string      $entity   e.g. 'sermon', 'event', 'setting', 'user'
     * @param string|int|null $entityId
     * @param string      $summary  one readable sentence for the log screen
     * @param array       $details  extra data (e.g. ['changes' => [key => [old, new]]])
     */
    function audit(string $action, string $entity, $entityId = null, string $summary = '', array $details = [], ?array $actor = null): void
    {
        $actor = $actor ?? ($_SESSION['user'] ?? null);
        try {
            $stmt = db_connect()->prepare(
                'INSERT INTO audit_log (user_id, user_email, action, entity, entity_id, summary, details, ip, user_agent)
                 VALUES (:uid, :email, :action, :entity, :eid, :summary, :details, :ip, :ua)'
            );
            $stmt->execute([
                ':uid' => isset($actor['id']) ? (int) $actor['id'] : null,
                ':email' => $actor['email'] ?? null,
                ':action' => mb_substr($action, 0, 40),
                ':entity' => mb_substr($entity, 0, 40),
                ':eid' => $entityId !== null ? mb_substr((string) $entityId, 0, 64) : null,
                ':summary' => mb_substr($summary, 0, 255),
                ':details' => $details ? json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                ':ip' => mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
                ':ua' => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);
        } catch (Throwable $e) {
            // Never let logging break the action itself, but make sure the failure is visible.
            log_exception($e, 'audit');
        }
    }

    /** Keys whose changes trigger an email alert to administrators and the finance contact. */
    function audit_is_giving_key(string $key): bool
    {
        return str_starts_with($key, 'giving.')
            || in_array($key, ['donate.bank_details', 'donate.momo_details', 'donate.chms_url'], true);
    }

    /** Email admins + the giving contact when payment details change. */
    function audit_alert_giving_change(array $changes): void
    {
        if (!$changes) {
            return;
        }
        try {
            $pdo = db_connect();
            $to = $pdo->query("SELECT email FROM users WHERE role = 'admin' AND is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
            $giving = (string) $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'contact.email_giving'")->fetchColumn();
            if ($giving !== '') {
                $to[] = $giving;
            }
            $to = array_unique(array_filter($to, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
            if (!$to) {
                return;
            }
            $who = ($_SESSION['user']['name'] ?? 'Unknown') . ' <' . ($_SESSION['user']['email'] ?? '?') . '>';
            $lines = [];
            foreach ($changes as $key => [$old, $new]) {
                $lines[] = "- {$key}\n    before: " . ($old === '' ? '(empty)' : $old) . "\n    after:  " . ($new === '' ? '(empty)' : $new);
            }
            $host = parse_url((string) env('APP_URL', 'https://bmiglobal.org'), PHP_URL_HOST) ?: 'bmiglobal.org';
            $body = "Giving details on the website were changed.\n\nChanged by: {$who}\nTime: " . date('Y-m-d H:i:s T')
                . "\nIP address: " . ($_SERVER['REMOTE_ADDR'] ?? '?') . "\n\n" . implode("\n", $lines)
                . "\n\nIf you did not expect this change, sign in to the admin panel, check the Audit Log and correct the details immediately.";
            require_once __DIR__ . '/mailer.php';
            send_mail($to, "[{$host}] Giving details changed", $body);
        } catch (Throwable $e) {
            log_exception($e, 'giving alert');
        }
    }
}
