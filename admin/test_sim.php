<?php
$schema = [
    'live' => [
        'fields' => [
            ['key' => 'live.embed_url', 'type' => 'url'],
            ['key' => 'live.schedule', 'type' => 'schedule_repeater']
        ]
    ]
];

$posted = [
    'live_embed_url' => '',
    'live_schedule' => [
        ['type' => 'date', 'name' => 'try', 'date' => date('Y-m-d'), 'time' => '10:30']
    ]
];

$_POST['end_and_archive'] = '1';
$_POST['active_group'] = 'live';

$update = [];

foreach ($schema as $groupKey => $group) {
    foreach ($group['fields'] as $f) {
        $key = $f['key'];
        $postKey = str_replace('.', '_', $key);
        $postedKey = array_key_exists($key, $posted) ? $key : (array_key_exists($postKey, $posted) ? $postKey : null);
        
        if ($postedKey) {
            $update[$key] = trim((string) $posted[$postedKey]);
            
            if ($key === 'live.embed_url' && isset($_POST['end_and_archive']) && $_POST['end_and_archive'] === '1') {
                $schedKey = str_replace('.', '_', 'live.schedule');
                if (isset($posted[$schedKey]) && is_array($posted[$schedKey])) {
                    foreach ($posted[$schedKey] as $index => $event) {
                        if ($event['name'] === 'try') {
                            unset($posted[$schedKey][$index]);
                            break;
                        }
                    }
                }
            }
        }
        
        if ($f['type'] === 'schedule_repeater') {
            if (isset($_POST['active_group']) && $_POST['active_group'] === $groupKey) {
                $events = [];
                if ($postedKey && is_array($posted[$postedKey])) {
                    foreach ($posted[$postedKey] as $event) {
                        $events[] = $event;
                    }
                }
                $update[$key] = json_encode($events);
            }
        }
    }
}

print_r($update);
