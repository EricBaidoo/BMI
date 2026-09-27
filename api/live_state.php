<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/settings.php';
header('Content-Type: application/json');

// Handle Email Notes POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'email_notes') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $notesHtml = $_POST['notes_html'] ?? '';

    if (!$email || empty($notesHtml)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or empty notes.']);
        exit;
    }

    $subject = "Your Sermon Notes - " . setting('site.title', 'Bridge Ministries');
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@bridgeministries.org" . "\r\n";

    $message = "
    <html>
    <head>
        <title>Sermon Notes</title>
        <style>
            body { font-family: sans-serif; padding: 20px; color: #333; line-height: 1.6; }
            h2 { color: #000; }
        </style>
    </head>
    <body>
        <h2>Your Sermon Notes</h2>
        {$notesHtml}
    </body>
    </html>
    ";

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
    }
    exit;
}

try {
    $pdo = db_connect();
    
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM live_state");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo json_encode([
        'status' => 'success',
        'current_prompt_html' => $settings['current_prompt_html'] ?? '',
        'current_notes_html' => $settings['current_notes_html'] ?? ''
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch live state'
    ]);
}
