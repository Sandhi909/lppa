<header class="app-navbar">
  <button class="btn btn-icon d-lg-none" id="btnMobileMenu" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
  <button class="btn btn-icon d-none d-lg-inline-flex" id="btnCollapse" aria-label="Ciutkan sidebar"><i class="fa-solid fa-bars-staggered"></i></button>
  <div class="ms-auto dropdown">
    <button class="btn btn-user dropdown-toggle" data-bs-toggle="dropdown">
      <span class="avatar"><?= e(strtoupper(substr(user()['nama'] ?? 'U', 0, 1))) ?></span>
      <span class="d-none d-sm-inline text-start">
        <strong><?= e(user()['nama'] ?? 'User') ?></strong>
        <small class="d-block"><?= e(str_replace('_', ' ', ucfirst(role()))) ?></small>
      </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="<?= home_url() ?>"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
      <li><a class="dropdown-item" href="<?= base_url(match(role()) {
          'admin' => 'admin/profile.php',
          'mitra' => 'mitra/profile.php',
          'wali_kelas', 'wali_santri' => 'wali/profile.php',
          'orang_tua' => 'orang_tua/profile.php',
          default => ''
      }) ?>"><i class="fa-solid fa-key me-2"></i>Ubah Password</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout.php') ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Keluar</a></li>
    </ul>
  </div>
</header>
