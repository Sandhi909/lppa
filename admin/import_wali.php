<?php
/**
 * Script Import Otomatis Data Wali Kelas dan Wali Santri dari Excel
 * File ini hanya dijalankan sekali untuk import data awal
 */
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

$PAGE_TITLE = 'Import Data Wali Kelas & Wali Santri';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Import Data Wali']];
include __DIR__ . '/../includes/header.php';

// Cek apakah PhpSpreadsheet tersedia
$auto = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($auto)) {
    echo '<div class="alert alert-danger">PhpSpreadsheet tidak terinstall. Jalankan: composer require phpoffice/phpspreadsheet</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}
require_once $auto;

// Path file Excel
$excelFile = 'C:\\Users\\IT\\Downloads\\data walikelas dan wali santri.xlsx';

if (!file_exists($excelFile)) {
    echo '<div class="alert alert-danger">File Excel tidak ditemukan: ' . e($excelFile) . '</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, false);
    
    // Cari kolom
    $header = $data[0] ?? [];
    $colWaliSantri = null;
    $colWaliKelas = null;
    $colUnit = null;
    
    foreach ($header as $i => $h) {
        $h = strtoupper(trim($h));
        if ($h === 'WALI SANTRI') $colWaliSantri = $i;
        if ($h === 'WALI KELAS') $colWaliKelas = $i;
        if ($h === 'UNIT') $colUnit = $i;
    }
    
    if ($colWaliSantri === null && $colWaliKelas === null) {
        echo '<div class="alert alert-danger">Kolom "WALI SANTRI" atau "WALI KELAS" tidak ditemukan di header Excel.</div>';
        include __DIR__ . '/../includes/footer.php';
        exit;
    }
    
    // Ambil data (skip header)
    array_shift($data);
    
    // Filter unit yang valid untuk Wali Kelas
    $unitValid = ['SD', 'SMP', 'SMA'];
    $unitIds = [];
    $units = db()->query("SELECT id, nama_unit FROM units WHERE nama_unit IN ('SD','SMP','SMA')")->fetchAll();
    foreach ($units as $u) {
        $unitIds[strtoupper($u['nama_unit'])] = (int)$u['id'];
    }
    
    // Function generate username
    function generateUsername($nama) {
        // Hapus karakter khusus, convert ke lowercase
        $username = strtolower($nama);
        $username = preg_replace('/[^a-z\s]/', '', $username);
        // Ganti spasi dengan titik
        $username = preg_replace('/\s+/', '.', trim($username));
        // Hapus titik berlebih
        $username = preg_replace('/\.+/', '.', $username);
        return $username;
    }
    
    // Function cek duplikat username
    function usernameExists($username) {
        $st = db()->prepare('SELECT id FROM users WHERE username = ?');
        $st->execute([$username]);
        return $st->fetch() !== false;
    }
    
    // Function generate username unik
    function getUniqueUsername($baseUsername) {
        $username = $baseUsername;
        $counter = 2;
        while (usernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        return $username;
    }
    
    // Function cek duplikat nama di recommenders
    function recommenderExists($nama, $jenis) {
        $st = db()->prepare('SELECT id FROM recommenders WHERE nama = ? AND jenis = ?');
        $st->execute([$nama, $jenis]);
        return $st->fetch() !== false;
    }
    
    // Password default
    $defaultPassword = '12345';
    $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
    
    // Statistics
    $stats = [
        'wali_santri_created' => 0,
        'wali_santri_skipped' => 0,
        'wali_kelas_created' => 0,
        'wali_kelas_skipped' => 0,
        'total_accounts' => 0,
        'sample_accounts' => []
    ];
    
    // Process Wali Santri
    if ($colWaliSantri !== null) {
        foreach ($data as $row) {
            $nama = trim($row[$colWaliSantri] ?? '');
            if ($nama === '') continue;
            
            // Cek duplikat
            if (recommenderExists($nama, 'wali_santri')) {
                $stats['wali_santri_skipped']++;
                continue;
            }
            
            // Generate username
            $username = generateUsername($nama);
            $username = getUniqueUsername($username);
            
            // Insert ke users
            $st = db()->prepare('INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)');
            $st->execute([$nama, $username, $passwordHash, 'wali_santri']);
            $userId = (int)db()->lastInsertId();
            
            // Insert ke recommenders
            $st = db()->prepare('INSERT INTO recommenders (user_id, nama, jenis, status) VALUES (?, ?, ?, ?)');
            $st->execute([$userId, $nama, 'wali_santri', 'aktif']);
            
            $stats['wali_santri_created']++;
            $stats['total_accounts']++;
            
            if (count($stats['sample_accounts']) < 10) {
                $stats['sample_accounts'][] = [
                    'nama' => $nama,
                    'username' => $username,
                    'jenis' => 'Wali Santri'
                ];
            }
        }
    }
    
    // Process Wali Kelas
    if ($colWaliKelas !== null) {
        foreach ($data as $row) {
            $nama = trim($row[$colWaliKelas] ?? '');
            $unit = strtoupper(trim($row[$colUnit] ?? ''));
            
            if ($nama === '') continue;
            
            // Skip jika unit bukan SD, SMP, SMA
            if (!in_array($unit, $unitValid, true)) {
                $stats['wali_kelas_skipped']++;
                continue;
            }
            
            // Cek duplikat
            if (recommenderExists($nama, 'wali_kelas')) {
                $stats['wali_kelas_skipped']++;
                continue;
            }
            
            // Get unit_id
            $unitId = $unitIds[$unit] ?? null;
            if (!$unitId) {
                $stats['wali_kelas_skipped']++;
                continue;
            }
            
            // Generate username
            $username = generateUsername($nama);
            $username = getUniqueUsername($username);
            
            // Insert ke users
            $st = db()->prepare('INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)');
            $st->execute([$nama, $username, $passwordHash, 'wali_kelas']);
            $userId = (int)db()->lastInsertId();
            
            // Insert ke recommenders
            $st = db()->prepare('INSERT INTO recommenders (user_id, nama, unit_id, jenis, status) VALUES (?, ?, ?, ?, ?)');
            $st->execute([$userId, $nama, $unitId, 'wali_kelas', 'aktif']);
            
            $stats['wali_kelas_created']++;
            $stats['total_accounts']++;
            
            if (count($stats['sample_accounts']) < 10) {
                $stats['sample_accounts'][] = [
                    'nama' => $nama,
                    'username' => $username,
                    'jenis' => 'Wali Kelas',
                    'unit' => $unit
                ];
            }
        }
    }
    
    echo '<div class="card mb-3"><div class="card-header">Hasil Import Data Wali Kelas & Wali Santri</div><div class="card-body">';
    echo '<div class="row g-3">';
    echo '<div class="col-md-3"><div class="stat-card"><div class="label">Wali Santri Dibuat</div><div class="value">' . $stats['wali_santri_created'] . '</div></div></div>';
    echo '<div class="col-md-3"><div class="stat-card"><div class="label">Wali Santri Skip</div><div class="value">' . $stats['wali_santri_skipped'] . '</div></div></div>';
    echo '<div class="col-md-3"><div class="stat-card"><div class="label">Wali Kelas Dibuat</div><div class="value">' . $stats['wali_kelas_created'] . '</div></div></div>';
    echo '<div class="col-md-3"><div class="stat-card"><div class="label">Wali Kelas Skip</div><div class="value">' . $stats['wali_kelas_skipped'] . '</div></div></div>';
    echo '</div>';
    echo '<div class="alert alert-success mt-3"><strong>Total Akun Dibuat:</strong> ' . $stats['total_accounts'] . '</div>';
    
    if (!empty($stats['sample_accounts'])) {
        echo '<h5 class="mt-4">Contoh 10 Akun Pertama:</h5>';
        echo '<table class="table table-sm"><thead><tr><th>Nama</th><th>Username</th><th>Jenis</th><th>Unit</th></tr></thead><tbody>';
        foreach ($stats['sample_accounts'] as $acc) {
            echo '<tr><td>' . e($acc['nama']) . '</td><td>' . e($acc['username']) . '</td><td>' . e($acc['jenis']) . '</td><td>' . e($acc['unit'] ?? '-') . '</td></tr>';
        }
        echo '</tbody></table>';
    }
    
    echo '<div class="alert alert-info mt-3"><strong>Password Default:</strong> 12345 (untuk semua akun baru)</div>';
    echo '</div></div>';
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . e($e->getMessage()) . '</div>';
}

include __DIR__ . '/../includes/footer.php';
