<?php
$posted = [
    'live_schedule' => [
        ['type' => 'date', 'name' => 'try', 'date' => date('Y-m-d'), 'time' => '10:30']
    ]
];
$schema = ['live.embed_url', 'live.schedule'];
foreach ($schema as $f) {
    if ($f === 'live.embed_url') {
        $schedKey = 'live_schedule';
        foreach ($posted[$schedKey] as $index => $event) {
            unset($posted[$schedKey][$index]);
        }
    } else if ($f === 'live.schedule') {
        print_r($posted['live_schedule']);
    }
}
