<?php
require __DIR__ . '/_scope.php';
$rows = students_list($WHERE, $PARAMS);
$periode = [];
foreach ($rows as $r) {
    $b = date('Y-m', strtotime($r['tanggal_pendaftaran']));
    $periode[$b] = ($periode[$b] ?? 0) + poin_baris($r, $JENIS);
}
krsort($periode);
$bulanIni = date('Y-m');
$poinBulanIni = $periode[$bulanIni] ?? 0;
$feeBulanIni = fee_dari_poin($poinBulanIni, $JENIS);
$aturan = db()->prepare("SELECT * FROM fee_rules WHERE jenis_rekomendator=? AND status='aktif' ORDER BY poin_minimum");
$aturan->execute([$JENIS]); $aturan = $aturan->fetchAll();

$PAGE_TITLE = 'Rincian Fee Rekomendasi';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('wali/index.php')], ['label' => 'Rincian Fee']];
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Poin (semua periode)</div><div class="value"><?= poin_fmt($REKAP['poin']) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Kategori Fee</div><div class="value" style="font-size:16px"><?= e($REKAP['kategori']) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Fee</div><div class="value"><?= rupiah($REKAP['fee']) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Fee Periode Berjalan (<?= e($bulanIni) ?>)</div><div class="value"><?= rupiah($feeBulanIni['total']) ?></div></div></div>
</div>
<?php render_filter_bar($_GET, ['ta','mitra','periode']); ?>
<div class="row g-3">
  <div class="col-lg-6"><div class="card h-100"><div class="card-header">Rekap per Periode (Bulan)</div><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-hover mb-0"><thead><tr><th>Periode</th><th class="text-end">Poin</th><th>Kategori</th><th class="text-end">Nominal/Poin</th><th class="text-end">Total Fee</th></tr></thead><tbody>
    <?php if (!$periode): ?><tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-calendar-xmark"></i>Belum ada perolehan poin.</div></td></tr><?php endif; ?>
    <?php foreach ($periode as $bln => $poin): $f = fee_dari_poin($poin, $JENIS); ?>
      <tr><td><?= e($bln) ?></td><td class="text-end"><?= poin_fmt($poin) ?></td><td><?= e($f['kategori']) ?></td><td class="text-end"><?= rupiah($f['nominal']) ?></td><td class="text-end"><strong><?= rupiah($f['total']) ?></strong></td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div></div></div>
  <div class="col-lg-6"><div class="card h-100"><div class="card-header">Tabel Aturan Fee Berlaku (<?= e($JENIS_LABEL) ?>)</div><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-hover mb-0"><thead><tr><th>Kategori</th><th>Rentang Poin</th><th class="text-end">Nominal Fee per Poin</th></tr></thead><tbody>
    <?php foreach ($aturan as $a): ?>
      <tr><td><?= e($a['kategori'] ?: '-') ?></td><td><?= poin_fmt($a['poin_minimum']) ?> &ndash; <?= $a['poin_maksimum'] !== null ? poin_fmt($a['poin_maksimum']) : '∞' ?> poin</td>
      <td class="text-end"><?= rupiah($a['nominal_fee']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <div class="p-3 border-top small text-muted"><i class="fa-solid fa-circle-info me-1"></i>
      Total Fee dihitung dari Total Poin &times; Nominal Fee per Poin sesuai kategori.<br>Poin: 1 poin bila hanya Wali Kelas yang merekomendasikan; bila ada Wali Santri, Wali Santri 1 poin dan Wali Kelas 0,5 poin.</div>
  </div></div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
