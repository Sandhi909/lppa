<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);

// Get content from database
$kontenQuery = db()->query("SELECT * FROM konten_lppa ORDER BY bagian");
$konten = $kontenQuery->fetchAll();

// Get current content by bagian
$kontenArray = [];
foreach ($konten as $k) {
    $kontenArray[$k['bagian']] = $k;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $id = (int)($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $kontenText = trim($_POST['konten'] ?? '');
    
    if ($id && $judul && $kontenText) {
        try {
            // Handle image upload
            $gambar = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/konten/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                $filename = 'konten_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename);
                $gambar = $filename;
            }
            
            if ($gambar) {
                $stmt = db()->prepare("UPDATE konten_lppa SET judul = ?, konten = ?, gambar = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$judul, $kontenText, $gambar, $id]);
            } else {
                $stmt = db()->prepare("UPDATE konten_lppa SET judul = ?, konten = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$judul, $kontenText, $id]);
            }
            
            audit('Mengubah konten public LPPA', 'konten_lppa', $id);
            flash('success', 'Konten berhasil diperbarui.');
        } catch (Exception $e) {
            flash('danger', 'Gagal memperbarui konten: ' . $e->getMessage());
        }
    } else {
        flash('danger', 'Data tidak lengkap.');
    }
    
    redirect(base_url('admin/konten_lppa.php'));
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Manajemen Konten Public LPPA</h3>
        <a href="<?= base_url('admin/index.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bagian</th>
                            <th>Judul</th>
                            <th>Konten</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kontenArray as $bagian => $k): ?>
                            <tr>
                                <td>
                                    <?php
                                    $bagianLabel = match($bagian) {
                                        'tentang' => 'Tentang LPPA',
                                        'visi' => 'Visi',
                                        'misi' => 'Misi',
                                        'tujuan' => 'Tujuan',
                                        'kontak' => 'Kontak',
                                        default => $bagian
                                    };
                                    ?>
                                    <?= e($bagianLabel) ?>
                                </td>
                                <td><?= e($k['judul']) ?></td>
                                <td><?= substr(strip_tags($k['konten']), 0, 100) ?>...</td>
                                <td>
                                    <?php if ($k['gambar']): ?>
                                        <img src="<?= base_url('uploads/konten/' . e($k['gambar'])) ?>" alt="" style="max-width: 100px; max-height: 100px;">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editKonten('<?= $bagian ?>')">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </button>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Konten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="editForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="editId">
                    
                    <div class="mb-3">
                        <label class="form-label">Bagian</label>
                        <input type="text" class="form-control" value="" id="editBagian" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" required id="editJudul">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Konten <span class="text-danger">*</span></label>
                        <textarea name="konten" class="form-control" rows="10" required id="editKonten"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar (opsional)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <?php if (isset($k) && $k['gambar']): ?>
                            <small class="text-info">File saat ini: <?= e($k['gambar']) ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const kontenData = <?= json_encode($kontenArray) ?>;

function editKonten(bagian) {
    const data = kontenData[bagian];
    if (!data) return;
    
    document.getElementById('editId').value = data.id;
    document.getElementById('editBagian').value = data.bagian;
    document.getElementById('editJudul').value = data.judul;
    document.getElementById('editKonten').value = data.konten;
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
