<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['aksi'] ?? '';
    if ($act === 'simpan') {
        $id = (int)($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? ''); $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? ''); $role = $_POST['role'] ?? 'admin';
        $status = ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif';
        $pass = $_POST['password'] ?? '';
        if ($nama === '' || $username === '') { flash('danger', 'Nama dan username wajib diisi.'); redirect('users.php'); }
        if (!in_array($role, ['admin','mitra','wali_kelas','wali_santri','orang_tua','direksi'], true)) { flash('danger', 'Role tidak valid.'); redirect('users.php'); }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('danger', 'Email tidak valid.'); redirect('users.php'); }
        $cek = db()->prepare('SELECT id FROM users WHERE username=? AND id<>?'); $cek->execute([$username, $id]);
        if ($cek->fetch()) { flash('danger', 'Username sudah digunakan.'); redirect('users.php'); }
        if ($id) {
            db()->prepare('UPDATE users SET nama=?, username=?, email=?, role=?, status=? WHERE id=?')
               ->execute([$nama, $username, $email ?: null, $role, $status, $id]);
            if ($pass !== '') {
                if (strlen($pass) < 6) { flash('danger', 'Password minimal 6 karakter.'); redirect('users.php'); }
                db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($pass, PASSWORD_DEFAULT), $id]);
            }
            audit('Mengubah akun pengguna: ' . $username, 'users', $id);
            flash('success', 'Akun pengguna diperbarui.');
        } else {
            if (strlen($pass) < 6) { flash('danger', 'Password minimal 6 karakter.'); redirect('users.php'); }
            db()->prepare('INSERT INTO users (nama, username, email, password, role, status) VALUES (?,?,?,?,?,?)')
               ->execute([$nama, $username, $email ?: null, password_hash($pass, PASSWORD_DEFAULT), $role, $status]);
            audit('Menambah akun pengguna: ' . $username, 'users', (int)db()->lastInsertId());
            flash('success', 'Akun pengguna ditambahkan.');
        }
        redirect('users.php');
    }
}
if (($_GET['aksi'] ?? '') === 'toggle') {
    $id = (int)$_GET['id'];
    if ($id === (int)user()['id']) flash('danger', 'Tidak dapat menonaktifkan akun sendiri.');
    else { db()->prepare("UPDATE users SET status=IF(status='aktif','nonaktif','aktif') WHERE id=?")->execute([$id]); flash('success', 'Status akun diperbarui.'); }
    redirect('users.php');
}
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)$_GET['id'];
    if ($id === (int)user()['id']) flash('danger', 'Tidak dapat menghapus akun sendiri.');
    else { db()->prepare('DELETE FROM users WHERE id=?')->execute([$id]); audit('Menghapus akun pengguna', 'users', $id); flash('success', 'Akun dihapus.'); }
    redirect('users.php');
}
$row = null;
if (($_GET['aksi'] ?? '') === 'edit') { $st = db()->prepare('SELECT * FROM users WHERE id=?'); $st->execute([(int)$_GET['id']]); $row = $st->fetch(); }
$fr = $_GET['role'] ?? ''; $q = trim($_GET['q'] ?? '');
$w = ['1=1']; $p = [];
if ($fr !== '') { $w[] = 'role=?'; $p[] = $fr; }
if ($q !== '') { $w[] = '(nama LIKE ? OR username LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
$st = db()->prepare('SELECT * FROM users WHERE ' . implode(' AND ', $w) . ' ORDER BY role, nama');
$st->execute($p); $list = $st->fetchAll();
$roleLabel = ['admin' => 'Admin LPPA', 'mitra' => 'Mitra Bimbel', 'wali_kelas' => 'Wali Kelas', 'wali_santri' => 'Wali Santri', 'orang_tua' => 'Orang Tua', 'direksi' => 'Direksi'];

$PAGE_TITLE = 'Manajemen Akun Pengguna';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Akun Pengguna']];
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
  <div class="col-lg-4"><form method="post" class="card"><?= csrf_field() ?>
    <input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
    <div class="card-header"><?= $row ? 'Edit Akun' : 'Tambah Akun' ?></div>
    <div class="card-body">
      <div class="mb-3"><label class="form-label">Nama Lengkap *</label><input name="nama" class="form-control" required value="<?= e($row['nama'] ?? '') ?>"></div>
      <div class="mb-3"><label class="form-label">Username *</label><input name="username" class="form-control" required value="<?= e($row['username'] ?? '') ?>"></div>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($row['email'] ?? '') ?>"></div>
      <div class="mb-3"><label class="form-label">Role *</label><select name="role" class="form-select">
        <?php foreach ($roleLabel as $k => $v): ?><option value="<?= $k ?>" <?= ($row['role'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
      </select></div>
      <div class="mb-3"><label class="form-label">Password <?= $row ? '(kosongkan bila tidak diubah)' : '*' ?></label>
        <input type="text" name="password" class="form-control" <?= $row ? '' : 'required minlength="6"' ?>></div>
      <div><label class="form-label">Status</label><select name="status" class="form-select">
        <option value="aktif" <?= ($row['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= ($row['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option></select></div>
    </div>
    <div class="card-footer bg-white text-end"><?php if ($row): ?><a href="users" class="btn btn-light">Batal</a><?php endif; ?>
      <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button></div>
  </form></div>
  <div class="col-lg-8">
    <form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
      <div class="col-md-6"><label class="form-label">Cari</label><input name="q" class="form-control" value="<?= e($q) ?>" placeholder="Nama / username"></div>
      <div class="col-md-4"><label class="form-label">Role</label><select name="role" class="form-select"><option value="">Semua</option>
        <?php foreach ($roleLabel as $k => $v): ?><option value="<?= $k ?>" <?= $fr === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter"></i></button></div>
    </div></form>
    <div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-hover align-middle"><thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>
    <?php foreach ($list as $i => $r): ?>
      <tr><td><?= $i + 1 ?></td><td><?= e($r['nama']) ?></td><td><code><?= e($r['username']) ?></code></td>
      <td><small><?= e($r['email'] ?: '-') ?></small></td><td><?= e($roleLabel[$r['role']]) ?></td><td><?= badge_status($r['status']) ?></td>
      <td class="text-end text-nowrap">
        <a class="btn btn-sm btn-light" href="?aksi=edit&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
        <a class="btn btn-sm btn-light" href="?aksi=toggle&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-power-off"></i></a>
        <a class="btn btn-sm btn-light text-danger" href="?aksi=hapus&id=<?= (int)$r['id'] ?>" data-confirm="Hapus akun <?= e($r['username']) ?>?"><i class="fa-solid fa-trash"></i></a>
      </td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div></div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
