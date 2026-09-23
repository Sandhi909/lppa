<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'direksi']);
require_once __DIR__ . '/../includes/queries.php';

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

[$where, $params] = student_filters($_GET);
$jenis = $_GET['jenis'] ?? '';
$rekap = rekap_rekomendator($where, $params, $jenis);
$totPoin = array_sum(array_column($rekap, 'poin'));
$totFee  = array_sum(array_column($rekap, 'fee'));

$PAGE_TITLE = 'Rekap Rekomendator';
$BREADCRUMB = role() === 'direksi' 
    ? [['label' => 'Dashboard', 'url' => base_url('admin/direksi_dashboard.php')], ['label' => 'Rekap Rekomendator']]
    : [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Rekap Rekomendator']];
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon"><i class="fa-solid fa-users"></i></span>
    <div><div class="label">Jumlah Rekomendator</div><div class="value"><?= count($rekap) ?></div></div></div></div>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon navy"><i class="fa-solid fa-star"></i></span>
    <div><div class="label">Total Poin</div><div class="value"><?= poin_fmt($totPoin) ?></div></div></div></div>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon amber"><i class="fa-solid fa-money-bill-wave"></i></span>
    <div><div class="label">Total Fee</div><div class="value"><?= rupiah($totFee) ?></div></div></div></div>
</div>
<form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-2"><label class="form-label">Tahun Ajaran</label><select name="academic_year_id" class="form-select"><?= select_options(opsi('academic_years','nama_tahun_ajaran',''), $_GET['academic_year_id'] ?? '', 'Semua') ?></select></div>
  <div class="col-md-2"><label class="form-label">Mitra</label><select name="partner_id" class="form-select"><?= select_options(opsi('partners','nama_mitra',''), $_GET['partner_id'] ?? '', 'Semua') ?></select></div>
  <div class="col-md-2"><label class="form-label">Unit</label><select name="unit_id" class="form-select"><?= select_options(opsi('units','nama_unit',''), $_GET['unit_id'] ?? '', 'Semua') ?></select></div>
  <div class="col-md-2"><label class="form-label">Jenis</label><select name="jenis" class="form-select"><option value="">Semua</option>
    <option value="wali_kelas" <?= $jenis === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
    <option value="wali_santri" <?= $jenis === 'wali_santri' ? 'selected' : '' ?>>Wali Santri</option></select></div>
  <div class="col-md-2"><label class="form-label">Dari</label><input type="date" name="dari" class="form-control" value="<?= e($_GET['dari'] ?? '') ?>"></div>
  <div class="col-md-2"><label class="form-label">Sampai</label><input type="date" name="sampai" class="form-control" value="<?= e($_GET['sampai'] ?? '') ?>"></div>
  <div class="col-12 d-flex gap-2"><button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Terapkan</button>
    <a href="recap" class="btn btn-light">Reset</a>
    <a class="btn btn-navy ms-auto" href="<?= base_url('reports/export.php') ?>?jenis_laporan=rekap&<?= e(http_build_query($_GET)) ?>&format=csv"><i class="fa-solid fa-file-csv me-1"></i>Export CSV</a>
    <a class="btn btn-navy" href="<?= base_url('reports/export.php') ?>?jenis_laporan=rekap&<?= e(http_build_query($_GET)) ?>&format=excel"><i class="fa-solid fa-file-excel me-1"></i>Export Excel</a>
    <button type="button" class="btn btn-light" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>Cetak</button>
  </div>
</div></form>
<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr><th>#</th><th>Nama Rekomendator</th><th>Jenis</th><th>Tahun Ajaran</th><th>Unit</th>
  <th class="text-end">Total Siswa</th><th class="text-end">Reguler</th><th class="text-end">Khusus</th><th class="text-end">Total Poin</th><th>Kategori Fee</th><th class="text-end">Total Fee</th></tr></thead><tbody>
<?php if (!$rekap): ?><tr><td colspan="11"><div class="empty-state"><i class="fa-solid fa-user-slash"></i>Belum ada data rekomendasi pada filter ini.</div></td></tr><?php endif; ?>
<?php foreach ($rekap as $i => $r): ?>
  <?php 
  $ayName = '';
  if ($r['academic_year_id']) {
      $ay = db()->prepare('SELECT nama_tahun_ajaran FROM academic_years WHERE id = ?');
      $ay->execute([$r['academic_year_id']]);
      $ayName = $ay->fetchColumn() ?: '-';
  } else {
      $ayName = '-';
  }
  ?>
  <tr><td><?= $i + 1 ?></td><td><strong><?= e($r['nama']) ?></strong></td>
  <td><span class="badge <?= $r['jenis'] === 'wali_kelas' ? 'bg-primary-soft' : 'bg-success-soft' ?>"><?= $r['jenis'] === 'wali_kelas' ? 'Wali Kelas' : 'Wali Santri' ?></span></td>
  <td><?= e($ayName) ?></td>
  <td><?= e($r['unit']) ?></td><td class="text-end"><?= $r['siswa'] ?></td>
  <td class="text-end"><?= (int)($r['siswa_reguler'] ?? 0) ?></td><td class="text-end"><?= (int)($r['siswa_khusus'] ?? 0) ?></td>
  <td class="text-end"><strong><?= poin_fmt($r['poin']) ?></strong></td><td><?= e($r['kategori']) ?></td>
  <td class="text-end"><?= rupiah($r['fee']) ?></td></tr>
<?php endforeach; ?>
</tbody><tfoot class="table-light"><tr><th colspan="6" class="text-end">Total</th>
  <th class="text-end"><?= array_sum(array_column($rekap, 'siswa_reguler')) ?></th><th class="text-end"><?= array_sum(array_column($rekap, 'siswa_khusus')) ?></th>
  <th class="text-end"><?= poin_fmt($totPoin) ?></th><th></th><th class="text-end"><?= rupiah($totFee) ?></th></tr></tfoot></table>
</div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
