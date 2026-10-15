<?php
$cur = basename($_SERVER['PHP_SELF']);
$dir = basename(dirname($_SERVER['PHP_SELF']));
function nav_item(string $url, string $icon, string $label, bool $active): string {
    return '<li><a class="nav-link' . ($active ? ' active' : '') . '" href="' . e($url) . '">
        <i class="fa-solid ' . $icon . '"></i><span>' . e($label) . '</span></a></li>';
}
?>
<aside class="app-sidebar" id="sidebar">
  <div class="sidebar-brand d-flex align-items-center gap-2">
    <img src="<?= base_url('assets/images/LPPA.png') ?>" alt="LPPA Logo" class="sidebar-logo" style="max-width:40px;height:40px;width:auto">
    <span>LPPA <small>Kemitraan Bimbel</small></span>
  </div>
  <nav><ul class="nav-list">
  <?php if (role() === 'admin'): ?>
    <li class="nav-caption">Utama</li>
    <?= nav_item(base_url('admin/index.php'), 'fa-gauge-high', 'Dashboard', $dir==='admin' && $cur==='index.php') ?>
    <li class="nav-caption">Data Master</li>
    <?= nav_item(base_url('admin/partners.php'), 'fa-handshake', 'Mitra Bimbel', $cur==='partners.php') ?>
    <?= nav_item(base_url('admin/wali_kelas.php'), 'fa-chalkboard-user', 'Wali Kelas', $cur==='wali_kelas.php') ?>
    <?= nav_item(base_url('admin/wali_santri.php'), 'fa-user-shield', 'Wali Santri', $cur==='wali_santri.php') ?>
    <?= nav_item(base_url('admin/students.php'), 'fa-user-graduate', 'Data Siswa', $dir==='admin' && $cur==='students.php') ?>
    <?= nav_item(base_url('admin/import.php'), 'fa-file-import', 'Import Data Siswa', $cur==='import.php') ?>
    <?= nav_item(base_url('admin/master.php'), 'fa-layer-group', 'Tahun Ajaran / Unit / Program', $cur==='master.php') ?>
    <!-- Jadwal Pemakaian Ruang - Hidden for now -->
    <!-- <?= nav_item(base_url('admin/jadwal-pemakaian-ruang.php'), 'fa-calendar-days', 'Jadwal Pemakaian Ruang', $cur==='jadwal-pemakaian-ruang.php') ?> -->
    <!-- <?= nav_item(base_url('admin/rekap-jadwal-pemakaian.php'), 'fa-chart-pie', 'Rekap Jadwal Fee', $cur==='rekap-jadwal-pemakaian.php') ?> -->
    <li class="nav-caption">Pengaturan</li>
    <?= nav_item(base_url('admin/profit_sharing.php'), 'fa-percent', 'Bagi Hasil per Mitra', $cur==='profit_sharing.php') ?>
    <?= nav_item(base_url('admin/setoran_bagi_hasil.php'), 'fa-hand-holding-dollar', 'Setoran Bagi Hasil', $cur==='setoran_bagi_hasil.php') ?>
    <?= nav_item(base_url('admin/fee_rules.php'), 'fa-money-bill-trend-up', 'Fee Rekomendator', $cur==='fee_rules.php') ?>
    <!-- Konfigurasi Tarif Fee - Hidden for now -->
    <!-- <?= nav_item(base_url('admin/config-fee.php'), 'fa-gear', 'Konfigurasi Tarif Fee', $cur==='config-fee.php') ?> -->
    <?= nav_item(base_url('admin/users.php'), 'fa-users-gear', 'Akun Pengguna', $cur==='users.php') ?>
    <li class="nav-caption">Export Data</li>
    <?= nav_item(base_url('admin/export_parent_accounts.php'), 'fa-file-pdf', 'Export Akun Orang Tua', $cur==='export_parent_accounts.php') ?>
    <?= nav_item(base_url('admin/export_recommender_accounts.php'), 'fa-file-pdf', 'Export Akun Rekomendator', $cur==='export_recommender_accounts.php') ?>
    <li class="nav-caption">Absensi & Pencapaian</li>
    <?= nav_item(base_url('admin/absensi.php'), 'fa-calendar-check', 'Absensi Siswa', $cur==='absensi.php') ?>
    <?= nav_item(base_url('admin/pencapaian.php'), 'fa-trophy', 'Pencapaian Siswa', $cur==='pencapaian.php') ?>
    <li class="nav-caption">Konten Public</li>
    <?= nav_item(base_url('admin/konten_lppa.php'), 'fa-file-lines', 'Konten LPPA', $cur==='konten_lppa.php') ?>
    <?= nav_item(base_url('admin/konten_mitra.php'), 'fa-building', 'Konten Mitra', $cur==='konten_mitra.php') ?>
    <?= nav_item(base_url('admin/galeri_mitra.php'), 'fa-images', 'Galeri Mitra', $cur==='galeri_mitra.php') ?>
    <li class="nav-caption">Laporan</li>
    <?= nav_item(base_url('reports/index.php'), 'fa-file-lines', 'Laporan', $dir==='reports') ?>
    <?= nav_item(base_url('admin/recap.php'), 'fa-ranking-star', 'Rekap Rekomendator', $cur==='recap.php') ?>
    <?= nav_item(base_url('admin/tax_report.php'), 'fa-percent', 'Laporan Pajak', $cur==='tax_report.php') ?>
    <?= nav_item(base_url('admin/audit.php'), 'fa-clock-rotate-left', 'Riwayat Aktivitas', $cur==='audit.php') ?>
  <?php elseif (role() === 'direksi'): ?>
    <li class="nav-caption">Utama</li>
    <?= nav_item(base_url('admin/direksi_dashboard.php'), 'fa-gauge-high', 'Dashboard', $cur==='direksi_dashboard.php') ?>
    <li class="nav-caption">Data</li>
    <?= nav_item(base_url('admin/students.php'), 'fa-user-graduate', 'Data Siswa', $cur==='students.php') ?>
    <?= nav_item(base_url('admin/pencapaian.php'), 'fa-trophy', 'Data Pencapaian', $cur==='pencapaian.php') ?>
    <?= nav_item(base_url('admin/recap.php'), 'fa-ranking-star', 'Data Rekomendator', $cur==='recap.php') ?>
  <?php elseif (role() === 'mitra'): ?>
    <li class="nav-caption">Utama</li>
    <?= nav_item(base_url('mitra/index.php'), 'fa-gauge-high', 'Dashboard', $dir==='mitra' && $cur==='index.php') ?>
    <?= nav_item(base_url('mitra/students.php'), 'fa-user-graduate', 'Data Siswa', $cur==='students.php') ?>
    <?= nav_item(base_url('mitra/import.php'), 'fa-file-import', 'Import Data Siswa', $cur==='import.php') ?>
    <?= nav_item(base_url('mitra/setoran_bagi_hasil.php'), 'fa-hand-holding-dollar', 'Setoran Bagi Hasil', $cur==='setoran_bagi_hasil.php') ?>
    <li class="nav-caption">Konten Public</li>
    <?= nav_item(base_url('mitra/konten.php'), 'fa-building', 'Konten Saya', $cur==='konten.php') ?>
    <?= nav_item(base_url('mitra/galeri.php'), 'fa-images', 'Galeri Saya', $cur==='galeri.php') ?>
    <li class="nav-caption">Absensi & Pencapaian</li>
    <?= nav_item(base_url('admin/absensi.php'), 'fa-calendar-check', 'Absensi Siswa', $cur==='absensi.php') ?>
    <?= nav_item(base_url('admin/pencapaian.php'), 'fa-trophy', 'Pencapaian Siswa', $cur==='pencapaian.php') ?>
    <li class="nav-caption">Export Data</li>
    <?= nav_item(base_url('admin/export_parent_accounts.php'), 'fa-file-pdf', 'Export Akun Orang Tua', $cur==='export_parent_accounts.php') ?>
    <li class="nav-caption">Laporan</li>
    <?= nav_item(base_url('reports/index.php'), 'fa-file-lines', 'Laporan Saya', $dir==='reports') ?>
  <?php elseif (role() === 'orang_tua'): ?>
    <li class="nav-caption">Utama</li>
    <?= nav_item(base_url('orang_tua/index.php'), 'fa-gauge-high', 'Dashboard', $dir==='orang_tua' && $cur==='index.php') ?>
    <?= nav_item(base_url('orang_tua/absensi.php'), 'fa-calendar-check', 'Absensi Anak', $cur==='absensi.php') ?>
    <?= nav_item(base_url('orang_tua/pencapaian.php'), 'fa-trophy', 'Pencapaian Anak', $cur==='pencapaian.php') ?>
    <?= nav_item(base_url('orang_tua/profile.php'), 'fa-user', 'Profil Akun', $cur==='profile.php') ?>
  <?php else: ?>
    <li class="nav-caption">Utama</li>
    <?= nav_item(base_url('wali/index.php'), 'fa-gauge-high', 'Dashboard', $cur==='index.php') ?>
    <?= nav_item(base_url('wali/students.php'), 'fa-user-graduate', 'Siswa Rekomendasi', $cur==='students.php') ?>
    <?= nav_item(base_url('wali/pencapaian.php'), 'fa-trophy', 'Pencapaian Siswa', $cur==='pencapaian.php') ?>
    <?= nav_item(base_url('wali/fee.php'), 'fa-money-bill-wave', 'Rincian Fee', $cur==='fee.php') ?>
  <?php endif; ?>
    <li class="nav-caption">Akun</li>
    <li><a class="nav-link text-danger-emphasis" href="<?= base_url('auth/logout.php') ?>"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span></a></li>
  </ul></nav>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
