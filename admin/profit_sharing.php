<?php
/**
 * Bagi Hasil Yayasan.
 * Sejak revisi ini persentase bagi hasil TIDAK lagi bersifat global.
 * Setiap Mitra memiliki kolom partners.persentase_bagi_hasil sendiri.
 * Halaman ini menampilkan pengaturan persentase seluruh Mitra (khusus Admin LPPA)
 * dan mengarahkan perubahan nilai ke halaman Manajemen Mitra.
 * Tabel lama profit_sharing_rules tidak lagi dibaca maupun dihapus.
 */
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

$q = trim($_GET['q'] ?? '');
$w = ['1=1']; $p = [];
if ($q !== '') { $w[] = 'pt.nama_mitra LIKE ?'; $p[] = "%$q%"; }
$sql = 'SELECT pt.id, pt.nama_mitra, pt.status, pt.persentase_bagi_hasil,
          (SELECT COUNT(*) FROM students s WHERE s.partner_id = pt.id) jml_siswa,
          (SELECT COALESCE(SUM(s.total_biaya),0) FROM students s WHERE s.partner_id = pt.id) total_biaya
        FROM partners pt WHERE ' . implode(' AND ', $w) . ' ORDER BY pt.nama_mitra';
$st = db()->prepare($sql); $st->execute($p); $list = $st->fetchAll();

$totalBiaya = 0.0; $totalYayasan = 0.0;
foreach ($list as $r) {
    $totalBiaya += (float)$r['total_biaya'];
    $bagiHasil = (float)$r['total_biaya'] * (float)$r['persentase_bagi_hasil'] / 100;
    $setoranBersih = hitung_total_setoran($bagiHasil, $r['id'], null);
    $totalYayasan += $setoranBersih;
}

$PAGE_TITLE = 'Bagi Hasil Yayasan per Mitra';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Bagi Hasil Yayasan']];
include __DIR__ . '/../includes/header.php';
?>
<div class="alert alert-info">
  <i class="fa-solid fa-circle-info me-1"></i>
  Persentase Bagi Hasil Yayasan ditetapkan <strong>per Mitra</strong>. Untuk mengubah nilainya, buka
  <a href="<?= base_url('admin/partners.php') ?>">Manajemen Mitra</a> &rarr; <strong>Edit</strong> pada Mitra terkait.
  Setiap perubahan persentase tercatat pada <a href="<?= base_url('admin/audit.php') ?>">Riwayat Aktivitas</a>.
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon"><i class="fa-solid fa-handshake"></i></span>
    <div><div class="label">Jumlah Mitra</div><div class="value"><?= count($list) ?></div></div></div></div>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon navy"><i class="fa-solid fa-hand-holding-dollar"></i></span>
    <div><div class="label">Total Biaya</div><div class="value"><?= rupiah($totalBiaya) ?></div></div></div></div>
  <div class="col-md-4"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon green"><i class="fa-solid fa-percent"></i></span>
    <div><div class="label">Total Pendapatan Yayasan</div><div class="value"><?= rupiah($totalYayasan) ?></div></div></div></div>
</div>

<form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-6"><label class="form-label">Cari Mitra</label><input name="q" class="form-control" value="<?= e($q) ?>" placeholder="Nama mitra"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Cari</button></div>
  <div class="col-md-2"><a href="profit_sharing" class="btn btn-light w-100">Reset</a></div>
</div></form>

<div class="card"><div class="card-header">Pengaturan Persentase Bagi Hasil Seluruh Mitra</div>
<div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr>
  <th>Nama Mitra</th><th>Status</th><th class="text-end">Persentase Bagi Hasil</th>
  <th class="text-end">Jumlah Siswa</th><th class="text-end">Total Biaya</th>
  <th class="text-end">Pendapatan Yayasan</th><th class="text-end no-print">Aksi</th>
</tr></thead><tbody>
<?php if (!$list): ?><tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-handshake-slash"></i>Belum ada mitra.</div></td></tr><?php endif; ?>
<?php foreach ($list as $r): ?>
  <?php
  $bagiHasil = (float)$r['total_biaya'] * (float)$r['persentase_bagi_hasil'] / 100;
  $setoranBersih = hitung_total_setoran($bagiHasil, $r['id']);
  ?>
  <tr>
    <td><strong><?= e($r['nama_mitra']) ?></strong></td>
    <td><?= badge_status($r['status']) ?></td>
    <td class="text-end"><span class="badge bg-primary-soft"><?= persen_fmt($r['persentase_bagi_hasil']) ?>%</span></td>
    <td class="text-end"><?= (int)$r['jml_siswa'] ?></td>
    <td class="text-end"><?= rupiah($r['total_biaya']) ?></td>
    <td class="text-end"><?= rupiah($setoranBersih) ?></td>
    <td class="text-end no-print"><a class="btn btn-sm btn-light" href="<?= base_url('admin/partners.php') ?>?aksi=edit&id=<?= (int)$r['id'] ?>">
      <i class="fa-solid fa-pen me-1"></i>Ubah Persentase</a></td>
  </tr>
<?php endforeach; ?>
</tbody><tfoot class="table-light"><tr>
  <th colspan="4" class="text-end">Total</th>
  <th class="text-end"><?= rupiah($totalBiaya) ?></th>
  <th class="text-end"><?= rupiah($totalYayasan) ?></th><th class="no-print"></th>
</tr></tfoot></table>
</div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
