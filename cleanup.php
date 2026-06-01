<?php
$dir = __DIR__;
$files = [
    'about.php', 'beliefs.php', 'blog.php', 'contact.php', 
    'donate.php', 'event-detail.php', 'events.php', 
    'flagship-programs.php', 'livestream.php', 
    'sermons.php', 'visit.php', 'index.php', 'ministries.php'
];

$results = [];

foreach ($files as $filename) {
    $filepath = $dir . '/' . $filename;
    if (!file_exists($filepath)) continue;
    
    $content = file_get_contents($filepath);
    $original = $content;

    // Fix broken -full (from rounded-full)
    $content = str_replace(' -full"', ' rounded-full"', $content);
    $content = str_replace(' -full ', ' rounded-full ', $content);
    $content = str_replace('"-full ', '"rounded-full ', $content);

    // Remove broken -t-lg, -b-md, etc.
    $content = preg_replace('/ -(?:t|b|l|r|tl|tr|bl|br)-(?:sm|md|lg|xl|2xl|3xl)\b/', '', $content);
    
    // Convert any remaining pixel values in tailwind classes
    $content = preg_replace_callback('/\[(\d+(?:\.\d+)?)px\]/', function($matches) {
        $remValue = (float)$matches[1] / 16;
        return '[' . $remValue . 'rem]';
    }, $content);

    if ($content !== $original) {
        file_put_contents($filepath, $content);
        $results[] = 'Fixed: ' . $filename;
    } else {
        $results[] = 'Clean: ' . $filename;
    }
}

echo "Cleanup done.\n";
echo implode("\n", $results);
?>
