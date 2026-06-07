<?php
$posted = [
    'live_schedule' => [
        ['type' => 'weekly', 'name' => 'Weekly Service'],
        ['type' => 'date', 'name' => 'try', 'date' => '2026-06-02', 'time' => '10:30']
    ]
];

$schedKey = 'live_schedule';
foreach ($posted[$schedKey] as $index => $event) {
    if ($event['name'] === 'try') {
        unset($posted[$schedKey][$index]);
    }
}

$events = [];
foreach ($posted[$schedKey] as $event) {
    $events[] = $event;
}
print_r($events);
