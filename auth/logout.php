<?php
require_once __DIR__ . '/../includes/auth.php';
if (is_login()) audit('Logout dari sistem', 'users', user()['id']);
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
redirect(base_url('auth/login.php'));
