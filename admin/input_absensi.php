<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);
require_once __DIR__ . '/../includes/queries.php';

// Filter scope for mitra
$partnerId = role() === 'mitra' ? my_partner_id() : null;
if (role() === 'mitra' && !$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get available academic years and partners
$academicYears = db()->query("SELECT * FROM academic_years WHERE status = 'aktif' ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $studentId = (int)($_POST['student_id'] ?? 0);
    $partnerIdInput = $partnerId ?: (int)($_POST['partner_id'] ?? 0);
    $academicYearId = (int)($_POST['academic_year_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $pertemuanKe = (int)($_POST['pertemuan_ke'] ?? 0);
    $status = $_POST['status'] ?? 'hadir';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    $errors = [];
    if (!$studentId) $errors[] = 'Siswa wajib dipilih.';
    if (!$partnerIdInput) $errors[] = 'Mitra wajib dipilih.';
    if (!$academicYearId) $errors[] = 'Tahun ajaran wajib dipilih.';
    if (!$tanggal) $errors[] = 'Tanggal wajib diisi.';
    if (!$pertemuanKe) $errors[] = 'Pertemuan ke- wajib diisi.';
    
    if (empty($errors)) {
        try {
            $stmt = db()->prepare("INSERT INTO absensi (student_id, partner_id, academic_year_id, tanggal, pertemuan_ke, status, keterangan, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$studentId, $partnerIdInput, $academicYearId, $tanggal, $pertemuanKe, $status, $keterangan, user()['id']]);
            
            audit('Menambah data absensi siswa', 'absensi', (int)db()->lastInsertId());
            flash('success', 'Data absensi berhasil ditambahkan.');
            redirect(base_url('admin/absensi.php'));
        } catch (Exception $e) {
            flash('danger', 'Gagal menambah data absensi: ' . $e->getMessage());
        }
    } else {
        flash('danger', implode(' ', $errors));
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Input Absensi Siswa</h3>
        <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select" required id="academicYearSelect">
                                <option value="">Pilih Tahun Ajaran</option>
                                <?php foreach ($academicYears as $ay): ?>
                                    <option value="<?= $ay['id'] ?>" data-tahun="<?= $ay['nama_tahun_ajaran'] ?>">
                                        <?= e($ay['nama_tahun_ajaran']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <?php if (role() === 'admin'): ?>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Mitra <span class="text-danger">*</span></label>
                            <select name="partner_id" class="form-select" required id="partnerSelect">
                                <option value="">Pilih Mitra</option>
                                <?php foreach ($partners as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= e($p['nama_mitra']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="partner_id" value="<?= $partnerId ?>">
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Siswa <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required id="studentSelect">
                                <option value="">Pilih Tahun Ajaran dan Mitra terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pertemuan Ke- <span class="text-danger">*</span></label>
                            <input type="number" name="pertemuan_ke" class="form-control" required min="1" value="1">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
// Load students based on academic year and partner
function loadStudents() {
    const academicYearId = document.getElementById('academicYearSelect').value;
    const partnerId = <?= $partnerId ?> || document.getElementById('partnerSelect').value;
    const studentSelect = document.getElementById('studentSelect');
    
    if (!academicYearId || !partnerId) {
        studentSelect.innerHTML = '<option value="">Pilih Tahun Ajaran dan Mitra terlebih dahulu</option>';
        return;
    }
    
    fetch('<?= base_url('admin/api_students.php') ?>?academic_year_id=' + academicYearId + '&partner_id=' + partnerId)
        .then(response => response.json())
        .then(data => {
            studentSelect.innerHTML = '<option value="">Pilih Siswa</option>';
            data.forEach(student => {
                studentSelect.innerHTML += '<option value="' + student.id + '">' + student.nama_siswa + ' - ' + student.kelas + ' - ' + student.program_bimbel + '</option>';
            });
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('academicYearSelect').addEventListener('change', loadStudents);
<?php if (role() === 'admin'): ?>
document.getElementById('partnerSelect').addEventListener('change', loadStudents);
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
