<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('ID absensi tidak ditemukan.');

// Get attendance data
$attendance = db()->prepare("SELECT a.*, s.nama_siswa, s.kelas, s.program_bimbel FROM absensi a LEFT JOIN students s ON a.student_id = s.id WHERE a.id = ?");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $pertemuanKe = (int)($_POST['pertemuan_ke'] ?? 0);
    $status = $_POST['status'] ?? 'hadir';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    $errors = [];
    if (!$pertemuanKe) $errors[] = 'Pertemuan ke- wajib diisi.';
    
    if (empty($errors)) {
        try {
            $stmt = db()->prepare("UPDATE absensi SET pertemuan_ke = ?, status = ?, keterangan = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$pertemuanKe, $status, $keterangan, $id]);
            
            audit('Mengubah data absensi siswa', 'absensi', $id);
            flash('success', 'Data absensi berhasil diperbarui.');
            redirect(base_url('admin/absensi.php'));
        } catch (Exception $e) {
            flash('danger', 'Gagal memperbarui data absensi: ' . $e->getMessage());
        }
    } else {
        flash('danger', implode(' ', $errors));
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Absensi Siswa</h3>
        <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Siswa:</strong> <?= e($attendance['nama_siswa']) ?><br>
                <strong>Kelas:</strong> <?= e($attendance['kelas']) ?><br>
                <strong>Program:</strong> <?= e($attendance['program_bimbel']) ?><br>
                <strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($attendance['tanggal'])) ?>
            </div>
            
            <form method="POST">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pertemuan Ke- <span class="text-danger">*</span></label>
                            <input type="number" name="pertemuan_ke" class="form-control" required min="1" value="<?= e($attendance['pertemuan_ke']) ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="hadir" <?= $attendance['status'] === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="izin" <?= $attendance['status'] === 'izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="sakit" <?= $attendance['status'] === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="alpa" <?= $attendance['status'] === 'alpa' ? 'selected' : '' ?>>Alpa</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"><?= e($attendance['keterangan'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
