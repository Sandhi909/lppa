<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

// Helper functions
function get_tarif_fee() {
    $config = db()->query("SELECT tarif_default FROM config_fee_settings WHERE jenis_fee = 'fee_wali_kelas_sd' AND status = 'aktif' LIMIT 1")->fetch();
    return $config ? (float)$config['tarif_default'] : 5000;
}

// Set default filter tahun ajaran ke paling terbaru jika belum dipilih
if (!isset($_GET['academic_year_id'])) {
    $latestAy = db()->query('SELECT id FROM academic_years ORDER BY id DESC LIMIT 1')->fetchColumn();
    if ($latestAy) $_GET['academic_year_id'] = $latestAy;
}
// Get filter parameters
$filterAcademicYear = $_GET['academic_year_id'] ?? '';
$filterPartner = $_GET['partner_id'] ?? '';
$filterKelas = $_GET['kelas_detail_id'] ?? '';
$filterBulan = $_GET['bulan'] ?? '';
$filterMinggu = $_GET['minggu_ke'] ?? '';

// Build base query
$where = ['1=1'];
$params = [];

if ($filterAcademicYear !== '') {
    $where[] = 'j.academic_year_id = ?';
    $params[] = (int)$filterAcademicYear;
}

if ($filterMinggu !== '') {
    $where[] = 'j.minggu_ke = ?';
    $params[] = (int)$filterMinggu;
}

if ($filterPartner !== '') {
    $where[] = 'j.partner_id = ?';
    $params[] = (int)$filterPartner;
}

if ($filterKelas !== '') {
    $where[] = 'j.kelas_detail_id = ?';
    $params[] = (int)$filterKelas;
}

if ($filterBulan !== '') {
    $where[] = 'MONTH(j.tanggal_mulai_minggu) = ?';
    $params[] = (int)$filterBulan;
}

$whereClause = implode(' AND ', $where);

// Get total pemakaian and fee
$totalPemakaian = db()->prepare("SELECT COUNT(*) as total FROM jadwal_pemakaian_ruang j WHERE $whereClause");
$totalPemakaian->execute($params);
$totalPemakaian = $totalPemakaian->fetch()['total'];

$totalFee = db()->prepare("SELECT COUNT(*) * j.tarif_fee as total FROM jadwal_pemakaian_ruang j WHERE $whereClause");
$totalFee->execute($params);
$totalFee = $totalFee->fetch()['total'];

// Rekap per bimbingan belajar
$rekapPartner = db()->prepare("
    SELECT p.nama_mitra, COUNT(*) as total_pemakaian, COUNT(*) * j.tarif_fee as total_fee
    FROM jadwal_pemakaian_ruang j
    LEFT JOIN partners p ON p.id = j.partner_id
    WHERE $whereClause
    GROUP BY p.id, p.nama_mitra
    ORDER BY total_pemakaian DESC
");
$rekapPartner->execute($params);
$rekapPartner = $rekapPartner->fetchAll();

// Rekap per kelas
$rekapKelas = db()->prepare("
    SELECT k.nama_kelas, COUNT(*) as total_pemakaian, COUNT(*) * j.tarif_fee as total_fee
    FROM jadwal_pemakaian_ruang j
    LEFT JOIN kelas_detail k ON k.id = j.kelas_detail_id
    WHERE $whereClause
    GROUP BY k.id, k.nama_kelas
    ORDER BY k.nama_kelas
");
$rekapKelas->execute($params);
$rekapKelas = $rekapKelas->fetchAll();

// Rekap per hari
$rekapHari = db()->prepare("
    SELECT j.hari, COUNT(*) as total_pemakaian, COUNT(*) * j.tarif_fee as total_fee
    FROM jadwal_pemakaian_ruang j
    WHERE $whereClause
    GROUP BY j.hari
    ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')
");
$rekapHari->execute($params);
$rekapHari = $rekapHari->fetchAll();

// Rekap per bulan
$rekapBulan = db()->prepare("
    SELECT MONTH(j.tanggal_mulai_minggu) as bulan, COUNT(*) as total_pemakaian, COUNT(*) * j.tarif_fee as total_fee
    FROM jadwal_pemakaian_ruang j
    WHERE $whereClause
    GROUP BY MONTH(j.tanggal_mulai_minggu)
    ORDER BY bulan
");
$rekapBulan->execute($params);
$rekapBulan = $rekapBulan->fetchAll();

// Rekap per minggu
$rekapMinggu = db()->prepare("
    SELECT j.minggu_ke, j.tanggal_mulai_minggu, j.tanggal_selesai_minggu, COUNT(*) as total_pemakaian, COUNT(*) * j.tarif_fee as total_fee
    FROM jadwal_pemakaian_ruang j
    WHERE $whereClause
    GROUP BY j.minggu_ke, j.tanggal_mulai_minggu, j.tanggal_selesai_minggu
    ORDER BY j.minggu_ke
");
$rekapMinggu->execute($params);
$rekapMinggu = $rekapMinggu->fetchAll();

// Rekap per wali kelas
$rekapWaliKelas = db()->prepare("
    SELECT u.nama as nama_wali_kelas, k.nama_kelas, COUNT(*) as total_pemakaian, COUNT(*) * j.tarif_fee as total_fee
    FROM jadwal_pemakaian_ruang j
    LEFT JOIN kelas_detail k ON k.id = j.kelas_detail_id
    LEFT JOIN students s ON s.kelas = k.nama_kelas AND s.unit_id = 3
    LEFT JOIN users u ON u.id = s.wali_kelas_id
    WHERE $whereClause
    GROUP BY u.id, u.nama, k.nama_kelas
    ORDER BY u.nama, k.nama_kelas
");
$rekapWaliKelas->execute($params);
$rekapWaliKelas = $rekapWaliKelas->fetchAll();

// Get dropdown data
$academicYears = db()->query("SELECT * FROM academic_years ORDER BY status DESC, nama_tahun_ajaran")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();
$kelasSD = db()->query("SELECT * FROM kelas_detail WHERE unit_id = 3 AND status = 'aktif' ORDER BY nama_kelas")->fetchAll();
$tarifFee = get_tarif_fee();

$PAGE_TITLE = 'Rekap Pemakaian Ruang & Fee Wali Kelas';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Jadwal Pemakaian Ruang', 'url' => base_url('admin/jadwal-pemakaian-ruang.php')], ['label' => 'Rekap']];
include __DIR__ . '/../includes/header.php';
?>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
  <div class="col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="text-muted text-uppercase small">Total Pemakaian</div>
            <div class="h3 mb-0"><?= $totalPemakaian ?></div>
          </div>
          <div class="bg-primary bg-opacity-10 p-3 rounded">
            <i class="fa-solid fa-calendar-check fa-2x text-primary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="text-muted text-uppercase small">Total Fee Wali Kelas</div>
            <div class="h3 mb-0">Rp<?= number_format($totalFee, 0, ',', '.') ?></div>
          </div>
          <div class="bg-success bg-opacity-10 p-3 rounded">
            <i class="fa-solid fa-money-bill-wave fa-2x text-success"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="text-muted text-uppercase small">Jumlah Bimbel</div>
            <div class="h3 mb-0"><?= count($rekapPartner) ?></div>
          </div>
          <div class="bg-info bg-opacity-10 p-3 rounded">
            <i class="fa-solid fa-building fa-2x text-info"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="text-muted text-uppercase small">Kelas Terpakai</div>
            <div class="h3 mb-0"><?= count($rekapKelas) ?></div>
          </div>
          <div class="bg-warning bg-opacity-10 p-3 rounded">
            <i class="fa-solid fa-chalkboard fa-2x text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter -->
<form class="card mb-4" method="get">
  <div class="card-body row g-3">
    <div class="col-md-3">
      <label class="form-label">Tahun Ajaran</label>
      <select name="academic_year_id" class="form-select">
        <option value="">Semua</option>
        <?php foreach ($academicYears as $ay): ?>
          <option value="<?= $ay['id'] ?>" <?= $filterAcademicYear == $ay['id'] ? 'selected' : '' ?>><?= e($ay['nama_tahun_ajaran']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Bimbingan Belajar</label>
      <select name="partner_id" class="form-select">
        <option value="">Semua</option>
        <?php foreach ($partners as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $filterPartner == $p['id'] ? 'selected' : '' ?>><?= e($p['nama_mitra']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Kelas</label>
      <select name="kelas_detail_id" class="form-select">
        <option value="">Semua</option>
        <?php foreach ($kelasSD as $k): ?>
          <option value="<?= $k['id'] ?>" <?= $filterKelas == $k['id'] ? 'selected' : '' ?>><?= e($k['nama_kelas']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Bulan</label>
      <select name="bulan" class="form-select">
        <option value="">Semua</option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?= $i ?>" <?= $filterBulan == $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Minggu</label>
      <select name="minggu_ke" class="form-select">
        <option value="">Semua</option>
        <?php for ($i = 1; $i <= 52; $i++): ?>
          <option value="<?= $i ?>" <?= $filterMinggu == $i ? 'selected' : '' ?>>Minggu <?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2 align-self-end">
      <button class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Filter</button>
      <a href="rekap-jadwal-pemakaian" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a>
    </div>
  </div>
</form>

<!-- Rekap Tables -->
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Rekap per Bimbingan Belajar</div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead><tr><th>Bimbingan Belajar</th><th class="text-end">Pemakaian</th><th class="text-end">Fee</th></tr></thead>
          <tbody>
            <?php foreach ($rekapPartner as $r): ?>
            <tr>
              <td><?= e($r['nama_mitra']) ?></td>
              <td class="text-end"><?= $r['total_pemakaian'] ?></td>
              <td class="text-end">Rp<?= number_format($r['total_fee'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Rekap per Kelas</div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead><tr><th>Kelas</th><th class="text-end">Pemakaian</th><th class="text-end">Fee</th></tr></thead>
          <tbody>
            <?php foreach ($rekapKelas as $r): ?>
            <tr>
              <td><?= e($r['nama_kelas']) ?></td>
              <td class="text-end"><?= $r['total_pemakaian'] ?></td>
              <td class="text-end">Rp<?= number_format($r['total_fee'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Rekap per Hari</div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead><tr><th>Hari</th><th class="text-end">Pemakaian</th><th class="text-end">Fee</th></tr></thead>
          <tbody>
            <?php foreach ($rekapHari as $r): ?>
            <tr>
              <td><?= e($r['hari']) ?></td>
              <td class="text-end"><?= $r['total_pemakaian'] ?></td>
              <td class="text-end">Rp<?= number_format($r['total_fee'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Rekap per Bulan</div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead><tr><th>Bulan</th><th class="text-end">Pemakaian</th><th class="text-end">Fee</th></tr></thead>
          <tbody>
            <?php foreach ($rekapBulan as $r): ?>
            <tr>
              <td><?= date('F', mktime(0, 0, 0, $r['bulan'], 1)) ?></td>
              <td class="text-end"><?= $r['total_pemakaian'] ?></td>
              <td class="text-end">Rp<?= number_format($r['total_fee'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($rekapMinggu)): ?>
<div class="card mt-4">
  <div class="card-header">Rekap per Minggu</div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Minggu</th><th>Periode</th><th class="text-end">Pemakaian</th><th class="text-end">Fee</th></tr></thead>
      <tbody>
        <?php foreach ($rekapMinggu as $r): ?>
        <tr>
          <td>Minggu <?= $r['minggu_ke'] ?></td>
          <td><?= date('d M', strtotime($r['tanggal_mulai_minggu'])) ?> - <?= date('d M Y', strtotime($r['tanggal_selesai_minggu'])) ?></td>
          <td class="text-end"><?= $r['total_pemakaian'] ?></td>
          <td class="text-end">Rp<?= number_format($r['total_fee'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($rekapWaliKelas)): ?>
<div class="card mt-4">
  <div class="card-header">Rekap per Wali Kelas</div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Nama Wali Kelas</th><th>Kelas</th><th class="text-end">Pemakaian</th><th class="text-end">Fee</th></tr></thead>
      <tbody>
        <?php foreach ($rekapWaliKelas as $r): ?>
        <tr>
          <td><?= e($r['nama_wali_kelas']) ?></td>
          <td><?= e($r['nama_kelas']) ?></td>
          <td class="text-end"><?= $r['total_pemakaian'] ?></td>
          <td class="text-end">Rp<?= number_format($r['total_fee'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
