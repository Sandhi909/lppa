<?php
declare(strict_types=1);

/* ---------- Keamanan dasar ---------- */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
            http_response_code(419);
            die('CSRF token tidak valid. Muat ulang halaman.');
        }
    }
}

/* ---------- Flash & redirect ---------- */
function flash(string $type, string $msg): void { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }
function get_flash(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function redirect(string $url): void {
    // Strip .php untuk clean URL (juga di tengah query string)
    $url = preg_replace('/\.php(?=\?|$)/', '', $url);
    header('Location: ' . $url);
    exit;
}

/* ---------- Format ---------- */
function rupiah($n): string { return 'Rp' . number_format((float)$n, 0, ',', '.'); }
function poin_fmt($n): string { return rtrim(rtrim(number_format((float)$n, 1, ',', '.'), '0'), ','); }
function tgl_id(?string $d): string { return $d ? date('d/m/Y', strtotime($d)) : '-'; }

/* ---------- Status pembayaran (hanya angsuran & lunas) ---------- */
const STATUS_LIST = ['angsuran' => 'Angsuran', 'lunas' => 'Lunas'];

function status_normal(?string $s): string {
    $s = strtolower(trim((string)$s));
    return $s === 'lunas' ? 'lunas' : 'angsuran';
}
function status_label(?string $s): string { return STATUS_LIST[status_normal($s)]; }

function badge_status(string $s): string {
    $key = strtolower($s);
    $map = ['lunas' => 'success', 'angsuran' => 'warning', 'aktif' => 'success', 'nonaktif' => 'danger'];
    $cls = $map[$key] ?? 'secondary';
    $txt = isset(STATUS_LIST[$key]) ? STATUS_LIST[$key] : $s;
    return '<span class="badge bg-' . $cls . '-soft">' . e($txt) . '</span>';
}

/* ---------- Unit ---------- */
const UNIT_TETAP = ['SD', 'SMP', 'SMA'];

/** Daftar unit tetap: [id => nama] sesuai urutan baku */
function unit_map(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $rows = db()->query("SELECT id, nama_unit FROM units WHERE status='aktif'")->fetchAll();
    $byName = [];
    foreach ($rows as $r) $byName[$r['nama_unit']] = (int)$r['id'];
    $cache = [];
    foreach (UNIT_TETAP as $n) if (isset($byName[$n])) $cache[$byName[$n]] = $n;
    return $cache;
}
function unit_nama(?int $id): string { return $id ? (unit_map()[$id] ?? '') : ''; }
function unit_id_dari_nama(string $nama): ?int {
    foreach (unit_map() as $id => $n) if (strcasecmp($n, trim($nama)) === 0) return $id;
    return null;
}
/** Opsi unit untuk <select> */
function unit_options(?int $selected): string {
    $h = '<option value="">-- Pilih Unit --</option>';
    foreach (unit_map() as $id => $nama) {
        $sel = ((int)$selected === $id) ? ' selected' : '';
        $h .= '<option value="' . $id . '"' . $sel . '>' . e($nama) . '</option>';
    }
    return $h;
}

/* ---------- Triwulan ---------- */
const TRIWULAN_LIST = ['I' => 'I', 'II' => 'II', 'III' => 'III', 'IV' => 'IV'];

/** Opsi triwulan untuk <select> */
function triwulan_options(?string $selected): string {
    $h = '<option value="">-- Pilih Triwulan --</option>';
    foreach (TRIWULAN_LIST as $k => $v) {
        $sel = ($selected === $k) ? ' selected' : '';
        $h .= '<option value="' . $k . '"' . $sel . '>' . e($v) . '</option>';
    }
    return $h;
}

/* ---------- Audit ---------- */
function audit(string $aktivitas, string $tabel = '', $data_id = null, $lama = null, $baru = null): void {
    $userId = $_SESSION['user']['id'] ?? null;
    
    // Validasi user_id - hanya gunakan jika user masih ada di tabel users
    if ($userId !== null) {
        $check = db()->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
        $check->execute([$userId]);
        if ($check->fetch() === false) {
            $userId = null; // User tidak ditemukan, gunakan NULL
        }
    }
    
    $st = db()->prepare('INSERT INTO audit_logs (user_id, aktivitas, tabel, data_id, data_lama, data_baru) VALUES (?,?,?,?,?,?)');
    $st->execute([
        $userId, $aktivitas, $tabel, $data_id,
        $lama !== null ? json_encode($lama, JSON_UNESCAPED_UNICODE) : null,
        $baru !== null ? json_encode($baru, JSON_UNESCAPED_UNICODE) : null,
    ]);
}

/* ---------- Bisnis: bagi hasil Yayasan (per Mitra) ---------- */
/**
 * Persentase bagi hasil Yayasan milik SATU Mitra (kolom partners.persentase_bagi_hasil).
 * Tidak ada lagi persentase global: setiap Mitra memiliki nilainya sendiri.
 */
function persentase_mitra($partnerId): float {
    static $cache = [];
    $partnerId = (int)$partnerId;
    if ($partnerId <= 0) return 0.0;
    if (array_key_exists($partnerId, $cache)) return $cache[$partnerId];
    $st = db()->prepare('SELECT persentase_bagi_hasil FROM partners WHERE id = ?');
    $st->execute([$partnerId]);
    $v = $st->fetchColumn();
    return $cache[$partnerId] = ($v !== false ? (float)$v : 0.0);
}
/** Pendapatan Yayasan = Pendapatan Siswa x Persentase Bagi Hasil Mitra / 100 - Pajak */
function pendapatan_yayasan(float $pendapatan, $partnerId): float {
    $bagiHasil = $pendapatan * persentase_mitra($partnerId) / 100;
    return hitung_total_setoran($bagiHasil, $partnerId);
}

/** Get tax configuration for a partner (with academic_year_id support) */
function get_tax_config($partnerId, ?int $academicYearId = null): array {
    static $cache = [];
    $partnerId = (int)$partnerId;
    if ($partnerId <= 0) return ['enabled' => false, 'percentage' => 0, 'method' => 'none', 'manual_nominal' => 0];
    
    $cacheKey = $partnerId . '_' . ($academicYearId ?? 'null');
    if (array_key_exists($cacheKey, $cache)) return $cache[$cacheKey];
    
    // Try to get from partner_tax_config first (year-specific)
    if ($academicYearId) {
        $st = db()->prepare('SELECT tax_enabled, tax_percentage, tax_method, tax_manual_nominal FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
        $st->execute([$partnerId, $academicYearId]);
        $row = $st->fetch();
        if ($row) {
            return $cache[$cacheKey] = [
                'enabled' => (bool)$row['tax_enabled'],
                'percentage' => (float)$row['tax_percentage'],
                'method' => $row['tax_method'],
                'manual_nominal' => (float)$row['tax_manual_nominal']
            ];
        }
    }
    
    // Fallback to partners table (global)
    $st = db()->prepare('SELECT tax_enabled, tax_percentage, tax_method, tax_manual_nominal FROM partners WHERE id = ?');
    $st->execute([$partnerId]);
    $row = $st->fetch();
    if (!$row) {
        return $cache[$cacheKey] = ['enabled' => false, 'percentage' => 0, 'method' => 'none', 'manual_nominal' => 0];
    }
    return $cache[$cacheKey] = [
        'enabled' => (bool)$row['tax_enabled'],
        'percentage' => (float)$row['tax_percentage'],
        'method' => $row['tax_method'],
        'manual_nominal' => (float)$row['tax_manual_nominal']
    ];
}

/** Calculate tax amount from bagi hasil yayasan */
function hitung_pajak(float $bagiHasil, $partnerId, ?int $academicYearId = null): float {
    if ($bagiHasil <= 0) return 0.0;
    $tax = get_tax_config($partnerId, $academicYearId);
    if (!$tax['enabled']) {
        return 0.0;
    }
    
    if ($tax['method'] === 'percentage') {
        return $bagiHasil * $tax['percentage'] / 100;
    } elseif ($tax['method'] === 'manual') {
        // Pajak manual nominal tetap, tapi 0 jika tidak ada bagi hasil; jangan melebihi bagi hasil
        return min($tax['manual_nominal'], $bagiHasil);
    }
    
    return 0.0;
}

/** Calculate total setoran after tax */
function hitung_total_setoran(float $bagiHasil, $partnerId, ?int $academicYearId = null): float {
    $pajak = hitung_pajak($bagiHasil, $partnerId, $academicYearId);
    return $bagiHasil - $pajak;
}
/** Format persentase: 15.00 -> "15", 12.50 -> "12,5" */
function persen_fmt($n): string {
    $s = number_format((float)$n, 2, ',', '.');
    if (strpos($s, ',') !== false) $s = rtrim(rtrim($s, '0'), ',');
    return $s;
}
/** Daftar persentase seluruh Mitra: [partner_id => persentase] */
function persentase_semua_mitra(): array {
    $out = [];
    foreach (db()->query('SELECT id, persentase_bagi_hasil FROM partners')->fetchAll() as $r) {
        $out[(int)$r['id']] = (float)$r['persentase_bagi_hasil'];
    }
    return $out;
}

/* ---------- Bisnis: validasi data siswa ---------- */
/**
 * Validasi nominal biaya.
 * - Biaya Pendaftaran dan Biaya Program tidak boleh negatif
 * - Total Biaya dihitung otomatis = Biaya Pendaftaran + Biaya Program
 */
function validasi_biaya(float $biayaPendaftaran, float $biayaProgram): array {
    $err = [];
    if ($biayaPendaftaran < 0) $err[] = 'Biaya Pendaftaran tidak boleh negatif.';
    if ($biayaProgram < 0) $err[] = 'Biaya Program tidak boleh negatif.';
    return $err;
}
/** Validasi aturan Wali Santri berdasarkan field is_psam (backend, tidak hanya JavaScript) */
function validasi_wali_santri(string $isPsam, $waliSantriId): array {
    $err = [];
    if ($isPsam === 'Ya') {
        if (!$waliSantriId) $err[] = 'Untuk siswa PSAM, data Wali Santri wajib diisi.';
    } else {
        // Jika bukan PSAM, Wali Santri tidak disimpan
    }
    return $err;
}
/** Wali Santri final yang boleh disimpan (non-PSAM selalu NULL) */
function wali_santri_tersimpan(string $isPsam, $waliSantriId): ?int {
    if ($isPsam !== 'Ya') return null;
    return $waliSantriId ? (int)$waliSantriId : null;
}

/* ---------- Bisnis: poin rekomendator ---------- */
function poin_wali_kelas(?int $wk, ?int $ws): float {
    if (!$wk) return 0;
    return $ws ? 0.5 : 1.0;
}
function poin_wali_santri(?int $ws): float { return $ws ? 1.0 : 0.0; }

function fee_dari_poin(float $poin, string $jenis, ?string $tanggal = null, ?int $academic_year_id = null): array {
    $tanggal = $tanggal ?: date('Y-m-d');
    $ayCondition = '';
    $params = [$jenis, $tanggal, $tanggal, $poin, $poin];
    
    if ($academic_year_id !== null) {
        $ayCondition = 'AND (academic_year_id IS NULL OR academic_year_id = ?)';
        $params[] = $academic_year_id;
    }
    
    $st = db()->prepare("SELECT * FROM fee_rules
        WHERE status='aktif' AND jenis_rekomendator=? AND tanggal_mulai <= ?
          AND (tanggal_selesai IS NULL OR tanggal_selesai >= ?)
          $ayCondition
          AND poin_minimum <= ? AND (poin_maksimum IS NULL OR poin_maksimum >= ?)
        ORDER BY academic_year_id DESC, poin_minimum DESC LIMIT 1");
    $st->execute($params);
    $r = $st->fetch();
    if (!$r || $poin <= 0) return ['kategori' => '-', 'nominal' => 0.0, 'total' => 0.0];
    $nominal = (float)$r['nominal_fee']; // nominal fee PER POIN
    return ['kategori' => $r['kategori'] ?: ($r['poin_minimum'] . '-' . ($r['poin_maksimum'] ?? '+') . ' poin'),
            'nominal' => $nominal,
            'total'   => $poin * $nominal]; // Total Fee = Total Poin x Nominal Fee per Poin
}

/* ---------- Query helper ---------- */
function opsi(string $table, string $label = 'nama', string $where = "status='aktif'"): array {
    $sql = "SELECT id, $label AS nama FROM $table" . ($where ? " WHERE $where" : '') . " ORDER BY $label";
    return db()->query($sql)->fetchAll();
}
function select_options(array $rows, $selected, string $placeholder = '-- Pilih --'): string {
    $h = '<option value="">' . e($placeholder) . '</option>';
    foreach ($rows as $r) {
        $sel = ((string)$selected === (string)$r['id']) ? ' selected' : '';
        $h .= '<option value="' . e($r['id']) . '"' . $sel . '>' . e($r['nama']) . '</option>';
    }
    return $h;
}
/** Daftar Program Bimbel unik yang sudah pernah diinput (untuk filter) */
function daftar_program_bimbel(?int $partnerId = null): array {
    $sql = "SELECT DISTINCT program_bimbel FROM students WHERE program_bimbel IS NOT NULL AND program_bimbel <> ''";
    $p = [];
    if ($partnerId) { $sql .= ' AND partner_id = ?'; $p[] = $partnerId; }
    $sql .= ' ORDER BY program_bimbel';
    $st = db()->prepare($sql); $st->execute($p);
    return array_column($st->fetchAll(), 'program_bimbel');
}
