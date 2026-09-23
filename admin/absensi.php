<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);
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
$statusFilter = $_GET['status'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Build query
$where = ["1=1"];
$params = [];

if ($partnerId) {
    $where[] = "a.partner_id = ?";
    $params[] = $partnerId;
}

if ($academicYearId) {
    $where[] = "a.academic_year_id = ?";
    $params[] = $academicYearId;
}

if ($partnerFilterId) {
    $where[] = "a.partner_id = ?";
    $params[] = $partnerFilterId;
}

if ($statusFilter) {
    $where[] = "a.status = ?";
    $params[] = $statusFilter;
}

if ($startDate) {
    $where[] = "a.tanggal >= ?";
    $params[] = $startDate;
}

if ($endDate) {
    $where[] = "a.tanggal <= ?";
    $params[] = $endDate;
}

$whereClause = implode(' AND ', $where);

// Get attendance data
$attendance = db()->prepare("SELECT a.*, s.nama_siswa, s.kelas, s.program_bimbel, p.nama_mitra, ay.nama_tahun_ajaran
FROM absensi a
LEFT JOIN students s ON a.student_id = s.id
LEFT JOIN partners p ON a.partner_id = p.id
LEFT JOIN academic_years ay ON a.academic_year_id = ay.id
WHERE $whereClause
ORDER BY a.tanggal DESC, a.pertemuan_ke ASC");
$attendance->execute($params);
$attendance = $attendance->fetchAll();

// Get filter options
$academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Absensi Siswa</h3>
        <div>
            <a href="<?= base_url('admin/input_absensi_batch.php') ?>" class="btn btn-success">
                <i class="fa-solid fa-users"></i> Input Batch
            </a>
            <a href="<?= base_url('admin/input_absensi.php') ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Input Satu Siswa
            </a>
        </div>
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
                <?php if (role() === 'admin'): ?>
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="hadir" <?= $statusFilter === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                        <option value="izin" <?= $statusFilter === 'izin' ? 'selected' : '' ?>>Izin</option>
                        <option value="sakit" <?= $statusFilter === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                        <option value="alpa" <?= $statusFilter === 'alpa' ? 'selected' : '' ?>>Alpa</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?= e($startDate) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="<?= e($endDate) ?>">
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
                            <th>Pertemuan Ke-</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Program</th>
                            <th>Mitra</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendance)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">Belum ada data absensi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendance as $a): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($a['tanggal'])) ?></td>
                                    <td><?= e($a['pertemuan_ke']) ?></td>
                                    <td><?= e($a['nama_siswa']) ?></td>
                                    <td><?= e($a['kelas']) ?></td>
                                    <td><?= e($a['program_bimbel']) ?></td>
                                    <td><?= e($a['nama_mitra']) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = match($a['status']) {
                                            'hadir' => 'success',
                                            'izin' => 'warning',
                                            'sakit' => 'info',
                                            'alpa' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($a['status']) ?></span>
                                    </td>
                                    <td><?= e($a['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/edit_absensi.php?id=' . $a['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/hapus_absensi.php?id=' . $a['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data absensi ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
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
