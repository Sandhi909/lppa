<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $d = [
      'jenis_rekomendator' => in_array($_POST['jenis_rekomendator'] ?? '', ['wali_kelas','wali_santri'], true) ? $_POST['jenis_rekomendator'] : 'wali_kelas',
      'kategori' => trim($_POST['kategori'] ?? ''),
      'poin_minimum' => (float)($_POST['poin_minimum'] ?? 0),
      'poin_maksimum' => $_POST['poin_maksimum'] !== '' ? (float)$_POST['poin_maksimum'] : null,
      'nominal_fee' => (float)($_POST['nominal_fee'] ?? 0),
      'tanggal_mulai' => $_POST['tanggal_mulai'] ?: date('Y-m-d'),
      'tanggal_selesai' => $_POST['tanggal_selesai'] ?: null,
      'status' => ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif',
    ];
    if ($d['poin_maksimum'] !== null && $d['poin_maksimum'] < $d['poin_minimum']) { flash('danger', 'Poin maksimum harus lebih besar dari poin minimum.'); redirect('fee_rules.php'); }
    if ($d['nominal_fee'] < 0) { flash('danger', 'Nominal fee tidak boleh negatif.'); redirect('fee_rules.php'); }
    if ($id) {
        $set = implode(',', array_map(fn($k) => "$k=:$k", array_keys($d)));
        db()->prepare("UPDATE fee_rules SET $set WHERE id=:id")->execute($d + ['id' => $id]);
        audit('Mengubah aturan fee rekomendator', 'fee_rules', $id, null, $d);
    } else {
        $c = implode(',', array_keys($d)); $ph = ':' . implode(',:', array_keys($d));
        db()->prepare("INSERT INTO fee_rules ($c) VALUES ($ph)")->execute($d);
        audit('Menambah aturan fee rekomendator', 'fee_rules', (int)db()->lastInsertId(), null, $d);
    }
    flash('success', 'Aturan fee berhasil disimpan.');
    redirect('fee_rules.php');
}
if (($_GET['aksi'] ?? '') === 'toggle') {
    db()->prepare("UPDATE fee_rules SET status=IF(status='aktif','nonaktif','aktif') WHERE id=?")->execute([(int)$_GET['id']]);
    flash('success', 'Status aturan diperbarui.'); redirect('fee_rules.php');
}
if (($_GET['aksi'] ?? '') === 'hapus') {
    db()->prepare('DELETE FROM fee_rules WHERE id=?')->execute([(int)$_GET['id']]);
    audit('Menghapus aturan fee', 'fee_rules', (int)$_GET['id']);
    flash('success', 'Aturan dihapus.'); redirect('fee_rules.php');
}
$row = null;
if (($_GET['aksi'] ?? '') === 'edit') { $st = db()->prepare('SELECT * FROM fee_rules WHERE id=?'); $st->execute([(int)$_GET['id']]); $row = $st->fetch(); }
$list = db()->query('SELECT * FROM fee_rules ORDER BY jenis_rekomendator, poin_minimum')->fetchAll();

$PAGE_TITLE = 'Pengaturan Fee Rekomendator';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Fee Rekomendator']];
include __DIR__ . '/../includes/header.php';
?>
<div class="alert alert-light border small">
  <i class="fa-solid fa-circle-info me-1 text-primary"></i>
  <strong>Aturan poin:</strong> siswa dengan Wali Kelas saja &rarr; Wali Kelas 1 poin. Siswa dengan Wali Kelas &amp; Wali Santri &rarr; Wali Santri 1 poin, Wali Kelas 0,5 poin. Nominal fee diambil dari tabel di bawah (bukan dikunci di kode).
</div>
<div class="row g-3">
  <div class="col-lg-4"><form method="post" class="card"><?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
    <div class="card-header"><?= $row ? 'Edit Aturan Fee' : 'Tambah Aturan Fee' ?></div>
    <div class="card-body">
      <div class="mb-3"><label class="form-label">Jenis Rekomendator *</label><select name="jenis_rekomendator" class="form-select">
        <option value="wali_kelas" <?= ($row['jenis_rekomendator'] ?? '') === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
        <option value="wali_santri" <?= ($row['jenis_rekomendator'] ?? '') === 'wali_santri' ? 'selected' : '' ?>>Wali Santri</option></select></div>
      <div class="mb-3"><label class="form-label">Kategori</label><input name="kategori" class="form-control" value="<?= e($row['kategori'] ?? '') ?>" placeholder="Kategori 1"></div>
      <div class="row g-2 mb-3">
        <div class="col-6"><label class="form-label">Poin Minimum *</label><input type="number" step="0.5" min="0" name="poin_minimum" class="form-control" required value="<?= e($row['poin_minimum'] ?? '1') ?>"></div>
        <div class="col-6"><label class="form-label">Poin Maksimum</label><input type="number" step="0.5" min="0" name="poin_maksimum" class="form-control" value="<?= e($row['poin_maksimum'] ?? '') ?>" placeholder="kosong = tanpa batas"></div>
      </div>
      <div class="mb-3"><label class="form-label">Nominal Fee per Poin (Rp) *</label><input type="number" min="0" step="1000" name="nominal_fee" class="form-control" required value="<?= e($row['nominal_fee'] ?? '0') ?>"></div>
      <div class="row g-2 mb-3">
        <div class="col-6"><label class="form-label">Mulai Berlaku *</label><input type="date" name="tanggal_mulai" class="form-control" required value="<?= e($row['tanggal_mulai'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-6"><label class="form-label">Selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= e($row['tanggal_selesai'] ?? '') ?>"></div>
      </div>
      <div><label class="form-label">Status</label><select name="status" class="form-select">
        <option value="aktif" <?= ($row['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= ($row['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
    </div>
    <div class="card-footer bg-white text-end"><?php if ($row): ?><a href="fee_rules" class="btn btn-light">Batal</a><?php endif; ?>
      <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button></div>
  </form></div>
  <div class="col-lg-8"><div class="card"><div class="card-header">Tabel Fee Bertingkat</div>
    <div class="px-3 pt-3 small text-muted"><i class="fa-solid fa-circle-info me-1"></i>Total Fee dihitung dari Total Poin &times; Nominal Fee per Poin sesuai kategori.</div><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-hover align-middle"><thead><tr><th>Jenis</th><th>Kategori</th><th>Rentang Poin</th><th class="text-end">Nominal Fee per Poin</th><th>Berlaku</th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>
    <?php if (!$list): ?><tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-table-list"></i>Belum ada aturan fee.</div></td></tr><?php endif; ?>
    <?php foreach ($list as $r): ?>
      <tr><td><span class="badge <?= $r['jenis_rekomendator'] === 'wali_kelas' ? 'bg-primary-soft' : 'bg-success-soft' ?>"><?= $r['jenis_rekomendator'] === 'wali_kelas' ? 'Wali Kelas' : 'Wali Santri' ?></span></td>
      <td><?= e($r['kategori'] ?: '-') ?></td>
      <td><?= poin_fmt($r['poin_minimum']) ?> &ndash; <?= $r['poin_maksimum'] !== null ? poin_fmt($r['poin_maksimum']) : '∞' ?> poin</td>
      <td class="text-end"><strong><?= rupiah($r['nominal_fee']) ?></strong></td>
      <td><small><?= tgl_id($r['tanggal_mulai']) ?> &ndash; <?= $r['tanggal_selesai'] ? tgl_id($r['tanggal_selesai']) : 'seterusnya' ?></small></td>
      <td><?= badge_status($r['status']) ?></td>
      <td class="text-end text-nowrap">
        <a class="btn btn-sm btn-light" href="?aksi=edit&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
        <a class="btn btn-sm btn-light" href="?aksi=toggle&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-power-off"></i></a>
        <a class="btn btn-sm btn-light text-danger" href="?aksi=hapus&id=<?= (int)$r['id'] ?>" data-confirm="Hapus aturan fee ini?"><i class="fa-solid fa-trash"></i></a>
      </td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div></div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
