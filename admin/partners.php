<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['aksi'] ?? '';
    if ($act === 'simpan') {
        $id = (int)($_POST['id'] ?? 0);
        $d = [
            'nama_mitra' => trim($_POST['nama_mitra'] ?? ''),
            'penanggung_jawab' => trim($_POST['penanggung_jawab'] ?? ''),
            'telepon' => trim($_POST['telepon'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'alamat' => trim($_POST['alamat'] ?? ''),
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?: null,
            'status' => ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif',
            'keterangan' => trim($_POST['keterangan'] ?? ''),
            'persentase_bagi_hasil' => round((float)str_replace(',', '.', (string)($_POST['persentase_bagi_hasil'] ?? '')), 2),
            'tax_enabled' => isset($_POST['tax_enabled']) ? 1 : 0,
            'tax_percentage' => round((float)str_replace(',', '.', (string)($_POST['tax_percentage'] ?? 0)), 2),
            'tax_method' => $_POST['tax_method'] ?? 'none',
            'tax_manual_nominal' => round((float)str_replace(',', '.', (string)($_POST['tax_manual_nominal'] ?? 0)), 2),
            'is_public' => isset($_POST['is_public']) ? 1 : 0,
            'display_order' => (int)($_POST['display_order'] ?? 0),
            'public_description' => trim($_POST['public_description'] ?? ''),
            'public_programs' => trim($_POST['public_programs'] ?? ''),
            'public_levels' => trim($_POST['public_levels'] ?? ''),
            'public_program_unggulan' => trim($_POST['public_program_unggulan'] ?? ''),
            'public_website' => trim($_POST['public_website'] ?? ''),
            'public_whatsapp' => trim($_POST['public_whatsapp'] ?? ''),
            'public_address' => trim($_POST['public_address'] ?? ''),
            'deskripsi_public' => trim($_POST['deskripsi_public'] ?? ''),
            'keunggulan_public' => trim($_POST['keunggulan_public'] ?? ''),
            'video_url' => trim($_POST['video_url'] ?? ''),
            'video_platform' => $_POST['video_platform'] ?? 'youtube',
        ];
        if ($d['nama_mitra'] === '') { flash('danger', 'Nama mitra wajib diisi.'); redirect('partners.php'); }
        if (($_POST['persentase_bagi_hasil'] ?? '') === '') { flash('danger', 'Persentase Bagi Hasil Yayasan wajib diisi.'); redirect('partners.php'); }
        if ($d['persentase_bagi_hasil'] < 0 || $d['persentase_bagi_hasil'] > 100) { flash('danger', 'Persentase Bagi Hasil Yayasan harus antara 0% sampai 100%.'); redirect('partners.php'); }
        if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) { flash('danger', 'Format email tidak valid.'); redirect('partners.php'); }
        
        // Tax validation
        if ($d['tax_enabled']) {
            if ($d['tax_method'] === 'percentage' && ($d['tax_percentage'] ?? 0) <= 0) {
                flash('danger', 'Persentase pajak wajib diisi untuk metode persentase.'); redirect('partners.php');
            }
            if ($d['tax_method'] === 'manual' && ($d['tax_manual_nominal'] ?? 0) <= 0) {
                flash('danger', 'Nominal pajak wajib diisi untuk metode manual.'); redirect('partners.php');
            }
        }
        if ($id) {
            $cek = db()->prepare('SELECT persentase_bagi_hasil FROM partners WHERE id=?');
            $cek->execute([$id]);
            $pctLama = (float)$cek->fetchColumn();
            
            // Handle file uploads
            if (!empty($_FILES['public_logo']['name'])) {
                $logoName = time() . '_logo_' . basename($_FILES['public_logo']['name']);
                $logoPath = __DIR__ . '/../uploads/partners/' . $logoName;
                if (move_uploaded_file($_FILES['public_logo']['tmp_name'], $logoPath)) {
                    $d['public_logo'] = $logoName;
                }
            }
            
            if (!empty($_FILES['public_cover']['name'])) {
                $coverName = time() . '_cover_' . basename($_FILES['public_cover']['name']);
                $coverPath = __DIR__ . '/../uploads/partners/' . $coverName;
                if (move_uploaded_file($_FILES['public_cover']['tmp_name'], $coverPath)) {
                    $d['public_cover'] = $coverName;
                }
            }
            
            // Handle PDF upload
            if (!empty($_FILES['pdf_file']['name'])) {
                $pdfName = time() . '_pdf_' . basename($_FILES['pdf_file']['name']);
                $pdfPath = __DIR__ . '/../uploads/partner_pdfs/' . $pdfName;
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfPath)) {
                    // Insert or update PDF record
                    $pdfTitle = trim($_POST['pdf_title'] ?? 'Program Lengkap');
                    $pdfDesc = trim($_POST['pdf_description'] ?? '');
                    $pdfPublic = isset($_POST['pdf_public']) ? 1 : 0;
                    
                    // Check if PDF already exists for this partner
                    $checkPdf = db()->prepare('SELECT id FROM partner_programs_pdfs WHERE partner_id = ?');
                    $checkPdf->execute([$id]);
                    $existingPdf = $checkPdf->fetch();
                    
                    if ($existingPdf) {
                        // Update existing PDF
                        db()->prepare('UPDATE partner_programs_pdfs SET pdf_title = ?, pdf_description = ?, pdf_file = ?, is_public = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
                            ->execute([$pdfTitle, $pdfDesc, $pdfName, $pdfPublic, $existingPdf['id']]);
                    } else {
                        // Insert new PDF
                        db()->prepare('INSERT INTO partner_programs_pdfs (partner_id, pdf_title, pdf_description, pdf_file, is_public) VALUES (?, ?, ?, ?, ?)')
                            ->execute([$id, $pdfTitle, $pdfDesc, $pdfName, $pdfPublic]);
                    }
                }
            }
            
            $set = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($d)));
            db()->prepare("UPDATE partners SET $set WHERE id=:id")->execute($d + ['id' => $id]);
            audit('Mengubah mitra: ' . $d['nama_mitra'], 'partners', $id, null, $d);
            if (abs($pctLama - $d['persentase_bagi_hasil']) > 0.0001) {
                // Audit khusus perubahan persentase bagi hasil Yayasan
                audit('Mengubah Persentase Bagi Hasil Yayasan Mitra ' . $d['nama_mitra']
                    . ' dari ' . persen_fmt($pctLama) . '% menjadi ' . persen_fmt($d['persentase_bagi_hasil']) . '%',
                    'partners', $id,
                    ['nama_mitra' => $d['nama_mitra'], 'persentase_bagi_hasil' => $pctLama],
                    ['nama_mitra' => $d['nama_mitra'], 'persentase_bagi_hasil' => $d['persentase_bagi_hasil']]);
            }
            flash('success', 'Data mitra diperbarui.');
        } else {
            // Handle file uploads for new partner
            if (!empty($_FILES['public_logo']['name'])) {
                $logoName = time() . '_logo_' . basename($_FILES['public_logo']['name']);
                $logoPath = __DIR__ . '/../uploads/partners/' . $logoName;
                if (move_uploaded_file($_FILES['public_logo']['tmp_name'], $logoPath)) {
                    $d['public_logo'] = $logoName;
                }
            }
            
            if (!empty($_FILES['public_cover']['name'])) {
                $coverName = time() . '_cover_' . basename($_FILES['public_cover']['name']);
                $coverPath = __DIR__ . '/../uploads/partners/' . $coverName;
                if (move_uploaded_file($_FILES['public_cover']['tmp_name'], $coverPath)) {
                    $d['public_cover'] = $coverName;
                }
            }
            
            $c = implode(',', array_keys($d)); $ph = ':' . implode(',:', array_keys($d));
            db()->prepare("INSERT INTO partners ($c) VALUES ($ph)")->execute($d);
            $newId = (int)db()->lastInsertId();
            
            // Handle PDF upload for new partner
            if (!empty($_FILES['pdf_file']['name'])) {
                $pdfName = time() . '_pdf_' . basename($_FILES['pdf_file']['name']);
                $pdfPath = __DIR__ . '/../uploads/partner_pdfs/' . $pdfName;
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfPath)) {
                    $pdfTitle = trim($_POST['pdf_title'] ?? 'Program Lengkap');
                    $pdfDesc = trim($_POST['pdf_description'] ?? '');
                    $pdfPublic = isset($_POST['pdf_public']) ? 1 : 0;
                    
                    db()->prepare('INSERT INTO partner_programs_pdfs (partner_id, pdf_title, pdf_description, pdf_file, is_public) VALUES (?, ?, ?, ?, ?)')
                        ->execute([$newId, $pdfTitle, $pdfDesc, $pdfName, $pdfPublic]);
                }
            }
            
            audit('Menambah mitra: ' . $d['nama_mitra'] . ' (Bagi Hasil Yayasan ' . persen_fmt($d['persentase_bagi_hasil']) . '%)', 'partners', $newId, null, $d);
            flash('success', 'Mitra baru ditambahkan.');
        }
        redirect('partners.php');
    }
    if ($act === 'akun') {
        $pid = (int)$_POST['partner_id'];
        $st = db()->prepare('SELECT * FROM partners WHERE id=?'); $st->execute([$pid]); $p = $st->fetch();
        $username = trim($_POST['username'] ?? ''); $pass = $_POST['password'] ?? '';
        if (!$p || $username === '' || strlen($pass) < 6) { flash('danger', 'Username wajib diisi & password minimal 6 karakter.'); redirect('partners.php'); }
        $cek = db()->prepare('SELECT id FROM users WHERE username=?'); $cek->execute([$username]);
        if ($cek->fetch()) { flash('danger', 'Username sudah digunakan.'); redirect('partners.php'); }
        db()->prepare('INSERT INTO users (nama, username, email, password, role, status) VALUES (?,?,?,?,"mitra","aktif")')
            ->execute([$p['nama_mitra'], $username, $p['email'] ?: null, password_hash($pass, PASSWORD_DEFAULT)]);
        $uid = (int)db()->lastInsertId();
        db()->prepare('UPDATE partners SET user_id=? WHERE id=?')->execute([$uid, $pid]);
        audit('Membuat akun mitra: ' . $username, 'users', $uid);
        flash('success', 'Akun login mitra berhasil dibuat.');
        redirect('partners.php');
    }
}
if (($_GET['aksi'] ?? '') === 'toggle') {
    $id = (int)$_GET['id'];
    db()->prepare("UPDATE partners SET status = IF(status='aktif','nonaktif','aktif') WHERE id=?")->execute([$id]);
    audit('Mengubah status mitra', 'partners', $id);
    flash('success', 'Status mitra diperbarui.'); redirect('partners.php');
}
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)$_GET['id'];
    $c = db()->prepare('SELECT COUNT(*) FROM students WHERE partner_id=?'); $c->execute([$id]);
    if ($c->fetchColumn() > 0) flash('danger', 'Mitra tidak dapat dihapus karena sudah memiliki data siswa. Nonaktifkan saja.');
    else {
        db()->prepare('DELETE FROM partners WHERE id=?')->execute([$id]);
        audit('Menghapus mitra', 'partners', $id);
        flash('success', 'Mitra dihapus.');
    }
    redirect('partners.php');
}

$row = null;
$pdfData = null;
if (($_GET['aksi'] ?? '') === 'edit') {
    $st = db()->prepare('SELECT * FROM partners WHERE id=?'); $st->execute([(int)$_GET['id']]); $row = $st->fetch();
    
    // Get PDF data for this partner
    $pdfSt = db()->prepare('SELECT * FROM partner_programs_pdfs WHERE partner_id = ? ORDER BY uploaded_at DESC LIMIT 1');
    $pdfSt->execute([(int)$_GET['id']]);
    $pdfData = $pdfSt->fetch();
    
    if ($pdfData) {
        $row['pdf_title'] = $pdfData['pdf_title'];
        $row['pdf_description'] = $pdfData['pdf_description'];
        $row['pdf_file'] = $pdfData['pdf_file'];
        $row['pdf_public'] = $pdfData['is_public'];
    }
}
$q = trim($_GET['q'] ?? ''); $fs = $_GET['status'] ?? '';
$w = ['1=1']; $p = [];
if ($q !== '') { $w[] = '(nama_mitra LIKE ? OR penanggung_jawab LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
if ($fs !== '') { $w[] = 'status=?'; $p[] = $fs; }
$sql = 'SELECT pt.*, u.username, (SELECT COUNT(*) FROM students s WHERE s.partner_id=pt.id) jml_siswa,
        (SELECT COALESCE(SUM(s.total_biaya),0) FROM students s WHERE s.partner_id=pt.id) total_biaya
        FROM partners pt LEFT JOIN users u ON u.id=pt.user_id WHERE ' . implode(' AND ', $w) . ' ORDER BY pt.nama_mitra';
$st = db()->prepare($sql); $st->execute($p); $list = $st->fetchAll();

$PAGE_TITLE = 'Manajemen Mitra Bimbel';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Mitra Bimbel']];
include __DIR__ . '/../includes/header.php';
?>
<?php if (($_GET['aksi'] ?? '') === 'tambah' || $row): ?>
<form method="post" class="card mb-3" enctype="multipart/form-data">
  <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
  <div class="card-header"><?= $row ? 'Edit Mitra' : 'Tambah Mitra' ?></div>
  <div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Nama Mitra *</label><input name="nama_mitra" class="form-control" required value="<?= e($row['nama_mitra'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Penanggung Jawab</label><input name="penanggung_jawab" class="form-control" value="<?= e($row['penanggung_jawab'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Nomor Telepon</label><input name="telepon" class="form-control" value="<?= e($row['telepon'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($row['email'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Tanggal Mulai Kerja Sama</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= e($row['tanggal_mulai'] ?? date('Y-m-d')) ?>"></div>
    <div class="col-md-4"><label class="form-label">Persentase Bagi Hasil Yayasan *</label>
      <div class="input-group">
        <input type="number" name="persentase_bagi_hasil" class="form-control" required
               step="0.01" min="0" max="100"
               value="<?= e($row['persentase_bagi_hasil'] ?? '') ?>" placeholder="Contoh: 15 atau 12.5">
        <span class="input-group-text">%</span>
      </div>
      <div class="form-text">Persentase khusus Mitra ini. Contoh: <code>15</code> = 15%, <code>12.5</code> = 12,5%.</div></div>
    <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select">
      <option value="aktif" <?= ($row['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
      <option value="nonaktif" <?= ($row['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
    <div class="col-12"><hr class="my-2"></div>
    <?php if ($row): ?>
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-info mb-0"><i class="fa-solid fa-calendar-days me-2"></i>Pajak per Tahun Ajaran (Override Global)</h6>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#taxPerYearModal"><i class="fa-solid fa-plus me-1"></i>Tambah/Edit</button>
      </div>
      <div id="taxPerYearList" class="table-responsive-wrapper" style="max-height: 250px; overflow-y: auto;">
        <div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
    </div>
    <?php endif; ?>
    <div class="col-12"><hr class="my-2"></div>
    <div class="col-md-3">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="tax_enabled" name="tax_enabled" <?= ($row['tax_enabled'] ?? 0) ? 'checked' : '' ?>>
        <label class="form-check-label" for="tax_enabled">Gunakan Pajak</label>
      </div>
    </div>
    <div class="col-md-3 tax-field" style="<?= ($row['tax_enabled'] ?? 0) ? 'display:block;' : 'display:none;' ?>">
      <label class="form-label">Metode Pajak</label>
      <select name="tax_method" class="form-select" id="tax_method">
        <option value="none" <?= ($row['tax_method'] ?? 'none') === 'none' ? 'selected' : '' ?>>Tidak Ada</option>
        <option value="percentage" <?= ($row['tax_method'] ?? 'none') === 'percentage' ? 'selected' : '' ?>>Persentase</option>
        <option value="manual" <?= ($row['tax_method'] ?? 'none') === 'manual' ? 'selected' : '' ?>>Manual</option>
      </select>
    </div>
    <div class="col-md-3 tax-field tax-percentage-field" style="<?= ($row['tax_enabled'] ?? 0) && (($row['tax_method'] ?? 'none') === 'percentage') ? 'display:block;' : 'display:none;' ?>">
      <label class="form-label">Persentase Pajak (%)</label>
      <input type="number" name="tax_percentage" class="form-control" step="0.01" min="0" max="100" value="<?= e($row['tax_percentage'] ?? 0) ?>">
    </div>
    <div class="col-md-3 tax-field tax-manual-field" style="<?= ($row['tax_enabled'] ?? 0) && (($row['tax_method'] ?? 'none') === 'manual') ? 'display:block;' : 'display:none;' ?>">
      <label class="form-label">Nominal Pajak (Rp)</label>
      <input type="number" name="tax_manual_nominal" class="form-control" step="1" min="0" value="<?= e($row['tax_manual_nominal'] ?? 0) ?>">
    </div>
    <div class="col-md-6"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"><?= e($row['alamat'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2"><?= e($row['keterangan'] ?? '') ?></textarea></div>
    <div class="col-12"><hr class="my-2"></div>
    <div class="col-12"><h6 class="text-primary mb-3"><i class="fa-solid fa-globe me-2"></i>Informasi Publik (Halaman Mitra Bimbel)</h6></div>
    <div class="col-md-3">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" <?= ($row['is_public'] ?? 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_public">Tampilkan di Halaman Publik</label>
      </div>
    </div>
    <div class="col-md-3"><label class="form-label">Urutan Tampil</label><input type="number" name="display_order" class="form-control" value="<?= e($row['display_order'] ?? 0) ?>"></div>
    <div class="col-12"><label class="form-label">Deskripsi Publik</label><textarea name="public_description" class="form-control" rows="3" placeholder="Deskripsi singkat untuk halaman publik"><?= e($row['public_description'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Program Bimbel</label><input name="public_programs" class="form-control" value="<?= e($row['public_programs'] ?? '') ?>" placeholder="Contoh: Matematika, Bahasa Inggris, IPA"></div>
    <div class="col-md-6"><label class="form-label">Jenjang Pendidikan</label><input name="public_levels" class="form-control" value="<?= e($row['public_levels'] ?? '') ?>" placeholder="Contoh: SD, SMP, SMA (pisahkan dengan koma)"></div>
    <div class="col-md-6"><label class="form-label">Program Unggulan</label><input name="public_program_unggulan" class="form-control" value="<?= e($row['public_program_unggulan'] ?? '') ?>" placeholder="Contoh: Persiapan UTBK, Bimbel Intensif"></div>
    <div class="col-md-6"><label class="form-label">Deskripsi Lengkap</label><textarea name="deskripsi_public" class="form-control" rows="3" placeholder="Deskripsi lengkap program dan keunggulan"><?= e($row['deskripsi_public'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Keunggulan</label><textarea name="keunggulan_public" class="form-control" rows="3" placeholder="Keunggulan program bimbel"><?= e($row['keunggulan_public'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Website</label><input type="url" name="public_website" class="form-control" value="<?= e($row['public_website'] ?? '') ?>" placeholder="https://example.com"></div>
    <div class="col-md-6"><label class="form-label">WhatsApp</label><input name="public_whatsapp" class="form-control" value="<?= e($row['public_whatsapp'] ?? '') ?>" placeholder="08123456789"></div>
    <div class="col-md-6"><label class="form-label">Alamat Publik</label><textarea name="public_address" class="form-control" rows="2"><?= e($row['public_address'] ?? '') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Logo Mitra</label><input type="file" name="public_logo" class="form-control" accept="image/*"><?php if (!empty($row['public_logo'])): ?><br><small class="text-muted">Logo saat ini: <?= e($row['public_logo']) ?></small><?php endif; ?></div>
    <div class="col-md-6"><label class="form-label">Cover Image</label><input type="file" name="public_cover" class="form-control" accept="image/*"><?php if (!empty($row['public_cover'])): ?><br><small class="text-muted">Cover saat ini: <?= e($row['public_cover']) ?></small><?php endif; ?></div>
    <div class="col-12"><hr class="my-2"></div>
    <div class="col-12"><h6 class="text-success mb-3"><i class="fa-solid fa-file-pdf me-2"></i>Program Lengkap (PDF)</h6></div>
    <div class="col-md-6"><label class="form-label">Judul PDF</label><input name="pdf_title" class="form-control" value="<?= e($row['pdf_title'] ?? '') ?>" placeholder="Contoh: Program Lengkap 2024"></div>
    <div class="col-md-6"><label class="form-label">Deskripsi PDF</label><input name="pdf_description" class="form-control" value="<?= e($row['pdf_description'] ?? '') ?>" placeholder="Deskripsi singkat program"></div>
    <div class="col-md-6"><label class="form-label">File PDF</label><input type="file" name="pdf_file" class="form-control" accept=".pdf"><?php if (!empty($row['pdf_file'])): ?><br><small class="text-muted">PDF saat ini: <?= e($row['pdf_file']) ?></small><?php endif; ?></div>
    <div class="col-md-6">
      <div class="form-check form-switch mt-4">
        <input class="form-check-input" type="checkbox" id="pdf_public" name="pdf_public" <?= ($row['pdf_public'] ?? 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="pdf_public">Tampilkan PDF di Halaman Publik</label>
      </div>
    </div>
    <div class="col-12"><hr class="my-2"></div>
    <div class="col-12"><h6 class="text-primary mb-3"><i class="fa-solid fa-video me-2"></i>Video Profil</h6></div>
    <div class="col-md-6"><label class="form-label">URL Video</label><input name="video_url" class="form-control" value="<?= e($row['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=..."></div>
    <div class="col-md-6"><label class="form-label">Platform Video</label>
        <select name="video_platform" class="form-select">
            <option value="youtube" <?= ($row['video_platform'] ?? 'youtube') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
            <option value="drive" <?= ($row['video_platform'] ?? 'youtube') === 'drive' ? 'selected' : '' ?>>Google Drive</option>
            <option value="other" <?= ($row['video_platform'] ?? 'youtube') === 'other' ? 'selected' : '' ?>>Lainnya</option>
        </select>
    </div>
  </div>
  <div class="card-footer bg-white text-end"><a href="partners" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button></div>
</form>
<?php endif; ?>

<form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-4"><label class="form-label">Cari Mitra</label><input name="q" class="form-control" value="<?= e($q) ?>" placeholder="Nama mitra / penanggung jawab"></div>
  <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">
    <option value="">Semua</option><option value="aktif" <?= $fs === 'aktif' ? 'selected' : '' ?>>Aktif</option>
    <option value="nonaktif" <?= $fs === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
  <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Filter</button>
    <a href="partners" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a></div>
  <div class="col-md-2 text-end"><a href="?aksi=tambah" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Tambah</a></div>
</div></form>

<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr>
  <th>ID</th><th>Nama Mitra</th><th>Penanggung Jawab</th><th>Kontak</th><th>Mulai</th>
  <th class="text-end">Bagi Hasil</th>
  <th class="text-end">Siswa</th><th class="text-end">Total Biaya</th>
  <th>Akun</th><th>Status</th><th class="text-end">Aksi</th>
</tr></thead><tbody>
<?php if (!$list): ?><tr><td colspan="12"><div class="empty-state"><i class="fa-solid fa-handshake-slash"></i>Belum ada mitra.</div></td></tr><?php endif; ?>
<?php foreach ($list as $r): ?>
<tr>
  <td>MTR-<?= str_pad((string)$r['id'], 3, '0', STR_PAD_LEFT) ?></td>
  <td><strong><?= e($r['nama_mitra']) ?></strong><br><small class="text-muted"><?= e($r['alamat']) ?></small></td>
  <td><?= e($r['penanggung_jawab'] ?: '-') ?></td>
  <td><small><?= e($r['telepon'] ?: '-') ?><br><?= e($r['email'] ?: '-') ?></small></td>
  <td><?= tgl_id($r['tanggal_mulai']) ?></td>
  <td class="text-end"><span class="badge bg-primary-soft"><?= persen_fmt($r['persentase_bagi_hasil']) ?>%</span></td>
  <td class="text-end"><?= (int)$r['jml_siswa'] ?></td>
  <td class="text-end"><?= rupiah($r['total_biaya']) ?></td>
  <td><?php if ($r['username']): ?><span class="badge bg-primary-soft"><?= e($r['username']) ?></span>
      <?php else: ?><button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#akun<?= (int)$r['id'] ?>"><i class="fa-solid fa-user-plus me-1"></i>Buat Akun</button><?php endif; ?></td>
  <td><?= badge_status($r['status']) ?></td>
  <td class="text-end text-nowrap">
    <a class="btn btn-sm btn-light" href="?aksi=edit&id=<?= (int)$r['id'] ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
    <a class="btn btn-sm btn-light" href="?aksi=toggle&id=<?= (int)$r['id'] ?>" title="Aktif/Nonaktif"><i class="fa-solid fa-power-off"></i></a>
    <a class="btn btn-sm btn-light text-danger" href="?aksi=hapus&id=<?= (int)$r['id'] ?>" data-confirm="Hapus mitra <?= e($r['nama_mitra']) ?>?"><i class="fa-solid fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<?php foreach ($list as $r): ?>
<?php if (!$r['username']): ?>
<div class="modal fade" id="akun<?= (int)$r['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="post" class="modal-content">
  <?= csrf_field() ?><input type="hidden" name="aksi" value="akun"><input type="hidden" name="partner_id" value="<?= (int)$r['id'] ?>">
  <div class="modal-header"><h5 class="modal-title">Buat Akun untuk <?= e($r['nama_mitra']) ?></h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
  <div class="modal-body">
    <div class="mb-2"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
    <div><label class="form-label">Password (min. 6 karakter)</label><input name="password" type="text" class="form-control" required minlength="6"></div>
  </div>
  <div class="modal-footer"><button class="btn btn-primary">Buat Akun</button></div>
</form></div></div></div>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table></div></div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const taxEnabled = document.getElementById('tax_enabled');
  const taxFields = document.querySelectorAll('.tax-field');
  const taxMethod = document.getElementById('tax_method');
  const taxPercentageField = document.querySelector('.tax-percentage-field');
  const taxManualField = document.querySelector('.tax-manual-field');
  
  function toggleTaxFields() {
    const show = taxEnabled.checked;
    taxFields.forEach(field => {
      if (!field.classList.contains('tax-percentage-field') && !field.classList.contains('tax-manual-field')) {
        field.style.display = show ? 'block' : 'none';
      }
    });
    
    if (show) {
      toggleTaxMethodFields();
    } else {
      taxPercentageField.style.display = 'none';
      taxManualField.style.display = 'none';
    }
  }
  
  function toggleTaxMethodFields() {
    const method = taxMethod.value;
    
    if (method === 'percentage') {
      taxPercentageField.style.display = 'block';
      taxManualField.style.display = 'none';
    } else if (method === 'manual') {
      taxPercentageField.style.display = 'none';
      taxManualField.style.display = 'block';
    } else {
      taxPercentageField.style.display = 'none';
      taxManualField.style.display = 'none';
    }
  }
  
  if (taxEnabled) {
    taxEnabled.addEventListener('change', toggleTaxFields);
    // Initial state
    toggleTaxFields();
  }
  
  if (taxMethod) {
    taxMethod.addEventListener('change', toggleTaxMethodFields);
    // Initial state
    toggleTaxMethodFields();
  }
});

// Load tax per year list
<?php if ($row): ?>
function loadTaxPerYearList() {
  const partnerId = <?= (int)($row['id'] ?? 0) ?>;
  if (!partnerId) return;
  
  fetch('api_tax_config.php?partner_id=' + partnerId)
    .then(r => r.json())
    .then(data => {
      const html = data.length ? `<table class="table table-sm mb-0">
        <thead><tr><th>Tahun Ajaran</th><th class="text-end">Metode</th><th class="text-end">Nilai</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>${data.map(d => `<tr>
          <td>${d.tahun_ajaran}</td>
          <td class="text-end"><span class="badge ${d.method === 'percentage' ? 'bg-info-soft' : d.method === 'manual' ? 'bg-warning-soft' : 'bg-secondary-soft'}">${d.method_label}</span></td>
          <td class="text-end">${d.method === 'percentage' ? d.percentage + '%' : 'Rp' + d.manual_nominal}</td>
          <td class="text-end"><button class="btn btn-sm btn-light" onclick="editTaxYear(${d.academic_year_id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-sm btn-light text-danger" onclick="deleteTaxYear(${d.academic_year_id})" title="Hapus"><i class="fa-solid fa-trash"></i></button></td>
        </tr>`).join('')}</tbody>
      </table>` : '<div class="alert alert-info mb-0">Belum ada pajak per tahun ajaran.</div>';
      document.getElementById('taxPerYearList').innerHTML = html;
    }).catch(e => {
      document.getElementById('taxPerYearList').innerHTML = '<div class="alert alert-danger mb-0">Error loading data</div>';
    });
}

function editTaxYear(ayId) {
  document.getElementById('taxYearId').value = ayId;
  document.getElementById('taxYearSelect').value = ayId;
  
  fetch('api_tax_config.php?partner_id=' + <?= (int)($row['id'] ?? 0) ?> + '&academic_year_id=' + ayId)
    .then(r => r.json())
    .then(data => {
      const item = data[0];
      if (item) {
        document.getElementById('taxYearEnabled').checked = item.enabled;
        document.getElementById('taxYearMethod').value = item.method;
        document.getElementById('taxYearPercentage').value = item.percentage;
        document.getElementById('taxYearManual').value = item.manual_nominal;
        toggleTaxYearFields();
      }
    });
  
  new bootstrap.Modal(document.getElementById('taxPerYearModal')).show();
}

function deleteTaxYear(ayId) {
  if (!confirm('Hapus pajak tahun ajaran ini?')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('partner_id', <?= (int)($row['id'] ?? 0) ?>);
  fd.append('academic_year_id', ayId);
  
  fetch('api_tax_config.php', {method: 'POST', body: fd})
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Pajak dihapus');
        loadTaxPerYearList();
      }
    });
}

function toggleTaxYearFields() {
  const enabled = document.getElementById('taxYearEnabled').checked;
  const method = document.getElementById('taxYearMethod').value;
  document.querySelector('.tax-year-fields').style.display = enabled ? 'block' : 'none';
  document.querySelector('.tax-year-percentage-field').style.display = (enabled && method === 'percentage') ? 'block' : 'none';
  document.querySelector('.tax-year-manual-field').style.display = (enabled && method === 'manual') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
  loadTaxPerYearList();
  
  const taxYearEnabled = document.getElementById('taxYearEnabled');
  const taxYearMethod = document.getElementById('taxYearMethod');
  if (taxYearEnabled) taxYearEnabled.addEventListener('change', toggleTaxYearFields);
  if (taxYearMethod) taxYearMethod.addEventListener('change', toggleTaxYearFields);
  
  const taxYearForm = document.getElementById('taxYearForm');
  if (taxYearForm) {
    taxYearForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      fd.append('partner_id', <?= (int)($row['id'] ?? 0) ?>);
      fetch('api_tax_config.php', {method: 'POST', body: fd})
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            alert('Pajak disimpan');
            bootstrap.Modal.getInstance(document.getElementById('taxPerYearModal')).hide();
            loadTaxPerYearList();
            document.getElementById('taxYearForm').reset();
          } else {
            alert('Error: ' + (data.error || 'Unknown error'));
          }
        });
    });
  }
});
<?php endif; ?>
</script>

<?php if ($row): ?>
<div class="modal fade" id="taxPerYearModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" id="taxYearForm">
      <div class="modal-header">
        <h5 class="modal-title">Pajak per Tahun Ajaran</h5>
        <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="taxYearId">
        <div class="mb-3">
          <label class="form-label">Tahun Ajaran</label>
          <select id="taxYearSelect" name="academic_year_id" class="form-select" required>
            <option value="">Pilih Tahun Ajaran</option>
            <?php
            $allYears = db()->query('SELECT id, nama_tahun_ajaran FROM academic_years ORDER BY nama_tahun_ajaran')->fetchAll();
            foreach ($allYears as $ay) {
              echo '<option value="' . (int)$ay['id'] . '">' . e($ay['nama_tahun_ajaran']) . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="mb-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="taxYearEnabled" name="tax_enabled">
            <label class="form-check-label" for="taxYearEnabled">Gunakan Pajak</label>
          </div>
        </div>
        <div class="tax-year-fields" style="display:none;">
          <div class="mb-3">
            <label class="form-label">Metode Pajak</label>
            <select id="taxYearMethod" name="tax_method" class="form-select">
              <option value="none">Tidak Ada</option>
              <option value="percentage">Persentase</option>
              <option value="manual">Manual</option>
            </select>
          </div>
          <div class="mb-3 tax-year-percentage-field" style="display:none;">
            <label class="form-label">Persentase Pajak (%)</label>
            <input type="number" id="taxYearPercentage" name="tax_percentage" class="form-control" step="0.01" min="0" max="100" value="0">
          </div>
          <div class="mb-3 tax-year-manual-field" style="display:none;">
            <label class="form-label">Nominal Pajak (Rp)</label>
            <input type="number" id="taxYearManual" name="tax_manual_nominal" class="form-control" step="1" min="0" value="0">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
