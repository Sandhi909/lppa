<?php
require __DIR__ . '/_scope.php';
$rows = students_list($WHERE, $PARAMS, 10);
$perBulan = [];
foreach (students_list($WHERE, $PARAMS) as $r) {
    $b = date('Y-m', strtotime($r['tanggal_pendaftaran']));
    $perBulan[$b] = ($perBulan[$b] ?? 0) + poin_baris($r, $JENIS);
}
ksort($perBulan);
$PAGE_TITLE = 'Dashboard ' . $JENIS_LABEL;
$BREADCRUMB = [['label' => 'Dashboard']];
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon navy"><i class="fa-solid fa-user"></i></span>
    <div><div class="label"><?= e($JENIS_LABEL) ?> &middot; Unit <?= e($REC['nama_unit'] ?: '-') ?></div><div class="value" style="font-size:16px"><?= e($REC['nama']) ?></div></div></div></div>
  <div class="col-md-3"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon"><i class="fa-solid fa-user-graduate"></i></span>
    <div><div class="label">Total Siswa Terhubung</div><div class="value"><?= (int)$REKAP['siswa'] ?></div></div></div></div>
  <div class="col-md-3"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon amber"><i class="fa-solid fa-star"></i></span>
    <div><div class="label">Total Poin</div><div class="value"><?= poin_fmt($REKAP['poin']) ?></div><div class="text-muted small mt-1">Reguler: <?= (int)($REKAP['siswa_reguler'] ?? 0) ?> / Khusus: <?= (int)($REKAP['siswa_khusus'] ?? 0) ?></div></div></div></div>
  <div class="col-md-3"><div class="stat-card d-flex gap-3 align-items-center"><span class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></span>
    <div><div class="label">Total Fee (<?= e($REKAP['kategori']) ?>)</div><div class="value"><?= rupiah($REKAP['fee']) ?></div></div></div></div>
</div>
<?php render_filter_bar($_GET, ['ta','unit','periode']); ?>
<div class="row g-3">
  <div class="col-lg-5"><div class="card h-100"><div class="card-header">Perolehan Poin per Bulan</div>
    <div class="card-body"><div class="chart-box"><canvas id="cPoin"></canvas></div></div></div></div>
  <div class="col-lg-7"><div class="card h-100"><div class="card-header d-flex justify-content-between">
    <span>Siswa Rekomendasi Terbaru</span><a class="btn btn-sm btn-light" href="students">Lihat Semua</a></div>
    <div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper"><table class="table table-hover mb-0">
      <thead><tr><th>Nama Siswa</th><th>Unit / Kelas</th><th>Mitra</th><th>Program</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th class="text-end">Poin</th></tr></thead><tbody>
      <?php if (!$rows): ?><tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada siswa rekomendasi.</div></td></tr><?php endif; ?>
      <?php foreach ($rows as $r): ?><tr>
        <td><?= e($r['nama_siswa']) ?></td><td><?= e($r['nama_unit'] ?: '-') ?> / <?= e($r['kelas'] ?: '-') ?></td>
        <td><?= e($r['nama_mitra']) ?></td><td><?= e($r['program_bimbel'] ?: '-') ?></td>
        <td>
          <?php 
          $kategori = $r['kategori_peserta'] ?? 'reguler';
          if ($kategori === 'khusus'): 
          ?>
          <span class="badge bg-warning text-dark small">Khusus</span>
          <?php else: ?>
          <span class="badge bg-success small">Reguler</span>
          <?php endif; ?>
        </td>
        <td><?= tgl_id($r['tanggal_pendaftaran']) ?></td><td><?= badge_status($r['status_pembayaran']) ?></td>

        <td class="text-end"><?= poin_fmt(poin_baris($r, $JENIS)) ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div></div></div>
</div>
<script>
new Chart(document.getElementById('cPoin'),{type:'bar',
 data:{labels:<?= json_encode(array_keys($perBulan)) ?>,datasets:[{label:'Poin',data:<?= json_encode(array_values($perBulan)) ?>,backgroundColor:'#1e6fd9',borderRadius:6}]},
 options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
