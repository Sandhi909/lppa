<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $password_konfirmasi = $_POST['password_konfirmasi'] ?? '';
    
    if ($password_lama === '' || $password_baru === '' || $password_konfirmasi === '') {
        $error = 'Semua field password wajib diisi.';
    } elseif ($password_baru !== $password_konfirmasi) {
        $error = 'Password baru dan konfirmasi tidak cocok.';
    } else {
        $st = db()->prepare('SELECT password FROM users WHERE id=?');
        $st->execute([user()['id']]);
        $u = $st->fetch();
        
        if (!$u || !password_verify($password_lama, $u['password'])) {
            $error = 'Password lama salah.';
        } else {
            $hash = password_hash($password_baru, PASSWORD_DEFAULT);
            db()->prepare('UPDATE users SET password=? WHERE id=?')
                ->execute([$hash, user()['id']]);
            audit('Mengubah password sendiri', 'users', user()['id']);
            flash('success', 'Password berhasil diubah.');
            redirect('profile.php');
        }
    }
}

$PAGE_TITLE = 'Ubah Password';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Ubah Password']];
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Ubah Password</div>
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Password Lama</label>
            <input type="password" name="password_lama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password_baru" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_konfirmasi" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-key me-1"></i>Ubah Password</button>
          <a href="index" class="btn btn-light">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
