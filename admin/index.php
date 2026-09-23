<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';
require_once __DIR__ . '/../includes/setoran_bagi_hasil.php';

// Get tahun ajaran terbaru (paling terbaru, id DESC)
$latestTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$latestTAId = $latestTA ? (int)$latestTA['id'] : null;

// Set default filter ke tahun ajaran terbaru jika belum dipilih (isset check agar bisa pilih "Semua")
if (!isset($_GET['academic_year_id']) && $latestTAId) {
    $_GET['academic_year_id'] = $latestTAId;
}

// Ensure academic_year_id is integer
if (!empty($_GET['academic_year_id'])) {
    $_GET['academic_year_id'] = (int)$_GET['academic_year_id'];
}

// Use the filtered GET for queries
$filterGet = $_GET;

[$where, $params] = student_filters($filterGet);
$sum = students_summary($where, $params);
$mitraAktif = (int)db()->query("SELECT COUNT(*) FROM partners WHERE status='aktif'")->fetchColumn();
$rekap = rekap_rekomendator($where, $params);
$totalFee = array_sum(array_column($rekap, 'fee'));

$perMitra = group_sum($where, $params, 'p.id', 'p.nama_mitra', 'LEFT JOIN partners p ON p.id=s.partner_id');
$perUnit  = chart_per_unit($where, $params);

// Rekap per Mitra untuk dashboard
$rekapMitra = rekap_per_mitra($where, $params);

$PAGE_TITLE = 'Dashboard Admin LPPA';
$BREADCRUMB = [['label' => 'Dashboard']];
include __DIR__ . '/../includes/header.php';

$cards = [
  ['Jumlah Mitra Aktif', number_format($mitraAktif), 'fa-handshake', 'navy'],
  ['Total Data Siswa', number_format((int)$sum['total']), 'fa-user-graduate', ''],
  ['Total Biaya Pendaftaran', rupiah($sum['biaya_pendaftaran']), 'fa-sack-dollar', 'navy'],
  ['Total Biaya Program', rupiah($sum['biaya_program']), 'fa-sack-dollar', 'blue'],
  ['Total Biaya', rupiah($sum['total_biaya']), 'fa-money-bill-wave', 'green'],
  ['Total Fee Rekomendator', rupiah($totalFee), 'fa-money-bill-trend-up', 'amber'],
];
?>
<?php render_filter_bar($filterGet, ['ta','mitra','unit','program','periode']); ?>
<div class="row g-3 mb-3">
<?php foreach ($cards as $c): ?>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card d-flex align-items-center gap-3">
      <span class="stat-icon <?= $c[3] ?>"><i class="fa-solid <?= $c[2] ?>"></i></span>
      <div><div class="label"><?= e($c[0]) ?></div><div class="value"><?= e($c[1]) ?></div></div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<div class="row g-3">
  <div class="col-lg-6"><div class="card h-100"><div class="card-header">Jumlah Peserta Berdasarkan Unit</div>
    <div class="card-body"><div class="chart-box"><canvas id="cUnit"></canvas></div></div></div></div>
  <div class="col-lg-6"><div class="card h-100"><div class="card-header">Jumlah Siswa per Mitra</div>
    <div class="card-body"><div class="chart-box"><canvas id="cSiswaMitra"></canvas></div></div></div></div>
  <div class="col-12"><div class="card"><div class="card-header">Ringkasan Biaya per Mitra</div>
    <div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper"><table class="table table-hover mb-0">
      <thead><tr><th>Nama Mitra</th><th class="text-end">Persentase Bagi Hasil</th>
        <th class="text-end">Jumlah Siswa</th><th class="text-end">Biaya Pendaftaran</th>
        <th class="text-end">Biaya Program</th><th class="text-end">Total Biaya</th>
        <th class="text-end">Bagi Hasil</th><th class="text-end">Pajak</th><th class="text-end">Setoran Bersih</th><th class="text-end">Sisa Setoran</th></tr></thead><tbody>
      <?php if (!$rekapMitra): ?><tr><td colspan="10"><div class="empty-state"><i class="fa-solid fa-handshake-slash"></i>Belum ada data.</div></td></tr><?php endif; ?>
      <?php foreach ($rekapMitra as $m): ?>
        <?php
        $bagiHasil = $m['total_biaya'] * $m['persentase_bagi_hasil'] / 100;
        $setoranBersih = hitung_total_setoran($bagiHasil, $m['partner_id']);
        $taxInfo = info_pajak_mitra($m['partner_id']);
        $sisaSetoran = $setoranBersih - total_setoran_disetujui($m['partner_id']);
        ?>
        <tr><td><?= e($m['nama_mitra']) ?></td>
        <td class="text-end"><span class="badge bg-primary-soft"><?= persen_fmt($m['persentase_bagi_hasil']) ?>%</span></td>
        <td class="text-end"><?= (int)$m['jml'] ?></td>
        <td class="text-end"><?= rupiah($m['biaya_pendaftaran']) ?></td>
        <td class="text-end"><?= rupiah($m['biaya_program']) ?></td>
        <td class="text-end"><?= rupiah($m['total_biaya']) ?></td>
        <td class="text-end"><?= rupiah($bagiHasil) ?></td>
        <td class="text-end"><?= $taxInfo['pajak_nominal'] > 0 ? '<span class="text-danger">-' . rupiah($taxInfo['pajak_nominal']) . '</span>' : '-' ?></td>
        <td class="text-end"><strong><?= rupiah($setoranBersih) ?></strong></td>
        <td class="text-end <?= $sisaSetoran > 0 ? 'text-danger' : 'text-success' ?>"><?= rupiah($sisaSetoran) ?></td></tr>
      <?php endforeach; ?>
    </table></div></div></div></div>
  <div class="col-12"><div class="card"><div class="card-header d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <span>Rekap Rekomendator Teratas</span>
        <select name="academic_year_id" class="form-select form-select-sm" style="width: 200px;" onchange="window.location.href='?academic_year_id='+this.value">
          <?= select_options(opsi('academic_years','nama_tahun_ajaran',''), $_GET['academic_year_id'] ?? '', 'Semua') ?>
        </select>
      </div>
      <a class="btn btn-sm btn-light" href="<?= base_url('admin/recap.php') ?>?<?= e(http_build_query($_GET)) ?>">Lihat Semua</a></div>
    <div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper"><table class="table table-hover mb-0">
      <thead><tr><th>#</th><th>Nama</th><th>Jenis</th><th>Tahun Ajaran</th><th>Unit</th><th class="text-end">Total</th>
        <th class="text-end">Reguler</th><th class="text-end">Khusus</th><th class="text-end">Poin</th><th>Kategori</th><th class="text-end">Total Fee</th></tr></thead><tbody>
      <?php if (!$rekap): ?><tr><td colspan="11"><div class="empty-state"><i class="fa-solid fa-user-slash"></i>Belum ada rekomendasi.</div></td></tr><?php endif; ?>
      <?php foreach (array_slice($rekap, 0, 8) as $i => $r): ?>
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
        <tr><td><?= $i + 1 ?></td><td><?= e($r['nama']) ?></td>
        <td><?= $r['jenis'] === 'wali_kelas' ? 'Wali Kelas' : 'Wali Santri' ?></td>
        <td><?= e($ayName) ?></td>
        <td><?= e($r['unit']) ?></td><td class="text-end"><?= (int)$r['siswa'] ?></td>
        <td class="text-end"><?= (int)($r['siswa_reguler'] ?? 0) ?></td><td class="text-end"><?= (int)($r['siswa_khusus'] ?? 0) ?></td>
        <td class="text-end"><?= poin_fmt($r['poin']) ?></td><td><?= e($r['kategori']) ?></td>
        <td class="text-end"><?= rupiah($r['fee']) ?></td></tr>
      <?php endforeach; ?>
    </tbody></table></div></div></div></div>
</div>
<script>
const base={responsive:true,maintainAspectRatio:false};
const money=v=>'Rp'+Number(v).toLocaleString('id-ID');
const BLUE='#1e6fd9',NAVY='#0f2f5f',GREEN='#16a34a',AMBER='#d97706',RED='#dc2626';

// Unit Chart
if (document.getElementById('cUnit')) {
  const unitLabels = <?= json_encode(array_keys($perUnit)) ?>;
  const unitData = <?= json_encode(array_values($perUnit)) ?>;
  if (unitLabels.length > 0 && unitData.length > 0) {
    new Chart(document.getElementById('cUnit'), {type:'bar',
     data:{labels:unitLabels,
        datasets:[{label:'Jumlah Siswa',data:unitData,
          backgroundColor:[BLUE,NAVY,GREEN,AMBER,RED],borderRadius:6}]},
     options:{...base, plugins:{legend:{display:false},
        title:{display:true,text:'Jumlah Peserta Berdasarkan Unit'}},
        scales:{x:{title:{display:true,text:'Unit'}},
          y:{beginAtZero:true,ticks:{precision:0},title:{display:true,text:'Jumlah Siswa'}}}}});
  }
}

// Mitra Siswa Chart
if (document.getElementById('cSiswaMitra')) {
  const mitraLabels = <?= json_encode(array_column($perMitra, 'label')) ?>;
  const mitraData = <?= json_encode(array_map('intval', array_column($perMitra, 'jml'))) ?>;
  if (mitraLabels.length > 0 && mitraData.length > 0) {
    new Chart(document.getElementById('cSiswaMitra'), {type:'bar',
      data:{labels:mitraLabels,
        datasets:[{label:'Jumlah Siswa',data:mitraData,backgroundColor:BLUE,borderRadius:6}]},
      options:{...base, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
  }
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
