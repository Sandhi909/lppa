<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra']);
require_once __DIR__ . '/../includes/setoran_bagi_hasil.php';
require_once __DIR__ . '/../includes/queries.php';

$pid = my_partner_id();
if (!$pid) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

$SELF = basename($_SERVER['PHP_SELF']);

if (($_GET['aksi'] ?? '') === 'bukti') {
    setoran_kirim_bukti((int)($_GET['id'] ?? 0), $pid);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['aksi'] ?? '';

    if ($act === 'tambah') {
    $tanggal    = $_POST['tanggal_setoran'] ?: date('Y-m-d');
    $nominal    = (float)($_POST['nominal'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');
    $academicYearId = (int)($_POST['academic_year_id'] ?? 0);

    $err = setoran_validasi_nominal($nominal);
    if (!$academicYearId) {
        $err[] = 'Tahun ajaran wajib dipilih.';
    }
    if (empty($_FILES['bukti']['name'])) {
        $err[] = 'Bukti transfer wajib diunggah.';
    }
    if ($err) {
        flash('danger', implode(' ', $err));
        redirect($SELF . '?aksi=tambah');
    }

    try {
        $bukti = setoran_unggah_bukti($_FILES['bukti'] ?? []);
    } catch (RuntimeException $ex) {
        flash('danger', $ex->getMessage());
        redirect($SELF . '?aksi=tambah');
    }
    if (!$bukti) {
        flash('danger', 'Bukti transfer wajib diunggah.');
        redirect($SELF . '?aksi=tambah');
    }

    $data = [
        'partner_id'      => $pid,
        'academic_year_id' => $academicYearId,
        'tanggal_setoran' => $tanggal,
        'nominal'         => $nominal,
        'bukti_file'      => $bukti,
        'keterangan'      => $keterangan,
        'status'          => 'pending',
        'sumber'          => 'mitra',
        'created_by'      => (int)($_SESSION['user']['id'] ?? 0),
    ];
    $newId = setoran_insert($data);
    audit('Mitra menambah setoran bagi hasil: ' . rupiah($nominal) . ' (Pending)', 'setoran_bagi_hasil', $newId, null, $data);
    flash('success', 'Setoran berhasil dikirim. Menunggu persetujuan Admin LPPA.');
    redirect($SELF);
    }

    if ($act === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $row = setoran_by_id($id);
    if (!$row || (int)$row['partner_id'] !== $pid || $row['status'] !== 'pending') {
        flash('danger', 'Hanya setoran dengan status Pending milik Anda yang dapat diubah.');
        redirect($SELF);
    }
    $tanggal    = $_POST['tanggal_setoran'] ?: date('Y-m-d');
    $nominal    = (float)($_POST['nominal'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    $err = setoran_validasi_nominal($nominal);
    if ($err) {
        flash('danger', implode(' ', $err));
        redirect($SELF . '?aksi=edit&id=' . $id);
    }
    try {
        $bukti = setoran_unggah_bukti($_FILES['bukti'] ?? [], $row['bukti_file'] ?: null);
    } catch (RuntimeException $ex) {
        flash('danger', $ex->getMessage());
        redirect($SELF . '?aksi=edit&id=' . $id);
    }

    setoran_update($id, [
        'tanggal_setoran' => $tanggal,
        'nominal'         => $nominal,
        'bukti_file'      => $bukti !== null ? $bukti : $row['bukti_file'],
        'keterangan'      => $keterangan,
    ]);
    audit('Mitra mengubah setoran bagi hasil #' . $id, 'setoran_bagi_hasil', $id, $row, $_POST);
    flash('success', 'Setoran bagi hasil berhasil diubah.');
    redirect($SELF);
    }

    if ($act === 'hapus') {
    $id = (int)($_POST['id'] ?? 0);
    $row = setoran_by_id($id);
    if (!$row || (int)$row['partner_id'] !== $pid || $row['status'] !== 'pending') {
        flash('danger', 'Hanya setoran dengan status Pending milik Anda yang dapat dihapus.');
    } else {
        setoran_delete($id);
        audit('Mitra menghapus setoran bagi hasil #' . $id, 'setoran_bagi_hasil', $id, $row);
        flash('success', 'Setoran bagi hasil #' . $id . ' berhasil dihapus.');
    }
    redirect($SELF);
    }
}

$aksi = $_GET['aksi'] ?? '';
$editRow = null;
if ($aksi === 'edit') {
    $editRow = setoran_by_id((int)($_GET['id'] ?? 0));
    if (!$editRow || (int)$editRow['partner_id'] !== $pid || $editRow['status'] !== 'pending') {
        $editRow = null;
        flash('warning', 'Setoran tidak ditemukan atau tidak dapat diedit.');
    }
}

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

$filterAcademicYear = (int)($_GET['academic_year_id'] ?? 0);
$ringkasan = ringkasan_setoran_mitra($pid, $filterAcademicYear);
$filters = ['partner_id' => $pid, 'academic_year_id' => $filterAcademicYear ?: null];
$per = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$total = setoran_count($filters);
$rows = setoran_list($filters, $per, ($page - 1) * $per);

$stMitra = db()->prepare('SELECT nama_mitra FROM partners WHERE id = ?');
$stMitra->execute([$pid]);
$namaMitra = $stMitra->fetchColumn() ?: 'Mitra';

$PAGE_TITLE = 'Setoran Bagi Hasil';
$BREADCRUMB = [
    ['label' => 'Dashboard', 'url' => base_url('mitra/index.php')],
    ['label' => 'Setoran Bagi Hasil'],
];
include __DIR__ . '/../includes/header.php';
?>

<div class="alert alert-info">
  <i class="fa-solid fa-circle-info me-1"></i>
  Setoran bagi hasil dapat ditambahkan berkali-kali. Setiap pengajuan berstatus <strong>Pending</strong>
  sampai Admin LPPA menyetujui atau menolak.
</div>

<div class="row g-3 mb-3">
  <?php
  // Use the selected academic year filter
  $taxFilters = $filterAcademicYear ? ['academic_year_id' => $filterAcademicYear] : [];
  [$where, $params] = student_filters($taxFilters, ['partner_id' => $pid]);
  $taxInfo = info_pajak_mitra($pid, $where, $params);
  ?>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon navy"><i class="fa-solid fa-file-invoice-dollar"></i></span>
    <div><div class="label">Tagihan Bagi Hasil</div>
      <div class="value"><?= rupiah($ringkasan['tagihan']) ?></div>
      <div class="small text-muted">Dari data siswa & persentase mitra</div></div></div></div>
  <?php if ($taxInfo['pajak_nominal'] > 0): ?>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon amber"><i class="fa-solid fa-percent"></i></span>
    <div><div class="label">Pajak (<?= persen_fmt($taxInfo['pajak_percentage']) ?>%)</div>
      <div class="value text-danger">-<?= rupiah($taxInfo['pajak_nominal']) ?></div>
      <div class="small text-muted">Dipotong dari bagi hasil</div></div></div></div>
  <?php endif; ?>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon green"><i class="fa-solid fa-circle-check"></i></span>
    <div><div class="label">Total Setoran Disetujui</div>
      <div class="value"><?= rupiah($ringkasan['disetujui']) ?></div></div></div></div>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon <?= $ringkasan['sisa'] > 0 ? 'amber' : 'green' ?>"><i class="fa-solid fa-scale-balanced"></i></span>
    <div><div class="label">Sisa Setoran</div>
      <div class="value"><?= rupiah($ringkasan['sisa']) ?></div></div></div></div>
</div>

<?php if ($aksi === 'tambah'): ?>
<form method="post" enctype="multipart/form-data" class="card mb-3">
  <?= csrf_field() ?>
  <input type="hidden" name="aksi" value="tambah">
  <div class="card-header"><i class="fa-solid fa-plus me-2"></i>Tambah Setoran Bagi Hasil</div>
  <div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Mitra</label>
      <input type="text" class="form-control" readonly value="<?= e($namaMitra) ?>"></div>
    <div class="col-md-4"><label class="form-label">Tahun Ajaran *</label>
      <select name="academic_year_id" class="form-select" required>
        <option value="">-- Pilih Tahun Ajaran --</option>
        <?php 
        $academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
        foreach ($academicYears as $ay): 
        ?>
          <option value="<?= $ay['id'] ?>" <?= $filterAcademicYear == $ay['id'] ? 'selected' : '' ?>><?= e($ay['nama_tahun_ajaran']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-4"><label class="form-label">Tanggal Setoran *</label>
      <input type="date" name="tanggal_setoran" class="form-control" required value="<?= e(date('Y-m-d')) ?>"></div>
    <div class="col-md-4"><label class="form-label">Nominal (Rp) *</label>
      <input type="number" name="nominal" class="form-control" min="0" step="any" required placeholder="0"></div>
    <div class="col-md-6"><label class="form-label">Bukti Transfer *</label>
      <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
      <div class="form-text">JPG, PNG, atau PDF maks. 2 MB.</div></div>
    <div class="col-md-6"><label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control" maxlength="255" placeholder="Contoh: Transfer BCA ke rekening LPPA"></div>
    <div class="col-12"><div class="alert alert-light border mb-0 small">
      Setoran akan berstatus <strong>Pending</strong> hingga disetujui Admin. Sisa setoran saat ini:
      <strong><?= rupiah($ringkasan['sisa']) ?></strong>.</div></div>
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a href="<?= e($SELF) ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i>Kirim Setoran</button>
  </div>
</form>
<?php endif; ?>

<?php if ($aksi === 'edit' && $editRow): ?>
<form method="post" enctype="multipart/form-data" class="card mb-3">
  <?= csrf_field() ?>
  <input type="hidden" name="aksi" value="update">
  <input type="hidden" name="id" value="<?= (int)$editRow['id'] ?>">
  <div class="card-header"><i class="fa-solid fa-pen me-2"></i>Edit Setoran Bagi Hasil</div>
  <div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Mitra</label>
      <input type="text" class="form-control" readonly value="<?= e($namaMitra) ?>"></div>
    <div class="col-md-4"><label class="form-label">Tanggal Setoran *</label>
      <input type="date" name="tanggal_setoran" class="form-control" required value="<?= e($editRow['tanggal_setoran']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Nominal (Rp) *</label>
      <input type="number" name="nominal" class="form-control" min="0" step="any" required value="<?= e((string)(float)$editRow['nominal']) ?>"></div>
    <div class="col-md-12"><label class="form-label">Bukti Transfer</label>
      <?php if ($editRow['bukti_file']): ?>
        <a href="<?= e(setoran_bukti_url($SELF, (int)$editRow['id'])) ?>" target="_blank" class="btn btn-sm btn-light mb-2">
          <i class="fa-solid fa-paperclip"></i> Lihat bukti lama</a><br>
      <?php endif; ?>
      <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
      <div class="form-text">Opsional. Biarkan kosong jika tidak ingin mengganti.</div></div>
    <div class="col-12"><label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control" maxlength="255" value="<?= e($editRow['keterangan'] ?: '') ?>"></div>
    <div class="col-12"><div class="alert alert-light border mb-0 small">
      Setoran akan berstatus <strong>Pending</strong> hingga disetujui Admin. Sisa setoran saat ini: <strong><?= rupiah($ringkasan['sisa']) ?></strong>.</div></div>
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a href="<?= e($SELF) ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan</button>
  </div>
</form>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
  <div class="small text-muted">Riwayat setoran <strong><?= e($namaMitra) ?></strong> — <?= number_format($total, 0, ',', '.') ?> entri</div>
  <a href="?aksi=tambah" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tambah Setoran</a>
</div>

<form class="card mb-3" method="get" data-noload>
  <div class="card-body row g-2 align-items-end">
    <div class="col-md-4"><label class="form-label">Tahun Ajaran</label>
      <select name="academic_year_id" class="form-select">
        <option value="">Semua</option>
        <?php 
        $academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
        foreach ($academicYears as $ay): 
        ?>
          <option value="<?= $ay['id'] ?>" <?= $filterAcademicYear == $ay['id'] ? 'selected' : '' ?>><?= e($ay['nama_tahun_ajaran']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-8 d-flex gap-2">
      <button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Filter</button>
      <a href="<?= e($SELF) ?>" class="btn btn-light" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </div>
  </div>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle mb-0">
  <thead><tr>
    <th>Tanggal</th><th>Tahun Ajaran</th><th class="text-end">Nominal</th><th>Status</th><th>Bukti</th>
    <th>Keterangan</th><th>Catatan Admin</th><th class="text-end no-print">Aksi</th>
  </tr></thead>
  <tbody>
  <?php if (!$rows): ?>
    <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada setoran. Klik Tambah Setoran untuk mengajukan.</div></td></tr>
  <?php endif; ?>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td class="text-nowrap"><?= tgl_id($r['tanggal_setoran']) ?><br><small class="text-muted">#<?= (int)$r['id'] ?></small></td>
      <td><?= e($r['nama_tahun_ajaran'] ?? '-') ?></td>
      <td class="text-end fw-semibold"><?= rupiah($r['nominal']) ?></td>
      <td><?= badge_setoran_status($r['status']) ?></td>
      <td><?php if ($r['bukti_file']): ?>
        <a href="<?= e(setoran_bukti_url($SELF, (int)$r['id'])) ?>" target="_blank" class="btn btn-sm btn-light">
          <i class="fa-solid fa-paperclip"></i> Lihat</a>
      <?php else: ?>-<?php endif; ?></td>
      <td><small><?= e($r['keterangan'] ?: '-') ?></small></td>
      <td><small class="text-muted"><?= e($r['catatan_review'] ?: ($r['status'] === 'pending' ? 'Menunggu review' : '-')) ?></small></td>
      <td class="text-end text-nowrap no-print">
        <?php if ($r['status'] === 'pending'): ?>
          <a href="?aksi=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus setoran #<?= (int)$r['id'] ?> ini? Data tidak bisa dikembalikan.')">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash"></i></button>
          </form>
        <?php else: ?>
          <span class="text-muted">—</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div></div></div>
<?php pagination($total, $per, $page, $_GET); ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
