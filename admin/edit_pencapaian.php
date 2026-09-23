<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('ID pencapaian tidak ditemukan.');

// Get achievement data
$achievement = db()->prepare("SELECT ps.*, s.nama_siswa, s.kelas, s.program_bimbel FROM pencapaian_siswa ps LEFT JOIN students s ON ps.student_id = s.id WHERE ps.id = ?");
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

// Get available data for dropdowns
$jenisPencapaian = db()->query("SELECT * FROM jenis_pencapaian WHERE is_aktif = 1 ORDER BY nama")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $jenisPencapaianId = (int)($_POST['jenis_pencapaian_id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $nilai = $_POST['nilai'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    
    $errors = [];
    if (!$jenisPencapaianId) $errors[] = 'Jenis pencapaian wajib dipilih.';
    if (!$judul) $errors[] = 'Judul wajib diisi.';
    
    if (empty($errors)) {
        try {
            // Handle file uploads
            $filePdf = $achievement['file_pdf'];
            $fileGambar = $achievement['file_gambar'];
            
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/pencapaian/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                // Delete old file
                if ($filePdf && file_exists($uploadDir . $filePdf)) {
                    unlink($uploadDir . $filePdf);
                }
                
                $ext = strtolower(pathinfo($_FILES['file_pdf']['name'], PATHINFO_EXTENSION));
                $filename = 'pdf_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['file_pdf']['tmp_name'], $uploadDir . $filename);
                $filePdf = $filename;
            }
            
            if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/pencapaian/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                // Delete old file
                if ($fileGambar && file_exists($uploadDir . $fileGambar)) {
                    unlink($uploadDir . $fileGambar);
                }
                
                $ext = strtolower(pathinfo($_FILES['file_gambar']['name'], PATHINFO_EXTENSION));
                $filename = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['file_gambar']['tmp_name'], $uploadDir . $filename);
                $fileGambar = $filename;
            }
            
            $stmt = db()->prepare("UPDATE pencapaian_siswa SET jenis_pencapaian_id = ?, judul = ?, deskripsi = ?, nilai = ?, tanggal = ?, file_pdf = ?, file_gambar = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$jenisPencapaianId, $judul, $deskripsi, $nilai ?: null, $tanggal ?: null, $filePdf, $fileGambar, $id]);
            
            audit('Mengubah data pencapaian siswa', 'pencapaian_siswa', $id);
            flash('success', 'Data pencapaian berhasil diperbarui.');
            redirect(base_url('admin/pencapaian.php'));
        } catch (Exception $e) {
            flash('danger', 'Gagal memperbarui data pencapaian: ' . $e->getMessage());
        }
    } else {
        flash('danger', implode(' ', $errors));
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Pencapaian Siswa</h3>
        <a href="<?= base_url('admin/pencapaian.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Siswa:</strong> <?= e($achievement['nama_siswa']) ?><br>
                <strong>Kelas:</strong> <?= e($achievement['kelas']) ?><br>
                <strong>Program:</strong> <?= e($achievement['program_bimbel']) ?>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Jenis Pencapaian <span class="text-danger">*</span></label>
                            <select name="jenis_pencapaian_id" class="form-select" required>
                                <?php foreach ($jenisPencapaian as $jp): ?>
                                    <option value="<?= $jp['id'] ?>" <?= $achievement['jenis_pencapaian_id'] == $jp['id'] ? 'selected' : '' ?>>
                                        <?= e($jp['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required value="<?= e($achievement['judul']) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nilai (opsional)</label>
                            <input type="number" name="nilai" class="form-control" step="0.01" value="<?= e($achievement['nilai']) ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal (opsional)</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $achievement['tanggal'] ? date('Y-m-d', strtotime($achievement['tanggal'])) : '' ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">File PDF (opsional)</label>
                            <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                            <?php if ($achievement['file_pdf']): ?>
                                <small class="text-info">File saat ini: <?= e($achievement['file_pdf']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">File Gambar (opsional)</label>
                            <input type="file" name="file_gambar" class="form-control" accept="image/*">
                            <?php if ($achievement['file_gambar']): ?>
                                <small class="text-info">File saat ini: <?= e($achievement['file_gambar']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= e($achievement['deskripsi'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <a href="<?= base_url('admin/pencapaian.php') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
