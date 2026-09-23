<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra']);
require_once __DIR__ . '/../includes/functions.php';

$pid = my_partner_id();
if (!$pid) die('Akun mitra belum terhubung dengan data Mitra.');

$success = '';
$error = '';

// Get existing partner data
$partner = db()->prepare("SELECT * FROM partners WHERE id = ?");
$partner->execute([$pid]);
$partner = $partner->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $nama_mitra = trim($_POST['nama_mitra'] ?? '');
    $public_whatsapp = trim($_POST['public_whatsapp'] ?? '');
    $public_website = trim($_POST['public_website'] ?? '');
    $public_address = trim($_POST['public_address'] ?? '');
    $public_description = trim($_POST['public_description'] ?? '');
    $public_program_unggulan = trim($_POST['public_program_unggulan'] ?? '');
    $public_levels = isset($_POST['public_levels']) ? implode(',', $_POST['public_levels']) : '';
    $video_url = trim($_POST['video_url'] ?? '');
    $video_platform = $_POST['video_platform'] ?? 'youtube';

    // Handle Logo Upload
    $public_logo = $partner['public_logo'];
    if (isset($_FILES['public_logo']) && $_FILES['public_logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['public_logo']['name'], PATHINFO_EXTENSION);
        $filename = 'logo_' . $pid . '_' . time() . '.' . $ext;
        $target = __DIR__ . '/../uploads/partners/' . $filename;
        if (!is_dir(__DIR__ . '/../uploads/partners/')) mkdir(__DIR__ . '/../uploads/partners/', 0777, true);
        if (move_uploaded_file($_FILES['public_logo']['tmp_name'], $target)) {
            $public_logo = $filename;
        }
    }

    // Handle Cover Upload
    $public_cover = $partner['public_cover'];
    if (isset($_FILES['public_cover']) && $_FILES['public_cover']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['public_cover']['name'], PATHINFO_EXTENSION);
        $filename = 'cover_' . $pid . '_' . time() . '.' . $ext;
        $target = __DIR__ . '/../uploads/partners/' . $filename;
        if (move_uploaded_file($_FILES['public_cover']['tmp_name'], $target)) {
            $public_cover = $filename;
        }
    }

    try {
        $st = db()->prepare("UPDATE partners SET 
            nama_mitra = ?, 
            public_whatsapp = ?, 
            public_website = ?, 
            public_address = ?, 
            public_description = ?, 
            public_program_unggulan = ?, 
            public_levels = ?, 
            public_logo = ?, 
            public_cover = ?,
            video_url = ?,
            video_platform = ?
            WHERE id = ?");
        $st->execute([
            $nama_mitra, $public_whatsapp, $public_website, $public_address, 
            $public_description, $public_program_unggulan, $public_levels,
            $public_logo, $public_cover, $video_url, $video_platform, $pid
        ]);
        
        // Handle PDF Upload
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $pdf_title = trim($_POST['pdf_title'] ?? 'Program Lengkap');
            $pdf_desc = trim($_POST['pdf_description'] ?? '');
            
            $ext = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) === 'pdf') {
                $filename = 'prog_' . $pid . '_' . time() . '.pdf';
                $target = __DIR__ . '/../uploads/partner_pdfs/' . $filename;
                if (!is_dir(__DIR__ . '/../uploads/partner_pdfs/')) mkdir(__DIR__ . '/../uploads/partner_pdfs/', 0777, true);
                
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target)) {
                    $st_pdf = db()->prepare("INSERT INTO partner_programs_pdfs (partner_id, pdf_title, pdf_description, pdf_file, is_public) VALUES (?, ?, ?, ?, 1)");
                    $st_pdf->execute([$pid, $pdf_title, $pdf_desc, $filename]);
                }
            } else {
                $error = 'File harus berformat PDF.';
            }
        }

        audit('Mengupdate profil publik mitra', 'partners', $pid);
        $success = 'Profil berhasil diperbarui.';
        
        // Refresh data
        $partner = db()->prepare("SELECT * FROM partners WHERE id = ?");
        $partner->execute([$pid]);
        $partner = $partner->fetch();
    } catch (Exception $e) {
        $error = 'Gagal memperbarui profil: ' . $e->getMessage();
    }
}

// Get PDFs
$pdfs = db()->prepare("SELECT * FROM partner_programs_pdfs WHERE partner_id = ? ORDER BY uploaded_at DESC");
$pdfs->execute([$pid]);
$pdfs = $pdfs->fetchAll();

if (isset($_GET['hapus_pdf'])) {
    $pdf_id = (int)$_GET['hapus_pdf'];
    $st = db()->prepare("SELECT pdf_file FROM partner_programs_pdfs WHERE id = ? AND partner_id = ?");
    $st->execute([$pdf_id, $pid]);
    $f = $st->fetch();
    if ($f) {
        @unlink(__DIR__ . '/../uploads/partner_pdfs/' . $f['pdf_file']);
        db()->prepare("DELETE FROM partner_programs_pdfs WHERE id = ?")->execute([$pdf_id]);
        flash('success', 'PDF berhasil dihapus.');
        redirect('konten_mitra.php');
    }
}

$PAGE_TITLE = 'Profil Publik Mitra';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => 'index.php'], ['label' => 'Profil Publik']];
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Informasi Publik Mitra</div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lembaga / Mitra</label>
                            <input type="text" name="nama_mitra" class="form-control" value="<?= e($partner['nama_mitra']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor WhatsApp (Public)</label>
                            <input type="text" name="public_whatsapp" class="form-control" value="<?= e($partner['public_whatsapp']) ?>" placeholder="Contoh: 628123456789">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="public_website" class="form-control" value="<?= e($partner['public_website']) ?>" placeholder="https://...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap (Public)</label>
                        <textarea name="public_address" class="form-control" rows="2"><?= e($partner['public_address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat (Tentang Kami)</label>
                        <textarea name="public_description" class="form-control" rows="4" placeholder="Jelaskan mengenai lembaga bimbingan belajar Anda..."><?= e($partner['public_description']) ?></textarea>
                        <div class="form-text">Akan muncul di halaman daftar bimbingan belajar dan bagian Tentang Kami.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Program Unggulan</label>
                        <input type="text" name="public_program_unggulan" class="form-control" value="<?= e($partner['public_program_unggulan']) ?>" placeholder="Contoh: Super Intensif UTBK">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenjang Pendidikan (Pilih yang tersedia)</label>
                        <div class="d-flex gap-3">
                            <?php $current_levels = explode(',', $partner['public_levels'] ?? ''); ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="public_levels[]" value="SD" id="lvlSD" <?= in_array('SD', $current_levels) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lvlSD">SD</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="public_levels[]" value="SMP" id="lvlSMP" <?= in_array('SMP', $current_levels) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lvlSMP">SMP</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="public_levels[]" value="SMA" id="lvlSMA" <?= in_array('SMA', $current_levels) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lvlSMA">SMA</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo (Transparan disarankan)</label>
                            <input type="file" name="public_logo" class="form-control" accept="image/*">
                            <?php if ($partner['public_logo']): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/partners/' . $partner['public_logo']) ?>" height="50">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gambar Cover / Banner</label>
                            <input type="file" name="public_cover" class="form-control" accept="image/*">
                            <?php if ($partner['public_cover']): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/partners/' . $partner['public_cover']) ?>" height="50">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">URL Video (YouTube / Drive)</label>
                            <input type="url" name="video_url" class="form-control" value="<?= e($partner['video_url']) ?>" placeholder="https://...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Platform Video</label>
                            <select name="video_platform" class="form-select">
                                <option value="youtube" <?= $partner['video_platform'] === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                <option value="drive" <?= $partner['video_platform'] === 'drive' ? 'selected' : '' ?>>Google Drive</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Upload PDF Program (Program Lengkap)</h5>
                    <div class="mb-3">
                        <label class="form-label">Judul PDF</label>
                        <input type="text" name="pdf_title" class="form-control" placeholder="Contoh: Brosur Program 2024">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat PDF</label>
                        <textarea name="pdf_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File PDF</label>
                        <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Daftar PDF Program</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($pdfs)): ?>
                        <div class="p-4 text-center text-muted">Belum ada PDF yang diupload.</div>
                    <?php endif; ?>
                    <?php foreach ($pdfs as $pdf): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1 text-truncate" style="max-width: 200px;"><?= e($pdf['pdf_title']) ?></h6>
                                <a href="?hapus_pdf=<?= $pdf['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus PDF ini?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                            <small class="text-muted d-block"><?= tgl_id($pdf['uploaded_at']) ?></small>
                            <a href="<?= base_url('uploads/partner_pdfs/' . $pdf['pdf_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fa-solid fa-file-pdf me-1"></i> Lihat PDF
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
