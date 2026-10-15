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
            'nama' => trim($_POST['nama'] ?? ''),
            'nomor_induk' => trim($_POST['nomor_induk'] ?? ''),
            'unit_id' => $_POST['unit_id'] ?: null,
            'jenis' => in_array($_POST['jenis'] ?? '', ['wali_kelas', 'wali_santri'], true) ? $_POST['jenis'] : 'wali_kelas',
            'telepon' => trim($_POST['telepon'] ?? ''),
            'status' => ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif',
        ];
        if ($d['nama'] === '') { flash('danger', 'Nama wajib diisi.'); redirect('recommenders.php'); }
        if ($id) {
            $set = implode(',', array_map(fn($k) => "$k=:$k", array_keys($d)));
            db()->prepare("UPDATE recommenders SET $set WHERE id=:id")->execute($d + ['id' => $id]);
            audit('Mengubah rekomendator: ' . $d['nama'], 'recommenders', $id, null, $d);
            flash('success', 'Data rekomendator diperbarui.');
        } else {
            $c = implode(',', array_keys($d)); $ph = ':' . implode(',:', array_keys($d));
            db()->prepare("INSERT INTO recommenders ($c) VALUES ($ph)")->execute($d);
            audit('Menambah rekomendator: ' . $d['nama'], 'recommenders', (int)db()->lastInsertId(), null, $d);
            flash('success', 'Rekomendator ditambahkan.');
        }
        redirect('recommenders.php');
    }
    if ($act === 'akun') {
        $rid = (int)$_POST['rec_id'];
        $st = db()->prepare('SELECT * FROM recommenders WHERE id=?'); $st->execute([$rid]); $r = $st->fetch();
        $username = trim($_POST['username'] ?? ''); $pass = $_POST['password'] ?? ''; $email = trim($_POST['email'] ?? '');
        if (!$r || $username === '' || $pass === '') { flash('danger', 'Username dan password wajib diisi.'); redirect('recommenders.php'); }
        $cek = db()->prepare('SELECT id FROM users WHERE username=?'); $cek->execute([$username]);
        if ($cek->fetch()) { flash('danger', 'Username sudah digunakan.'); redirect('recommenders.php'); }
        db()->prepare('INSERT INTO users (nama, username, email, password, role) VALUES (?,?,?,?,?)')
            ->execute([$r['nama'], $username, $email ?: null, password_hash($pass, PASSWORD_DEFAULT), $r['jenis']]);
        $uid = (int)db()->lastInsertId();
        db()->prepare('UPDATE recommenders SET user_id=? WHERE id=?')->execute([$uid, $rid]);
        audit('Membuat akun rekomendator: ' . $username, 'users', $uid);
        flash('success', 'Akun login rekomendator dibuat.');
        redirect('recommenders.php');
    }
    if ($act === 'reset') {
        $uid = (int)$_POST['user_id']; $pass = $_POST['password'] ?? '';
        if ($pass === '') { flash('danger', 'Password wajib diisi.'); redirect('recommenders.php'); }
        db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($pass, PASSWORD_DEFAULT), $uid]);
        audit('Reset password pengguna', 'users', $uid);
        flash('success', 'Password berhasil direset.'); redirect('recommenders.php');
    }
}
if (($_GET['aksi'] ?? '') === 'toggle') {
    db()->prepare("UPDATE recommenders SET status=IF(status='aktif','nonaktif','aktif') WHERE id=?")->execute([(int)$_GET['id']]);
    flash('success', 'Status diperbarui.'); redirect('recommenders.php');
}
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)$_GET['id'];
    $c = db()->prepare('SELECT COUNT(*) FROM students WHERE wali_kelas_id=? OR wali_santri_id=?'); $c->execute([$id, $id]);
    if ($c->fetchColumn() > 0) flash('danger', 'Tidak dapat dihapus, rekomendator sudah memiliki data siswa. Nonaktifkan saja.');
    else { db()->prepare('DELETE FROM recommenders WHERE id=?')->execute([$id]); audit('Menghapus rekomendator', 'recommenders', $id); flash('success', 'Rekomendator dihapus.'); }
    redirect('recommenders.php');
}
$row = null;
if (($_GET['aksi'] ?? '') === 'edit') { $st = db()->prepare('SELECT * FROM recommenders WHERE id=?'); $st->execute([(int)$_GET['id']]); $row = $st->fetch(); }

$q = trim($_GET['q'] ?? ''); $fu = $_GET['unit_id'] ?? ''; $fj = $_GET['jenis'] ?? '';
$w = ['1=1']; $p = [];
if ($q !== '') { $w[] = '(r.nama LIKE ? OR r.nomor_induk LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
if ($fu !== '') { $w[] = 'r.unit_id=?'; $p[] = $fu; }
if ($fj !== '') { $w[] = 'r.jenis=?'; $p[] = $fj; }
$st = db()->prepare('SELECT r.*, u.nama_unit, us.username, us.email, us.id AS uid FROM recommenders r
   LEFT JOIN units u ON u.id=r.unit_id LEFT JOIN users us ON us.id=r.user_id
   WHERE ' . implode(' AND ', $w) . ' ORDER BY r.jenis, r.nama');
$st->execute($p); $list = $st->fetchAll();
$units = opsi('units', 'nama_unit', '');

$PAGE_TITLE = 'Manajemen Rekomendator';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Rekomendator']];
include __DIR__ . '/../includes/header.php';
?>
<?php if (($_GET['aksi'] ?? '') === 'tambah' || $row): ?>
<form method="post" class="card mb-3"><?= csrf_field() ?>
  <input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
  <div class="card-header"><?= $row ? 'Edit Rekomendator' : 'Tambah Rekomendator' ?></div>
  <div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Nama *</label><input name="nama" class="form-control" required value="<?= e($row['nama'] ?? '') ?>"></div>
    <div class="col-md-3"><label class="form-label">Nomor Induk / NIP</label><input name="nomor_induk" class="form-control" value="<?= e($row['nomor_induk'] ?? '') ?>"></div>
    <div class="col-md-2"><label class="form-label">Unit</label><select name="unit_id" class="form-select"><?= select_options($units, $row['unit_id'] ?? '') ?></select></div>
    <div class="col-md-3"><label class="form-label">Jenis Rekomendator *</label><select name="jenis" class="form-select">
      <option value="wali_kelas" <?= ($row['jenis'] ?? '') === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
      <option value="wali_santri" <?= ($row['jenis'] ?? '') === 'wali_santri' ? 'selected' : '' ?>>Wali Santri</option></select></div>
    <div class="col-md-3"><label class="form-label">Nomor Telepon</label><input name="telepon" class="form-control" value="<?= e($row['telepon'] ?? '') ?>"></div>
    <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">
      <option value="aktif" <?= ($row['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
      <option value="nonaktif" <?= ($row['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
    <div class="col-12"><small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Satu orang boleh didaftarkan lebih dari satu kali bila memiliki dua jenis peran (Wali Kelas dan Wali Santri).</small></div>
  </div>
  <div class="card-footer bg-white text-end"><a href="recommenders" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button></div>
</form>
<?php endif; ?>

<form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-3"><label class="form-label">Cari Nama</label><input name="q" class="form-control" value="<?= e($q) ?>"></div>
  <div class="col-md-2"><label class="form-label">Unit</label><select name="unit_id" class="form-select"><?= select_options($units, $fu, 'Semua') ?></select></div>
  <div class="col-md-3"><label class="form-label">Jenis</label><select name="jenis" class="form-select">
    <option value="">Semua</option><option value="wali_kelas" <?= $fj === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
    <option value="wali_santri" <?= $fj === 'wali_santri' ? 'selected' : '' ?>>Wali Santri</option></select></div>
  <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
    <a href="recommenders" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a></div>
  <div class="col-md-2"><a href="?aksi=tambah" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Tambah</a></div>
</div></form>

<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr>
<th>ID</th><th>Nama</th><th>No. Induk</th><th>Unit</th><th>Jenis</th><th>Akun</th><th>Email</th><th>Telepon</th><th>Status</th><th class="text-end">Aksi</th>
</tr></thead><tbody>
<?php if (!$list): ?><tr><td colspan="10"><div class="empty-state"><i class="fa-solid fa-user-slash"></i>Belum ada rekomendator.</div></td></tr><?php endif; ?>
<?php foreach ($list as $r): ?>
<tr>
  <td>REK-<?= str_pad((string)$r['id'], 3, '0', STR_PAD_LEFT) ?></td>
  <td><strong><?= e($r['nama']) ?></strong></td>
  <td><?= e($r['nomor_induk'] ?: '-') ?></td>
  <td><?= e($r['nama_unit'] ?: '-') ?></td>
  <td><span class="badge <?= $r['jenis'] === 'wali_kelas' ? 'bg-primary-soft' : 'bg-success-soft' ?>"><?= $r['jenis'] === 'wali_kelas' ? 'Wali Kelas' : 'Wali Santri' ?></span></td>
  <td><?php if ($r['username']): ?><span class="badge bg-secondary-soft"><?= e($r['username']) ?></span>
      <?php else: ?><button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#ak<?= (int)$r['id'] ?>"><i class="fa-solid fa-user-plus me-1"></i>Buat</button><?php endif; ?></td>
  <td><small><?= e($r['email'] ?: '-') ?></small></td>
  <td><small><?= e($r['telepon'] ?: '-') ?></small></td>
  <td><?= badge_status($r['status']) ?></td>
  <td class="text-end text-nowrap">
    <a class="btn btn-sm btn-light" href="?aksi=edit&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
    <?php if ($r['uid']): ?><button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#rs<?= (int)$r['id'] ?>" title="Reset Password"><i class="fa-solid fa-key"></i></button><?php endif; ?>
    <a class="btn btn-sm btn-light" href="?aksi=toggle&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-power-off"></i></a>
    <a class="btn btn-sm btn-light text-danger" href="?aksi=hapus&id=<?= (int)$r['id'] ?>" data-confirm="Hapus rekomendator <?= e($r['nama']) ?>?"><i class="fa-solid fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<?php foreach ($list as $r): ?>
  <?php if (!$r['username']): ?>
  <div class="modal fade" id="ak<?= (int)$r['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="post" class="modal-content">
    <?= csrf_field() ?><input type="hidden" name="aksi" value="akun"><input type="hidden" name="rec_id" value="<?= (int)$r['id'] ?>">
    <div class="modal-header"><h5 class="modal-title">Buat Akun - <?= e($r['nama']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-2"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
      <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
      <div><label class="form-label">Password</label><input name="password" type="text" class="form-control" required></div>
    </div><div class="modal-footer"><button class="btn btn-primary">Buat Akun</button></div>
  </form></div></div>
  <?php else: ?>
  <div class="modal fade" id="rs<?= (int)$r['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="post" class="modal-content">
    <?= csrf_field() ?><input type="hidden" name="aksi" value="reset"><input type="hidden" name="user_id" value="<?= (int)$r['uid'] ?>">
    <div class="modal-header"><h5 class="modal-title">Reset Password - <?= e($r['nama']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><label class="form-label">Password Baru</label><input name="password" type="text" class="form-control" required></div>
    <div class="modal-footer"><button class="btn btn-primary">Simpan</button></div>
  </form></div></div>
  <?php endif; ?>
<?php endforeach; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
