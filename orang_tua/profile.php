<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role(['orang_tua']);
require_once __DIR__ . '/../includes/queries.php';

$PAGE_TITLE = 'Profil Akun';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('orang_tua/index.php')], ['label' => 'Profil Akun']];

$userId = user()['id'];

// Get user data
$user = db()->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$userId]);
$user = $user->fetch();

// Get student data (semua enrollment/mitra yang terhubung)
$studentRows = db()->prepare("SELECT s.*, p.nama_mitra, ay.nama_tahun_ajaran
FROM siswa_orang_tua sot
LEFT JOIN students s ON sot.student_id = s.id
LEFT JOIN partners p ON s.partner_id = p.id
LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
WHERE sot.user_id = ? ORDER BY s.id");
$studentRows->execute([$userId]);
$studentRows = $studentRows->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($nama) || empty($email)) {
        flash('danger', 'Nama dan email wajib diisi.');
        redirect(base_url('orang_tua/profile.php'));
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('danger', 'Email tidak valid.');
        redirect(base_url('orang_tua/profile.php'));
    }
    
    if ($password && strlen($password) < 6) {
        flash('danger', 'Password minimal 6 karakter.');
        redirect(base_url('orang_tua/profile.php'));
    }
    
    try {
        if ($password) {
            $stmt = db()->prepare("UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT), $userId]);
        } else {
            $stmt = db()->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
            $stmt->execute([$nama, $email, $userId]);
        }
        
        // Update session
        $_SESSION['user']['nama'] = $nama;
        
        flash('success', 'Profil berhasil diperbarui.');
        redirect(base_url('orang_tua/profile.php'));
    } catch (Exception $e) {
        flash('danger', 'Gagal memperbarui profil: ' . $e->getMessage());
        redirect(base_url('orang_tua/profile.php'));
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa-solid fa-user-edit me-2"></i>Ubah Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                                    <small class="text-muted">Username tidak dapat diubah</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="Orang Tua" disabled>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= e($user['nama']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-muted">(opsional)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Isi jika ingin mengubah password">
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i>Simpan Perubahan
                            </button>
                            <a href="<?= home_url() ?>" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($studentRows): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa-solid fa-user-graduate me-2"></i>Data Anak
                            <?php if (count($studentRows) > 1): ?><span class="badge bg-info ms-2"><?= count($studentRows) ?> Mitra/Program</span><?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Mitra</th>
                                        <th>Program Bimbel</th>
                                        <th>Kelas</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Status Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentRows as $st): ?>
                                        <tr>
                                            <td><strong><?= e($st['nama_siswa']) ?></strong></td>
                                            <td><?= e($st['nama_mitra']) ?></td>
                                            <td><?= e($st['program_bimbel'] ?: '-') ?></td>
                                            <td><?= e($st['kelas'] ?: '-') ?></td>
                                            <td><?= e($st['nama_tahun_ajaran'] ?: '-') ?></td>
                                            <td><?= badge_status($st['status_pembayaran']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
