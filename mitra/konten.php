<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra']);
require_once __DIR__ . '/../includes/queries.php';
require_once __DIR__ . '/../includes/functions.php';

$partnerId = my_partner_id();
if (!$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get partner data
$partner = db()->prepare("SELECT * FROM partners WHERE id = ?");
$partner->execute([$partnerId]);
$partner = $partner->fetch();

if (!$partner) {
    die('Data mitra tidak ditemukan.');
}

// Get PDF data
$pdfData = db()->prepare("SELECT * FROM partner_programs_pdfs WHERE partner_id = ? ORDER BY uploaded_at DESC");
$pdfData->execute([$partnerId]);
$pdfData = $pdfData->fetchAll();

// Handle PDF upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'upload_pdf') {
    csrf_check();
    
    $pdfTitle = trim($_POST['pdf_title'] ?? 'Program Lengkap');
    $pdfDesc = trim($_POST['pdf_description'] ?? '');
    $pdfPublic = isset($_POST['pdf_public']) ? 1 : 0;
    
    if (!empty($_FILES['pdf_file']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/partner_pdfs/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $pdfName = time() . '_pdf_' . basename($_FILES['pdf_file']['name']);
        $pdfPath = $uploadDir . $pdfName;
        
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfPath)) {
            // Check if PDF already exists
            $checkPdf = db()->prepare('SELECT id FROM partner_programs_pdfs WHERE partner_id = ?');
            $checkPdf->execute([$partnerId]);
            $existingPdf = $checkPdf->fetch();
            
            if ($existingPdf) {
                // Update existing
                db()->prepare('UPDATE partner_programs_pdfs SET pdf_title = ?, pdf_description = ?, pdf_file = ?, is_public = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
                    ->execute([$pdfTitle, $pdfDesc, $pdfName, $pdfPublic, $existingPdf['id']]);
                flash('success', 'PDF berhasil diperbarui.');
            } else {
                // Insert new
                db()->prepare('INSERT INTO partner_programs_pdfs (partner_id, pdf_title, pdf_description, pdf_file, is_public) VALUES (?, ?, ?, ?, ?)')
                    ->execute([$partnerId, $pdfTitle, $pdfDesc, $pdfName, $pdfPublic]);
                flash('success', 'PDF berhasil ditambahkan.');
            }
        } else {
            flash('danger', 'Gagal mengupload PDF.');
        }
    } else {
        flash('danger', 'File PDF wajib diupload.');
    }
    
    redirect(base_url('mitra/konten.php'));
}

// Handle About content update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'update_konten') {
    csrf_check();

    $deskripsiPublic  = trim($_POST['deskripsi_public'] ?? '');
    $keunggulanPublic = trim($_POST['keunggulan_public'] ?? '');

    db()->prepare('UPDATE partners SET deskripsi_public = ?, keunggulan_public = ? WHERE id = ?')
        ->execute([$deskripsiPublic, $keunggulanPublic, $partnerId]);

    audit('Mengubah konten public mitra', 'partners', $partnerId);
    flash('success', 'Konten berhasil disimpan.');
    redirect(base_url('mitra/konten.php'));
}

// Handle delete PDF
if (($_GET['aksi'] ?? '') === 'hapus_pdf') {
    $pdfId = (int)($_GET['pdf_id'] ?? 0);
    if ($pdfId) {
        try {
            $pdf = db()->prepare("SELECT * FROM partner_programs_pdfs WHERE id = ? AND partner_id = ?");
            $pdf->execute([$pdfId, $partnerId]);
            $pdf = $pdf->fetch();
            
            if ($pdf) {
                // Delete file
                $uploadDir = __DIR__ . '/../uploads/partner_pdfs/';
                if ($pdf['pdf_file'] && file_exists($uploadDir . $pdf['pdf_file'])) {
                    unlink($uploadDir . $pdf['pdf_file']);
                }
                
                db()->prepare("DELETE FROM partner_programs_pdfs WHERE id = ?")->execute([$pdfId]);
                flash('success', 'PDF berhasil dihapus.');
            }
        } catch (Exception $e) {
            flash('danger', 'Gagal menghapus PDF: ' . $e->getMessage());
        }
    }
    redirect(base_url('mitra/konten.php'));
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Konten Public Mitra</h3>
        <a href="<?= base_url('mitra/index.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Tentang Kami</h5>
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="aksi" value="update_konten">
                <div class="mb-3">
                    <label class="form-label">Deskripsi Lengkap</label>
                    <textarea name="deskripsi_public" class="form-control" rows="5" placeholder="Deskripsi lengkap program dan keunggulan mitra"><?= e($partner['deskripsi_public'] ?? '') ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Keunggulan</label>
                    <textarea name="keunggulan_public" class="form-control" rows="5" placeholder="Keunggulan program bimbel"><?= e($partner['keunggulan_public'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Upload PDF Program</h5>
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="aksi" value="upload_pdf">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Judul PDF</label>
                            <input type="text" name="pdf_title" class="form-control" value="Program Lengkap" placeholder="Contoh: Program Lengkap 2024">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi PDF</label>
                            <input type="text" name="pdf_description" class="form-control" placeholder="Deskripsi singkat program">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">File PDF <span class="text-danger">*</span></label>
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="pdf_public" name="pdf_public" checked>
                                <label class="form-check-label" for="pdf_public">Tampilkan di Halaman Publik</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-upload"></i> Upload PDF
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">PDF yang Tersedia</h5>
            <?php if (empty($pdfData)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-file-pdf fa-3x mb-3"></i>
                    <p>Belum ada PDF yang diupload.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>File</th>
                                <th>Status Publik</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pdfData as $pdf): ?>
                                <tr>
                                    <td><?= e($pdf['pdf_title']) ?></td>
                                    <td><?= e($pdf['pdf_description'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('uploads/partner_pdfs/' . e($pdf['pdf_file'])) ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa-solid fa-file-pdf"></i> Lihat
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($pdf['is_public']): ?>
                                            <span class="badge bg-success">Publik</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Private</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('mitra/konten.php?aksi=hapus_pdf&pdf_id=' . $pdf['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus PDF ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
