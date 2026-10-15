<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/setoran_bagi_hasil.php';

echo "=== MULTI-YEAR TAX FINAL VERIFICATION ===\n\n";

$partnerId = 5; // PRIMAGAMA
echo "Testing Mitra: PRIMAGAMA (ID: {$partnerId})\n\n";

// Get partner percentage
$partner = db()->query("SELECT persentase_bagi_hasil FROM partners WHERE id = {$partnerId}")->fetch();
$bhPercentage = (float)$partner['persentase_bagi_hasil'];
echo "Persentase Bagi Hasil: {$bhPercentage}%\n\n";

// Test scenarios
$scenarios = [
    ['ta_id' => 1, 'ta_name' => '2024/2025', 'tax_method' => 'manual', 'tax_value' => 500000],
    ['ta_id' => 2, 'ta_name' => '2025/2026', 'tax_method' => 'percentage', 'tax_value' => 15],
];

$allPass = true;

foreach ($scenarios as $scenario) {
    $taId = $scenario['ta_id'];
    $taName = $scenario['ta_name'];
    
    echo "=== TA: {$taName} ===\n";
    
    // 1. Tax config
    $taxConfig = get_tax_config($partnerId, $taId);
    echo "Tax Config: method={$taxConfig['method']}, percentage={$taxConfig['percentage']}%, manual={$taxConfig['manual_nominal']}\n";
    
    $configPass = ($taxConfig['method'] === $scenario['tax_method']);
    echo "Config Status: " . ($configPass ? "✓" : "✗") . "\n";
    $allPass = $allPass && $configPass;
    
    // 2. Get test students only
    $students = db()->prepare("SELECT nama_siswa, total_biaya FROM students WHERE partner_id = ? AND academic_year_id = ? AND nama_siswa LIKE 'SISWA TEST%'");
    $students->execute([$partnerId, $taId]);
    $studentsList = $students->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nTest Students ({$taName}): " . count($studentsList) . "\n";
    
    // Calculate AGGREGATE first, then tax
    $totalBiaya = 0;
    foreach ($studentsList as $s) {
        echo "  - {$s['nama_siswa']}: Rp" . number_format($s['total_biaya'], 0, ',', '.') . "\n";
        $totalBiaya += $s['total_biaya'];
    }
    
    $totalBH = $totalBiaya * $bhPercentage / 100;
    $pajak = hitung_pajak($totalBH, $partnerId, $taId);
    $setoran = hitung_total_setoran($totalBH, $partnerId, $taId);
    
    echo "\nCalculation (Test Students Only):\n";
    echo "  Total Biaya: Rp" . number_format($totalBiaya, 0, ',', '.') . "\n";
    echo "  Bagi Hasil: Rp" . number_format($totalBH, 0, ',', '.') . "\n";
    echo "  Pajak: Rp" . number_format($pajak, 0, ',', '.') . "\n";
    echo "  Setoran Bersih: Rp" . number_format($setoran, 0, ',', '.') . "\n";
    
    // 3. Verify expected
    if ($scenario['tax_method'] === 'manual') {
        $expectedPajak = min($scenario['tax_value'], $totalBH);
    } else {
        $expectedPajak = $totalBH * $scenario['tax_value'] / 100;
    }
    
    $calcPass = (abs($expectedPajak - $pajak) < 0.01);
    echo "Tax Calculation: " . ($calcPass ? "✓ PASS" : "✗ FAIL") . " (Expected: {$expectedPajak}, Actual: {$pajak})\n";
    $allPass = $allPass && $calcPass;
    
    // 4. info_pajak_mitra (ALL students for this mitra+year)
    $taxInfo = info_pajak_mitra($partnerId, '1=1', [], $taId);
    echo "\nDashboard Calculation (ALL PRIMAGAMA students in {$taName}):\n";
    echo "  Bagi Hasil Kotor: Rp" . number_format($taxInfo['bagi_hasil_kotor'], 0, ',', '.') . "\n";
    echo "  Pajak: Rp" . number_format($taxInfo['pajak_nominal'], 0, ',', '.') . "\n";
    echo "  Setoran Bersih: Rp" . number_format($taxInfo['total_bersih'], 0, ',', '.') . "\n";
    
    // Verify tax calculation consistency
    $expectedAllPajak = hitung_pajak($taxInfo['bagi_hasil_kotor'], $partnerId, $taId);
    $dashboardPass = (abs($expectedAllPajak - $taxInfo['pajak_nominal']) < 0.01);
    echo "Dashboard Consistency: " . ($dashboardPass ? "✓ PASS" : "✗ FAIL") . "\n";
    $allPass = $allPass && $dashboardPass;
    
    echo "\n" . str_repeat("-", 70) . "\n\n";
}

echo "=== RESULT ===\n";
if ($allPass) {
    echo "✓ ALL TESTS PASSED\n";
    echo "Sistem pajak multi-tahun bekerja dengan benar.\n";
} else {
    echo "✗ SOME TESTS FAILED\n";
    echo "Ada masalah dengan kalkulasi pajak.\n";
}

echo "\n=== NEXT: MANUAL BROWSER VERIFICATION ===\n";
echo "1. http://localhost/lppa/admin/index.php\n";
echo "   Filter: Tahun Ajaran 2024/2025 -> Lihat PRIMAGAMA row\n";
echo "   Filter: Tahun Ajaran 2025/2026 -> Lihat PRIMAGAMA row\n\n";
echo "2. http://localhost/lppa/admin/tax_per_mitra.php\n";
echo "   Lihat config untuk PRIMAGAMA per tahun\n\n";
echo "3. Login mitra dan cek dashboard mereka\n";
