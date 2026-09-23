<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

// Helper functions
function get_tarif_fee() {
    $config = db()->query("SELECT tarif_default FROM config_fee_settings WHERE jenis_fee = 'fee_wali_kelas_sd' AND status = 'aktif' LIMIT 1")->fetch();
    return $config ? (float)$config['tarif_default'] : 5000;
}

function calculate_week_dates($academicYearId, $mingguKe) {
    $ay = db()->prepare("SELECT tanggal_mulai FROM academic_years WHERE id = ?");
    $ay->execute([$academicYearId]);
    $ay = $ay->fetch();
    
    if (!$ay) return null;
    
    $startDate = new DateTime($ay['tanggal_mulai']);
    $startDate->modify('+' . (($mingguKe - 1) * 7) . ' days');
    
    $endDate = clone $startDate;
    $endDate->modify('+6 days');
    
    return [
        'minggu_ke' => $mingguKe,
        'tanggal_mulai' => $startDate->format('Y-m-d'),
        'tanggal_selesai' => $endDate->format('Y-m-d'),
        'display' => $startDate->format('d') . '–' . $endDate->format('d M Y')
    ];
}

function get_partner_color($partnerId) {
    $colors = [
        '#1e6fd9', '#16a34a', '#d97706', '#dc2626', '#0891b2', 
        '#7c3aed', '#db2777', '#ea580c', '#65a30d', '#0891b2'
    ];
    return $colors[$partnerId % count($colors)];
}

// Get filter parameters
$filterAcademicYear = $_GET['academic_year_id'] ?? '';
$filterMinggu = $_GET['minggu_ke'] ?? 1;

// Default to active academic year, fallback ke tahun ajaran terbaru (paling baru)
if ($filterAcademicYear === '') {
    $activeAY = db()->query("SELECT id FROM academic_years WHERE status='aktif' ORDER BY id DESC LIMIT 1")->fetch();
    if ($activeAY) {
        $filterAcademicYear = $activeAY['id'];
    } else {
        $latestAY = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
        if ($latestAY) $filterAcademicYear = $latestAY['id'];
    }
}

// Calculate current week dates
$currentWeekDates = calculate_week_dates($filterAcademicYear, $filterMinggu);

// Get data for specific week
$jadwal = db()->prepare("
    SELECT j.*, p.nama_mitra, k.nama_kelas, ay.nama_tahun_ajaran
    FROM jadwal_pemakaian_ruang j
    LEFT JOIN partners p ON p.id = j.partner_id
    LEFT JOIN kelas_detail k ON k.id = j.kelas_detail_id
    LEFT JOIN academic_years ay ON ay.id = j.academic_year_id
    WHERE j.academic_year_id = ? AND j.minggu_ke = ?
    ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), k.nama_kelas, j.jam_mulai
");
$jadwal->execute([(int)$filterAcademicYear, (int)$filterMinggu]);
$jadwal = $jadwal->fetchAll();

// Organize data for grid
$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$kelasList = ['1A', '1B', '1C', '2A', '2B', '2C', '2D', '3A', '3B', '3C', '3D', '4A', '4B', '4C', '5A', '5B', '5C', '6A', '6B', '6C'];

$grid = [];
foreach ($kelasList as $kelas) {
    foreach ($hariList as $hari) {
        $grid[$kelas][$hari] = [];
    }
}

foreach ($jadwal as $j) {
    if (isset($grid[$j['nama_kelas']][$j['hari']])) {
        $grid[$j['nama_kelas']][$j['hari']][] = $j;
    }
}

// Get dropdown data
$academicYears = db()->query("SELECT * FROM academic_years ORDER BY status DESC, nama_tahun_ajaran")->fetchAll();
$tarifFee = get_tarif_fee();

// Get available weeks
$availableWeeks = [];
if ($filterAcademicYear) {
    for ($i = 1; $i <= 52; $i++) {
        $weekDates = calculate_week_dates($filterAcademicYear, $i);
        if ($weekDates) {
            $availableWeeks[] = $weekDates;
        }
    }
}

$PAGE_TITLE = 'Tampilan Jadwal Grid';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Jadwal Pemakaian Ruang', 'url' => base_url('admin/jadwal-pemakaian-ruang.php')], ['label' => 'Grid View']];
include __DIR__ . '/../includes/header.php';
?>

<style>
.schedule-grid {
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
}

.schedule-grid th {
    background: #1e6fd9;
    color: white;
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    border: 1px solid #ddd;
}

.schedule-grid td {
    border: 1px solid #ddd;
    padding: 4px;
    vertical-align: top;
    min-width: 120px;
    max-width: 150px;
}

.schedule-grid .kelas-cell {
    background: #f8f9fa;
    font-weight: 700;
    text-align: center;
    padding: 8px;
    border: 1px solid #ddd;
}

.schedule-item {
    background: white;
    border-radius: 4px;
    padding: 6px;
    margin-bottom: 4px;
    border-left: 4px solid;
    font-size: 11px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.schedule-item:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    cursor: pointer;
}

.schedule-item .bimbel-name {
    font-weight: 700;
    margin-bottom: 2px;
    color: #0f2f5f;
}

.schedule-item .jam {
    color: #666;
    font-size: 10px;
}

.schedule-item .fee {
    color: #16a34a;
    font-weight: 600;
    font-size: 10px;
    margin-top: 2px;
}

.empty-cell {
    background: #fafafa;
    height: 60px;
}
</style>

<!-- Week Navigation -->
<div class="card mb-4">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-3">
        <label class="form-label">Tahun Ajaran</label>
        <select name="academic_year_id" class="form-select" onchange="location.href='?academic_year_id='+this.value+'&minggu_ke=1'">
          <?php foreach ($academicYears as $ay): ?>
            <option value="<?= $ay['id'] ?>" <?= $filterAcademicYear == $ay['id'] ? 'selected' : '' ?>>
              <?= e($ay['nama_tahun_ajaran']) ?> (<?= $ay['status'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Navigasi Minggu</label>
        <div class="d-flex align-items-center gap-2">
          <?php if ($filterMinggu > 1): ?>
            <a href="?academic_year_id=<?= $filterAcademicYear ?>&minggu_ke=<?= $filterMinggu - 1 ?>" class="btn btn-outline-primary">
              <i class="fa-solid fa-chevron-left"></i> Minggu <?= $filterMinggu - 1 ?>
            </a>
          <?php endif; ?>
          
          <select class="form-select" onchange="location.href='?academic_year_id=<?= $filterAcademicYear ?>&minggu_ke='+this.value">
            <?php foreach ($availableWeeks as $week): ?>
              <option value="<?= $week['minggu_ke'] ?>" <?= $filterMinggu == $week['minggu_ke'] ? 'selected' : '' ?>>
                Minggu <?= $week['minggu_ke'] ?>: <?= $week['display'] ?>
              </option>
            <?php endforeach; ?>
          </select>
          
          <?php if ($filterMinggu < 52): ?>
            <a href="?academic_year_id=<?= $filterAcademicYear ?>&minggu_ke=<?= $filterMinggu + 1 ?>" class="btn btn-outline-primary">
              Minggu <?= $filterMinggu + 1 ?> <i class="fa-solid fa-chevron-right"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">View</label>
        <a href="jadwal-pemakaian-ruang?academic_year_id=<?= $filterAcademicYear ?>&minggu_ke=<?= $filterMinggu ?>" class="btn btn-light w-100">
          <i class="fa-solid fa-list"></i> List View
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Summary -->
<div class="row g-4 mb-4">
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Minggu Aktif</div>
        <div class="h4 mb-0">Minggu <?= $filterMinggu ?></div>
        <div class="text-muted small"><?= $currentWeekDates['display'] ?? '-' ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Total Jadwal</div>
        <div class="h3 mb-0"><?= count($jadwal) ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Total Fee</div>
        <div class="h3 mb-0">Rp<?= number_format(count($jadwal) * $tarifFee, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Tarif per Pemakaian</div>
        <div class="h3 mb-0">Rp<?= number_format($tarifFee, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Grid Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive-wrapper mobile-table-wrapper">
      <table class="schedule-grid">
        <thead>
          <tr>
            <th>Kelas / Hari</th>
            <?php foreach ($hariList as $hari): ?>
              <th><?= $hari ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($kelasList as $kelas): ?>
          <tr>
            <td class="kelas-cell"><?= $kelas ?></td>
            <?php foreach ($hariList as $hari): ?>
              <td>
                <?php if (!empty($grid[$kelas][$hari])): ?>
                  <?php foreach ($grid[$kelas][$hari] as $item): ?>
                    <div class="schedule-item" style="border-left-color: <?= get_partner_color($item['partner_id']) ?>">
                      <div class="bimbel-name"><?= e($item['nama_mitra']) ?></div>
                      <div class="jam"><?= e($item['jam_mulai']) ?> - <?= e($item['jam_selesai']) ?></div>
                      <div class="fee">Rp<?= number_format($item['tarif_fee'], 0, ',', '.') ?></div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="empty-cell"></div>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
