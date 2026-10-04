<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrf_check();
if (auth_check()) {
    audit('logout', 'user', auth_user()['id'], 'Signed out');
}
auth_logout();
header('Location: login.php?reason=signed_out');
exit;
