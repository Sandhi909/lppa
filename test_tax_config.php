<?php
require_once __DIR__ . '/config/database.php';

echo "=== SET TAX CONFIG TEST ===\n\n";

// Mitra PRIMAGAMA = ID 5
// TA 2024/2025 = ID 1
// TA 2025/2026 = ID 2

$partnerId = 5;

// Config 1: TA 2024/2025, Manual Rp500000
$ta1 = 1;
$result1 = db()->prepare('INSERT INTO partner_tax_config (partner_id, academic_year_id, tax_enabled, tax_method, tax_manual_nominal) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE tax_enabled=VALUES(tax_enabled), tax_method=VALUES(tax_method), tax_manual_nominal=VALUES(tax_manual_nominal)')
    ->execute([$partnerId, $ta1, 1, 'manual', 500000]);
echo "✓ Set Mitra 5, TA 1, Manual Rp500000\n";

// Config 2: TA 2025/2026, Percentage 15%
$ta2 = 2;
$result2 = db()->prepare('INSERT INTO partner_tax_config (partner_id, academic_year_id, tax_enabled, tax_method, tax_percentage) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE tax_enabled=VALUES(tax_enabled), tax_method=VALUES(tax_method), tax_percentage=VALUES(tax_percentage)')
    ->execute([$partnerId, $ta2, 1, 'percentage', 15]);
echo "✓ Set Mitra 5, TA 2, Percentage 15%\n";

echo "\n=== VERIFY CONFIG ===\n";
$configs = db()->query('SELECT pt.*, ay.nama_tahun_ajaran FROM partner_tax_config pt JOIN academic_years ay ON ay.id = pt.academic_year_id WHERE partner_id = 5 ORDER BY pt.academic_year_id')->fetchAll(PDO::FETCH_ASSOC);
foreach ($configs as $c) {
    echo "TA: {$c['nama_tahun_ajaran']}, Enabled: {$c['tax_enabled']}, Method: {$c['tax_method']}, Percentage: {$c['tax_percentage']}, Manual: {$c['tax_manual_nominal']}\n";
}

echo "\n=== TEST TAX CALCULATION ===\n";

// Include functions
require_once __DIR__ . '/includes/functions.php';

// Test 1: TA 2024/2025, Bagi Hasil Rp1000000, Pajak Manual Rp500000
$bagiHasil1 = 1000000;
$pajak1 = hitung_pajak($bagiHasil1, $partnerId, $ta1);
$setoran1 = hitung_total_setoran($bagiHasil1, $partnerId, $ta1);
echo "TA 2024/2025 (Manual): BH={$bagiHasil1}, Pajak={$pajak1}, Setoran={$setoran1}\n";
echo "  Expected: Pajak=500000, Setoran=500000\n";
echo "  Status: " . ($pajak1 == 500000 && $setoran1 == 500000 ? "✓ PASS" : "✗ FAIL") . "\n";

// Test 2: TA 2025/2026, Bagi Hasil Rp1000000, Pajak 15%
$bagiHasil2 = 1000000;
$pajak2 = hitung_pajak($bagiHasil2, $partnerId, $ta2);
$setoran2 = hitung_total_setoran($bagiHasil2, $partnerId, $ta2);
echo "TA 2025/2026 (Percentage): BH={$bagiHasil2}, Pajak={$pajak2}, Setoran={$setoran2}\n";
echo "  Expected: Pajak=150000, Setoran=850000\n";
echo "  Status: " . ($pajak2 == 150000 && $setoran2 == 850000 ? "✓ PASS" : "✗ FAIL") . "\n";

// Test 3: TA 2024/2025, Bagi Hasil Rp200000, Pajak Manual > BH (should be capped)
$bagiHasil3 = 200000;
$pajak3 = hitung_pajak($bagiHasil3, $partnerId, $ta1);
$setoran3 = hitung_total_setoran($bagiHasil3, $partnerId, $ta1);
echo "TA 2024/2025 (Manual, capped): BH={$bagiHasil3}, Pajak={$pajak3}, Setoran={$setoran3}\n";
echo "  Expected: Pajak=200000, Setoran=0\n";
echo "  Status: " . ($pajak3 == 200000 && $setoran3 == 0 ? "✓ PASS" : "✗ FAIL") . "\n";
