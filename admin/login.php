<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if (auth_check()) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (auth_attempt($email, $password)) {
        $redirect = (string) ($_GET['redirect'] ?? 'index.php');
        $safe = filter_var($redirect, FILTER_VALIDATE_URL) ? 'index.php' : $redirect;
        header('Location: ' . $safe);
        exit;
    }
    $error = 'Invalid credentials, or too many failed attempts. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin Login | Bridge Ministries International</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-slate-900">Admin Login</h1>
        <p class="text-sm text-slate-600 mt-1">Bridge Ministries International</p>

        <?php if ($error !== ''): ?>
            <div class="mt-4 rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="mt-6 space-y-4" autocomplete="off">
            <?php echo csrf_field(); ?>
            <label class="block text-sm">
                <span class="font-medium">Email</span>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </label>
            <label class="block text-sm">
                <span class="font-medium">Password</span>
                <input type="password" name="password" required
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </label>
            <button type="submit" class="w-full rounded bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 font-semibold">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-xs text-slate-500"><a href="../index.php" class="hover:underline">&larr; Back to website</a></p>
    </div>
</body>
</html>
