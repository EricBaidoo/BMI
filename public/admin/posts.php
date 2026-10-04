<?php
require_once __DIR__ . '/../../includes/auth.php';
auth_require('content');
require_once __DIR__ . '/../../includes/admin_crud.php';

admin_crud_page([
    'table' => 'posts',
    'entity' => 'post',
    'singular' => 'Post',
    'plural' => 'Blog Posts',
    'page' => 'posts.php',
    'intro' => 'Write blog posts, announcements and devotionals. Untick "Published" to keep a draft off the website.',
    'order' => 'COALESCE(published_at, created_at) DESC, id DESC',
    'title_field' => 'title',
    'slug' => ['field' => 'slug', 'from' => 'title'],
    'image' => ['field' => 'post_image', 'category' => 'posts', 'label' => 'Post image'],
    'fields' => [
        'title' => ['label' => 'Title', 'required' => true, 'max' => 200],
        'category' => ['label' => 'Category', 'type' => 'select', 'options' => ['blog' => 'Blog', 'announcement' => 'Announcement', 'devotional' => 'Devotional'], 'default' => 'blog'],
        'published_at' => ['label' => 'Published (visible on the website)', 'type' => 'publish'],
        'content' => ['label' => 'Content', 'type' => 'textarea', 'rows' => 12, 'required' => true],
    ],
    'card' => fn (array $p) => [
        'title' => (string) $p['title'],
        'meta' => [ucfirst((string) $p['category']) . ' · ' . ($p['published_at'] ? 'Published ' . date('M j, Y', strtotime((string) $p['published_at'])) : 'Draft')],
        'badge' => $p['published_at'] ? ['Published', 'bg-emerald-100 text-emerald-700 border-emerald-200'] : ['Draft', 'bg-slate-100 text-slate-700 border-slate-200'],
        'view' => $p['published_at'] ? '../blog?post=' . rawurlencode((string) $p['slug']) : null,
    ],
]);
