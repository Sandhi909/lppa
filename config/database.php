<?php
// Konfigurasi koneksi database (PDO)
// Support .env tanpa composer: override via file .env di root project.
// Prioritas: environment variable > .env > default di bawah ini.
// File .env TIDAK di-commit (lihat .gitignore), contohnya di .env.example.

// Load .env sederhana jika ada
(function () {
    $envFile = __DIR__ . '/../.env';
    if (!file_exists($envFile)) return;
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';') continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim(trim($v), '"\'');
        if ($k === '') continue;
        if (getenv($k) === false) putenv("$k=$v");
        if (!isset($_ENV[$k])) $_ENV[$k] = $v;
        if (!isset($_SERVER[$k])) $_SERVER[$k] = $v;
    }
})();

if (!function_exists('lppa_env')) {
    function lppa_env(string $key, $default = null) {
        $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($v !== false && $v !== '' && $v !== null) ? $v : $default;
    }
}

if (!defined('DB_HOST')) define('DB_HOST', lppa_env('DB_HOST', '127.0.0.1'));
if (!defined('DB_PORT')) define('DB_PORT', lppa_env('DB_PORT', '3306'));
if (!defined('DB_NAME')) define('DB_NAME', lppa_env('DB_NAME', 'lppa_bimbel'));
if (!defined('DB_USER')) define('DB_USER', lppa_env('DB_USER', 'root'));
if (!defined('DB_PASS')) define('DB_PASS', lppa_env('DB_PASS', ''));
if (!defined('DB_CHARSET')) define('DB_CHARSET', lppa_env('DB_CHARSET', 'utf8mb4'));

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $port = (string) DB_PORT;
        $dsn = 'mysql:host=' . DB_HOST . ($port !== '' ? ';port=' . $port : '') . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}
