<?php
$url = 'https://assets.mixkit.co/videos/preview/mixkit-light-burst-among-clouds-22165-large.mp4';
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n" .
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n"
    ]
];
$context = stream_context_create($opts);
$videoData = file_get_contents($url, false, $context);
if ($videoData !== false) {
    if (!is_dir('c:/xampp/htdocs/BMI/assets/video')) {
        mkdir('c:/xampp/htdocs/BMI/assets/video', 0777, true);
    }
    file_put_contents('c:/xampp/htdocs/BMI/assets/video/hero.mp4', $videoData);
    echo "Downloaded: " . strlen($videoData) . " bytes\n";
} else {
    echo "Failed to download.\n";
}

