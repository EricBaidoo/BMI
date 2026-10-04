<?php
require_once __DIR__ . '/../../includes/auth.php';
auth_require('content');
require_once __DIR__ . '/../../includes/admin_crud.php';

admin_crud_page([
    'table' => 'sermons',
    'entity' => 'sermon',
    'singular' => 'Sermon',
    'plural' => 'Sermons',
    'page' => 'sermons.php',
    'intro' => 'Add, edit and remove the sermons shown on the website and in the podcast feed.',
    'order' => 'sermon_date DESC, id DESC',
    'title_field' => 'title',
    'image' => ['field' => 'sermon_image', 'category' => 'sermons', 'label' => 'Sermon image'],
    'fields' => [
        'title' => ['label' => 'Title', 'required' => true, 'max' => 200],
        'speaker' => ['label' => 'Speaker', 'required' => true, 'max' => 120],
        'sermon_date' => ['label' => 'Date', 'type' => 'date', 'required' => true],
        'topic' => ['label' => 'Topic', 'max' => 120],
        'media_type' => ['label' => 'Media type', 'type' => 'select', 'options' => ['audio' => 'Audio', 'video' => 'Video', 'text' => 'Text'], 'default' => 'audio'],
        'media_url' => ['label' => 'Media link', 'type' => 'url', 'hint' => 'YouTube, Facebook, Spotify or audio file link'],
        'content' => ['label' => 'Summary / notes', 'type' => 'textarea', 'rows' => 5],
    ],
    'card' => fn (array $s) => [
        'title' => (string) $s['title'],
        'meta' => array_filter([
            $s['speaker'] . ' · ' . date('M j, Y', strtotime((string) $s['sermon_date'])),
            $s['topic'] ? 'Topic: ' . $s['topic'] : null,
        ]),
        'badge' => [
            'video' => ['Video', 'bg-red-100 text-red-700 border-red-200'],
            'audio' => ['Audio', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
        ][$s['media_type']] ?? ['Text', 'bg-slate-100 text-slate-700 border-slate-200'],
        'view' => '../sermon?id=' . (int) $s['id'],
    ],
]);
