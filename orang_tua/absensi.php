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

// Get attendance data untuk semua enrollment
$attendance = db()->prepare("SELECT a.*, p.nama_mitra, s.program_bimbel, s.nama_siswa
FROM absensi a
LEFT JOIN partners p ON a.partner_id = p.id
LEFT JOIN students s ON a.student_id = s.id
WHERE a.student_id IN ($studentIdsStr)
ORDER BY a.tanggal DESC");
$attendance->execute();
$attendance = $attendance->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Absensi Anak</h3>
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
                            <th>Program</th>
                            <th>Pertemuan Ke-</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendance)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data absensi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendance as $a): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($a['tanggal'])) ?></td>
                                    <td><?= e($a['nama_mitra']) ?></td>
                                    <td><?= e($a['program_bimbel']) ?></td>
                                    <td><?= e($a['pertemuan_ke']) ?></td>
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
