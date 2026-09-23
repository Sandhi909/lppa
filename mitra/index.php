<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra']);
require_once __DIR__ . '/../includes/queries.php';
require_once __DIR__ . '/../includes/setoran_bagi_hasil.php';
$pid = my_partner_id();
if (!$pid) die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

// Use the filtered GET for queries
$filterGet = $_GET;

[$where, $params] = student_filters($filterGet, ['partner_id' => $pid]);
$sum = students_summary($where, $params);
$pct = persentase_mitra($pid); // persentase milik Mitra ini saja
// Hitung fee bagi hasil yayasan dalam nominal (untuk kotak dashboard)
$bagiHasilKotor = (float)$sum['total_biaya'] * (float)$pct / 100;
$taxInfoCard = info_pajak_mitra($pid, $where, $params);
$feeBagiHasilNominal = $taxInfoCard['total_bersih']; // sudah potong pajak jika ada
if ($taxInfoCard['pajak_nominal'] <= 0) $feeBagiHasilNominal = $bagiHasilKotor;
$perUnit = chart_per_unit($where, $params);
$st = db()->prepare("SELECT DATE_FORMAT(s.tanggal_pendaftaran,'%Y-%m') bln, COUNT(*) jml,
    COALESCE(SUM(s.total_biaya),0) total_biaya FROM students s WHERE $where GROUP BY bln ORDER BY bln");
$st->execute($params); $perBulan = $st->fetchAll();
$terbaru = students_list($where, $params, 5);
[$wA, $pA] = student_filters([], ['partner_id' => $pid, 'status' => 'angsuran']);
$angsuran = students_list($wA, $pA, 5);

$PAGE_TITLE = 'Dashboard Mitra';
$BREADCRUMB = [['label' => 'Dashboard']];
include __DIR__ . '/../includes/header.php';
$cards = [
  ['Total Data Siswa', number_format((int)$sum['total']), 'fa-user-graduate', ''],
  ['Total Biaya Pendaftaran', rupiah($sum['biaya_pendaftaran']), 'fa-sack-dollar', 'navy'],
  ['Total Biaya Program', rupiah($sum['biaya_program']), 'fa-sack-dollar', 'blue'],
  ['Total Biaya', rupiah($sum['total_biaya']), 'fa-money-bill-wave', 'green'],
  ['Persentase Bagi Hasil Yayasan', persen_fmt($pct) . '%', 'fa-percent', 'navy'],
  ['Fee Bagi Hasil Yayasan', rupiah($feeBagiHasilNominal), 'fa-hand-holding-dollar', 'amber'],
];
?>
<?php render_filter_bar($filterGet, ['ta','unit','program','periode']); ?>
<div class="row g-3 mb-3">
<?php foreach ($cards as $c): ?>
  <div class="col-6 col-md-3 col-xl-2"><div class="stat-card d-flex align-items-center gap-3">
    <span class="stat-icon <?= $c[3] ?>"><i class="fa-solid <?= $c[2] ?>"></i></span>
    <div><div class="label"><?= e($c[0]) ?></div><div class="value"><?= e($c[1]) ?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-3">
  <div class="col-lg-6"><div class="card h-100"><div class="card-header">Jumlah Peserta Berdasarkan Unit</div>
    <div class="card-body"><div class="chart-box"><canvas id="cUnit"></canvas></div></div></div></div>
  <div class="col-lg-6"><div class="card h-100"><div class="card-header">Total Biaya per Bulan</div>
    <div class="card-body"><div class="chart-box"><canvas id="cBayar"></canvas></div></div></div></div>
  <div class="col-lg-4"><div class="card h-100"><div class="card-header">Ringkasan Bagi Hasil Yayasan</div><div class="card-body">
    <?php
    $bagiHasilKotor = (float)$sum['total_biaya'] * $pct / 100;
    $taxInfo = info_pajak_mitra($pid, $where, $params);
    ?>
    <table class="table table-sm mb-0">
      <tr><td class="text-muted">Total Biaya</td><td class="text-end"><?= rupiah($sum['total_biaya']) ?></td></tr>
      <tr><td class="text-muted">Persentase Bagi Hasil Yayasan</td><td class="text-end"><strong><?= persen_fmt($pct) ?>%</strong></td></tr>
      <tr class="table-light"><td><strong>Bagi Hasil Yayasan</strong></td><td class="text-end"><strong><?= rupiah($bagiHasilKotor) ?></strong></td></tr>
      <?php if ($taxInfo['pajak_nominal'] > 0): ?>
      <tr><td class="text-muted">Pajak (<?= persen_fmt($taxInfo['pajak_percentage']) ?>%)</td><td class="text-end text-danger">-<?= rupiah($taxInfo['pajak_nominal']) ?></td></tr>
      <tr class="table-light"><td><strong>Total Setoran</strong></td><td class="text-end"><strong><?= rupiah($taxInfo['total_bersih']) ?></strong></td></tr>
      <?php endif; ?>
      <tr><td class="text-muted">Bagian Mitra</td><td class="text-end"><?= rupiah((float)$sum['total_biaya'] - $bagiHasilKotor) ?></td></tr>
    </table>
    <p class="small text-muted mt-3 mb-0">Persentase bagi hasil ditetapkan oleh Admin LPPA dan hanya dapat diubah oleh Admin LPPA.
    Perhitungan otomatis mengikuti nilai Total Biaya terbaru.</p>
  </div></div></div>
  <div class="col-lg-8"><div class="card h-100"><div class="card-header d-flex justify-content-between">
    <span>Siswa Terbaru</span><a href="<?= base_url('mitra/students.php') ?>" class="btn btn-sm btn-light">Lihat Semua</a></div>
    <div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper"><table class="table table-hover mb-0">
      <thead><tr><th>Nama</th><th>Program Bimbel</th><th class="text-end">Biaya Pendaftaran</th><th class="text-end">Biaya Program</th><th class="text-end">Total Biaya</th><th>Status</th></tr></thead><tbody>
      <?php if (!$terbaru): ?><tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada siswa.</div></td></tr><?php endif; ?>
      <?php foreach ($terbaru as $r): ?><tr><td><?= e($r['nama_siswa']) ?><br><small class="text-muted"><?= tgl_id($r['tanggal_pendaftaran']) ?></small></td>
        <td><?= e($r['program_bimbel'] ?: '-') ?></td><td class="text-end"><?= rupiah($r['biaya_pendaftaran']) ?></td>
        <td class="text-end"><?= rupiah($r['biaya_program']) ?></td>
        <td class="text-end"><?= rupiah($r['total_biaya']) ?></td><td><?= badge_status($r['status_pembayaran']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div></div></div>
  <div class="col-12"><div class="card"><div class="card-header">Siswa Berstatus Angsuran</div><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-hover mb-0"><thead><tr><th>Nama</th><th>Unit</th><th>Kelas</th><th class="text-end">Biaya Pendaftaran</th><th class="text-end">Biaya Program</th><th class="text-end">Total Biaya</th><th class="no-print"></th></tr></thead><tbody>
    <?php if (!$angsuran): ?><tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-circle-check"></i>Tidak ada siswa berstatus angsuran.</div></td></tr><?php endif; ?>
    <?php foreach ($angsuran as $r): ?><tr><td><?= e($r['nama_siswa']) ?></td><td><?= e($r['nama_unit'] ?: '-') ?></td><td><?= e($r['kelas'] ?: '-') ?></td>
      <td class="text-end"><?= rupiah($r['biaya_pendaftaran']) ?></td><td class="text-end"><?= rupiah($r['biaya_program']) ?></td>
      <td class="text-end"><?= rupiah($r['total_biaya']) ?></td>
      <td class="text-end no-print"><a class="btn btn-sm btn-primary" href="<?= base_url('mitra/students.php') ?>?aksi=detail&id=<?= (int)$r['id'] ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td></tr><?php endforeach; ?>
    </tbody></table></div></div></div></div>
</div>
<script>
const base={responsive:true,maintainAspectRatio:false};
const money=v=>'Rp'+Number(v).toLocaleString('id-ID');

// Unit Chart
if (document.getElementById('cUnit')) {
  const unitLabels = <?= json_encode(array_keys($perUnit)) ?>;
  const unitData = <?= json_encode(array_values($perUnit)) ?>;
  if (unitLabels.length > 0 && unitData.length > 0) {
    new Chart(document.getElementById('cUnit'),{type:'bar',
     data:{labels:unitLabels,datasets:[{label:'Jumlah Siswa',data:unitData,backgroundColor:'#1e6fd9',borderRadius:6}]},
     options:{...base,plugins:{legend:{display:false},title:{display:true,text:'Jumlah Peserta Berdasarkan Unit'}},
      scales:{x:{title:{display:true,text:'Unit'}},y:{beginAtZero:true,ticks:{precision:0},title:{display:true,text:'Jumlah Siswa'}}}}});
  }
}

// Biaya Chart
if (document.getElementById('cBayar')) {
  const bulanLabels = <?= json_encode(array_column($perBulan,'bln')) ?>;
  const biayaData = <?= json_encode(array_map('floatval',array_column($perBulan,'total_biaya'))) ?>;
  if (bulanLabels.length > 0 && biayaData.length > 0) {
    new Chart(document.getElementById('cBayar'),{type:'line',
     data:{labels:bulanLabels,datasets:[{label:'Total Biaya',data:biayaData,borderColor:'#16a34a',backgroundColor:'rgba(22,163,74,.15)',fill:true,tension:.35}]},
     options:{...base,scales:{y:{beginAtZero:true,ticks:{callback:money}}},plugins:{tooltip:{callbacks:{label:c=>money(c.raw)}}}}});
  }
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
