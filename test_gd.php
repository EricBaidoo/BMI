<?php
if (!function_exists('imagewebp')) {
    echo "imagewebp does not exist\n";
} else {
    echo "imagewebp exists\n";
}

$info = gd_info();
print_r($info);
