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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                            900: '#0f172a',
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>
<body class="bg-brand-50 font-sans antialiased text-slate-800 flex h-screen overflow-hidden">
    
    <!-- Left: Decorative Side -->
    <div class="hidden lg:flex w-1/2 bg-brand-950 relative overflow-hidden items-center justify-center flex-col p-12 text-center text-white">
        <!-- Abstract gradient blobs behind -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-40"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-40"></div>
        
        <div class="relative z-10 max-w-md mx-auto">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl mx-auto flex items-center justify-center text-3xl font-extrabold shadow-2xl shadow-blue-500/30 mb-8 border border-white/10">
                B
            </div>
            <h1 class="text-4xl font-bold mb-4 tracking-tight">Bridge Ministries International</h1>
            <p class="text-brand-100/70 text-lg">Central Administration Dashboard. Manage your content, events, sermons, and settings from one secure place.</p>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100">
            <div class="lg:hidden mb-8 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl mx-auto flex items-center justify-center text-white text-2xl font-extrabold shadow-lg mb-4">
                    B
                </div>
                <h1 class="text-2xl font-bold text-slate-900">BMI Admin</h1>
            </div>
            
            <h2 class="text-2xl font-bold text-slate-900 hidden lg:block">Welcome back</h2>
            <p class="text-sm text-slate-500 mt-1 hidden lg:block">Please sign in to your account.</p>

            <?php if ($error !== ''): ?>
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm flex gap-3 items-center">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-8 space-y-5" autocomplete="off">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400"
                           placeholder="you@example.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           placeholder="••••••••">
                </div>
                
                <button type="submit" class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2.5 font-semibold transition-colors shadow-sm shadow-blue-500/30 mt-2">
                    Sign in to Dashboard
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <a href="../index.php" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to website
                </a>
            </div>
        </div>
    </div>

</body>
</html>
