<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('ID absensi tidak ditemukan.');

// Get attendance data for scope check
$attendance = db()->prepare("SELECT * FROM absensi WHERE id = ?");
$attendance->execute([$id]);
$attendance = $attendance->fetch();

if (!$attendance) die('Data absensi tidak ditemukan.');

// Check scope for mitra
if (role() === 'mitra') {
    $partnerId = my_partner_id();
    if ($attendance['partner_id'] != $partnerId) {
        die('Akses ditolak.');
    }
}

try {
    db()->prepare("DELETE FROM absensi WHERE id = ?")->execute([$id]);
    audit('Menghapus data absensi siswa', 'absensi', $id, $attendance, null);
    flash('success', 'Data absensi berhasil dihapus.');
} catch (Exception $e) {
    flash('danger', 'Gagal menghapus data absensi: ' . $e->getMessage());
}

redirect(base_url('admin/absensi.php'));
