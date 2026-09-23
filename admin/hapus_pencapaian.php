<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('ID pencapaian tidak ditemukan.');

// Get achievement data for scope check and file deletion
$achievement = db()->prepare("SELECT * FROM pencapaian_siswa WHERE id = ?");
$achievement->execute([$id]);
$achievement = $achievement->fetch();

if (!$achievement) die('Data pencapaian tidak ditemukan.');

// Check scope for mitra
if (role() === 'mitra') {
    $partnerId = my_partner_id();
    if ($achievement['partner_id'] != $partnerId) {
        die('Akses ditolak.');
    }
}

try {
    // Delete files
    $uploadDir = __DIR__ . '/../uploads/pencapaian/';
    if ($achievement['file_pdf'] && file_exists($uploadDir . $achievement['file_pdf'])) {
        unlink($uploadDir . $achievement['file_pdf']);
    }
    if ($achievement['file_gambar'] && file_exists($uploadDir . $achievement['file_gambar'])) {
        unlink($uploadDir . $achievement['file_gambar']);
    }
    
    db()->prepare("DELETE FROM pencapaian_siswa WHERE id = ?")->execute([$id]);
    audit('Menghapus data pencapaian siswa', 'pencapaian_siswa', $id, $achievement, null);
    flash('success', 'Data pencapaian berhasil dihapus.');
} catch (Exception $e) {
    flash('danger', 'Gagal menghapus data pencapaian: ' . $e->getMessage());
}

redirect(base_url('admin/pencapaian.php'));
