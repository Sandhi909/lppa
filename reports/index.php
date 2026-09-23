<?php
require __DIR__ . '/_scope.php';
$rep = build_report($jl, $WHERE, $PARAMS);
$sum = students_summary($WHERE, $PARAMS);
$qs = http_build_query($_GET);
$PAGE_TITLE = 'Laporan';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => home_url()], ['label' => 'Laporan']];
include __DIR__ . '/../includes/header.php';
?>
<form class="card mb-3 no-print" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-4"><label class="form-label">Jenis Laporan</label><select name="jenis_laporan" class="form-select">
    <?php foreach ($JENIS_LAPORAN as $k => $v): ?><option value="<?= $k ?>" <?= $jl === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?>
  </select></div>
  <div class="col-md-2"><label class="form-label">Tahun Ajaran</label><select name="academic_year_id" class="form-select"><?= select_options(opsi('academic_years','nama_tahun_ajaran',''), $_GET['academic_year_id'] ?? '', 'Semua') ?></select></div>
  <?php if (!$SCOPE_PARTNER): ?>
  <div class="col-md-2"><label class="form-label">Mitra</label><select name="partner_id" class="form-select"><?= select_options(opsi('partners','nama_mitra',''), $_GET['partner_id'] ?? '', 'Semua') ?></select></div>
  <?php endif; ?>
  <div class="col-md-2"><label class="form-label">Unit</label><select name="unit_id" class="form-select"><?= select_options(opsi('units','nama_unit',''), $_GET['unit_id'] ?? '', 'Semua') ?></select></div>
  <div class="col-md-2"><label class="form-label">Program Bimbel</label>
    <input name="program" class="form-control" list="listProgramLaporan" placeholder="Semua" value="<?= e($_GET['program'] ?? '') ?>">
    <datalist id="listProgramLaporan"><?php foreach (daftar_program_bimbel() as $pr) echo '<option value="' . e($pr) . '">'; ?></datalist></div>
  <div class="col-md-2"><label class="form-label">Status Pembayaran</label><select name="status" class="form-select"><option value="">Semua</option>
    <?php foreach (STATUS_LIST as $k => $lbl): ?><option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option><?php endforeach; ?></select></div>

  <div class="col-md-2"><label class="form-label">Rekomendator</label><select name="rekomendator_id" class="form-select">
    <?= select_options(db()->query('SELECT id, nama FROM recommenders ORDER BY nama')->fetchAll(), $_GET['rekomendator_id'] ?? '', 'Semua') ?></select></div>
  <div class="col-md-2"><label class="form-label">Periode Dari</label><input type="date" name="dari" class="form-control" value="<?= e($_GET['dari'] ?? '') ?>"></div>
  <div class="col-md-2"><label class="form-label">Sampai</label><input type="date" name="sampai" class="form-control" value="<?= e($_GET['sampai'] ?? '') ?>"></div>
  <div class="col-12 d-flex flex-wrap gap-2 pt-2">
    <button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Tampilkan</button>
    <a href="index" class="btn btn-light">Reset</a>
    <a class="btn btn-navy ms-auto" href="export?<?= e($qs) ?>&format=csv"><i class="fa-solid fa-file-csv me-1"></i>Export CSV</a>
    <a class="btn btn-navy" href="export?<?= e($qs) ?>&format=excel"><i class="fa-solid fa-file-excel me-1"></i>Export Excel</a>
    <button type="button" class="btn btn-light" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>Cetak</button>
  </div>
</div></form>

<div class="row g-3 mb-3">
  <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Total Siswa</div><div class="value"><?= number_format((int)$sum['total']) ?></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Total Biaya Pendaftaran</div><div class="value"><?= rupiah($sum['biaya_pendaftaran']) ?></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Total Biaya Program</div><div class="value"><?= rupiah($sum['biaya_program']) ?></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Total Biaya</div><div class="value"><?= rupiah($sum['total_biaya']) ?></div></div></div>

</div>

<div class="card"><div class="card-header"><?= e($JENIS_LAPORAN[$jl]) ?></div>
<div class="card-body p-0"><div class="table-responsive">
<table class="table table-hover align-middle"><thead><tr>
<?php foreach ($rep['header'] as $i => $h): ?><th class="<?= in_array($i, $rep['money'], true) ? 'text-end' : '' ?>"><?= e($h) ?></th><?php endforeach; ?>
</tr></thead><tbody>
<?php if (!$rep['rows']): ?><tr><td colspan="<?= count($rep['header']) ?>"><div class="empty-state"><i class="fa-solid fa-file-circle-question"></i>Tidak ada data pada filter ini.</div></td></tr><?php endif; ?>
<?php foreach ($rep['rows'] as $r): ?><tr>
  <?php foreach ($r as $i => $c): ?>
    <td class="<?= in_array($i, $rep['money'], true) ? 'text-end' : '' ?>">
      <?= in_array($i, $rep['money'], true) ? rupiah($c) : e(is_float($c) ? poin_fmt($c) : $c) ?></td>
  <?php endforeach; ?>
</tr><?php endforeach; ?>
</tbody></table></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
