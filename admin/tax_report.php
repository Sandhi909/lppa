<?php
/**
 * Laporan Detail Pajak per Mitra per Tahun Ajaran
 */
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/queries.php';
require_once __DIR__ . '/../includes/setoran_bagi_hasil.php';

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

$filterAcademicYearId = (int)($_GET['academic_year_id'] ?? 0) ?: null;
$filterPartnerId = (int)($_GET['partner_id'] ?? 0) ?: null;

// Build data
$data = [];
$totalBagiHasil = 0;
$totalPajak = 0;
$totalSetoran = 0;

$sql = "SELECT p.id, p.nama_mitra,
               COALESCE(SUM(s.total_biaya * COALESCE(p.persentase_bagi_hasil, 0) / 100), 0) as bagi_hasil_kotor
        FROM partners p
        LEFT JOIN students s ON s.partner_id = p.id";

$where = ['1=1'];
$params = [];

if ($filterAcademicYearId) {
    $where[] = 's.academic_year_id = ?';
    $params[] = $filterAcademicYearId;
}
if ($filterPartnerId) {
    $where[] = 'p.id = ?';
    $params[] = $filterPartnerId;
}

$sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' GROUP BY p.id, p.nama_mitra ORDER BY p.nama_mitra';

$st = db()->prepare($sql);
$st->execute($params);
$partners = $st->fetchAll();

foreach ($partners as $p) {
    $bagiHasil = (float)$p['bagi_hasil_kotor'];
    $pajak = hitung_pajak($bagiHasil, $p['id'], $filterAcademicYearId);
    $setoran = $bagiHasil - $pajak;
    
    $taxConfig = get_tax_config($p['id'], $filterAcademicYearId);
    
    $data[] = [
        'partner_id' => $p['id'],
        'nama_mitra' => $p['nama_mitra'],
        'bagi_hasil' => $bagiHasil,
        'pajak' => $pajak,
        'setoran' => $setoran,
        'tax_method' => $taxConfig['method'],
        'tax_percentage' => $taxConfig['percentage'],
        'tax_manual_nominal' => $taxConfig['manual_nominal'],
        'tax_enabled' => $taxConfig['enabled'],
    ];
    
    $totalBagiHasil += $bagiHasil;
    $totalPajak += $pajak;
    $totalSetoran += $setoran;
}

$PAGE_TITLE = 'Laporan Detail Pajak per Mitra';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Laporan Pajak']];
include __DIR__ . '/../includes/header.php';
?>

<form class="card mb-3" method="get" data-noload>
  <div class="card-body row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Tahun Ajaran</label>
      <select name="academic_year_id" class="form-select">
        <option value="">Semua</option>
        <?= select_options(opsi('academic_years','nama_tahun_ajaran',''), $_GET['academic_year_id'] ?? '', '') ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Mitra</label>
      <select name="partner_id" class="form-select">
        <option value="">Semua</option>
        <?= select_options(opsi('partners','nama_mitra',''), $_GET['partner_id'] ?? '', '') ?>
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
      <a href="tax_report" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a>
    </div>
  </div>
</form>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="stat-card d-flex gap-3 align-items-center">
      <span class="stat-icon navy"><i class="fa-solid fa-hand-holding-dollar"></i></span>
      <div><div class="label">Total Bagi Hasil Kotor</div><div class="value"><?= rupiah($totalBagiHasil) ?></div></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card d-flex gap-3 align-items-center">
      <span class="stat-icon amber"><i class="fa-solid fa-percent"></i></span>
      <div><div class="label">Total Pajak</div><div class="value text-danger"><?= rupiah($totalPajak) ?></div></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card d-flex gap-3 align-items-center">
      <span class="stat-icon green"><i class="fa-solid fa-circle-check"></i></span>
      <div><div class="label">Total Setoran Bersih</div><div class="value"><?= rupiah($totalSetoran) ?></div></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">Detail Pajak per Mitra<?php if ($filterAcademicYearId) {
    $ay = db()->prepare('SELECT nama_tahun_ajaran FROM academic_years WHERE id = ?');
    $ay->execute([$filterAcademicYearId]);
    $ayName = $ay->fetchColumn();
    echo ' - ' . e($ayName);
  } ?></div>
  <div class="card-body p-0">
    <div class="table-responsive-wrapper mobile-table-wrapper">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Mitra</th>
            <th class="text-end">Bagi Hasil Kotor</th>
            <th class="text-end">Metode Pajak</th>
            <th class="text-end">Pajak</th>
            <th class="text-end">Setoran Bersih</th>
            <th class="text-end">%</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$data): ?>
            <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-handshake-slash"></i>Belum ada data.</div></td></tr>
          <?php endif; ?>
          <?php foreach ($data as $r): ?>
            <tr>
              <td><strong><?= e($r['nama_mitra']) ?></strong></td>
              <td class="text-end"><?= rupiah($r['bagi_hasil']) ?></td>
              <td class="text-end">
                <?php if (!$r['tax_enabled']): ?>
                  <span class="badge bg-secondary-soft">Tidak Ada</span>
                <?php elseif ($r['tax_method'] === 'percentage'): ?>
                  <span class="badge bg-info-soft"><?= persen_fmt($r['tax_percentage']) ?>%</span>
                <?php elseif ($r['tax_method'] === 'manual'): ?>
                  <span class="badge bg-warning-soft">Manual</span>
                <?php endif; ?>
              </td>
              <td class="text-end <?= $r['pajak'] > 0 ? 'text-danger' : '' ?>">
                <strong><?= rupiah($r['pajak']) ?></strong>
                <?php if ($r['tax_enabled'] && $r['tax_method'] === 'manual'): ?>
                  <br><small class="text-muted">(Rp<?= number_format($r['tax_manual_nominal'], 0, ',', '.') ?>)</small>
                <?php endif; ?>
              </td>
              <td class="text-end"><strong><?= rupiah($r['setoran']) ?></strong></td>
              <td class="text-end"><?= $r['bagi_hasil'] > 0 ? persen_fmt($r['pajak'] / $r['bagi_hasil'] * 100) . '%' : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th>Total</th>
            <th class="text-end"><?= rupiah($totalBagiHasil) ?></th>
            <th></th>
            <th class="text-end text-danger"><strong><?= rupiah($totalPajak) ?></strong></th>
            <th class="text-end"><strong><?= rupiah($totalSetoran) ?></strong></th>
            <th class="text-end"><?= $totalBagiHasil > 0 ? persen_fmt($totalPajak / $totalBagiHasil * 100) . '%' : '-' ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
