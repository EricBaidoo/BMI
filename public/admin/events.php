<?php
require_once __DIR__ . '/../../includes/auth.php';
auth_require('content');
require_once __DIR__ . '/../../includes/admin_crud.php';

admin_crud_page([
    'table' => 'events',
    'entity' => 'event',
    'singular' => 'Event',
    'plural' => 'Events',
    'page' => 'events.php',
    'intro' => 'Special events appear on the Events page; flagship programmes appear on Flagship Programs.',
    'order' => 'event_date DESC, event_time DESC, id DESC',
    'title_field' => 'title',
    'slug' => ['field' => 'slug', 'from' => 'title'],
    'image' => ['field' => 'event_image', 'category' => 'events', 'label' => 'Event image'],
    'fields' => [
        'title' => ['label' => 'Title', 'required' => true, 'max' => 200],
        'event_type' => ['label' => 'Type', 'type' => 'select', 'options' => ['special' => 'Special event', 'flagship' => 'Flagship programme'], 'default' => 'special'],
        'event_date' => ['label' => 'Start date', 'type' => 'date', 'required' => true],
        'end_date' => ['label' => 'End date', 'type' => 'date', 'hint' => 'for events over several days'],
        'event_time' => ['label' => 'Start time', 'type' => 'time'],
        'venue' => ['label' => 'Venue', 'max' => 200],
        'description' => ['label' => 'Description', 'type' => 'textarea', 'rows' => 5],
    ],
    'card' => fn (array $e) => [
        'title' => (string) $e['title'],
        'meta' => array_filter([
            date('M j, Y', strtotime((string) $e['event_date']))
                . ($e['end_date'] ? ' – ' . date('M j, Y', strtotime((string) $e['end_date'])) : '')
                . ($e['event_time'] ? ' · ' . date('g:i A', strtotime((string) $e['event_time'])) : ''),
            $e['venue'] ?: null,
        ]),
        'badge' => $e['event_type'] === 'flagship'
            ? ['Flagship', 'bg-amber-100 text-amber-800 border-amber-200']
            : ['Special', 'bg-blue-100 text-blue-800 border-blue-200'],
        'view' => '../event-detail?id=' . (int) $e['id'],
    ],
]);
