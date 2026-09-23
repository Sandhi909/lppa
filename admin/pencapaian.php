<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'direksi', 'mitra']);
require_once __DIR__ . '/../includes/queries.php';

// Filter scope for mitra
$partnerId = role() === 'mitra' ? my_partner_id() : null;
if (role() === 'mitra' && !$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

// Get filters
$academicYearId = $_GET['academic_year_id'] ?? '';
$partnerFilterId = $_GET['partner_id'] ?? '';
$jenisId = $_GET['jenis_pencapaian_id'] ?? '';

// Build query
$where = ["1=1"];
$params = [];

if ($partnerId) {
    $where[] = "ps.partner_id = ?";
    $params[] = $partnerId;
}

if ($academicYearId) {
    $where[] = "ps.academic_year_id = ?";
    $params[] = $academicYearId;
}

if ($partnerFilterId) {
    $where[] = "ps.partner_id = ?";
    $params[] = $partnerFilterId;
}

if ($jenisId) {
    $where[] = "ps.jenis_pencapaian_id = ?";
    $params[] = $jenisId;
}

$whereClause = implode(' AND ', $where);

// Get achievement data
$achievements = db()->prepare("SELECT ps.*, s.nama_siswa, s.kelas, s.program_bimbel, p.nama_mitra, ay.nama_tahun_ajaran, jp.nama as jenis_nama
FROM pencapaian_siswa ps
LEFT JOIN students s ON ps.student_id = s.id
LEFT JOIN partners p ON ps.partner_id = p.id
LEFT JOIN academic_years ay ON ps.academic_year_id = ay.id
LEFT JOIN jenis_pencapaian jp ON ps.jenis_pencapaian_id = jp.id
WHERE $whereClause
ORDER BY ps.tanggal DESC");
$achievements->execute($params);
$achievements = $achievements->fetchAll();

// Get filter options
$academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();
$jenisPencapaian = db()->query("SELECT * FROM jenis_pencapaian WHERE is_aktif = 1 ORDER BY nama")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Pencapaian Siswa</h3>
        <?php if (role() === 'admin' || role() === 'mitra'): ?>
        <div>
            <a href="<?= base_url('admin/input_pencapaian.php') ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Input Pencapaian
            </a>
            <?php if (role() === 'admin'): ?>
            <a href="<?= base_url('admin/jenis_pencapaian.php') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-list"></i> Jenis Pencapaian
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
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
                <?php if (role() === 'admin' || role() === 'direksi'): ?>
                <div class="col-md-2">
                    <label class="form-label">Mitra</label>
                    <select name="partner_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($partners as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $partnerFilterId == $p['id'] ? 'selected' : '' ?>>
                                <?= e($p['nama_mitra']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-2">
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
                            <th>Program</th>
                            <th>Mitra</th>
                            <th>Jenis</th>
                            <th>Judul</th>
                            <th>Nilai</th>
                            <th>Lampiran</th>
                            <?php if (role() === 'admin' || role() === 'mitra'): ?>
                            <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($achievements)): ?>
                            <tr>
                                <td colspan="<?= (role() === 'admin' || role() === 'mitra') ? 10 : 9 ?>" class="text-center text-muted">Belum ada data pencapaian.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($achievements as $ach): ?>
                                <tr>
                                    <td><?= $ach['tanggal'] ? date('d/m/Y', strtotime($ach['tanggal'])) : '-' ?></td>
                                    <td><?= e($ach['nama_siswa']) ?></td>
                                    <td><?= e($ach['kelas']) ?></td>
                                    <td><?= e($ach['program_bimbel']) ?></td>
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
                                    <?php if (role() === 'admin' || role() === 'mitra'): ?>
                                    <td>
                                        <a href="<?= base_url('admin/edit_pencapaian.php?id=' . $ach['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/hapus_pencapaian.php?id=' . $ach['id']) ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus data pencapaian ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
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
