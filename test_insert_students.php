<?php
require_once __DIR__ . '/config/database.php';

echo "=== INSERT TEST STUDENTS ===\n\n";

// Mitra PRIMAGAMA = 5
// TA 2024/2025 = 1
// TA 2025/2026 = 2

// Get PRIMAGAMA user_id
$mitra = db()->query('SELECT user_id FROM partners WHERE id = 5')->fetch();
$mitraUserId = $mitra['user_id'];

$students = [
    [
        'nama_siswa' => 'SISWA TEST 1',
        'academic_year_id' => 1,
        'partner_id' => 5,
        'biaya_pendaftaran' => 500000,
        'biaya_program' => 1500000,
    ],
    [
        'nama_siswa' => 'SISWA TEST 2',
        'academic_year_id' => 2,
        'partner_id' => 5,
        'biaya_pendaftaran' => 600000,
        'biaya_program' => 2400000,
    ],
    [
        'nama_siswa' => 'SISWA TEST 3',
        'academic_year_id' => 2,
        'partner_id' => 5,
        'biaya_pendaftaran' => 800000,
        'biaya_program' => 1200000,
    ],
];

foreach ($students as $s) {
    $totalBiaya = $s['biaya_pendaftaran'] + $s['biaya_program'];
    $result = db()->prepare('INSERT INTO students (nama_siswa, partner_id, academic_year_id, biaya_pendaftaran, biaya_program, total_biaya) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([
            $s['nama_siswa'],
            $s['partner_id'],
            $s['academic_year_id'],
            $s['biaya_pendaftaran'],
            $s['biaya_program'],
            $totalBiaya
        ]);
    echo "✓ Insert: {$s['nama_siswa']}, TA ID {$s['academic_year_id']}, Total Biaya {$totalBiaya}\n";
}

echo "\n=== VERIFY STUDENTS ===\n";
$testStudents = db()->query('SELECT s.id, s.nama_siswa, ay.nama_tahun_ajaran, p.nama_mitra, s.biaya_pendaftaran, s.biaya_program, s.total_biaya FROM students s JOIN academic_years ay ON ay.id = s.academic_year_id JOIN partners p ON p.id = s.partner_id WHERE s.partner_id = 5 AND s.nama_siswa LIKE "SISWA TEST%"')->fetchAll(PDO::FETCH_ASSOC);

foreach ($testStudents as $st) {
    echo "ID: {$st['id']}, Nama: {$st['nama_siswa']}, TA: {$st['nama_tahun_ajaran']}, Total: {$st['total_biaya']}\n";
}
