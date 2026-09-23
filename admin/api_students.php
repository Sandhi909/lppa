<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$academicYearId = (int)($_GET['academic_year_id'] ?? 0);
$partnerId = (int)($_GET['partner_id'] ?? 0);

// For mitra, enforce partner scope
if (role() === 'mitra') {
    $partnerId = my_partner_id();
}

if (!$academicYearId || !$partnerId) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$students = db()->prepare("SELECT id, nama_siswa, kelas, program_bimbel FROM students WHERE academic_year_id = ? AND partner_id = ? ORDER BY nama_siswa ASC");
$students->execute([$academicYearId, $partnerId]);
$students = $students->fetchAll();

header('Content-Type: application/json');
echo json_encode($students);
