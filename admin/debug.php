<?php
file_put_contents(__DIR__ . '/files_dump.txt', print_r($_FILES, true) . "\n" . print_r($_POST, true));
echo "Dumped";
