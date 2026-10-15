<?php
require_once __DIR__ . '/config/database.php';

echo "=== MULTI-YEAR TAX SYSTEM TEST ===\n\n";

// Cek mitra yang ada
echo "=== MITRA ===\n";
$partners = db()->query('SELECT id, nama_mitra, status FROM partners ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
foreach ($partners as $p) {
    echo "ID: {$p['id']}, Nama: {$p['nama_mitra']}, Status: {$p['status']}\n";
}

echo "\n=== TAHUN AJARAN ===\n";
$years = db()->query('SELECT id, nama_tahun_ajaran FROM academic_years ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($years as $y) {
    echo "ID: {$y['id']}, Nama: {$y['nama_tahun_ajaran']}\n";
}

echo "\n=== TAX CONFIG (partner_tax_config) ===\n";
$taxconfigs = db()->query('SELECT pt.*, ay.nama_tahun_ajaran, p.nama_mitra 
    FROM partner_tax_config pt 
    JOIN academic_years ay ON ay.id = pt.academic_year_id 
    JOIN partners p ON p.id = pt.partner_id
    ORDER BY p.id, ay.id')->fetchAll(PDO::FETCH_ASSOC);
if ($taxconfigs) {
    foreach ($taxconfigs as $t) {
        echo "Mitra: {$t['nama_mitra']}, TA: {$t['nama_tahun_ajaran']}, Enabled: " . (int)$t['tax_enabled'] . ", Method: {$t['tax_method']}, Percentage: {$t['tax_percentage']}, Manual: {$t['tax_manual_nominal']}\n";
    }
} else {
    echo "No tax config found.\n";
}

echo "\n=== STUDENTS ===\n";
$students = db()->query('SELECT s.id, s.nama_siswa, p.nama_mitra, ay.nama_tahun_ajaran, s.total_biaya 
    FROM students s 
    JOIN partners p ON p.id = s.partner_id 
    LEFT JOIN academic_years ay ON ay.id = s.academic_year_id
    ORDER BY s.partner_id, s.academic_year_id')->fetchAll(PDO::FETCH_ASSOC);
if ($students) {
    foreach ($students as $s) {
        echo "ID: {$s['id']}, Nama: {$s['nama_siswa']}, Mitra: {$s['nama_mitra']}, TA: {$s['nama_tahun_ajaran']}, Total Biaya: {$s['total_biaya']}\n";
    }
} else {
    echo "No students found.\n";
}

echo "\n=== EXPECTED TEST SCENARIO ===\n";
echo "Scenario 1: Mitra A, TA 2024/2025 -> Manual Rp500000\n";
echo "Scenario 2: Mitra A, TA 2025/2026 -> Percentage 15%\n";
echo "Scenario 3: Student 1 -> Mitra A, TA 2024/2025, Total Biaya Rp2000000 -> Pajak Manual Rp500000\n";
echo "Scenario 4: Student 2 -> Mitra A, TA 2025/2026, Total Biaya Rp3000000 -> Pajak 15% dari bagi hasil\n";
echo "Expected bagi hasil (2024/2025) = Rp2000000 * persentase_mitra / 100\n";
echo "Expected bagi hasil (2025/2026) = Rp3000000 * persentase_mitra / 100\n";

echo "\n=== VERIFICATION TEST ===\n";

// Ambil mitra pertama (Mitra A)
$partnerId = $partners[0]['id'];
echo "Using partner_id: {$partnerId}\n";

// Ambil dua tahun ajaran terakhir
$ta2024 = null;
$ta2025 = null;
foreach ($years as $y) {
    if (stripos($y['nama_tahun_ajaran'], '2024') !== false) $ta2024 = $y['id'];
    if (stripos($y['nama_tahun_ajaran'], '2025') !== false) $ta2025 = $y['id'];
}

if (!$ta2024) $ta2024 = $years[0]['id'];
if (!$ta2025 && count($years) > 1) $ta2025 = $years[1]['id'];

echo "TA 2024/2025 ID: " . ($ta2024 ?: 'NOT FOUND') . "\n";
echo "TA 2025/2026 ID: " . ($ta2025 ?: 'NOT FOUND') . "\n";

if ($ta2024) {
    $tax2024 = db()->prepare('SELECT * FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
    $tax2024->execute([$partnerId, $ta2024]);
    $t2024 = $tax2024->fetch();
    echo "Tax 2024/2025: " . ($t2024 ? "Enabled=" . (int)$t2024['tax_enabled'] . ", Method={$t2024['tax_method']}, Nominal={$t2024['tax_manual_nominal']}" : 'NOT FOUND') . "\n";
}

if ($ta2025) {
    $tax2025 = db()->prepare('SELECT * FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
    $tax2025->execute([$partnerId, $ta2025]);
    $t2025 = $tax2025->fetch();
    echo "Tax 2025/2026: " . ($t2025 ? "Enabled=" . (int)$t2025['tax_enabled'] . ", Method={$t2025['tax_method']}, Percentage={$t2025['tax_percentage']}, Nominal={$t2025['tax_manual_nominal']}" : 'NOT FOUND') . "\n";
}

echo "\n=== END TEST ===\n";
