<?php
$dir = __DIR__;
$files = [
    'beliefs.php', 'blog.php', 'contact.php', 
    'donate.php', 'event-detail.php', 'events.php', 
    'flagship-programs.php', 'livestream.php', 
    'sermons.php', 'visit.php', 'index.php'
];

$results = [];

foreach ($files as $filename) {
    $filepath = $dir . '/' . $filename;
    if (!file_exists($filepath)) continue;
    
    $content = file_get_contents($filepath);
    $original = $content;

    // 1. Expand Widths
    $content = preg_replace('/max-w-7xl\s+mx-auto\s+px-4\s+sm:px-6\s+lg:px-8/', 'w-[90%] max-w-[112.5rem] mx-auto', $content);
    $content = preg_replace('/max-w-7xl\s+mx-auto/', 'w-[90%] max-w-[112.5rem] mx-auto', $content);

    // 2. Remove Rounded Corners (preserve rounded-full)
    $content = preg_replace('/\brounded(?:-sm|-md|-lg|-xl|-2xl|-3xl)?\b/', '', $content);

    // 3. Convert px to rem in arbitrary tailwind classes e.g. text-[14px]
    $content = preg_replace_callback('/\[(\d+(?:\.\d+)?)px\]/', function($matches) {
        $remValue = (float)$matches[1] / 16;
        return '[' . $remValue . 'rem]';
    }, $content);

    if ($content !== $original) {
        file_put_contents($filepath, $content);
        $results[] = 'Updated: ' . $filename;
    } else {
        $results[] = 'No changes: ' . $filename;
    }
}

echo "Done processing pages.\n";
echo implode("\n", $results);
?>
