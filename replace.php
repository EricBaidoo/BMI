<?php
$dir = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        if (strpos($content, 'w=2000') !== false) {
            $content = str_replace('w=2000', 'w=1200', $content);
            file_put_contents($path, $content);
        }
    }
}
echo "Done";
