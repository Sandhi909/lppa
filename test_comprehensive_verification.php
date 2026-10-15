<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/setoran_bagi_hasil.php';

echo "=== COMPREHENSIVE MULTI-YEAR TAX VERIFICATION ===\n\n";

$partnerId = 5; // PRIMAGAMA
$partnerName = 'PRIMAGAMA';

echo "Testing Mitra: {$partnerName} (ID: {$partnerId})\n\n";

// Get partner percentage
$partner = db()->query("SELECT persentase_bagi_hasil FROM partners WHERE id = {$partnerId}")->fetch();
$bhPercentage = (float)$partner['persentase_bagi_hasil'];
echo "Persentase Bagi Hasil Mitra: {$bhPercentage}%\n\n";

// Test scenarios
$scenarios = [
    ['ta_id' => 1, 'ta_name' => '2024/2025', 'expected_tax_method' => 'manual', 'expected_tax_value' => 500000],
    ['ta_id' => 2, 'ta_name' => '2025/2026', 'expected_tax_method' => 'percentage', 'expected_tax_value' => 15],
];

$allPass = true;

foreach ($scenarios as $scenario) {
    $taId = $scenario['ta_id'];
    $taName = $scenario['ta_name'];
    
    echo "=== TESTING TA: {$taName} ===\n";
    
    // 1. Verify tax config
    $taxConfig = get_tax_config($partnerId, $taId);
    echo "1. Tax Config:\n";
    echo "   Method: {$taxConfig['method']}\n";
    echo "   Percentage: {$taxConfig['percentage']}%\n";
    echo "   Manual Nominal: Rp" . number_format($taxConfig['manual_nominal'], 0, ',', '.') . "\n";
    
    $configPass = ($taxConfig['method'] === $scenario['expected_tax_method']);
    echo "   Status: " . ($configPass ? "✓ PASS" : "✗ FAIL - Expected method: {$scenario['expected_tax_method']}") . "\n";
    $allPass = $allPass && $configPass;
    
    // 2. Get students for this TA
    $students = db()->prepare("SELECT id, nama_siswa, total_biaya FROM students WHERE partner_id = ? AND academic_year_id = ? AND nama_siswa LIKE 'SISWA TEST%'")->execute([$partnerId, $taId]);
    $students = db()->prepare("SELECT id, nama_siswa, total_biaya FROM students WHERE partner_id = ? AND academic_year_id = ? AND nama_siswa LIKE 'SISWA TEST%'");
    $students->execute([$partnerId, $taId]);
    $studentsList = $students->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n2. Students in TA {$taName}: " . count($studentsList) . "\n";
    
    $totalBH = 0;
    $totalPajak = 0;
    $totalSetoran = 0;
    
    foreach ($studentsList as $s) {
        $bh = $s['total_biaya'] * $bhPercentage / 100;
        $pajak = hitung_pajak($bh, $partnerId, $taId);
        $setoran = hitung_total_setoran($bh, $partnerId, $taId);
        
        $totalBH += $bh;
        $totalPajak += $pajak;
        $totalSetoran += $setoran;
        
        echo "   - {$s['nama_siswa']}: Total={$s['total_biaya']}, BH={$bh}, Pajak={$pajak}, Setoran={$setoran}\n";
    }
    
    // 3. Verify info_pajak_mitra calculation
    echo "\n3. Dashboard Admin Calculation (info_pajak_mitra):\n";
    $taxInfo = info_pajak_mitra($partnerId, '1=1', [], $taId);
    echo "   Bagi Hasil Kotor: {$taxInfo['bagi_hasil_kotor']}\n";
    echo "   Pajak Nominal: {$taxInfo['pajak_nominal']}\n";
    echo "   Total Bersih: {$taxInfo['total_bersih']}\n";
    
    $infoPass = (abs($taxInfo['bagi_hasil_kotor'] - $totalBH) < 0.01 && abs($taxInfo['pajak_nominal'] - $totalPajak) < 0.01);
    echo "   Status: " . ($infoPass ? "✓ PASS" : "✗ FAIL - Mismatch with manual calculation") . "\n";
    $allPass = $allPass && $infoPass;
    
    // 4. Expected results verification
    echo "\n4. Expected Tax Calculation:\n";
    if ($scenario['expected_tax_method'] === 'manual') {
        $expectedPajak = min($scenario['expected_tax_value'], $totalBH);
        echo "   Expected Pajak (Manual): Rp" . number_format($expectedPajak, 0, ',', '.') . "\n";
    } else {
        $expectedPajak = $totalBH * $scenario['expected_tax_value'] / 100;
        echo "   Expected Pajak ({$scenario['expected_tax_value']}%): Rp" . number_format($expectedPajak, 0, ',', '.') . "\n";
    }
    echo "   Actual Pajak: Rp" . number_format($totalPajak, 0, ',', '.') . "\n";
    
    $calcPass = (abs($expectedPajak - $totalPajak) < 0.01);
    echo "   Status: " . ($calcPass ? "✓ PASS" : "✗ FAIL") . "\n";
    $allPass = $allPass && $calcPass;
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// 5. Overall summary
echo "=== OVERALL TEST RESULT ===\n";
echo $allPass ? "✓ ALL TESTS PASSED\n" : "✗ SOME TESTS FAILED\n";
echo "\nSistem pajak multi-tahun per mitra: " . ($allPass ? "WORKING CORRECTLY" : "NEEDS FIXING") . "\n";

// 6. Instructions for manual verification
echo "\n=== MANUAL VERIFICATION STEPS ===\n";
echo "1. Dashboard Admin (admin/index.php):\n";
echo "   - Filter Tahun Ajaran: 2024/2025\n";
echo "   - Lihat row PRIMAGAMA: kolom Pajak dan Setoran Bersih\n";
echo "   - Filter Tahun Ajaran: 2025/2026\n";
echo "   - Lihat row PRIMAGAMA: kolom Pajak dan Setoran Bersih\n\n";
echo "2. Dashboard Mitra (mitra/index.php):\n";
echo "   - Login sebagai PRIMAGAMA\n";
echo "   - Filter Tahun Ajaran: 2024/2025 - lihat Fee Bagi Hasil Yayasan\n";
echo "   - Filter Tahun Ajaran: 2025/2026 - lihat Fee Bagi Hasil Yayasan\n\n";
echo "3. Export Report (reports/export.php?jenis_laporan=mitra):\n";
echo "   - Export dan cek kolom Bagi Hasil Kotor, Pajak, Setoran Bersih\n";
echo "   - Verifikasi per tahun ajaran\n";
