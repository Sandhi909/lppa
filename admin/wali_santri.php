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
            'unit_id' => null, // Wali Santri tidak memiliki Unit
            'jenis' => 'wali_santri', // Fixed untuk Wali Santri
            'telepon' => trim($_POST['telepon'] ?? ''),
            'email' => trim($_POST['email'] ?? '') ?: null,
            'status' => ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif',
        ];
        if ($d['nama'] === '') { flash('danger', 'Nama wajib diisi.'); redirect('wali_santri.php'); }
        if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) { flash('danger', 'Format email tidak valid.'); redirect('wali_santri.php'); }
        if ($id) {
            $set = implode(',', array_map(fn($k) => "$k=:$k", array_keys($d)));
            db()->prepare("UPDATE recommenders SET $set WHERE id=:id")->execute($d + ['id' => $id]);
            audit('Mengubah Wali Santri: ' . $d['nama'], 'recommenders', $id, null, $d);
            flash('success', 'Data Wali Santri diperbarui.');
        } else {
            $c = implode(',', array_keys($d)); $ph = ':' . implode(',:', array_keys($d));
            db()->prepare("INSERT INTO recommenders ($c) VALUES ($ph)")->execute($d);
            audit('Menambah Wali Santri: ' . $d['nama'], 'recommenders', (int)db()->lastInsertId(), null, $d);
            flash('success', 'Wali Santri ditambahkan.');
        }
        redirect('wali_santri.php');
    }
    if ($act === 'akun') {
        $rid = (int)$_POST['rec_id'];
        $st = db()->prepare('SELECT * FROM recommenders WHERE id=?'); $st->execute([$rid]); $r = $st->fetch();
        $username = trim($_POST['username'] ?? ''); $pass = $_POST['password'] ?? ''; $email = trim($_POST['email'] ?? '');
        if (!$r || $username === '' || strlen($pass) < 6) { flash('danger', 'Username wajib & password minimal 6 karakter.'); redirect('wali_santri.php'); }
        $cek = db()->prepare('SELECT id FROM users WHERE username=?'); $cek->execute([$username]);
        if ($cek->fetch()) { flash('danger', 'Username sudah digunakan.'); redirect('wali_santri.php'); }
        db()->prepare('INSERT INTO users (nama, username, email, password, role, status) VALUES (?,?,?,?,?,"aktif")')
            ->execute([$r['nama'], $username, $email ?: null, password_hash($pass, PASSWORD_DEFAULT), 'wali_santri']);
        $uid = (int)db()->lastInsertId();
        db()->prepare('UPDATE recommenders SET user_id=? WHERE id=?')->execute([$uid, $rid]);
        audit('Membuat akun Wali Santri: ' . $username, 'users', $uid);
        flash('success', 'Akun login Wali Santri dibuat.');
        redirect('wali_santri.php');
    }
    if ($act === 'reset') {
        $uid = (int)$_POST['user_id']; $pass = $_POST['password'] ?? '';
        if (strlen($pass) < 6) { flash('danger', 'Password minimal 6 karakter.'); redirect('wali_santri.php'); }
        db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($pass, PASSWORD_DEFAULT), $uid]);
        audit('Reset password pengguna', 'users', $uid);
        flash('success', 'Password berhasil direset.'); redirect('wali_santri.php');
    }
}
if (($_GET['aksi'] ?? '') === 'toggle') {
    db()->prepare("UPDATE recommenders SET status=IF(status='aktif','nonaktif','aktif') WHERE id=?")->execute([(int)$_GET['id']]);
    flash('success', 'Status diperbarui.'); redirect('wali_santri.php');
}
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)$_GET['id'];
    $c = db()->prepare('SELECT COUNT(*) FROM students WHERE wali_santri_id=?'); $c->execute([$id]);
    if ($c->fetchColumn() > 0) flash('danger', 'Tidak dapat dihapus, Wali Santri sudah memiliki data siswa. Nonaktifkan saja.');
    else { db()->prepare('DELETE FROM recommenders WHERE id=?')->execute([$id]); audit('Menghapus Wali Santri', 'recommenders', $id); flash('success', 'Wali Santri dihapus.'); }
    redirect('wali_santri.php');
}
$row = null;
if (($_GET['aksi'] ?? '') === 'edit') { $st = db()->prepare('SELECT * FROM recommenders WHERE id=?'); $st->execute([(int)$_GET['id']]); $row = $st->fetch(); }

$q = trim($_GET['q'] ?? '');
$w = ['r.jenis=?']; $p = ['wali_santri'];
if ($q !== '') { $w[] = '(r.nama LIKE ? OR r.nomor_induk LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
$st = db()->prepare('SELECT r.*, us.username, us.email AS user_email, us.id AS uid FROM recommenders r
   LEFT JOIN users us ON us.id=r.user_id
   WHERE ' . implode(' AND ', $w) . ' ORDER BY r.nama');
$st->execute($p); $list = $st->fetchAll();

$PAGE_TITLE = 'Manajemen Wali Santri';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Wali Santri']];
include __DIR__ . '/../includes/header.php';
?>
<?php if (($_GET['aksi'] ?? '') === 'tambah' || $row): ?>
<form method="post" class="card mb-3"><?= csrf_field() ?>
  <input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
  <div class="card-header"><?= $row ? 'Edit Wali Santri' : 'Tambah Wali Santri' ?></div>
  <div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Nama *</label><input name="nama" class="form-control" required value="<?= e($row['nama'] ?? '') ?>"></div>
    <div class="col-md-3"><label class="form-label">Nomor Induk / NIP</label><input name="nomor_induk" class="form-control" value="<?= e($row['nomor_induk'] ?? '') ?>"></div>
    <div class="col-md-3"><label class="form-label">Nomor Telepon</label><input name="telepon" class="form-control" value="<?= e($row['telepon'] ?? '') ?>"></div>
    <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select">
      <option value="aktif" <?= ($row['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
      <option value="nonaktif" <?= ($row['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($row['email'] ?? '') ?>"></div>
  </div>
  <div class="card-footer bg-white text-end"><a href="wali_santri" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button></div>
</form>
<?php endif; ?>

<form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-3"><label class="form-label">Cari Nama</label><input name="q" class="form-control" value="<?= e($q) ?>"></div>
  <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
    <a href="wali_santri" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a></div>
  <div class="col-md-2"><a href="?aksi=tambah" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Tambah</a></div>
</div></form>

<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr>
<th>ID</th><th>Nama</th><th>No. Induk</th><th>Telepon</th><th>Email</th><th>Akun</th><th>Status</th><th class="text-end">Aksi</th>
</tr></thead><tbody>
<?php if (!$list): ?><tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-user-slash"></i>Belum ada Wali Santri.</div></td></tr><?php endif; ?>
<?php foreach ($list as $r): ?>
<tr>
  <td>WS-<?= str_pad((string)$r['id'], 3, '0', STR_PAD_LEFT) ?></td>
  <td><strong><?= e($r['nama']) ?></strong></td>
  <td><?= e($r['nomor_induk'] ?: '-') ?></td>
  <td><?= e($r['telepon'] ?: '-') ?></td>
  <td><?= e($r['email'] ?: '-') ?></td>
  <td><?php if ($r['username']): ?><span class="badge bg-primary-soft"><?= e($r['username']) ?></span>
      <?php else: ?><button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#akun<?= (int)$r['id'] ?>"><i class="fa-solid fa-user-plus me-1"></i>Buat Akun</button><?php endif; ?></td>
  <td><?= badge_status($r['status']) ?></td>
  <td class="text-end text-nowrap">
    <a class="btn btn-sm btn-light" href="?aksi=edit&id=<?= (int)$r['id'] ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
    <a class="btn btn-sm btn-light" href="?aksi=toggle&id=<?= (int)$r['id'] ?>" title="Aktif/Nonaktif"><i class="fa-solid fa-power-off"></i></a>
    <a class="btn btn-sm btn-light text-danger" href="?aksi=hapus&id=<?= (int)$r['id'] ?>" data-confirm="Hapus Wali Santri <?= e($r['nama']) ?>?"><i class="fa-solid fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<?php foreach ($list as $r): ?>
<?php if (!$r['username']): ?>
<div class="modal fade" id="akun<?= (int)$r['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="post" class="modal-content">
  <?= csrf_field() ?><input type="hidden" name="aksi" value="akun"><input type="hidden" name="rec_id" value="<?= (int)$r['id'] ?>">
  <div class="modal-header"><h5 class="modal-title">Buat Akun untuk <?= e($r['nama']) ?></h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
  <div class="modal-body">
    <div class="mb-2"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($r['email'] ?? '') ?>"></div>
    <div><label class="form-label">Password (min. 6 karakter)</label><input name="password" type="text" class="form-control" required minlength="6"></div>
  </div>
  <div class="modal-footer"><button class="btn btn-primary">Buat Akun</button></div>
</form></div></div></div>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
