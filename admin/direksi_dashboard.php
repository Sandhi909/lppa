<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'direksi']);
require_once __DIR__ . '/../includes/queries.php';

// Get tahun ajaran terbaru (paling terbaru, id DESC)
$latestTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$latestTAId = $latestTA ? (int)$latestTA['id'] : null;

// Set default filter ke tahun ajaran terbaru jika belum dipilih (isset check agar bisa pilih "Semua")
if (!isset($_GET['academic_year_id']) && $latestTAId) {
    $_GET['academic_year_id'] = $latestTAId;
}

// Get filter values
$selectedAcademicYearId = $_GET['academic_year_id'] ?? '';
$taList = opsi('academic_years', 'nama_tahun_ajaran', '');

// Use student_filters to build WHERE clause
[$WHERE, $PARAMS] = student_filters($_GET);

// Get statistics
$totalStudents = students_count($WHERE, $PARAMS);

// Students by unit
$studentsByUnit = db()->prepare("SELECT u.nama_unit, COUNT(s.id) as jumlah FROM units u LEFT JOIN students s ON u.id = s.unit_id WHERE $WHERE GROUP BY u.id, u.nama_unit ORDER BY jumlah DESC");
$studentsByUnit->execute($PARAMS);
$studentsByUnit = $studentsByUnit->fetchAll();

// Students by partner
$studentsByPartner = db()->prepare("SELECT p.nama_mitra, COUNT(s.id) as jumlah FROM partners p LEFT JOIN students s ON p.id = s.partner_id WHERE $WHERE GROUP BY p.id, p.nama_mitra ORDER BY jumlah DESC");
$studentsByPartner->execute($PARAMS);
$studentsByPartner = $studentsByPartner->fetchAll();

// Students by academic year
$studentsByAcademicYear = db()->query("SELECT ay.nama_tahun_ajaran, COUNT(s.id) as jumlah FROM academic_years ay LEFT JOIN students s ON ay.id = s.academic_year_id GROUP BY ay.id, ay.nama_tahun_ajaran ORDER BY ay.nama_tahun_ajaran DESC")->fetchAll();

// Get unit-specific stats
$sdStudents = 0;
$smpStudents = 0;
$smaStudents = 0;
foreach ($studentsByUnit as $unit) {
    if ($unit['nama_unit'] === 'SD') $sdStudents = $unit['jumlah'];
    if ($unit['nama_unit'] === 'SMP') $smpStudents = $unit['jumlah'];
    if ($unit['nama_unit'] === 'SMA') $smaStudents = $unit['jumlah'];
}

// Recent students
$recentStudents = students_list($WHERE, $PARAMS, 10);

// Achievement stats for dashboard
if ($selectedAcademicYearId) {
    $totalAchievements = db()->prepare("SELECT COUNT(*) as cnt FROM pencapaian_siswa ps JOIN students s ON ps.student_id = s.id WHERE s.academic_year_id = ?");
    $totalAchievements->execute([(int)$selectedAcademicYearId]);
    $totalAchievements = $totalAchievements->fetchColumn();
} else {
    $totalAchievements = db()->query("SELECT COUNT(*) as cnt FROM pencapaian_siswa")->fetchColumn();
}

$PAGE_TITLE = 'Dashboard Direksi';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Dashboard Direksi']];
include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Dashboard Direksi</h3>
        <form method="get" class="d-flex gap-2">
            <select name="academic_year_id" class="form-select" style="width: 200px;">
                <?= select_options($taList, $selectedAcademicYearId, 'Semua Tahun Ajaran') ?>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="direksi_dashboard" class="btn btn-light">Reset</a>
        </form>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <span class="stat-icon navy"><i class="fa-solid fa-users"></i></span>
                <div>
                    <div class="label">Total Siswa</div>
                    <div class="value"><?= number_format($totalStudents) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <span class="stat-icon"><i class="fa-solid fa-graduation-cap"></i></span>
                <div>
                    <div class="label">Siswa SD</div>
                    <div class="value"><?= number_format($sdStudents) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <span class="stat-icon blue"><i class="fa-solid fa-book"></i></span>
                <div>
                    <div class="label">Siswa SMP</div>
                    <div class="value"><?= number_format($smpStudents) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <span class="stat-icon amber"><i class="fa-solid fa-trophy"></i></span>
                <div>
                    <div class="label">Siswa SMA</div>
                    <div class="value"><?= number_format($smaStudents) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card d-flex gap-3 align-items-center">
                <span class="stat-icon green"><i class="fa-solid fa-medal"></i></span>
                <div>
                    <div class="label">Total Pencapaian</div>
                    <div class="value"><?= number_format($totalAchievements) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card d-flex gap-3 align-items-center">
                <span class="stat-icon red"><i class="fa-solid fa-chart-line"></i></span>
                <div>
                    <div class="label">Tahun Ajaran Aktif</div>
                    <div class="value"><?= $selectedAcademicYearId ? e($taList[$selectedAcademicYearId]['nama'] ?? '-') : 'Semua' ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Jumlah Siswa per Unit</div>
                <div class="card-body">
                    <div class="chart-box"><canvas id="chartUnit"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Jumlah Siswa per Mitra</div>
                <div class="card-body">
                    <div class="chart-box"><canvas id="chartPartner"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Jumlah Siswa per Tahun Ajaran</div>
                <div class="card-body">
                    <div class="chart-box"><canvas id="chartAcademicYear"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Distribusi Siswa per Unit (Pie)</div>
                <div class="card-body">
                    <div class="chart-box"><canvas id="chartUnitPie"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Students -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Siswa Terbaru</span>
            <a class="btn btn-sm btn-light" href="<?= base_url('admin/students.php') ?>">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive-wrapper mobile-table-wrapper">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Program</th>
                            <th>Unit</th>
                            <th>Mitra</th>
                            <th>Tahun Ajaran</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$recentStudents): ?>
                            <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada data siswa.</div></td></tr>
                        <?php endif; ?>
                        <?php foreach ($recentStudents as $s): ?>
                            <tr>
                                <td><strong><?= e($s['nama_siswa']) ?></strong></td>
                                <td><?= e($s['kelas']) ?></td>
                                <td><?= e($s['program_bimbel']) ?></td>
                                <td><?= e($s['nama_unit']) ?></td>
                                <td><?= e($s['nama_mitra']) ?></td>
                                <td><?= e($s['nama_tahun_ajaran']) ?></td>
                                <td>
                                    <?php 
                                    $kategori = $s['kategori_peserta'] ?? 'reguler';
                                    if ($kategori === 'khusus'): 
                                    ?>
                                    <span class="badge bg-warning text-dark small">Khusus</span>
                                    <?php else: ?>
                                    <span class="badge bg-success small">Reguler</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="<?= base_url('admin/pencapaian.php') ?>" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i class="fa-solid fa-medal fa-3x text-primary mb-2"></i>
                    <h5 class="card-title">Kelola Pencapaian</h5>
                    <p class="card-text text-muted small">Lihat dan kelola pencapaian siswa</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('admin/rekap.php') ?>" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i class="fa-solid fa-chart-bar fa-3x text-success mb-2"></i>
                    <h5 class="card-title">Rekap Rekomendator</h5>
                    <p class="card-text text-muted small">Lihat rekap poin dan fee rekomendator</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('admin/students.php') ?>" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i class="fa-solid fa-user-graduate fa-3x text-info mb-2"></i>
                    <h5 class="card-title">Data Siswa</h5>
                    <p class="card-text text-muted small">Kelola data siswa lengkap</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
const base={responsive:true,maintainAspectRatio:false};
const BLUE='#1e6fd9',NAVY='#0f2f5f',GREEN='#16a34a',AMBER='#d97706',RED='#dc2626';

// Chart data
const unitData = {
    labels: <?= json_encode(array_column($studentsByUnit, 'nama_unit')) ?>,
    datasets: [{
        label: 'Jumlah Siswa',
        data: <?= json_encode(array_column($studentsByUnit, 'jumlah')) ?>,
        backgroundColor: [BLUE,NAVY,GREEN,AMBER,RED],
        borderRadius: 6
    }]
};

const partnerData = {
    labels: <?= json_encode(array_column($studentsByPartner, 'nama_mitra')) ?>,
    datasets: [{
        label: 'Jumlah Siswa',
        data: <?= json_encode(array_column($studentsByPartner, 'jumlah')) ?>,
        backgroundColor: BLUE,
        borderRadius: 6
    }]
};

const academicYearData = {
    labels: <?= json_encode(array_column($studentsByAcademicYear, 'nama_tahun_ajaran')) ?>,
    datasets: [{
        label: 'Jumlah Siswa',
        data: <?= json_encode(array_column($studentsByAcademicYear, 'jumlah')) ?>,
        backgroundColor: GREEN,
        borderRadius: 6
    }]
};

// Charts
if (document.getElementById('chartUnit')) {
    new Chart(document.getElementById('chartUnit'), {
        type: 'bar',
        data: unitData,
        options: {...base, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
    });
}

if (document.getElementById('chartPartner')) {
    new Chart(document.getElementById('chartPartner'), {
        type: 'bar',
        data: partnerData,
        options: {...base, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
    });
}

if (document.getElementById('chartAcademicYear')) {
    new Chart(document.getElementById('chartAcademicYear'), {
        type: 'bar',
        data: academicYearData,
        options: {...base, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
    });
}

if (document.getElementById('chartUnitPie')) {
    new Chart(document.getElementById('chartUnitPie'), {
        type: 'pie',
        data: unitData,
        options: {...base, plugins:{legend:{position:'bottom'}}}
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
