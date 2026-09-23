<?php
/** Halaman data siswa (dipakai admin & mitra). Variabel wajib: $SCOPE_PARTNER, $PAGE_TITLE, $BREADCRUMB */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/queries.php';
require_once __DIR__ . '/student_crud.php';
require_once __DIR__ . '/parent_account_helper.php';

$aksi = $_GET['aksi'] ?? '';
$row  = null;
$detail = null;
if ($aksi === 'edit' || $aksi === 'detail') {
    $st = db()->prepare('SELECT * FROM students WHERE id=?'); $st->execute([(int)($_GET['id'] ?? 0)]);
    $row = $st->fetch();
    if (!$row) { flash('danger', 'Data tidak ditemukan.'); redirect(basename($_SERVER['PHP_SELF'])); }
    student_scope_check($row, $SCOPE_PARTNER);
    if ($aksi === 'detail') { $detail = student_by_id((int)$row['id']); $row = null; }
} elseif ($aksi === 'tambah') {
    $row = [];
}

// Handle filter unit_id untuk reload wali kelas
if (isset($_GET['unit_id']) && $_GET['unit_id'] !== '' && $aksi === 'tambah') {
    $row['unit_id'] = (int)$_GET['unit_id'];
}

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

$dup = $_SESSION['duplikat_siswa'] ?? null;
unset($_SESSION['duplikat_siswa']);

$force = $SCOPE_PARTNER ? ['partner_id' => $SCOPE_PARTNER] : [];
[$where, $params] = student_filters($_GET, $force);
$per = 15; $page = max(1, (int)($_GET['page'] ?? 1));
$total = students_count($where, $params);
$rows = students_list($where, $params, $per, ($page - 1) * $per);
$sum = students_summary($where, $params);

include __DIR__ . '/header.php';
?>
<?php if ($dup): ?>
  <div class="alert alert-warning">
    <div class="fw-semibold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Data siswa ini sudah terdaftar.</div>
    <div class="small mb-2">Gunakan tombol <strong>Edit</strong> untuk memperbarui data yang sudah ada.</div>
    <a class="btn btn-sm btn-primary" href="?aksi=detail&id=<?= (int)$dup['id'] ?>">Lihat Data Siswa</a>
    <a class="btn btn-sm btn-light" href="<?= e(basename($_SERVER['PHP_SELF'])) ?>">Batal</a>
  </div>
<?php endif; ?>

<?php if ($aksi === 'detail' && $detail): ?>
  <div class="card mb-3">
    <div class="card-header"><i class="fa-solid fa-user-graduate me-2"></i>Detail Siswa
      <span class="badge bg-secondary-soft ms-2">ID Siswa: <?= (int)$detail['id'] ?></span></div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><div class="text-muted small">Nama Siswa</div><div class="fw-semibold"><?= e($detail['nama_siswa']) ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Tanggal Pendaftaran</div><div><?= tgl_id($detail['tanggal_pendaftaran']) ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Tahun Ajaran</div><div><?= e($detail['nama_tahun_ajaran'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Mitra Bimbel</div><div><?= e($detail['nama_mitra'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Unit</div><div><?= e($detail['nama_unit'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Kelas</div><div><?= e($detail['kelas'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Program Bimbel</div><div><?= e($detail['program_bimbel'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Kategori Peserta</div><div>
          <?php 
          $kategori = $detail['kategori_peserta'] ?? 'reguler';
          if ($kategori === 'khusus'): 
          ?>
          <span class="badge bg-warning text-dark"><i class="fa-solid fa-star me-1"></i>Kategori Khusus</span>
          <?php else: ?>
          <span class="badge bg-success">Reguler</span>
          <?php endif; ?>
        </div></div>
        <div class="col-md-4"><div class="text-muted small">Triwulan</div><div><?= e($detail['triwulan'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">PSAM</div><div><?= e($detail['is_psam'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Biaya Pendaftaran</div><div class="fw-semibold"><?= rupiah($detail['biaya_pendaftaran']) ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Biaya Program</div><div class="fw-semibold"><?= rupiah($detail['biaya_program']) ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Total Biaya</div><div class="fw-semibold"><?= rupiah($detail['total_biaya']) ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Status</div><div><?= badge_status($detail['status_pembayaran']) ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Wali Kelas</div><div><?= e($detail['wali_kelas_nama'] ?: '-') ?></div></div>
        <div class="col-md-4"><div class="text-muted small">Wali Santri</div><div><?= e($detail['wali_santri_nama'] ?: '-') ?></div></div>
        <div class="col-md-8"><div class="text-muted small">Keterangan</div><div><?= e($detail['keterangan'] ?: '-') ?></div></div>
      </div>
    </div>
    <?php 
    // Show parent account info
    $parentAccount = getParentAccount((int)$detail['id']);
    if ($parentAccount): 
    ?>
    <div class="card mt-3 border-0 shadow-lg">
      <div class="card-header bg-gradient-primary text-white">
        <i class="fa-solid fa-user-group me-2"></i>Akun Orang Tua
      </div>
      <div class="card-body bg-light">
        <div class="alert alert-success border-0 shadow-sm mb-4">
          <i class="fa-solid fa-info-circle me-2"></i>
          <strong>Penting:</strong> Berikan informasi akun ini kepada orang tua siswa untuk akses dashboard orang tua.
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body text-center">
                <div class="text-muted small mb-2">USERNAME</div>
                <div class="fw-bold text-primary" style="font-size: 1.8rem; letter-spacing: 1px;">
                  <?= e($parentAccount['username']) ?>
                </div>
                <div class="mt-2">
                  <button onclick="copyUsername()" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-copy me-1"></i>Copy
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body text-center">
                <div class="text-muted small mb-2">PASSWORD DEFAULT</div>
                <div class="fw-bold text-danger" style="font-size: 1.8rem; letter-spacing: 1px;">
                  12345
                </div>
                <div class="mt-2">
                  <button onclick="copyPassword()" class="btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-copy me-1"></i>Copy
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="text-muted small mb-1">STATUS AKUN</div>
                <div><?= badge_status($parentAccount['status']) ?></div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="text-muted small mb-1">URL LOGIN</div>
                <div class="small text-muted">
                  <code><?= base_url('auth/login.php') ?></code>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
              <button onclick="copyAllCredentials()" class="btn btn-primary">
                <i class="fa-solid fa-copy me-1"></i>Copy Semua Info
              </button>
              <a href="<?= base_url('print_parent.php?aksi=print_parent&id=' . (int)$detail['id']) ?>" class="btn btn-secondary" target="_blank">
                <i class="fa-solid fa-print me-1"></i>Cetak Info Akun
              </a>
              <a href="https://wa.me/?text=<?= urlencode("Akun Orang Tua LPPA\n\nUsername: " . $parentAccount['username'] . "\nPassword: 12345\n\nLogin: " . base_url('auth/login.php') . "\n\nSilakan ganti password setelah login pertama.") ?>" target="_blank" class="btn btn-success">
                <i class="fa-brands fa-whatsapp me-1"></i>Kirim WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    </style>
    
    <script>
    function copyUsername() {
        const username = '<?= e($parentAccount['username']) ?>';
        navigator.clipboard.writeText(username).then(() => {
            alert('Username berhasil disalin!');
        }).catch(err => {
            alert('Gagal menyalin: ' + err);
        });
    }
    
    function copyPassword() {
        navigator.clipboard.writeText('12345').then(() => {
            alert('Password berhasil disalin!');
        }).catch(err => {
            alert('Gagal menyalin: ' + err);
        });
    }
    
    function copyAllCredentials() {
        const username = '<?= e($parentAccount['username']) ?>';
        const password = '12345';
        const loginUrl = '<?= base_url('auth/login.php') ?>';
        const text = `Akun Orang Tua LPPA\n\nUsername: ${username}\nPassword: ${password}\nLogin: ${loginUrl}\n\nSilakan ganti password setelah login pertama.`;
        navigator.clipboard.writeText(text).then(() => {
            alert('Semua info akun berhasil disalin!');
        }).catch(err => {
            alert('Gagal menyalin: ' + err);
        });
    }
    </script>
    <?php endif; ?>
    <div class="card-footer bg-white d-flex gap-2 justify-content-end">
      <a href="<?= e(basename($_SERVER['PHP_SELF'])) ?>" class="btn btn-light">Kembali</a>
      <?php 
      $parentAccount = getParentAccount((int)$detail['id']);
      if (!$parentAccount): 
      ?>
        <a href="?aksi=create_parent&id=<?= (int)$detail['id'] ?>" class="btn btn-success" onclick="return confirm('Buat akun orang tua untuk siswa ini?')">
          <i class="fa-solid fa-user-plus me-1"></i>Buat Akun Orang Tua
        </a>
      <?php endif; ?>
      <?php if (role() === 'admin' || role() === 'mitra'): ?>
      <a href="?aksi=edit&id=<?= (int)$detail['id'] ?>" class="btn btn-light"><i class="fa-solid fa-pen me-1"></i>Edit</a>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php if ($aksi === 'tambah' || $aksi === 'edit'): ?>
  <?php include __DIR__ . '/student_form.php'; ?>
<?php else: ?>
  <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
    <div class="small text-muted">Menampilkan <strong><?= number_format($total, 0, ',', '.') ?></strong> siswa
      &middot; Total Biaya Pendaftaran <strong><?= rupiah($sum['biaya_pendaftaran']) ?></strong>
      &middot; Total Biaya Program <strong><?= rupiah($sum['biaya_program']) ?></strong>
      &middot; Total Biaya <strong><?= rupiah($sum['total_biaya']) ?></strong></div>
    <div class="d-flex gap-2">
      <?php if (role() === 'admin' || role() === 'mitra'): ?>
      <?php if ($SCOPE_PARTNER): ?><a href="<?= base_url('mitra/import.php') ?>" class="btn btn-light"><i class="fa-solid fa-file-import me-1"></i>Import</a><?php endif; ?>
      <?php if (!$SCOPE_PARTNER): ?><a href="<?= base_url('admin/import.php') ?>" class="btn btn-light"><i class="fa-solid fa-file-import me-1"></i>Import</a><?php endif; ?>
      <a href="?aksi=tambah" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tambah Siswa</a>
      <?php endif; ?>
    </div>
  </div>
  <?php render_filter_bar($_GET, $SCOPE_PARTNER
      ? ['q','ta','unit','program','status','kategori_peserta','wali_kelas','wali_santri']
      : ['q','ta','mitra','unit','program','status','kategori_peserta','wali_kelas','wali_santri']); ?>
  <div class="card"><div class="card-body p-0">
    <?php $canEdit = role() === 'admin' || role() === 'mitra'; $showMitra = !$SCOPE_PARTNER; $offsetNo = ($page - 1) * $per; include __DIR__ . '/student_table.php'; ?>
  </div></div>
  <?php pagination($total, $per, $page, $_GET); ?>
<?php endif; ?>
<?php include __DIR__ . '/footer.php'; ?>
