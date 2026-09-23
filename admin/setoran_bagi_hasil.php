<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/setoran_bagi_hasil.php';
require_once __DIR__ . '/../includes/queries.php';

$SELF = basename($_SERVER['PHP_SELF']);

if (($_GET['aksi'] ?? '') === 'bukti') {
    setoran_kirim_bukti((int)($_GET['id'] ?? 0));
}

/* ---------- POST: tambah manual / approve / reject ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['aksi'] ?? '';

    if ($act === 'tambah') {
        $partnerId = (int)($_POST['partner_id'] ?? 0);
        $tanggal   = $_POST['tanggal_setoran'] ?: date('Y-m-d');
        $nominal   = (float)($_POST['nominal'] ?? 0);
        $keterangan = trim($_POST['keterangan'] ?? '');
        $autoApprove = ($_POST['auto_approve'] ?? '1') === '1';
        $academicYearId = (int)($_POST['academic_year_id'] ?? 0);

        $err = setoran_validasi_nominal($nominal);
        if (!$partnerId) {
            $err[] = 'Mitra wajib dipilih.';
        }
        if (!$academicYearId) {
            $err[] = 'Tahun ajaran wajib dipilih.';
        }
        $cek = db()->prepare('SELECT id FROM partners WHERE id = ?');
        $cek->execute([$partnerId]);
        if (!$cek->fetch()) {
            $err[] = 'Mitra tidak ditemukan.';
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

        $status = $autoApprove ? 'disetujui' : 'pending';
        
        // Calculate tax for historical record
        $tax = get_tax_config($partnerId);
        $pajak = 0;
        if ($tax['enabled']) {
            if ($tax['method'] === 'percentage') {
                $pajak = $nominal * $tax['percentage'] / 100;
            } elseif ($tax['method'] === 'manual') {
                $pajak = $tax['manual_nominal'];
            }
        }
        
        $data = [
            'partner_id'      => $partnerId,
            'academic_year_id' => $academicYearId,
            'tanggal_setoran' => $tanggal,
            'nominal'         => $nominal,
            'bukti_file'      => $bukti,
            'keterangan'      => $keterangan,
            'status'          => $status,
            'sumber'          => 'admin',
            'created_by'      => (int)($_SESSION['user']['id'] ?? 0),
            'tax_method'      => $tax['method'],
            'tax_percentage'  => $tax['percentage'],
            'tax_manual_nominal' => $tax['manual_nominal'],
            'tax_amount'     => $pajak,
            'amount_before_tax' => $nominal,
            'amount_after_tax' => $nominal - $pajak,
        ];
        if ($autoApprove) {
            $data['reviewed_by'] = (int)($_SESSION['user']['id'] ?? 0);
            $data['reviewed_at'] = date('Y-m-d H:i:s');
        }

        $cols = implode(', ', array_keys($data));
        $ph = ':' . implode(', :', array_keys($data));
        db()->prepare("INSERT INTO setoran_bagi_hasil ($cols) VALUES ($ph)")->execute($data);
        $newId = (int)db()->lastInsertId();
        audit('Admin menambah setoran bagi hasil: ' . rupiah($nominal) . ' (' . setoran_status_label($status) . ')', 'setoran_bagi_hasil', $newId, null, $data);
        flash('success', 'Setoran bagi hasil berhasil dicatat. Status: ' . setoran_status_label($status) . '.');
        redirect($SELF);
    }

    if ($act === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $row = setoran_by_id($id);
        if (!$row || $row['status'] !== 'pending') {
            flash('danger', 'Hanya setoran dengan status Pending yang dapat diubah.');
            redirect($SELF);
        }
        $partnerId = (int)($_POST['partner_id'] ?? 0);
        $tanggal   = $_POST['tanggal_setoran'] ?: date('Y-m-d');
        $nominal   = (float)($_POST['nominal'] ?? 0);
        $keterangan = trim($_POST['keterangan'] ?? '');
        $err = setoran_validasi_nominal($nominal);
        if (!$partnerId) {
            $err[] = 'Mitra wajib dipilih.';
        }
        $cek = db()->prepare('SELECT id FROM partners WHERE id = ?');
        $cek->execute([$partnerId]);
        if (!$cek->fetch()) {
            $err[] = 'Mitra tidak ditemukan.';
        }
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
            'partner_id'      => $partnerId,
            'tanggal_setoran' => $tanggal,
            'nominal'         => $nominal,
            'bukti_file'      => $bukti !== null ? $bukti : $row['bukti_file'],
            'keterangan'      => $keterangan,
        ]);
        audit('Admin mengubah setoran bagi hasil #' . $id, 'setoran_bagi_hasil', $id, $row, $_POST);
        flash('success', 'Setoran bagi hasil berhasil diubah.');
        redirect($SELF);
    }

    if ($act === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        $row = setoran_by_id($id);
        if (!$row || $row['status'] !== 'pending') {
            flash('danger', 'Hanya setoran dengan status Pending yang dapat dihapus.');
        } else {
            setoran_delete($id);
            audit('Admin menghapus setoran bagi hasil #' . $id, 'setoran_bagi_hasil', $id, $row);
            flash('success', 'Setoran bagi hasil #' . $id . ' berhasil dihapus.');
        }
        redirect($SELF);
    }

    if ($act === 'review') {
        $id = (int)($_POST['id'] ?? 0);
        $keputusan = $_POST['keputusan'] ?? '';
        $catatan = trim($_POST['catatan_review'] ?? '');
        $row = setoran_by_id($id);
        if (!$row) {
            flash('danger', 'Data setoran tidak ditemukan.');
            redirect($SELF);
        }
        if ($row['status'] !== 'pending') {
            flash('warning', 'Setoran ini sudah diproses sebelumnya.');
            redirect($SELF);
        }
        if (!in_array($keputusan, ['disetujui', 'ditolak'], true)) {
            flash('danger', 'Keputusan tidak valid.');
            redirect($SELF);
        }
        setoran_update_status($id, $keputusan, $catatan !== '' ? $catatan : null);
        audit(
            'Admin ' . ($keputusan === 'disetujui' ? 'menyetujui' : 'menolak') . ' setoran bagi hasil #' . $id
                . ' (' . rupiah((float)$row['nominal']) . ')',
            'setoran_bagi_hasil',
            $id,
            ['status' => 'pending'],
            ['status' => $keputusan, 'catatan_review' => $catatan]
        );
        flash('success', 'Setoran #' . $id . ' berhasil ' . ($keputusan === 'disetujui' ? 'disetujui' : 'ditolak') . '.');
        redirect($SELF);
    }
}

$aksi = $_GET['aksi'] ?? '';
$editRow = null;
if ($aksi === 'edit') {
    $editRow = setoran_by_id((int)($_GET['id'] ?? 0));
    if (!$editRow || $editRow['status'] !== 'pending') {
        $editRow = null;
    }
}
$listMitra = opsi('partners', 'nama_mitra', '');
$filterPartner = (int)($_GET['partner_id'] ?? 0);
$filterAcademicYear = (int)($_GET['academic_year_id'] ?? 0);

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
    $filterAcademicYear = $defaultTAId;
}

$filters = array_filter([
    'partner_id' => $filterPartner ?: null,
    'academic_year_id' => $filterAcademicYear ?: null,
    'status'     => $_GET['status'] ?? '',
    'dari'       => $_GET['dari'] ?? '',
    'sampai'     => $_GET['sampai'] ?? '',
]);
$per = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$total = setoran_count($filters);
$rows = setoran_list($filters, $per, ($page - 1) * $per);

$ringkasanGlobal = ['tagihan' => 0.0, 'disetujui' => 0.0, 'sisa' => 0.0];
$mitraRingkasan = [];
foreach (db()->query('SELECT id, nama_mitra FROM partners ORDER BY nama_mitra')->fetchAll() as $m) {
    $pid = (int)$m['id'];
    $r = ringkasan_setoran_mitra($pid, $filterAcademicYear);
    $mitraRingkasan[$pid] = $r + ['nama_mitra' => $m['nama_mitra']];
    if (!$filterPartner || $filterPartner === $pid) {
        $ringkasanGlobal['tagihan'] += $r['tagihan'];
        $ringkasanGlobal['disetujui'] += $r['disetujui'];
        $ringkasanGlobal['sisa'] += $r['sisa'];
    }
}
if ($filterPartner && isset($mitraRingkasan[$filterPartner])) {
    $ringkasanGlobal = $mitraRingkasan[$filterPartner];
}

$PAGE_TITLE = 'Setoran Bagi Hasil';
$BREADCRUMB = [
    ['label' => 'Dashboard', 'url' => base_url('admin/index.php')],
    ['label' => 'Setoran Bagi Hasil'],
];
include __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon navy"><i class="fa-solid fa-file-invoice-dollar"></i></span>
    <div><div class="label">Tagihan Bagi Hasil<?= $filterPartner ? '' : ' (filter)' ?></div>
      <div class="value"><?= rupiah($ringkasanGlobal['tagihan']) ?></div></div></div></div>
  <?php if ($filterPartner): ?>
  <?php
  $taxInfo = info_pajak_mitra($filterPartner);
  if ($taxInfo['pajak_nominal'] > 0):
  ?>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon amber"><i class="fa-solid fa-percent"></i></span>
    <div><div class="label">Pajak (<?= persen_fmt($taxInfo['pajak_percentage']) ?>%)</div>
      <div class="value text-danger">-<?= rupiah($taxInfo['pajak_nominal']) ?></div>
      <div class="small text-muted">Dipotong dari bagi hasil</div></div></div></div>
  <?php endif; ?>
  <?php endif; ?>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon green"><i class="fa-solid fa-circle-check"></i></span>
    <div><div class="label">Total Setoran Disetujui</div>
      <div class="value"><?= rupiah($ringkasanGlobal['disetujui']) ?></div></div></div></div>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center">
    <span class="stat-icon <?= $ringkasanGlobal['sisa'] > 0 ? 'amber' : 'green' ?>"><i class="fa-solid fa-scale-balanced"></i></span>
    <div><div class="label">Sisa Setoran</div>
      <div class="value"><?= rupiah($ringkasanGlobal['sisa']) ?></div></div></div></div>
</div>

<?php if ($aksi === 'tambah'): ?>
<form method="post" enctype="multipart/form-data" class="card mb-3">
  <?= csrf_field() ?>
  <input type="hidden" name="aksi" value="tambah">
  <div class="card-header"><i class="fa-solid fa-plus me-2"></i>Input Setoran Manual (Admin)</div>
  <div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Mitra Bimbel *</label>
      <select name="partner_id" class="form-select" required>
        <option value="">-- Pilih Mitra --</option>
        <?php foreach ($listMitra as $m): ?>
          <option value="<?= (int)$m['id'] ?>"><?= e($m['nama']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-3"><label class="form-label">Tahun Ajaran *</label>
      <select name="academic_year_id" class="form-select" required>
        <option value="">-- Pilih Tahun Ajaran --</option>
        <?php 
        $academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
        foreach ($academicYears as $ay): 
        ?>
          <option value="<?= $ay['id'] ?>" <?= $filterAcademicYear == $ay['id'] ? 'selected' : '' ?>><?= e($ay['nama_tahun_ajaran']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-2"><label class="form-label">Tanggal Setoran *</label>
      <input type="date" name="tanggal_setoran" class="form-control" required value="<?= e(date('Y-m-d')) ?>"></div>
    <div class="col-md-3"><label class="form-label">Nominal (Rp) *</label>
      <input type="number" name="nominal" id="nominal_input" class="form-control" min="0" step="any" required placeholder="0"></div>
    <div class="col-md-2"><label class="form-label">Status awal</label>
      <select name="auto_approve" class="form-select">
        <option value="1">Langsung Disetujui</option>
        <option value="0">Pending</option>
      </select></div>
    <div class="col-md-4">
      <label class="form-label">Preview Pajak</label>
      <div id="tax_preview" class="border rounded p-2 bg-light">
        <small class="text-muted">Metode: Tidak ada</small><br>
        <small class="text-muted">Pajak: Rp0</small><br>
        <small class="text-success">Total: Rp0</small>
      </div>
    </div>
    <div class="col-md-4"><label class="form-label">Bukti Transfer</label>
      <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
      <div class="form-text">Opsional. JPG, PNG, atau PDF maks. 2 MB.</div></div>
    <div class="col-md-8"><label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control" maxlength="255"></div>
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a href="<?= e($SELF) ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button>
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
    <div class="col-md-4"><label class="form-label">Mitra Bimbel *</label>
      <select name="partner_id" id="partner_select" class="form-select" required>
        <option value="">-- Pilih Mitra --</option>
        <?php foreach ($listMitra as $m): ?>
          <option value="<?= (int)$m['id'] ?>" <?= (int)$m['id'] === (int)$editRow['partner_id'] ? 'selected' : '' ?>><?= e($m['nama']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-3"><label class="form-label">Tanggal Setoran *</label>
      <input type="date" name="tanggal_setoran" class="form-control" required value="<?= e($editRow['tanggal_setoran']) ?>"></div>
    <div class="col-md-3"><label class="form-label">Nominal (Rp) *</label>
      <input type="number" name="nominal" id="nominal_input" class="form-control" min="0" step="any" required value="<?= e((string)(float)$editRow['nominal']) ?>"></div>
    <div class="col-md-2"><label class="form-label">Status</label>
      <input type="text" class="form-control" readonly value="<?= e(setoran_status_label($editRow['status'])) ?>"></div>
    <div class="col-md-8">
      <label class="form-label">Preview Pajak</label>
      <div id="tax_preview" class="border rounded p-2 bg-light">
        <small class="text-muted">Metode: Tidak ada</small><br>
        <small class="text-muted">Pajak: Rp0</small><br>
        <small class="text-success">Total: Rp0</small>
      </div>
    </div>
    <div class="col-md-4"><label class="form-label">Bukti Transfer</label>
      <?php if ($editRow['bukti_file']): ?>
        <a href="<?= e(setoran_bukti_url($SELF, (int)$editRow['id'])) ?>" target="_blank" class="btn btn-sm btn-light mb-2">
          <i class="fa-solid fa-paperclip"></i> Lihat bukti lama</a><br>
      <?php endif; ?>
      <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
      <div class="form-text">Opsional. Biarkan kosong jika tidak ingin mengganti.</div></div>
    <div class="col-md-8"><label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control" maxlength="255" value="<?= e($editRow['keterangan'] ?: '') ?>"></div>
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a href="<?= e($SELF) ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan</button>
  </div>
</form>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
  <div class="small text-muted">Menampilkan <strong><?= number_format($total, 0, ',', '.') ?></strong> setoran</div>
  <a href="?aksi=tambah" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Input Manual</a>
</div>

<form class="card mb-3" method="get" data-noload>
  <div class="card-body row g-2 align-items-end">
    <div class="col-md-2"><label class="form-label">Tahun Ajaran</label>
      <select name="academic_year_id" class="form-select">
        <option value="">Semua</option>
        <?php 
        $academicYears = db()->query("SELECT * FROM academic_years ORDER BY nama_tahun_ajaran DESC")->fetchAll();
        foreach ($academicYears as $ay): 
        ?>
          <option value="<?= $ay['id'] ?>" <?= $filterAcademicYear == $ay['id'] ? 'selected' : '' ?>><?= e($ay['nama_tahun_ajaran']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-3"><label class="form-label">Mitra</label>
      <select name="partner_id" class="form-select">
        <option value="">Semua Mitra</option>
        <?php foreach ($listMitra as $m): ?>
          <option value="<?= (int)$m['id'] ?>" <?= $filterPartner === (int)$m['id'] ? 'selected' : '' ?>><?= e($m['nama']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-2"><label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="">Semua</option>
        <?php foreach (SETORAN_STATUS as $k => $lbl): ?>
          <option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-2"><label class="form-label">Dari</label>
      <input type="date" name="dari" class="form-control" value="<?= e($_GET['dari'] ?? '') ?>"></div>
    <div class="col-md-2"><label class="form-label">Sampai</label>
      <input type="date" name="sampai" class="form-control" value="<?= e($_GET['sampai'] ?? '') ?>"></div>
    <div class="col-md-1 d-flex gap-2">
      <button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
    </div>
    <div class="col-md-1 d-flex gap-2">
      <a href="<?= e($SELF) ?>" class="btn btn-light w-100" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </div>
  </div>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle mb-0">
  <thead><tr>
    <th>Tanggal</th><th>Tahun Ajaran</th><th>Mitra</th><th class="text-end">Nominal</th><th>Status</th>
    <th>Sumber</th><th>Bukti</th><th>Keterangan</th><th class="text-end no-print">Aksi</th>
  </tr></thead>
  <tbody>
  <?php if (!$rows): ?>
    <tr><td colspan="9"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada data setoran.</div></td></tr>
  <?php endif; ?>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td class="text-nowrap"><?= tgl_id($r['tanggal_setoran']) ?><br>
        <small class="text-muted">#<?= (int)$r['id'] ?></small></td>
      <td><?= e($r['nama_tahun_ajaran'] ?? '-') ?></td>
      <td><strong><?= e($r['nama_mitra']) ?></strong></td>
      <td class="text-end fw-semibold">
        <?= rupiah($r['nominal']) ?>
        <?php if ($r['tax_amount'] > 0): ?>
          <br><small class="text-muted">Pajak: <?= rupiah($r['tax_amount']) ?></small>
          <br><small class="text-success">Net: <?= rupiah($r['amount_after_tax']) ?></small>
        <?php endif; ?>
      </td>
      <td><?= badge_setoran_status($r['status']) ?></td>
      <td><span class="badge bg-secondary-soft"><?= $r['sumber'] === 'admin' ? 'Admin' : 'Mitra' ?></span></td>
      <td><?php if ($r['bukti_file']): ?>
        <a href="<?= e(setoran_bukti_url($SELF, (int)$r['id'])) ?>" target="_blank" class="btn btn-sm btn-light">
          <i class="fa-solid fa-paperclip"></i></a>
      <?php else: ?>-<?php endif; ?></td>
      <td><small><?= e($r['keterangan'] ?: '-') ?></small>
        <?php if ($r['catatan_review']): ?><br><small class="text-muted">Review: <?= e($r['catatan_review']) ?></small><?php endif; ?>
      </td>
      <td class="text-end text-nowrap no-print">
        <?php if ($r['status'] === 'pending'): ?>
          <a href="?aksi=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus setoran #<?= (int)$r['id'] ?> ini? Data tidak bisa dikembalikan.')">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash"></i></button>
          </form>
          <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalReview"
            data-id="<?= (int)$r['id'] ?>" data-nama="<?= e($r['nama_mitra']) ?>" data-nominal="<?= e((string)(float)$r['nominal']) ?>"
            data-keputusan="disetujui" title="Setujui"><i class="fa-solid fa-check"></i></button>
          <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalReview"
            data-id="<?= (int)$r['id'] ?>" data-nama="<?= e($r['nama_mitra']) ?>" data-nominal="<?= e((string)(float)$r['nominal']) ?>"
            data-keputusan="ditolak" title="Tolak"><i class="fa-solid fa-xmark"></i></button>
        <?php else: ?>
          <small class="text-muted"><?= e($r['reviewed_nama'] ?: '-') ?><br><?= $r['reviewed_at'] ? tgl_id(substr($r['reviewed_at'], 0, 10)) : '' ?></small>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div></div></div>
<?php pagination($total, $per, $page, $_GET); ?>

<?php if ($filterPartner && isset($mitraRingkasan[$filterPartner])): ?>
<div class="card mt-3"><div class="card-header">Ringkasan Mitra Terpilih</div>
<div class="card-body row g-3">
  <div class="col-md-4"><div class="text-muted small">Tagihan Bagi Hasil</div><div class="fw-semibold"><?= rupiah($mitraRingkasan[$filterPartner]['tagihan']) ?></div></div>
  <div class="col-md-4"><div class="text-muted small">Total Setoran Disetujui</div><div class="fw-semibold text-success"><?= rupiah($mitraRingkasan[$filterPartner]['disetujui']) ?></div></div>
  <div class="col-md-4"><div class="text-muted small">Sisa Setoran</div><div class="fw-semibold"><?= rupiah($mitraRingkasan[$filterPartner]['sisa']) ?></div></div>
</div></div>
<?php endif; ?>

<div class="modal fade" id="modalReview" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="review">
      <input type="hidden" name="id" id="rv_id">
      <input type="hidden" name="keputusan" id="rv_keputusan">
      <div class="modal-header"><h5 class="modal-title" id="rv_title">Proses Setoran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p class="mb-2" id="rv_info"></p>
        <label class="form-label">Catatan (opsional)</label>
        <textarea name="catatan_review" class="form-control" rows="2" maxlength="500"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" id="rv_submit">Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
document.getElementById('modalReview')?.addEventListener('show.bs.modal', function (ev) {
  const btn = ev.relatedTarget;
  document.getElementById('rv_id').value = btn.dataset.id;
  document.getElementById('rv_keputusan').value = btn.dataset.keputusan;
  const approve = btn.dataset.keputusan === 'disetujui';
  document.getElementById('rv_title').textContent = approve ? 'Setujui Setoran' : 'Tolak Setoran';
  document.getElementById('rv_info').textContent = (approve ? 'Setujui' : 'Tolak') + ' setoran '
    + btn.dataset.nama + ' sebesar Rp' + Number(btn.dataset.nominal).toLocaleString('id-ID') + '?';
  document.getElementById('rv_submit').className = 'btn ' + (approve ? 'btn-success' : 'btn-danger');
  document.getElementById('rv_submit').textContent = approve ? 'Setujui' : 'Tolak';
});

// Tax preview functionality
const nominalInput = document.getElementById('nominal_input');
const partnerSelect = document.querySelector('select[name="partner_id"]');
const taxPreview = document.getElementById('tax_preview');

function updateTaxPreview() {
  const nominal = parseFloat(nominalInput?.value) || 0;
  const partnerId = parseInt(partnerSelect?.value) || 0;
  
  if (partnerId === 0 || nominal === 0) {
    taxPreview.innerHTML = '<small class="text-muted">Metode: Tidak ada</small><br><small class="text-muted">Pajak: Rp0</small><br><small class="text-success">Total: Rp0</small>';
    return;
  }
  
  // Get tax config via AJAX
  fetch('<?= base_url('admin/api_tax_config.php') ?>?partner_id=' + partnerId)
    .then(response => response.json())
    .then(data => {
      let pajak = 0;
      let methodText = 'Tidak ada';
      
      if (data.enabled) {
        if (data.method === 'percentage') {
          pajak = nominal * data.percentage / 100;
          methodText = 'Persentase (' + data.percentage + '%)';
        } else if (data.method === 'manual') {
          pajak = data.manual_nominal;
          methodText = 'Manual';
        }
      }
      
      const total = nominal - pajak;
      
      taxPreview.innerHTML = `
        <small class="text-muted">Metode: ${methodText}</small><br>
        <small class="text-muted">Pajak: Rp${pajak.toLocaleString('id-ID')}</small><br>
        <small class="text-success">Total: Rp${total.toLocaleString('id-ID')}</small>
      `;
    })
    .catch(error => {
      taxPreview.innerHTML = '<small class="text-danger">Error memuat konfigurasi pajak</small>';
    });
}

if (nominalInput && partnerSelect) {
  nominalInput.addEventListener('input', updateTaxPreview);
  partnerSelect.addEventListener('change', updateTaxPreview);
  // Initial preview
  updateTaxPreview();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
