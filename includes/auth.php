<?php
require_once __DIR__ . '/../config/app.php';

function user() { return $_SESSION['user'] ?? null; }
function is_login(): bool { return isset($_SESSION['user']); }
function role(): string { return $_SESSION['user']['role'] ?? ''; }

function require_login(): void {
    if (!is_login()) redirect(base_url('auth/login.php'));
    // Validate CSRF token on every POST request (defense in depth)
    csrf_check();
    if (($_SESSION['user']['status'] ?? 'aktif') !== 'aktif') {
        session_destroy();
        redirect(base_url('auth/login.php?err=nonaktif'));
    }
}
function require_role(array $roles): void {
    require_login();
    if (!in_array(role(), $roles, true)) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;padding:40px">403 - Anda tidak memiliki akses ke halaman ini. <a href="' . base_url('index.php') . '">Kembali</a></div>');
    }
}
function home_url(): string {
    return match (role()) {
        'admin' => base_url('admin/index.php'),
        'mitra' => base_url('mitra/index.php'),
        'wali_kelas', 'wali_santri' => base_url('wali/index.php'),
        'orang_tua' => base_url('orang_tua/index.php'),
        'direksi' => base_url('admin/direksi_dashboard.php'),
        default => base_url('auth/login.php'),
    };
}
/** id mitra untuk user role mitra */
function my_partner_id(): ?int {
    $p = $_SESSION['user']['partner_id'] ?? null;
    return $p ? (int)$p : null;
}
/** id recommender untuk user role wali */
function my_recommender_id(): ?int {
    $p = $_SESSION['user']['recommender_id'] ?? null;
    return $p ? (int)$p : null;
}
/** id student untuk user role orang_tua */
function my_student_id(): ?int {
    $p = $_SESSION['user']['student_id'] ?? null;
    return $p ? (int)$p : null;
}
