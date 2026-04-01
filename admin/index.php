<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Bridge Ministries International</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold">Admin Dashboard (Starter)</h1>
        <p class="mt-2 text-slate-600">This is the initial admin area placeholder. Authentication and CRUD modules are next.</p>

        <div class="mt-6 grid md:grid-cols-2 gap-4">
            <a href="#" class="bg-white rounded border border-slate-200 p-4 block">Manage Sermons</a>
            <a href="#" class="bg-white rounded border border-slate-200 p-4 block">Manage Ministries</a>
            <a href="#" class="bg-white rounded border border-slate-200 p-4 block">Manage Events</a>
            <a href="#" class="bg-white rounded border border-slate-200 p-4 block">Manage Posts</a>
        </div>
    </div>
</body>
</html>
