<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role(['orang_tua']);
require_once __DIR__ . '/../includes/parent_account_helper.php';

$studentId = my_student_id();
if (!$studentId) {
    // Debug: Check if session has user data
    $userData = $_SESSION['user'] ?? null;
    if ($userData) {
        die('<div style="font-family:sans-serif;padding:40px">Data siswa tidak ditemukan di session. User ID: ' . ($userData['id'] ?? 'null') . '. Hubungi admin.</div>');
    } else {
        die('<div style="font-family:sans-serif;padding:40px">Session tidak valid. Silakan login kembali. <a href="' . base_url('auth/login.php') . '">Login</a></div>');
    }
}

// Ambil semua student_id yang terhubung ke parent account ini (untuk multi-program)
$linkedStudentIds = getLinkedStudentIds($_SESSION['user']['id']);

// Get student data untuk semua enrollment
$students = [];
foreach ($linkedStudentIds as $sid) {
    $stmt = db()->prepare("SELECT s.*, p.nama_mitra, ay.nama_tahun_ajaran, u.nama_unit
    FROM students s
    LEFT JOIN partners p ON s.partner_id = p.id
    LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
    LEFT JOIN units u ON s.unit_id = u.id
    WHERE s.id = ?");
    $stmt->execute([$sid]);
    $s = $stmt->fetch();
    if ($s) $students[] = $s;
}

// Gunakan student pertama sebagai primary
$student = $students[0] ?? null;

if (!$student) {
    die('<div style="font-family:sans-serif;padding:40px">Data siswa tidak ditemukan.</div>');
}

// Get attendance summary untuk semua enrollment
$studentIdsStr = implode(',', array_map('intval', $linkedStudentIds));
$attendanceSummary = db()->prepare("SELECT
COUNT(*) as total,
SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as alpa
FROM absensi WHERE student_id IN ($studentIdsStr)");
$attendanceSummary->execute();
$attendanceSummary = $attendanceSummary->fetch();

// Get recent achievements untuk semua enrollment
$recentAchievements = db()->prepare("SELECT ps.*, jp.nama as jenis_nama, p.nama_mitra
FROM pencapaian_siswa ps
LEFT JOIN jenis_pencapaian jp ON ps.jenis_pencapaian_id = jp.id
LEFT JOIN partners p ON ps.partner_id = p.id
WHERE ps.student_id IN ($studentIdsStr)
ORDER BY ps.tanggal DESC LIMIT 5");
$recentAchievements->execute();
$recentAchievements = $recentAchievements->fetchAll();

$PAGE_TITLE = 'Dashboard Orang Tua';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('orang_tua/index.php')]];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Student Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa-solid fa-user-graduate me-2"></i>Profil Anak</h5>
                    <?php if (count($students) > 1): ?>
                    <span class="badge bg-info"><?= count($students) ?> Mitra/Program</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Mitra</th>
                                    <th>Program Bimbel</th>
                                    <th>Kelas</th>
                                    <th>Unit</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Status Pembayaran</th>
                                    <th>Tanggal Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $st): ?>
                                    <tr>
                                        <td><strong><?= e($st['nama_siswa']) ?></strong></td>
                                        <td><?= e($st['nama_mitra']) ?></td>
                                        <td><?= e($st['program_bimbel'] ?: '-') ?></td>
                                        <td><?= e($st['kelas'] ?: '-') ?></td>
                                        <td><?= e($st['nama_unit'] ?: '-') ?></td>
                                        <td><?= e($st['nama_tahun_ajaran'] ?: '-') ?></td>
                                        <td><?= badge_status($st['status_pembayaran']) ?></td>
                                        <td><?= tgl_id($st['tanggal_pendaftaran']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($students) > 1): ?>
                    <p class="small text-muted mb-0 mt-2">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Anak terdaftar dengan akun orang tua yang sama di beberapa mitra. Data absensi &amp; pencapaian di bawah adalah gabungan dari seluruh mitra di atas.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa-solid fa-calendar-check me-2"></i>Ringkasan Absensi</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="value text-success"><?= $attendanceSummary['hadir'] ?? 0 ?></div>
                                <div class="label">Hadir</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="value text-warning"><?= $attendanceSummary['izin'] ?? 0 ?></div>
                                <div class="label">Izin</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="value text-info"><?= $attendanceSummary['sakit'] ?? 0 ?></div>
                                <div class="label">Sakit</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="value text-danger"><?= $attendanceSummary['alpa'] ?? 0 ?></div>
                                <div class="label">Alpa</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="<?= base_url('orang_tua/absensi.php') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-list me-1"></i>Lihat Detail Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa-solid fa-trophy me-2"></i>Pencapaian Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentAchievements)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="fa-solid fa-trophy fa-2x mb-2"></i>
                            <p>Belum ada data pencapaian.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentAchievements as $ach): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold"><?= e($ach['jenis_nama']) ?></div>
                                            <div class="small text-muted"><?= e($ach['judul']) ?></div>
                                            <div class="small text-muted"><?= tgl_id($ach['tanggal']) ?></div>
                                        </div>
                                        <?php if ($ach['nilai']): ?>
                                            <span class="badge bg-primary"><?= e($ach['nilai']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="<?= base_url('orang_tua/pencapaian.php') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-list me-1"></i>Lihat Semua Pencapaian
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa-solid fa-bolt me-2"></i>Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="<?= base_url('orang_tua/absensi.php') ?>" class="btn btn-outline-primary w-100 py-3">
                                <i class="fa-solid fa-calendar-check fa-2x mb-2"></i>
                                <div>Lihat Absensi</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('orang_tua/pencapaian.php') ?>" class="btn btn-outline-success w-100 py-3">
                                <i class="fa-solid fa-trophy fa-2x mb-2"></i>
                                <div>Lihat Pencapaian</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('orang_tua/profile.php') ?>" class="btn btn-outline-info w-100 py-3">
                                <i class="fa-solid fa-user fa-2x mb-2"></i>
                                <div>Profil Akun</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
