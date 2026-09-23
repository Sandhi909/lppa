<?php
require __DIR__ . '/_scope.php';
require_once __DIR__ . '/../includes/queries.php';

// Wali hanya bisa lihat pencapaian siswa rekomendasinya
// Filter tambahan untuk pencapaian
$academicYearId = $_GET['academic_year_id'] ?? '';
$jenisId = $_GET['jenis_pencapaian_id'] ?? '';

// Build query dengan scope wali: hanya siswa yang wali_kelas_id / wali_santri_id = REC_ID
$where = ["1=1"];
$params = [];

if ($JENIS === 'wali_kelas') {
    $where[] = "s.wali_kelas_id = ?";
    $params[] = $REC_ID;
} else {
    $where[] = "s.wali_santri_id = ?";
    $params[] = $REC_ID;
    // Wali Santri hanya yang PSAM Ya
    $where[] = "s.is_psam = 'Ya'";
}

if ($academicYearId) {
    $where[] = "ps.academic_year_id = ?";
    $params[] = $academicYearId;
}
if ($jenisId) {
    $where[] = "ps.jenis_pencapaian_id = ?";
    $params[] = $jenisId;
}
$whereClause = implode(' AND ', $where);

$achievements = db()->prepare("SELECT ps.*, s.nama_siswa, s.kelas, s.program_bimbel, p.nama_mitra, ay.nama_tahun_ajaran, jp.nama as jenis_nama
FROM pencapaian_siswa ps
JOIN students s ON ps.student_id = s.id
LEFT JOIN partners p ON ps.partner_id = p.id
LEFT JOIN academic_years ay ON ps.academic_year_id = ay.id
LEFT JOIN jenis_pencapaian jp ON ps.jenis_pencapaian_id = jp.id
WHERE $whereClause
ORDER BY ps.tanggal DESC");
$achievements->execute($params);
$achievements = $achievements->fetchAll();

$academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$jenisPencapaian = db()->query("SELECT * FROM jenis_pencapaian WHERE is_aktif = 1 ORDER BY nama")->fetchAll();

$PAGE_TITLE = 'Pencapaian Siswa Bimbingan';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('wali/index.php')], ['label' => 'Pencapaian Siswa']];
include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Pencapaian Siswa Saya</h3>
        <span class="badge bg-primary-soft"><?= e($JENIS_LABEL) ?>: <?= e($REC['nama']) ?></span>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($academicYears as $ay): ?>
                            <option value="<?= $ay['id'] ?>" <?= $academicYearId == $ay['id'] ? 'selected' : '' ?>>
                                <?= e($ay['nama_tahun_ajaran']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Pencapaian</label>
                    <select name="jenis_pencapaian_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($jenisPencapaian as $jp): ?>
                            <option value="<?= $jp['id'] ?>" <?= $jenisId == $jp['id'] ? 'selected' : '' ?>>
                                <?= e($jp['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
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
                                <td colspan="8" class="text-center text-muted">Belum ada data pencapaian untuk siswa bimbingan Anda.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($achievements as $ach): ?>
                                <tr>
                                    <td><?= $ach['tanggal'] ? date('d/m/Y', strtotime($ach['tanggal'])) : '-' ?></td>
                                    <td><?= e($ach['nama_siswa']) ?></td>
                                    <td><?= e($ach['kelas']) ?></td>
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
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($ach['file_gambar']): ?>
                                            <a href="<?= base_url('uploads/pencapaian/' . e($ach['file_gambar'])) ?>" target="_blank" class="btn btn-sm btn-success">
                                                <i class="fa-solid fa-image"></i>
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
