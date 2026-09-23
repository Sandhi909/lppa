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
$unitId = $_GET['unit_id'] ?? '';
$program = $_GET['program'] ?? '';
$kelas = $_GET['kelas'] ?? '';

// Build query
$where = ["1=1"];
$params = [];

if ($partnerId) {
    $where[] = "s.partner_id = ?";
    $params[] = $partnerId;
}

if ($academicYearId) {
    $where[] = "s.academic_year_id = ?";
    $params[] = $academicYearId;
}

if ($partnerFilterId) {
    $where[] = "s.partner_id = ?";
    $params[] = $partnerFilterId;
}

if ($unitId) {
    $where[] = "s.unit_id = ?";
    $params[] = $unitId;
}

if ($program) {
    $where[] = "s.program_bimbel LIKE ?";
    $params[] = "%$program%";
}

if ($kelas) {
    $where[] = "s.kelas LIKE ?";
    $params[] = "%$kelas%";
}

$whereClause = implode(' AND ', $where);

// Get students
$students = db()->prepare("SELECT s.*, p.nama_mitra, ay.nama_tahun_ajaran, u.nama_unit
FROM students s
LEFT JOIN partners p ON s.partner_id = p.id
LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
LEFT JOIN units u ON s.unit_id = u.id
WHERE $whereClause
ORDER BY s.nama_siswa ASC");
$students->execute($params);
$students = $students->fetchAll();

// Get filter options
$academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();
$units = db()->query("SELECT * FROM units WHERE status = 'aktif' ORDER BY nama_unit")->fetchAll();

// Handle batch submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $tanggal = $_POST['tanggal'] ?? '';
    $pertemuanKe = (int)($_POST['pertemuan_ke'] ?? 0);
    $partnerIdInput = $partnerId ?: (int)($_POST['partner_id'] ?? 0);
    $academicYearIdInput = (int)($_POST['academic_year_id'] ?? 0);

    $errors = [];
    if (!$tanggal) $errors[] = 'Tanggal wajib diisi.';
    if (!$pertemuanKe) $errors[] = 'Pertemuan ke- wajib diisi.';
    if (!$partnerIdInput) $errors[] = 'Mitra wajib dipilih.';
    if (!$academicYearIdInput) $errors[] = 'Tahun ajaran wajib diisi.';

    if (empty($errors)) {
        try {
            $successCount = 0;
            $skipCount = 0;

            // Cek apakah ada siswa yang dipilih
            $selectedStudents = $_POST['selected_students'] ?? [];

            if (empty($selectedStudents)) {
                flash('warning', 'Tidak ada siswa yang dipilih. Silakan pilih siswa terlebih dahulu.');
                redirect(base_url('admin/input_absensi_batch.php?' . http_build_query($_GET)));
            }

            foreach ($selectedStudents as $studentId) {
                $status = $_POST['attendance'][$studentId] ?? 'skip';

                if ($status === 'skip') {
                    $skipCount++;
                    continue;
                }

                $keterangan = $_POST['keterangan'][$studentId] ?? '';

                $stmt = db()->prepare("INSERT INTO absensi (student_id, partner_id, academic_year_id, tanggal, pertemuan_ke, status, keterangan, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$studentId, $partnerIdInput, $academicYearIdInput, $tanggal, $pertemuanKe, $status, $keterangan, user()['id']]);
                $successCount++;
            }

            if ($successCount > 0) {
                audit('Input batch absensi siswa', 'absensi', null, "Berhasil input $successCount siswa, skip $skipCount siswa");
                flash('success', "Berhasil input absensi untuk $successCount siswa. ($skipCount siswa dilewati)");
            } else {
                flash('warning', 'Tidak ada data absensi yang disimpan (semua dilewati).');
            }
            redirect(base_url('admin/absensi.php'));
        } catch (Exception $e) {
            flash('danger', 'Gagal menambah data absensi: ' . $e->getMessage());
        }
    } else {
        flash('danger', implode(' ', $errors));
    }
}

// Jika belum memilih tahun ajaran, arahkan otomatis ke tahun ajaran aktif (yang punya siswa terbanyak)
if (!$academicYearId) {
    $activeAyId = db()->query("SELECT ay.id FROM academic_years ay LEFT JOIN students s ON s.academic_year_id = ay.id WHERE ay.status = 'aktif' GROUP BY ay.id ORDER BY COUNT(s.id) DESC, ay.nama_tahun_ajaran DESC LIMIT 1")->fetchColumn();
    if (!$activeAyId) {
        $activeAyId = db()->query("SELECT academic_year_id FROM students GROUP BY academic_year_id ORDER BY COUNT(*) DESC LIMIT 1")->fetchColumn();
    }
    if ($activeAyId) {
        redirect(base_url('admin/input_absensi_batch.php?' . http_build_query(array_merge($_GET, ['academic_year_id' => (int)$activeAyId]))));
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Input Absensi Batch</h3>
        <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" id="filterForm" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select" onchange="submitFilter()">
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
                    <label class="form-label">Unit</label>
                    <select name="unit_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $unitId == $u['id'] ? 'selected' : '' ?>>
                                <?= e($u['nama_unit']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Program</label>
                    <input type="text" name="program" class="form-control" value="<?= e($program) ?>" placeholder="Cari program...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-control" value="<?= e($kelas) ?>" placeholder="Cari kelas...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Batch Input Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <?= csrf_field() ?>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pertemuan Ke- <span class="text-danger">*</span></label>
                        <input type="number" name="pertemuan_ke" class="form-control" required min="1" value="1">
                    </div>
                    <?php if (role() === 'admin'): ?>
                    <div class="col-md-3">
                        <label class="form-label">Mitra <span class="text-danger">*</span></label>
                        <select name="partner_id" class="form-select" required>
                            <option value="">Pilih Mitra</option>
                            <?php foreach ($partners as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $partnerFilterId == $p['id'] ? 'selected' : '' ?>>
                                    <?= e($p['nama_mitra']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="partner_id" value="<?= $partnerId ?>">
                    <?php endif; ?>
                    <input type="hidden" name="academic_year_id" value="<?= (int)$academicYearId ?>">
                </div>

                <?php if (!$academicYearId): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Pilih Tahun Ajaran pada filter di atas untuk menampilkan daftar siswa. Hanya siswa pada tahun ajaran terpilih yang akan ditampilkan.
                    </div>
                <?php elseif (empty($students)): ?>
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        Tidak ada siswa pada tahun ajaran terpilih yang sesuai filter ini.
                    </div>
                <?php else: ?>
                    <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="fa-solid fa-check-double"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                            <i class="fa-solid fa-times"></i> Batal Pilih
                        </button>
                        <span class="text-muted" id="selectedCount">0 / <?= count($students) ?> siswa dipilih</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Program</th>
                                    <th>Unit</th>
                                    <th style="width: 150px;">Status</th>
                                    <th style="width: 200px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $s): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input student-checkbox" name="selected_students[]" value="<?= $s['id'] ?>" onchange="updateSelectedCount()">
                                        </td>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= e($s['nama_siswa']) ?></strong></td>
                                        <td><?= e($s['kelas']) ?></td>
                                        <td><?= e($s['program_bimbel']) ?></td>
                                        <td><?= e($s['nama_unit']) ?></td>
                                        <td>
                                            <select name="attendance[<?= $s['id'] ?>]" class="form-select form-select-sm">
                                                <option value="skip">- Lewati -</option>
                                                <option value="hadir" selected>Hadir</option>
                                                <option value="izin">Izin</option>
                                                <option value="sakit">Sakit</option>
                                                <option value="alpa">Alpa</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="keterangan[<?= $s['id'] ?>]" class="form-control form-control-sm" placeholder="Keterangan...">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Total <strong><?= count($students) ?></strong> siswa pada tahun ajaran terpilih. Centang siswa yang ingin diinput absensinya, lalu pilih status per siswa.
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Simpan Absensi Terpilih
                        </button>
                        <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
function submitFilter() {
    document.getElementById('filterForm').submit();
}

function selectAll() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelectedCount();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllCheckbox').checked;
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.student-checkbox:checked').length;
    const total = document.querySelectorAll('.student-checkbox').length;
    const countEl = document.getElementById('selectedCount');
    if (countEl) countEl.textContent = checked + ' / ' + total + ' siswa dipilih';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
