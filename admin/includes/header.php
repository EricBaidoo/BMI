<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/settings.php';
auth_require();
$user = auth_user();

$currentPage = basename($_SERVER['PHP_SELF']);
$navItems = [
    'index.php' => ['icon' => 'home', 'label' => 'Dashboard'],
    'home_manager.php' => ['icon' => 'home', 'label' => 'Homepage Manager'],
    'pages.php' => ['icon' => 'document-text', 'label' => 'Page Content'],
    'sermons.php' => ['icon' => 'video-camera', 'label' => 'Sermons'],
    'events.php' => ['icon' => 'calendar', 'label' => 'Events'],
    'ministries.php' => ['icon' => 'user-group', 'label' => 'Ministries'],
    'posts.php' => ['icon' => 'newspaper', 'label' => 'Blog Posts'],
    'messages.php' => ['icon' => 'inbox', 'label' => 'Inbox'],
    'users.php' => ['icon' => 'shield-check', 'label' => 'User Management'],
    'settings.php' => ['icon' => 'cog', 'label' => 'Settings'],
];

function render_icon($name) {
    $icons = [
        'home' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>',
        'document-text' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
        'video-camera' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>',
        'calendar' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
        'user-group' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
        'newspaper' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>',
        'inbox' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>',
        'shield-check' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>',
        'cog' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
    ];
    return $icons[$name] ?? '';
}

$pageTitle = $pageTitle ?? 'Admin Dashboard | Bridge Ministries International';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <?php if (setting('site.favicon')): ?>
        <link rel="icon" href="/BMI/<?php echo htmlspecialchars(setting('site.favicon')); ?>">
    <?php endif; ?>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f8f9fa',
                            100: '#e8ecf1',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#0f172a', // deep slate
                            950: '#020617', // darker slate
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }
    </style>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>
<body class="bg-brand-50 text-slate-800 font-sans antialiased overflow-hidden flex h-screen">

    <!-- Mobile sidebar backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-72 bg-brand-950 text-slate-300 transition-transform duration-300 transform -translate-x-full lg:translate-x-0 flex flex-col h-full border-r border-slate-800 shadow-2xl lg:shadow-none">
        
        <!-- Logo Area -->
        <div class="flex items-center justify-between h-16 px-6 bg-brand-900/50 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <?php if (setting('site.favicon')): ?>
                    <img src="/BMI/<?php echo htmlspecialchars(setting('site.favicon')); ?>" alt="Logo" class="w-8 h-8 rounded object-contain bg-white">
                <?php else: ?>
                    <div class="w-8 h-8 rounded bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-sm shadow-inner">
                        B
                    </div>
                <?php endif; ?>
                <span class="font-bold text-white tracking-wide truncate">BMI Admin</span>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Manage System</p>
            <?php foreach ($navItems as $url => $item): ?>
                <?php $isActive = ($currentPage === $url); ?>
                <a href="<?php echo $url; ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group <?php echo $isActive ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <span class="<?php echo $isActive ? 'text-white' : 'text-slate-500 group-hover:text-blue-400'; ?> transition-colors">
                        <?php echo render_icon($item['icon']); ?>
                    </span>
                    <span class="font-medium text-sm"><?php echo htmlspecialchars($item['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 bg-brand-900/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 font-bold border border-slate-700">
                    <?php echo strtoupper(substr($user['name'] ?? $user['email'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate"><?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></p>
                    <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="profile.php" class="flex-1 text-center py-1.5 px-3 rounded bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300 hover:text-white transition-colors border border-slate-700">Profile</a>
                <a href="logout.php" class="flex-1 text-center py-1.5 px-3 rounded bg-red-900/30 hover:bg-red-900/50 text-xs font-medium text-red-400 hover:text-red-300 transition-colors border border-red-900/50">Sign out</a>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-brand-50">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shadow-sm z-10 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none p-1 rounded-md hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight hidden sm:block">
                    <?php echo htmlspecialchars($navItems[$currentPage]['label'] ?? 'Dashboard'); ?>
                </h2>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="../index.php" target="_blank" class="hidden sm:flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-full border border-slate-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    View Live Site
                </a>
                
                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-700 hidden md:block"><?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></span>
                    <div class="w-8 h-8 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center text-blue-700 font-bold text-sm shadow-sm">
                        <?php echo strtoupper(substr($user['name'] ?? $user['email'] ?? 'A', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-8">
