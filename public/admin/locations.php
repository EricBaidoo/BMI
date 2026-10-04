<?php
require_once __DIR__ . '/../../includes/auth.php';
auth_require('content');
require_once __DIR__ . '/../../includes/admin_crud.php';

$countries = ['GH' => 'Ghana', 'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada', 'NG' => 'Nigeria', 'ZZ' => 'Other'];

admin_crud_page([
    'table' => 'locations',
    'entity' => 'location',
    'singular' => 'Location',
    'plural' => 'Locations',
    'page' => 'locations.php',
    'intro' => 'Branches and fellowships shown on the Locations page. Write service times in the branch\'s own local time; visitors elsewhere automatically see their own time too.',
    'order' => 'sort_order ASC, name ASC',
    'title_field' => 'name',
    'image' => ['field' => 'location_image', 'category' => 'locations', 'label' => 'Photo of the building or congregation'],
    'fields' => [
        'name' => ['label' => 'Name', 'required' => true, 'max' => 150, 'hint' => 'e.g. Accra (Headquarters), Atlanta Fellowship'],
        'country' => ['label' => 'Country', 'type' => 'select', 'options' => $countries, 'default' => 'GH'],
        'city' => ['label' => 'City / state', 'max' => 120],
        'timezone' => ['label' => 'Time zone of the service times', 'type' => 'select', 'default' => 'Africa/Accra', 'options' => [
            'Africa/Accra' => 'Ghana (GMT)',
            'America/New_York' => 'US Eastern (New York, Atlanta)',
            'America/Chicago' => 'US Central (Chicago, Houston, Dallas)',
            'America/Denver' => 'US Mountain (Denver)',
            'America/Phoenix' => 'US Arizona (no daylight saving)',
            'America/Los_Angeles' => 'US Pacific (Los Angeles, Seattle)',
            'Europe/London' => 'United Kingdom',
            'America/Toronto' => 'Canada Eastern (Toronto)',
            'Africa/Lagos' => 'Nigeria (WAT)',
        ]],
        'address' => ['label' => 'Address', 'type' => 'textarea', 'rows' => 2],
        'service_times' => ['label' => 'Service times', 'type' => 'textarea', 'rows' => 4, 'hint' => 'one per line, e.g. "Sunday Worship: Sundays · 10:00 AM"'],
        'phone' => ['label' => 'Phone', 'max' => 40],
        'email' => ['label' => 'Email', 'type' => 'email', 'max' => 150],
        'map_query' => ['label' => 'Map search', 'max' => 255, 'hint' => 'what to search in Google Maps, e.g. the full address'],
        'sort_order' => ['label' => 'Order on the page', 'type' => 'number', 'default' => '0', 'hint' => 'lower numbers first'],
    ],
    'card' => fn (array $l) => [
        'title' => (string) $l['name'],
        'meta' => array_filter([
            trim(($l['city'] ? $l['city'] . ', ' : '') . ($countries[$l['country']] ?? $l['country'])),
            $l['service_times'] ? strtok((string) $l['service_times'], "\n") : null,
        ]),
        'badge' => [$l['country'], 'bg-slate-100 text-slate-700 border-slate-200'],
        'view' => '../locations#location-' . (int) $l['id'],
    ],
]);
