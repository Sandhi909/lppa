<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);
require_once __DIR__ . '/../includes/queries.php';

// Filter scope for mitra
$partnerId = role() === 'mitra' ? my_partner_id() : null;
if (role() === 'mitra' && !$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get partner filter for admin
$partnerFilterId = $_GET['partner_id'] ?? '';
if (role() === 'admin' && $partnerFilterId) {
    $partnerId = (int)$partnerFilterId;
}

// Get gallery photos
$where = ["1=1"];
$params = [];

if ($partnerId) {
    $where[] = "gm.partner_id = ?";
    $params[] = $partnerId;
}

$whereClause = implode(' AND ', $where);

$gallery = db()->prepare("SELECT gm.*, p.nama_mitra FROM galeri_mitra gm LEFT JOIN partners p ON gm.partner_id = p.id WHERE $whereClause ORDER BY gm.urutan ASC, gm.id DESC");
$gallery->execute($params);
$gallery = $gallery->fetchAll();

// Get partners for filter
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();

// Get partners for filter
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    csrf_check();
    
    $partnerIdInput = role() === 'mitra' ? $partnerId : (int)($_POST['partner_id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    if (!$partnerIdInput) {
        flash('danger', 'Mitra wajib dipilih.');
    } elseif (!isset($_FILES['file_gambar']) || $_FILES['file_gambar']['error'] !== UPLOAD_ERR_OK) {
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
            $maxOrder->execute([$partnerIdInput]);
            $maxOrder = $maxOrder->fetchColumn() ?: 0;
            
            $stmt = db()->prepare("INSERT INTO galeri_mitra (partner_id, judul, deskripsi, file_gambar, urutan, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$partnerIdInput, $judul, $deskripsi, $filename, $maxOrder + 1, user()['id']]);
            
            audit('Menambah foto galeri mitra', 'galeri_mitra', (int)db()->lastInsertId());
            flash('success', 'Foto galeri berhasil ditambahkan.');
        } catch (Exception $e) {
            flash('danger', 'Gagal menambah foto galeri: ' . $e->getMessage());
        }
    }
    
    redirect(base_url('admin/galeri_mitra.php'));
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
    redirect(base_url('admin/galeri_mitra.php'));
}

// Handle delete
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        try {
            // Get photo data for file deletion
            $photo = db()->prepare("SELECT * FROM galeri_mitra WHERE id = ?");
            $photo->execute([$id]);
            $photo = $photo->fetch();
            
            if ($photo) {
                // Check scope for mitra
                if (role() === 'mitra' && $photo['partner_id'] != $partnerId) {
                    die('Akses ditolak.');
                }
                
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
    redirect(base_url('admin/galeri_mitra.php'));
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

    <!-- Filter for admin -->
    <?php if (role() === 'admin'): ?>
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Mitra</label>
                    <select name="partner_id" class="form-select">
                        <option value="">Semua Mitra</option>
                        <?php foreach ($partners as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $partnerFilterId == $p['id'] ? 'selected' : '' ?>>
                                <?= e($p['nama_mitra']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

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
                                            <a href="<?= base_url('admin/galeri_mitra.php?aksi=hapus&id=' . $photo['id']) ?>" 
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
                    
                    <?php if (role() === 'admin'): ?>
                    <div class="mb-3">
                        <label class="form-label">Mitra <span class="text-danger">*</span></label>
                        <select name="partner_id" class="form-select" required>
                            <option value="">Pilih Mitra</option>
                            <?php foreach ($partners as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= e($p['nama_mitra']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="partner_id" value="<?= $partnerId ?>">
                    <?php endif; ?>
                    
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
