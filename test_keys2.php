<?php
// Simulate HTTP POST payload parsing
$query = http_build_query([
    'setting' => [
        'live.schedule' => ['event1'],
        'live.past_services' => ['past1']
    ]
]);
parse_str($query, $parsed);
print_r($parsed);
