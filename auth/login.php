<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
if (is_login()) redirect(home_url());

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $login = trim($_POST['login'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($login === '' || $pass === '') {
        $error = 'Username/email dan password wajib diisi.';
    } else {
        $st = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $st->execute([$login, $login]);
        $u = $st->fetch();
        if (!$u || !password_verify($pass, $u['password'])) {
            $error = 'Username/email atau password salah.';
        } elseif ($u['status'] !== 'aktif') {
            $error = 'Akun Anda tidak aktif. Hubungi Admin LPPA.';
        } else {
            $partner = db()->prepare('SELECT id FROM partners WHERE user_id = ? LIMIT 1');
            $partner->execute([$u['id']]);
            $rec = db()->prepare('SELECT id FROM recommenders WHERE user_id = ? LIMIT 1');
            $rec->execute([$u['id']]);
            $student = db()->prepare('SELECT student_id FROM siswa_orang_tua WHERE user_id = ? LIMIT 1');
            $student->execute([$u['id']]);
            $partnerId = $partner->fetchColumn();
            $recId = $rec->fetchColumn();
            $studentId = $student->fetchColumn();
            
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int)$u['id'], 'nama' => $u['nama'], 'username' => $u['username'],
                'role' => $u['role'], 'status' => $u['status'],
                'partner_id' => $partnerId !== false ? (int)$partnerId : null,
                'recommender_id' => $recId !== false ? (int)$recId : null,
                'student_id' => $studentId !== false ? (int)$studentId : null,
            ];
            audit('Login ke sistem', 'users', $u['id']);
            redirect(home_url());
        }
    }
}
if (isset($_GET['err']) && $_GET['err'] === 'nonaktif') $error = 'Sesi berakhir, akun tidak aktif.';
?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk &middot; <?= e(APP_NAME) ?></title>
<link rel="icon" href="<?= base_url('assets/images/LPPA.png') ?>" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head><body class="login-page">
<div class="login-card">
  <div class="login-logo"><img src="<?= base_url('assets/images/LPPA.png') ?>" alt="LPPA Logo" style="max-width:62px;width:100%;height:auto"></div>
  <h1 class="h5 text-center fw-semibold mb-1">SKB Lembaga Pengembangan Potensi Akademik</h1>
  <p class="text-center text-muted small mb-4">Silakan masuk untuk melanjutkan</p>
  <?php if ($error): ?>
    <div class="alert alert-danger py-2 small"><i class="fa-solid fa-circle-exclamation me-1"></i><?= e($error) ?></div>
  <?php endif; ?>
  <form method="post" data-noload>
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Username atau Email</label>
      <div class="input-group">
        <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
        <input name="login" class="form-control" required autofocus value="<?= e($_POST['login'] ?? '') ?>" placeholder="admin">
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text bg-white"><i class="fa-solid fa-lock text-muted"></i></span>
        <input name="password" id="pw" type="password" class="form-control" required placeholder="••••••••">
        <button class="btn btn-outline-secondary" type="button" id="togglePw" aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
      </div>
    </div>
    <button class="btn btn-primary w-100 py-2 fw-500"><i class="fa-solid fa-right-to-bracket me-2"></i>Masuk</button>
  </form>
  <p class="text-center text-muted small mt-4 mb-0">&copy; <?= date('Y') ?> <?= e(COPYRIGHT_HOLDER) ?></p>
</div>
<script>
document.getElementById('togglePw').addEventListener('click', function () {
  const p = document.getElementById('pw');
  const show = p.type === 'password';
  p.type = show ? 'text' : 'password';
  this.innerHTML = show ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
});
</script>
</body></html>
