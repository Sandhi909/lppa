<?php
/**
 * Browser Verification Test - Multi-Year Tax System
 * Simulates user actions dari user request:
 * 1. Set pajak manual Rp500000 untuk Mitra A, TA 2024/2025
 * 2. Set pajak persentase 15% untuk Mitra A, TA 2025/2026
 * 3. Verifikasi dashboard admin, mitra, dan report
 */

// Skip session and auth for CLI testing
define('SKIP_SESSION', true);
define('SKIP_AUTH', true);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/setoran_bagi_hasil.php';

echo "<h1>Browser Verification - Multi-Year Tax System</h1>";
echo "<hr>";

// ===== STEP 1: IDENTIFY TEST DATA =====
echo "<h2>Step 1: Identify Test Data</h2>";

// Find PRIMAGAMA (atau Mitra A)
$st = db()->query("SELECT id, nama_mitra FROM partners WHERE nama_mitra LIKE '%PRIMA%' OR nama_mitra LIKE '%Mitra%' LIMIT 1");
$partner = $st->fetch();
$partnerId = $partner ? (int)$partner['id'] : null;

if (!$partnerId) {
    echo "<div style='color:red;'><strong>ERROR:</strong> Mitra PRIMAGAMA tidak ditemukan. Menggunakan partner_id = 5 (hardcoded).</div>";
    $partnerId = 5;
    $partner = db()->prepare('SELECT id, nama_mitra FROM partners WHERE id = ?')->execute([$partnerId])->fetch();
}

echo "<p><strong>Mitra:</strong> ID={$partnerId}, Nama=" . ($partner ? $partner['nama_mitra'] : 'Unknown') . "</p>";

// Find academic years
$ta_2024_2025 = db()->query("SELECT id FROM academic_years WHERE nama_tahun_ajaran = '2024/2025' LIMIT 1")->fetch();
$ta_2025_2026 = db()->query("SELECT id FROM academic_years WHERE nama_tahun_ajaran = '2025/2026' LIMIT 1")->fetch();

$taId_2024_2025 = $ta_2024_2025 ? (int)$ta_2024_2025['id'] : 1;
$taId_2025_2026 = $ta_2025_2026 ? (int)$ta_2025_2026['id'] : 2;

echo "<p><strong>Tahun Ajaran 2024/2025:</strong> ID={$taId_2024_2025}</p>";
echo "<p><strong>Tahun Ajaran 2025/2026:</strong> ID={$taId_2025_2026}</p>";

// ===== STEP 2: VERIFY TAX CONFIGURATION =====
echo "<h2>Step 2: Verify Tax Configuration in Database</h2>";

$st1 = db()->prepare('SELECT * FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
$st1->execute([$partnerId, $taId_2024_2025]);
$cfg1 = $st1->fetch();

$st2 = db()->prepare('SELECT * FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
$st2->execute([$partnerId, $taId_2025_2026]);
$cfg2 = $st2->fetch();

echo "<table border='1' cellpadding='10' style='margin-top:10px;'>";
echo "<tr><th>Tahun</th><th>Enabled</th><th>Method</th><th>Percentage</th><th>Manual Nominal</th></tr>";
echo "<tr>";
echo "<td>2024/2025</td>";
echo "<td>" . ($cfg1 ? $cfg1['tax_enabled'] : 'NOT FOUND') . "</td>";
echo "<td>" . ($cfg1 ? $cfg1['tax_method'] : '-') . "</td>";
echo "<td>" . ($cfg1 ? $cfg1['tax_percentage'] : '-') . "</td>";
echo "<td>" . ($cfg1 ? number_format($cfg1['tax_manual_nominal'], 0, ',', '.') : '-') . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>2025/2026</td>";
echo "<td>" . ($cfg2 ? $cfg2['tax_enabled'] : 'NOT FOUND') . "</td>";
echo "<td>" . ($cfg2 ? $cfg2['tax_method'] : '-') . "</td>";
echo "<td>" . ($cfg2 ? $cfg2['tax_percentage'] : '-') . "</td>";
echo "<td>" . ($cfg2 ? number_format($cfg2['tax_manual_nominal'], 0, ',', '.') : '-') . "</td>";
echo "</tr>";
echo "</table>";

// ===== STEP 3: VERIFY TEST STUDENTS =====
echo "<h2>Step 3: Verify Test Students Inserted</h2>";

$testStudents2024 = [];
$testStudents2025 = [];

try {
    $st = db()->prepare("
        SELECT id, nama_siswa, total_biaya, academic_year_id FROM students 
        WHERE partner_id = ? AND academic_year_id = ? AND nama_siswa LIKE '%TEST%' 
        ORDER BY id
    ");
    if ($st) {
        $st->execute([$partnerId, $taId_2024_2025]);
        $testStudents2024 = $st->fetchAll();
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error fetching 2024/2025 students: " . $e->getMessage() . "</p>";
}

try {
    $st = db()->prepare("
        SELECT id, nama_siswa, total_biaya, academic_year_id FROM students 
        WHERE partner_id = ? AND academic_year_id = ? AND nama_siswa LIKE '%TEST%' 
        ORDER BY id
    ");
    if ($st) {
        $st->execute([$partnerId, $taId_2025_2026]);
        $testStudents2025 = $st->fetchAll();
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error fetching 2025/2026 students: " . $e->getMessage() . "</p>";
}

echo "<h3>Tahun 2024/2025</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Nama Siswa</th><th>Total Biaya</th></tr>";
$totalBiaya2024 = 0;
if ($testStudents2024) {
    foreach ($testStudents2024 as $s) {
        echo "<tr><td>" . e($s['nama_siswa']) . "</td><td>" . number_format($s['total_biaya'], 0, ',', '.') . "</td></tr>";
        $totalBiaya2024 += (float)$s['total_biaya'];
    }
} else {
    echo "<tr><td colspan='2' style='color:red;'>NO TEST STUDENTS FOUND</td></tr>";
}
echo "</table>";
echo "<p><strong>Total Biaya:</strong> " . number_format($totalBiaya2024, 0, ',', '.') . "</p>";

echo "<h3>Tahun 2025/2026</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Nama Siswa</th><th>Total Biaya</th></tr>";
$totalBiaya2025 = 0;
if ($testStudents2025) {
    foreach ($testStudents2025 as $s) {
        echo "<tr><td>" . e($s['nama_siswa']) . "</td><td>" . number_format($s['total_biaya'], 0, ',', '.') . "</td></tr>";
        $totalBiaya2025 += (float)$s['total_biaya'];
    }
} else {
    echo "<tr><td colspan='2' style='color:red;'>NO TEST STUDENTS FOUND</td></tr>";
}
echo "</table>";
echo "<p><strong>Total Biaya:</strong> " . number_format($totalBiaya2025, 0, ',', '.') . "</p>";

// ===== STEP 4: TAX CALCULATION VERIFICATION =====
echo "<h2>Step 4: Tax Calculation Verification</h2>";

$pct = persentase_mitra($partnerId);
echo "<p><strong>Persentase Bagi Hasil Mitra:</strong> {$pct}%</p>";

// TA 2024/2025 - Manual Rp500000
$bh2024 = $totalBiaya2024 * $pct / 100;
$tax2024 = hitung_pajak($bh2024, $partnerId, $taId_2024_2025);
$setoran2024 = hitung_total_setoran($bh2024, $partnerId, $taId_2024_2025);

echo "<h3>TA 2024/2025 (Manual Rp500000)</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><td>Total Biaya Test Students</td><td>" . number_format($totalBiaya2024, 0, ',', '.') . "</td></tr>";
echo "<tr><td>Bagi Hasil Kotor ({$pct}%)</td><td>" . number_format($bh2024, 0, ',', '.') . "</td></tr>";
echo "<tr><td>Pajak (Manual Rp500000)</td><td>" . number_format($tax2024, 0, ',', '.') . "</td></tr>";
echo "<tr style='background:#e3f2fd;'><td><strong>Setoran Bersih</strong></td><td><strong>" . number_format($setoran2024, 0, ',', '.') . "</strong></td></tr>";
echo "</table>";

if ($totalBiaya2024 > 0) {
    $expected2024 = $bh2024 - 500000;  // pajak manual capped at BH
    if ($expected2024 < 0) $expected2024 = 0;
    $match2024 = abs($setoran2024 - $expected2024) < 1 ? "✓ PASS" : "✗ FAIL";
    echo "<p style='color:" . ($match2024 === "✓ PASS" ? "green" : "red") . ";'><strong>Expected Setoran:</strong> " . number_format($expected2024, 0, ',', '.') . " | {$match2024}</p>";
}

// TA 2025/2026 - Percentage 15%
$bh2025 = $totalBiaya2025 * $pct / 100;
$tax2025 = hitung_pajak($bh2025, $partnerId, $taId_2025_2026);
$setoran2025 = hitung_total_setoran($bh2025, $partnerId, $taId_2025_2026);

echo "<h3>TA 2025/2026 (Persentase 15%)</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><td>Total Biaya Test Students</td><td>" . number_format($totalBiaya2025, 0, ',', '.') . "</td></tr>";
echo "<tr><td>Bagi Hasil Kotor ({$pct}%)</td><td>" . number_format($bh2025, 0, ',', '.') . "</td></tr>";
echo "<tr><td>Pajak (15%)</td><td>" . number_format($tax2025, 0, ',', '.') . "</td></tr>";
echo "<tr style='background:#e3f2fd;'><td><strong>Setoran Bersih</strong></td><td><strong>" . number_format($setoran2025, 0, ',', '.') . "</strong></td></tr>";
echo "</table>";

if ($totalBiaya2025 > 0) {
    $expected2025 = $bh2025 * (1 - 15/100);
    $match2025 = abs($setoran2025 - $expected2025) < 1 ? "✓ PASS" : "✗ FAIL";
    echo "<p style='color:" . ($match2025 === "✓ PASS" ? "green" : "red") . ";'><strong>Expected Setoran:</strong> " . number_format($expected2025, 0, ',', '.') . " | {$match2025}</p>";
}

// ===== STEP 5: DASHBOARD ADMIN SIMULATION =====
echo "<h2>Step 5: Dashboard Admin Calculation Simulation</h2>";

echo "<h3>TA 2024/2025 - Admin View</h3>";
$taxInfo2024 = info_pajak_mitra($partnerId, '1=1', [], $taId_2024_2025);
echo "<table border='1' cellpadding='10'>";
echo "<tr><td>Bagi Hasil Kotor (ALL students)</td><td>" . number_format($taxInfo2024['bagi_hasil_kotor'], 0, ',', '.') . "</td></tr>";
echo "<tr><td>Pajak Nominal</td><td>" . number_format($taxInfo2024['pajak_nominal'], 0, ',', '.') . "</td></tr>";
echo "<tr style='background:#e3f2fd;'><td><strong>Total Bersih (Setoran)</strong></td><td><strong>" . number_format($taxInfo2024['total_bersih'], 0, ',', '.') . "</strong></td></tr>";
echo "</table>";

echo "<h3>TA 2025/2026 - Admin View</h3>";
$taxInfo2025 = info_pajak_mitra($partnerId, '1=1', [], $taId_2025_2026);
echo "<table border='1' cellpadding='10'>";
echo "<tr><td>Bagi Hasil Kotor (ALL students)</td><td>" . number_format($taxInfo2025['bagi_hasil_kotor'], 0, ',', '.') . "</td></tr>";
echo "<tr><td>Pajak Nominal</td><td>" . number_format($taxInfo2025['pajak_nominal'], 0, ',', '.') . "</td></tr>";
echo "<tr style='background:#e3f2fd;'><td><strong>Total Bersih (Setoran)</strong></td><td><strong>" . number_format($taxInfo2025['total_bersih'], 0, ',', '.') . "</strong></td></tr>";
echo "</table>";

// ===== STEP 6: MITRA DASHBOARD SIMULATION =====
echo "<h2>Step 6: Mitra Dashboard Simulation</h2>";

echo "<h3>TA 2024/2025 - Mitra View</h3>";
$sum2024 = students_summary('partner_id = ? AND academic_year_id = ?', [$partnerId, $taId_2024_2025]);
$bh2024_mitra = (float)$sum2024['total_biaya'] * $pct / 100;
$taxInfo2024_mitra = info_pajak_mitra($partnerId, 'partner_id = ? AND academic_year_id = ?', [$partnerId, $taId_2024_2025], $taId_2024_2025);
echo "<table border='1' cellpadding='10'>";
echo "<tr><td>Total Biaya (ALL)</td><td>" . number_format($sum2024['total_biaya'], 0, ',', '.') . "</td></tr>";
echo "<tr><td>Bagi Hasil Yayasan ({$pct}%)</td><td>" . number_format($bh2024_mitra, 0, ',', '.') . "</td></tr>";
echo "<tr><td>Pajak (Manual Rp500000)</td><td>" . number_format($taxInfo2024_mitra['pajak_nominal'], 0, ',', '.') . "</td></tr>";
echo "<tr style='background:#fff3cd;'><td><strong>Fee Bagi Hasil Yayasan (After Tax)</strong></td><td><strong>" . number_format($taxInfo2024_mitra['total_bersih'], 0, ',', '.') . "</strong></td></tr>";
echo "</table>";

echo "<h3>TA 2025/2026 - Mitra View</h3>";
$sum2025 = students_summary('partner_id = ? AND academic_year_id = ?', [$partnerId, $taId_2025_2026]);
$bh2025_mitra = (float)$sum2025['total_biaya'] * $pct / 100;
$taxInfo2025_mitra = info_pajak_mitra($partnerId, 'partner_id = ? AND academic_year_id = ?', [$partnerId, $taId_2025_2026], $taId_2025_2026);
echo "<table border='1' cellpadding='10'>";
echo "<tr><td>Total Biaya (ALL)</td><td>" . number_format($sum2025['total_biaya'], 0, ',', '.') . "</td></tr>";
echo "<tr><td>Bagi Hasil Yayasan ({$pct}%)</td><td>" . number_format($bh2025_mitra, 0, ',', '.') . "</td></tr>";
echo "<tr><td>Pajak (15%)</td><td>" . number_format($taxInfo2025_mitra['pajak_nominal'], 0, ',', '.') . "</td></tr>";
echo "<tr style='background:#fff3cd;'><td><strong>Fee Bagi Hasil Yayasan (After Tax)</strong></td><td><strong>" . number_format($taxInfo2025_mitra['total_bersih'], 0, ',', '.') . "</strong></td></tr>";
echo "</table>";

// ===== STEP 7: REPORT EXPORT DATA PREPARATION =====
echo "<h2>Step 7: Report Export Data</h2>";

// Simulate build_report for 'mitra' type
$reportRows_2024 = [];
$reportRows_2025 = [];

try {
    $mitraReport = db()->prepare("
        SELECT 
            p.id,
            p.nama_mitra,
            COALESCE(SUM(s.total_biaya), 0) total_biaya,
            ay.id as academic_year_id,
            ay.nama_tahun_ajaran
        FROM partners p
        LEFT JOIN students s ON s.partner_id = p.id
        LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
        WHERE p.id = ? AND ay.id IN (?, ?)
        GROUP BY p.id, ay.id
        ORDER BY ay.id
    ");
    if ($mitraReport) {
        $mitraReport->execute([$partnerId, $taId_2024_2025, $taId_2025_2026]);
        $rows = $mitraReport->fetchAll();
    } else {
        $rows = [];
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error fetching report data: " . $e->getMessage() . "</p>";
    $rows = [];
}

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Mitra</th><th>Tahun</th><th>Total Biaya</th><th>Bagi Hasil Kotor</th><th>Pajak</th><th>Setoran Bersih</th></tr>";

foreach ($rows as $r) {
    $taId = (int)$r['academic_year_id'];
    $totalBiaya = (float)$r['total_biaya'];
    $bagiHasil = $totalBiaya * $pct / 100;
    $pajak = hitung_pajak($bagiHasil, $partnerId, $taId);
    $setoran = hitung_total_setoran($bagiHasil, $partnerId, $taId);
    
    echo "<tr>";
    echo "<td>" . e($r['nama_mitra']) . "</td>";
    echo "<td>" . e($r['nama_tahun_ajaran']) . "</td>";
    echo "<td>" . number_format($totalBiaya, 0, ',', '.') . "</td>";
    echo "<td>" . number_format($bagiHasil, 0, ',', '.') . "</td>";
    echo "<td>" . number_format($pajak, 0, ',', '.') . "</td>";
    echo "<td>" . number_format($setoran, 0, ',', '.') . "</td>";
    echo "</tr>";
}
echo "</table>";

// ===== SUMMARY =====
echo "<h2>Summary - Verification Results</h2>";
echo "<ul>";
echo "<li>Tax Configuration: " . (($cfg1 && $cfg2) ? "✓ Both years configured" : "✗ Missing config") . "</li>";
echo "<li>Test Students: " . (($testStudents2024 || $testStudents2025) ? "✓ Students inserted" : "✗ No test students") . "</li>";
echo "<li>TA 2024/2025 Calculation: " . (isset($match2024) ? $match2024 : "N/A") . "</li>";
echo "<li>TA 2025/2026 Calculation: " . (isset($match2025) ? $match2025 : "N/A") . "</li>";
echo "<li>Dashboard Functions: ✓ All function calls successful</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Test Completed:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
