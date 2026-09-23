<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

// Program Bimbel kini berupa input teks manual pada form siswa, sehingga tidak lagi menjadi data master.
$TABS = [
  'academic_years' => ['label' => 'Tahun Ajaran', 'col' => 'nama_tahun_ajaran', 'tanggal' => true],
  'units'          => ['label' => 'Unit',         'col' => 'nama_unit',          'tanggal' => false],
];
$tab = $_GET['tab'] ?? 'academic_years';
if (!isset($TABS[$tab])) $tab = 'academic_years';
$cfg = $TABS[$tab];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $t = $_POST['tab'] ?? ''; if (!isset($TABS[$t])) die('Tabel tidak valid.');
    $c = $TABS[$t];
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $status = ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif';
    if ($nama === '') { flash('danger', 'Nama wajib diisi.'); redirect("master.php?tab=$t"); }
    if ($c['tanggal']) {
        $mulai = $_POST['tanggal_mulai'] ?: null; $selesai = $_POST['tanggal_selesai'] ?: null;
        if ($id) db()->prepare("UPDATE $t SET {$c['col']}=?, tanggal_mulai=?, tanggal_selesai=?, status=? WHERE id=?")->execute([$nama, $mulai, $selesai, $status, $id]);
        else db()->prepare("INSERT INTO $t ({$c['col']}, tanggal_mulai, tanggal_selesai, status) VALUES (?,?,?,?)")->execute([$nama, $mulai, $selesai, $status]);
    } else {
        if ($id) db()->prepare("UPDATE $t SET {$c['col']}=?, status=? WHERE id=?")->execute([$nama, $status, $id]);
        else db()->prepare("INSERT INTO $t ({$c['col']}, status) VALUES (?,?)")->execute([$nama, $status]);
    }
    audit(($id ? 'Mengubah' : 'Menambah') . ' data master ' . $c['label'] . ': ' . $nama, $t, $id ?: (int)db()->lastInsertId());
    flash('success', $c['label'] . ' berhasil disimpan.');
    redirect("master.php?tab=$t");
}
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)$_GET['id'];
    $col = ['academic_years' => 'academic_year_id', 'units' => 'unit_id'][$tab];
    $c = db()->prepare("SELECT COUNT(*) FROM students WHERE $col=?"); $c->execute([$id]);
    if ($c->fetchColumn() > 0) flash('danger', 'Tidak dapat dihapus karena sudah digunakan pada data siswa.');
    elseif ($tab === 'units') flash('danger', 'Unit bersifat tetap (PSAM (SMP), PSAM (SMA), SD, SMP, SMA) dan tidak dapat dihapus.');
    else { db()->prepare("DELETE FROM $tab WHERE id=?")->execute([$id]); audit('Menghapus data master', $tab, $id); flash('success', 'Data dihapus.'); }
    redirect("master.php?tab=$tab");
}

$row = null;
if (($_GET['aksi'] ?? '') === 'edit') { $st = db()->prepare("SELECT * FROM $tab WHERE id=?"); $st->execute([(int)$_GET['id']]); $row = $st->fetch(); }
$list = db()->query("SELECT * FROM $tab ORDER BY id DESC")->fetchAll();

$PAGE_TITLE = 'Data Master: Tahun Ajaran & Unit';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Data Master']];

include __DIR__ . '/../includes/header.php';
?>
<ul class="nav nav-pills mb-3">
<?php foreach ($TABS as $k => $v): ?>
  <li class="nav-item"><a class="nav-link <?= $k === $tab ? 'active' : '' ?>" href="?tab=<?= $k ?>"><?= e($v['label']) ?></a></li>
<?php endforeach; ?>
</ul>
<div class="alert alert-info small"><i class="fa-solid fa-circle-info me-1"></i>
  Program Bimbel diisi manual pada form Data Siswa, sehingga tidak lagi dikelola sebagai data master.
  Unit bersifat tetap: <?= e(implode(', ', UNIT_TETAP)) ?>.</div>

<div class="row g-3">
  <div class="col-lg-4">
    <form method="post" class="card"><?= csrf_field() ?>
      <input type="hidden" name="tab" value="<?= e($tab) ?>"><input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
      <div class="card-header"><?= $row ? 'Edit' : 'Tambah' ?> <?= e($cfg['label']) ?></div>
      <div class="card-body">
        <div class="mb-3"><label class="form-label">Nama <?= e($cfg['label']) ?> *</label>
          <input name="nama" class="form-control" required value="<?= e($row[$cfg['col']] ?? '') ?>"></div>
        <?php if ($cfg['tanggal']): ?>
        <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= e($row['tanggal_mulai'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= e($row['tanggal_selesai'] ?? '') ?>"></div>
        <?php endif; ?>
        <div><label class="form-label">Status</label><select name="status" class="form-select">
          <option value="aktif" <?= ($row['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
          <option value="nonaktif" <?= ($row['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
      </div>
      <div class="card-footer bg-white text-end"><?php if ($row): ?><a href="?tab=<?= e($tab) ?>" class="btn btn-light">Batal</a><?php endif; ?>
        <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button></div>
    </form>
  </div>
  <div class="col-lg-8"><div class="card"><div class="card-header">Daftar <?= e($cfg['label']) ?></div><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-hover align-middle"><thead><tr><th>#</th><th>Nama</th>
      <?php if ($cfg['tanggal']): ?><th>Periode</th><?php endif; ?><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>
    <?php if (!$list): ?><tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-layer-group"></i>Belum ada data.</div></td></tr><?php endif; ?>
    <?php foreach ($list as $i => $r): ?>
      <tr><td><?= $i + 1 ?></td><td><strong><?= e($r[$cfg['col']]) ?></strong></td>
      <?php if ($cfg['tanggal']): ?><td><small><?= tgl_id($r['tanggal_mulai']) ?> &ndash; <?= tgl_id($r['tanggal_selesai']) ?></small></td><?php endif; ?>
      <td><?= badge_status($r['status']) ?></td>
      <td class="text-end text-nowrap">
        <a class="btn btn-sm btn-light" href="?tab=<?= e($tab) ?>&aksi=edit&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
        <a class="btn btn-sm btn-light text-danger" href="?tab=<?= e($tab) ?>&aksi=hapus&id=<?= (int)$r['id'] ?>" data-confirm="Hapus data ini?"><i class="fa-solid fa-trash"></i></a>
      </td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div></div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
