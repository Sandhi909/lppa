<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$jenisPencapaian = db()->query("SELECT * FROM jenis_pencapaian ORDER BY nama")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    csrf_check();
    
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    if ($nama) {
        try {
            $stmt = db()->prepare("INSERT INTO jenis_pencapaian (nama, deskripsi) VALUES (?, ?)");
            $stmt->execute([$nama, $deskripsi]);
            flash('success', 'Jenis pencapaian berhasil ditambahkan.');
        } catch (Exception $e) {
            flash('danger', 'Gagal menambah jenis pencapaian: ' . $e->getMessage());
        }
    } else {
        flash('danger', 'Nama jenis pencapaian wajib diisi.');
    }
    
    redirect(base_url('admin/jenis_pencapaian.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'edit') {
    csrf_check();
    
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $isAktif = isset($_POST['is_aktif']) ? 1 : 0;
    
    if ($id && $nama) {
        try {
            $stmt = db()->prepare("UPDATE jenis_pencapaian SET nama = ?, deskripsi = ?, is_aktif = ? WHERE id = ?");
            $stmt->execute([$nama, $deskripsi, $isAktif, $id]);
            flash('success', 'Jenis pencapaian berhasil diperbarui.');
        } catch (Exception $e) {
            flash('danger', 'Gagal memperbarui jenis pencapaian: ' . $e->getMessage());
        }
    } else {
        flash('danger', 'Data tidak lengkap.');
    }
    
    redirect(base_url('admin/jenis_pencapaian.php'));
}

if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        try {
            db()->prepare("DELETE FROM jenis_pencapaian WHERE id = ?")->execute([$id]);
            flash('success', 'Jenis pencapaian berhasil dihapus.');
        } catch (Exception $e) {
            flash('danger', 'Gagal menghapus jenis pencapaian: ' . $e->getMessage());
        }
    }
    redirect(base_url('admin/jenis_pencapaian.php'));
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Jenis Pencapaian</h3>
        <a href="<?= base_url('admin/pencapaian.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pencapaian
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Tambah Jenis Pencapaian</h5>
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="aksi" value="tambah">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" class="form-control">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jenisPencapaian as $jp): ?>
                            <tr>
                                <td><?= e($jp['nama']) ?></td>
                                <td><?= e($jp['deskripsi'] ?? '-') ?></td>
                                <td>
                                    <?php if ($jp['is_aktif']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editJenis(<?= htmlspecialchars(json_encode($jp)) ?>)">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <a href="<?= base_url('admin/jenis_pencapaian.php?aksi=hapus&id=' . $jp['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jenis pencapaian ini?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jenis Pencapaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="id" id="editId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required id="editNama">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" id="editDeskripsi">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_aktif" class="form-check-input" id="editIsAktif">
                            <label class="form-check-label" for="editIsAktif">Aktif</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editJenis(data) {
    document.getElementById('editId').value = data.id;
    document.getElementById('editNama').value = data.nama;
    document.getElementById('editDeskripsi').value = data.deskripsi || '';
    document.getElementById('editIsAktif').checked = data.is_aktif == 1;
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
