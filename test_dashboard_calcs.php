<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

echo "=== TEST DASHBOARD ADMIN CALCULATIONS ===\n\n";

// Mitra PRIMAGAMA = 5
// Persentase bagi hasil = ?
$mitra = db()->query('SELECT id, nama_mitra, persentase_bagi_hasil FROM partners WHERE id = 5')->fetch();
$percentage = (float)$mitra['persentase_bagi_hasil'];

echo "Mitra: {$mitra['nama_mitra']}\n";
echo "Persentase Bagi Hasil: {$percentage}%\n\n";

// Get students and calculate bagi hasil + pajak per tahun
$students = db()->query('SELECT s.id, s.nama_siswa, s.total_biaya, ay.id as ay_id, ay.nama_tahun_ajaran FROM students s JOIN academic_years ay ON ay.id = s.academic_year_id WHERE s.partner_id = 5 AND s.nama_siswa LIKE "SISWA TEST%"')->fetchAll(PDO::FETCH_ASSOC);

$summary = [];
foreach ($students as $st) {
    $bagiHasil = $st['total_biaya'] * $percentage / 100;
    $pajak = hitung_pajak($bagiHasil, 5, $st['ay_id']);
    $setoran = hitung_total_setoran($bagiHasil, 5, $st['ay_id']);
    
    $ta = $st['nama_tahun_ajaran'];
    if (!isset($summary[$ta])) {
        $summary[$ta] = [
            'ay_id' => $st['ay_id'],
            'total_biaya' => 0,
            'total_bagi_hasil' => 0,
            'total_pajak' => 0,
            'total_setoran' => 0,
            'students' => []
        ];
    }
    
    $summary[$ta]['total_biaya'] += $st['total_biaya'];
    $summary[$ta]['total_bagi_hasil'] += $bagiHasil;
    $summary[$ta]['total_pajak'] += $pajak;
    $summary[$ta]['total_setoran'] += $setoran;
    $summary[$ta]['students'][] = [
        'nama' => $st['nama_siswa'],
        'total_biaya' => $st['total_biaya'],
        'bagi_hasil' => $bagiHasil,
        'pajak' => $pajak,
        'setoran' => $setoran
    ];
}

echo "=== SUMMARY PER TAHUN AJARAN ===\n";
foreach ($summary as $ta => $data) {
    echo "\n{$ta}:\n";
    echo "  Total Biaya: {$data['total_biaya']}\n";
    echo "  Total Bagi Hasil: {$data['total_bagi_hasil']}\n";
    echo "  Total Pajak: {$data['total_pajak']}\n";
    echo "  Total Setoran: {$data['total_setoran']}\n";
    echo "  Detail Siswa:\n";
    foreach ($data['students'] as $s) {
        echo "    - {$s['nama']}: BH={$s['bagi_hasil']}, Pajak={$s['pajak']}, Setoran={$s['setoran']}\n";
    }
}

// Get tax config for verification
echo "\n=== TAX CONFIG VERIFICATION ===\n";
$configs = db()->query('SELECT pt.*, ay.nama_tahun_ajaran FROM partner_tax_config pt JOIN academic_years ay ON ay.id = pt.academic_year_id WHERE pt.partner_id = 5')->fetchAll(PDO::FETCH_ASSOC);
foreach ($configs as $c) {
    echo "TA {$c['nama_tahun_ajaran']}: Method={$c['tax_method']}, Percentage={$c['tax_percentage']}, Manual={$c['tax_manual_nominal']}\n";
}

// Expected test results
echo "\n=== EXPECTED RESULTS ===\n";
echo "TA 2024/2025 (Manual Rp500000):\n";
echo "  SISWA TEST 1: Biaya=2000000, BH=" . (2000000 * $percentage / 100) . ", Pajak=500000, Setoran=" . (2000000 * $percentage / 100 - 500000) . "\n";

echo "\nTA 2025/2026 (Percentage 15%):\n";
$bh2 = 3000000 * $percentage / 100;
$pajak2 = $bh2 * 15 / 100;
echo "  SISWA TEST 2: Biaya=3000000, BH=" . $bh2 . ", Pajak=" . $pajak2 . ", Setoran=" . ($bh2 - $pajak2) . "\n";
$bh3 = 2000000 * $percentage / 100;
$pajak3 = $bh3 * 15 / 100;
echo "  SISWA TEST 3: Biaya=2000000, BH=" . $bh3 . ", Pajak=" . $pajak3 . ", Setoran=" . ($bh3 - $pajak3) . "\n";
echo "  Total 2025/2026: BH=" . ($bh2 + $bh3) . ", Pajak=" . ($pajak2 + $pajak3) . ", Setoran=" . (($bh2 + $bh3) - ($pajak2 + $pajak3)) . "\n";
