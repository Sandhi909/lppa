<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);
require_once __DIR__ . '/../includes/queries.php';

// Filter scope for mitra
$partnerId = role() === 'mitra' ? my_partner_id() : null;
if (role() === 'mitra' && !$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get available data
$academicYears = db()->query("SELECT * FROM academic_years WHERE status = 'aktif' ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();
$jenisPencapaian = db()->query("SELECT * FROM jenis_pencapaian WHERE is_aktif = 1 ORDER BY nama")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $studentId = (int)($_POST['student_id'] ?? 0);
    $partnerIdInput = $partnerId ?: (int)($_POST['partner_id'] ?? 0);
    $academicYearId = (int)($_POST['academic_year_id'] ?? 0);
    $jenisPencapaianId = (int)($_POST['jenis_pencapaian_id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $nilai = $_POST['nilai'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    
    $errors = [];
    if (!$studentId) $errors[] = 'Siswa wajib dipilih.';
    if (!$partnerIdInput) $errors[] = 'Mitra wajib dipilih.';
    if (!$academicYearId) $errors[] = 'Tahun ajaran wajib dipilih.';
    if (!$jenisPencapaianId) $errors[] = 'Jenis pencapaian wajib dipilih.';
    if (!$judul) $errors[] = 'Judul wajib diisi.';
    
    if (empty($errors)) {
        try {
            // Handle file uploads
            $filePdf = null;
            $fileGambar = null;
            
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/pencapaian/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $ext = strtolower(pathinfo($_FILES['file_pdf']['name'], PATHINFO_EXTENSION));
                $filename = 'pdf_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['file_pdf']['tmp_name'], $uploadDir . $filename);
                $filePdf = $filename;
            }
            
            if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/pencapaian/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $ext = strtolower(pathinfo($_FILES['file_gambar']['name'], PATHINFO_EXTENSION));
                $filename = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['file_gambar']['tmp_name'], $uploadDir . $filename);
                $fileGambar = $filename;
            }
            
            $stmt = db()->prepare("INSERT INTO pencapaian_siswa (student_id, partner_id, academic_year_id, jenis_pencapaian_id, judul, deskripsi, nilai, tanggal, file_pdf, file_gambar, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$studentId, $partnerIdInput, $academicYearId, $jenisPencapaianId, $judul, $deskripsi, $nilai ?: null, $tanggal ?: null, $filePdf, $fileGambar, user()['id']]);
            
            audit('Menambah data pencapaian siswa', 'pencapaian_siswa', (int)db()->lastInsertId());
            flash('success', 'Data pencapaian berhasil ditambahkan.');
            redirect(base_url('admin/pencapaian.php'));
        } catch (Exception $e) {
            flash('danger', 'Gagal menambah data pencapaian: ' . $e->getMessage());
        }
    } else {
        flash('danger', implode(' ', $errors));
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Input Pencapaian Siswa</h3>
        <a href="<?= base_url('admin/pencapaian.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select" required id="academicYearSelect">
                                <option value="">Pilih Tahun Ajaran</option>
                                <?php foreach ($academicYears as $ay): ?>
                                    <option value="<?= $ay['id'] ?>">
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
                            <label class="form-label">Jenis Pencapaian <span class="text-danger">*</span></label>
                            <select name="jenis_pencapaian_id" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <?php foreach ($jenisPencapaian as $jp): ?>
                                    <option value="<?= $jp['id'] ?>">
                                        <?= e($jp['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nilai (opsional)</label>
                            <input type="number" name="nilai" class="form-control" step="0.01">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal (opsional)</label>
                            <input type="date" name="tanggal" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">File PDF (opsional)</label>
                            <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">File Gambar (opsional)</label>
                    <input type="file" name="file_gambar" class="form-control" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
                <a href="<?= base_url('admin/pencapaian.php') ?>" class="btn btn-secondary">Batal</a>
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
