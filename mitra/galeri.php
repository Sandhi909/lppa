<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra']);
require_once __DIR__ . '/../includes/queries.php';
require_once __DIR__ . '/../includes/functions.php';

$partnerId = my_partner_id();
if (!$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get gallery photos
$gallery = db()->prepare("SELECT * FROM galeri_mitra WHERE partner_id = ? ORDER BY urutan ASC");
$gallery->execute([$partnerId]);
$gallery = $gallery->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    csrf_check();
    
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    if (!isset($_FILES['file_gambar']) || $_FILES['file_gambar']['error'] !== UPLOAD_ERR_OK) {
        flash('danger', 'File gambar wajib diupload.');
    } else {
        try {
            $uploadDir = __DIR__ . '/../uploads/galeri/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $ext = strtolower(pathinfo($_FILES['file_gambar']['name'], PATHINFO_EXTENSION));
            $filename = 'galeri_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['file_gambar']['tmp_name'], $uploadDir . $filename);
            
            // Get max order
            $maxOrder = db()->prepare("SELECT MAX(urutan) as max_urutan FROM galeri_mitra WHERE partner_id = ?");
            $maxOrder->execute([$partnerId]);
            $maxOrder = $maxOrder->fetchColumn() ?: 0;
            
            $stmt = db()->prepare("INSERT INTO galeri_mitra (partner_id, judul, deskripsi, file_gambar, urutan, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$partnerId, $judul, $deskripsi, $filename, $maxOrder + 1, user()['id']]);
            
            audit('Menambah foto galeri mitra', 'galeri_mitra', (int)db()->lastInsertId());
            flash('success', 'Foto galeri berhasil ditambahkan.');
        } catch (Exception $e) {
            flash('danger', 'Gagal menambah foto galeri: ' . $e->getMessage());
        }
    }
    
    redirect(base_url('mitra/galeri.php'));
}

// Handle order update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'update_order') {
    csrf_check();
    
    $orders = $_POST['order'] ?? [];
    foreach ($orders as $id => $order) {
        $stmt = db()->prepare("UPDATE galeri_mitra SET urutan = ? WHERE id = ?");
        $stmt->execute([(int)$order, (int)$id]);
    }
    
    flash('success', 'Urutan foto berhasil diperbarui.');
    redirect(base_url('mitra/galeri.php'));
}

// Handle delete
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        try {
            // Get photo data for file deletion
            $photo = db()->prepare("SELECT * FROM galeri_mitra WHERE id = ? AND partner_id = ?");
            $photo->execute([$id, $partnerId]);
            $photo = $photo->fetch();
            
            if ($photo) {
                // Delete file
                $uploadDir = __DIR__ . '/../uploads/galeri/';
                if ($photo['file_gambar'] && file_exists($uploadDir . $photo['file_gambar'])) {
                    unlink($uploadDir . $photo['file_gambar']);
                }
                
                db()->prepare("DELETE FROM galeri_mitra WHERE id = ?")->execute([$id]);
                audit('Menghapus foto galeri mitra', 'galeri_mitra', $id, $photo, null);
                flash('success', 'Foto galeri berhasil dihapus.');
            }
        } catch (Exception $e) {
            flash('danger', 'Gagal menghapus foto galeri: ' . $e->getMessage());
        }
    }
    redirect(base_url('mitra/galeri.php'));
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Galeri Foto Mitra</h3>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus"></i> Tambah Foto
            </button>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($gallery)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-images fa-3x mb-3"></i>
                    <p>Belum ada foto galeri.</p>
                </div>
            <?php else: ?>
                <form method="POST" id="orderForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="update_order">
                    
                    <div class="row g-3">
                        <?php foreach ($gallery as $photo): ?>
                            <div class="col-md-3 col-sm-4 col-6">
                                <div class="card h-100">
                                    <div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">
                                        <img src="<?= base_url('uploads/galeri/' . e($photo['file_gambar'])) ?>" 
                                             alt="<?= e($photo['judul'] ?? 'Foto') ?>" 
                                             class="w-100 h-100 object-fit-cover">
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge bg-dark">#<?= $photo['urutan'] ?></span>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <?php if ($photo['judul']): ?>
                                            <h6 class="card-title mb-1"><?= e($photo['judul']) ?></h6>
                                        <?php endif; ?>
                                        <?php if ($photo['deskripsi']): ?>
                                            <p class="card-text small text-muted mb-2"><?= e(substr($photo['deskripsi'], 0, 50)) ?>...</p>
                                        <?php endif; ?>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <input type="number" name="order[<?= $photo['id'] ?>]" 
                                                   value="<?= $photo['urutan'] ?>" 
                                                   class="form-control form-control-sm" 
                                                   style="width: 60px;" 
                                                   min="1">
                                            <a href="<?= base_url('mitra/galeri.php?aksi=hapus&id=' . $photo['id']) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Hapus foto ini?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fa-solid fa-sort"></i> Update Urutan
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Foto Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="tambah">
                    
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">File Gambar <span class="text-danger">*</span></label>
                        <input type="file" name="file_gambar" class="form-control" accept="image/*" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
