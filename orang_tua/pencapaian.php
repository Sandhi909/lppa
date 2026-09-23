<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role(['orang_tua']);
require_once __DIR__ . '/../includes/parent_account_helper.php';

$studentId = my_student_id();
if (!$studentId) {
    die('<div style="font-family:sans-serif;padding:40px">Data siswa tidak ditemukan. Hubungi admin.</div>');
}

// Ambil semua student_id yang terhubung ke parent account ini
$linkedStudentIds = getLinkedStudentIds($_SESSION['user']['id']);
$studentIdsStr = implode(',', array_map('intval', $linkedStudentIds));

// Get achievements data untuk semua enrollment
$achievements = db()->prepare("SELECT ps.*, jp.nama as jenis_nama, p.nama_mitra, s.nama_siswa
FROM pencapaian_siswa ps
LEFT JOIN jenis_pencapaian jp ON ps.jenis_pencapaian_id = jp.id
LEFT JOIN partners p ON ps.partner_id = p.id
LEFT JOIN students s ON ps.student_id = s.id
WHERE ps.student_id IN ($studentIdsStr)
ORDER BY ps.tanggal DESC");
$achievements->execute();
$achievements = $achievements->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Pencapaian Anak</h3>
        <a href="<?= home_url() ?>" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Mitra</th>
                            <th>Jenis</th>
                            <th>Judul</th>
                            <th>Nilai</th>
                            <th>Lampiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($achievements)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data pencapaian.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($achievements as $ach): ?>
                                <tr>
                                    <td><?= $ach['tanggal'] ? date('d/m/Y', strtotime($ach['tanggal'])) : '-' ?></td>
                                    <td><?= e($ach['nama_mitra']) ?></td>
                                    <td><?= e($ach['jenis_nama']) ?></td>
                                    <td>
                                        <strong><?= e($ach['judul']) ?></strong>
                                        <?php if ($ach['deskripsi']): ?>
                                            <br><small class="text-muted"><?= e($ach['deskripsi']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ach['nilai']): ?>
                                            <span class="badge bg-primary"><?= e($ach['nilai']) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ach['file_pdf']): ?>
                                            <a href="<?= base_url('uploads/pencapaian/' . e($ach['file_pdf'])) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fa-solid fa-file-pdf"></i> PDF
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($ach['file_gambar']): ?>
                                            <a href="<?= base_url('uploads/pencapaian/' . e($ach['file_gambar'])) ?>" target="_blank" class="btn btn-sm btn-success">
                                                <i class="fa-solid fa-image"></i> Gambar
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$ach['file_pdf'] && !$ach['file_gambar']): ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
