<?php
declare(strict_types=1);

define('APP_NAME', 'SKB LPPA');
define('APP_SHORT', 'SKB LPPA');
define('COPYRIGHT_HOLDER', 'DPUB YAB');
date_default_timezone_set('Asia/Jakarta');

// BASE_URL otomatis (mendukung XAMPP subfolder maupun shared hosting)
function base_url(string $path = ''): string {
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    // naik satu level jika berada di dalam folder modul
    $parts = explode('/', trim($script, '/'));
    $last  = end($parts);
    if (in_array($last, ['admin', 'mitra', 'wali', 'orang_tua', 'auth', 'reports'], true)) {
        array_pop($parts);
    }
    $base = '/' . implode('/', array_filter($parts));
    $base = rtrim($base, '/');
    // Clean URL: hilangkan ekstensi .php dari path tautan internal
    if ($path !== '') {
        $qpos = strpos($path, '?');
        $pathOnly = $qpos !== false ? substr($path, 0, $qpos) : $path;
        $queryStr = $qpos !== false ? substr($path, $qpos) : '';
        if (substr($pathOnly, -4) === '.php') {
            $pathOnly = substr($pathOnly, 0, -4);
        }
        $path = $pathOnly . $queryStr;
    }
    return $base . '/' . ltrim($path, '/');
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
