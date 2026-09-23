<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

// Helper functions for jadwal
function get_tarif_fee() {
    $config = db()->query("SELECT tarif_default FROM config_fee_settings WHERE jenis_fee = 'fee_wali_kelas_sd' AND status = 'aktif' LIMIT 1")->fetch();
    return $config ? (float)$config['tarif_default'] : 5000;
}

function calculate_week_dates($academicYearId, $mingguKe) {
    // Get academic year start date
    $ay = db()->prepare("SELECT tanggal_mulai FROM academic_years WHERE id = ?");
    $ay->execute([$academicYearId]);
    $ay = $ay->fetch();
    
    if (!$ay) return null;
    
    // Calculate week dates (starting from academic year start)
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

function check_bentrok($academicYearId, $kelasDetailId, $hari, $jamMulai, $jamSelesai, $mingguKe, $excludeId = null) {
    $where = "academic_year_id = ? AND kelas_detail_id = ? AND hari = ? AND minggu_ke = ?";
    $params = [$academicYearId, $kelasDetailId, $hari, $mingguKe];
    
    if ($excludeId) {
        $where .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    // Check for overlapping schedules
    $sql = "SELECT * FROM jadwal_pemakaian_ruang 
            WHERE $where AND (
                (jam_mulai < ? AND jam_selesai > ?) OR
                (jam_mulai < ? AND jam_selesai > ?) OR
                (jam_mulai >= ? AND jam_selesai <= ?)
            )";
    
    $params[] = $jamSelesai;
    $params[] = $jamMulai;
    $params[] = $jamSelesai;
    $params[] = $jamMulai;
    $params[] = $jamMulai;
    $params[] = $jamSelesai;
    
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function calculate_duration_hours($jamMulai, $jamSelesai) {
    $start = strtotime($jamMulai);
    $end = strtotime($jamSelesai);
    return ($end - $start) / 3600; // Convert seconds to hours
}

// Get current week parameters
$currentAcademicYear = $_GET['academic_year_id'] ?? '';
$currentMinggu = $_GET['minggu_ke'] ?? 1;

// Default to active academic year, fallback ke tahun ajaran terbaru (paling baru)
if ($currentAcademicYear === '') {
    $activeAY = db()->query("SELECT id FROM academic_years WHERE status='aktif' ORDER BY id DESC LIMIT 1")->fetch();
    if ($activeAY) {
        $currentAcademicYear = $activeAY['id'];
    } else {
        $latestAY = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
        if ($latestAY) $currentAcademicYear = $latestAY['id'];
    }
}

// Calculate current week dates
$currentWeekDates = calculate_week_dates($currentAcademicYear, $currentMinggu);

// POST handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['aksi'] ?? '';
    
    if ($act === 'simpan') {
        $id = (int)($_POST['id'] ?? 0);
        $academicYearId = (int)($_POST['academic_year_id'] ?? 0);
        $mingguKe = (int)($_POST['minggu_ke'] ?? 1);
        $partnerId = (int)($_POST['partner_id'] ?? 0);
        $kelasDetailId = (int)($_POST['kelas_detail_id'] ?? 0);
        $hari = $_POST['hari'] ?? '';
        $jamMulai = $_POST['jam_mulai'] ?? '';
        $jamSelesai = $_POST['jam_selesai'] ?? '';
        
        // Calculate week dates
        $weekDates = calculate_week_dates($academicYearId, $mingguKe);
        if (!$weekDates) { flash('danger', 'Tahun ajaran tidak valid.'); redirect('jadwal-pemakaian-ruang.php'); }
        
        // Validasi
        if ($academicYearId === 0) { flash('danger', 'Tahun ajaran wajib dipilih.'); redirect('jadwal-pemakaian-ruang.php'); }
        if ($mingguKe < 1) { flash('danger', 'Minggu tidak valid.'); redirect('jadwal-pemakaian-ruang.php'); }
        if ($partnerId === 0) { flash('danger', 'Bimbingan belajar wajib dipilih.'); redirect('jadwal-pemakaian-ruang.php'); }
        if ($kelasDetailId === 0) { flash('danger', 'Kelas wajib dipilih.'); redirect('jadwal-pemakaian-ruang.php'); }
        if ($hari === '') { flash('danger', 'Hari wajib dipilih.'); redirect('jadwal-pemakaian-ruang.php'); }
        if ($jamMulai === '' || $jamSelesai === '') { flash('danger', 'Jam wajib diisi.'); redirect('jadwal-pemakaian-ruang.php'); }
        
        // Validasi durasi maksimal 3 jam
        $duration = calculate_duration_hours($jamMulai, $jamSelesai);
        if ($duration > 3) {
            flash('danger', 'Durasi pemakaian maksimal 3 jam pelajaran.'); redirect('jadwal-pemakaian-ruang.php');
        }
        
        if ($duration <= 0) {
            flash('danger', 'Jam selesai harus lebih besar dari jam mulai.'); redirect('jadwal-pemakaian-ruang.php');
        }
        
        // Validasi bentrok (only within same week)
        $bentrok = check_bentrok($academicYearId, $kelasDetailId, $hari, $jamMulai, $jamSelesai, $mingguKe, $id);
        if (!empty($bentrok)) {
            flash('danger', 'Jadwal bentrok dengan pemakaian ruang yang sudah terdaftar.'); redirect('jadwal-pemakaian-ruang.php');
        }
        
        $tarifFee = get_tarif_fee();
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($id) {
            // Update
            $st = db()->prepare("UPDATE jadwal_pemakaian_ruang SET 
                academic_year_id = ?, minggu_ke = ?, tanggal_mulai_minggu = ?, tanggal_selesai_minggu = ?,
                partner_id = ?, kelas_detail_id = ?, hari = ?, 
                jam_mulai = ?, jam_selesai = ?, tarif_fee = ?, updated_by = ? 
                WHERE id = ?");
            $st->execute([$academicYearId, $mingguKe, $weekDates['tanggal_mulai'], $weekDates['tanggal_selesai'], 
                          $partnerId, $kelasDetailId, $hari, $jamMulai, $jamSelesai, $tarifFee, $userId, $id]);
            audit('Mengubah jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', $id);
            flash('success', 'Jadwal diperbarui.');
        } else {
            // Insert
            $st = db()->prepare("INSERT INTO jadwal_pemakaian_ruang 
                (academic_year_id, minggu_ke, tanggal_mulai_minggu, tanggal_selesai_minggu,
                 partner_id, kelas_detail_id, hari, jam_mulai, jam_selesai, tarif_fee, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $st->execute([$academicYearId, $mingguKe, $weekDates['tanggal_mulai'], $weekDates['tanggal_selesai'], 
                          $partnerId, $kelasDetailId, $hari, $jamMulai, $jamSelesai, $tarifFee, $userId]);
            $newId = (int)db()->lastInsertId();
            audit('Menambah jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', $newId);
            flash('success', 'Jadwal berhasil ditambahkan.');
        }
        redirect('jadwal-pemakaian-ruang.php?academic_year_id=' . $academicYearId . '&minggu_ke=' . $mingguKe);
    }
    
    if ($act === 'copy_minggu') {
        $sourceMinggu = (int)($_POST['source_minggu'] ?? 0);
        $targetMinggu = (int)($_POST['target_minggu'] ?? 0);
        $academicYearId = (int)($_POST['academic_year_id'] ?? 0);
        
        if ($sourceMinggu === 0 || $targetMinggu === 0 || $academicYearId === 0) {
            flash('danger', 'Parameter tidak valid.'); redirect('jadwal-pemakaian-ruang.php');
        }
        
        // Calculate target week dates
        $targetWeekDates = calculate_week_dates($academicYearId, $targetMinggu);
        if (!$targetWeekDates) { flash('danger', 'Tahun ajaran tidak valid.'); redirect('jadwal-pemakaian-ruang.php'); }
        
        // Get source week schedules
        $sourceSchedules = db()->prepare("SELECT * FROM jadwal_pemakaian_ruang 
                                          WHERE academic_year_id = ? AND minggu_ke = ?");
        $sourceSchedules->execute([$academicYearId, $sourceMinggu]);
        $sourceSchedules = $sourceSchedules->fetchAll();
        
        if (empty($sourceSchedules)) {
            flash('warning', 'Tidak ada jadwal pada minggu sumber.'); redirect('jadwal-pemakaian-ruang.php');
        }
        
        // Copy schedules
        $userId = $_SESSION['user_id'] ?? null;
        $tarifFee = get_tarif_fee();
        
        foreach ($sourceSchedules as $schedule) {
            $st = db()->prepare("INSERT INTO jadwal_pemakaian_ruang 
                (academic_year_id, minggu_ke, tanggal_mulai_minggu, tanggal_selesai_minggu,
                 partner_id, kelas_detail_id, hari, jam_mulai, jam_selesai, tarif_fee, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $st->execute([$academicYearId, $targetMinggu, $targetWeekDates['tanggal_mulai'], $targetWeekDates['tanggal_selesai'], 
                          $schedule['partner_id'], $schedule['kelas_detail_id'], $schedule['hari'], 
                          $schedule['jam_mulai'], $schedule['jam_selesai'], $tarifFee, $userId]);
        }
        
        audit('Menyalin jadwal minggu', 'jadwal_pemakaian_ruang', null, "Dari minggu $sourceMinggu ke minggu $targetMinggu");
        flash('success', 'Jadwal berhasil disalin dari Minggu ' . $sourceMinggu . ' ke Minggu ' . $targetMinggu);
        redirect('jadwal-pemakaian-ruang.php?academic_year_id=' . $academicYearId . '&minggu_ke=' . $targetMinggu);
    }
}

if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)$_GET['id'];
    db()->prepare('DELETE FROM jadwal_pemakaian_ruang WHERE id=?')->execute([$id]);
    audit('Menghapus jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', $id);
    flash('success', 'Jadwal dihapus.');
    redirect('jadwal-pemakaian-ruang.php?academic_year_id=' . $currentAcademicYear . '&minggu_ke=' . $currentMinggu);
}

$row = null;
if (($_GET['aksi'] ?? '') === 'edit') {
    $st = db()->prepare('SELECT * FROM jadwal_pemakaian_ruang WHERE id=?');
    $st->execute([(int)$_GET['id']]);
    $row = $st->fetch();
}

// Get dropdown data
$academicYears = db()->query("SELECT * FROM academic_years ORDER BY status DESC, nama_tahun_ajaran")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();
$kelasSD = db()->query("SELECT * FROM kelas_detail WHERE unit_id = 3 AND status = 'aktif' ORDER BY nama_kelas")->fetchAll();
$tarifFee = get_tarif_fee();

// Get available weeks for current academic year
$availableWeeks = [];
if ($currentAcademicYear) {
    for ($i = 1; $i <= 52; $i++) {
        $weekDates = calculate_week_dates($currentAcademicYear, $i);
        if ($weekDates) {
            $availableWeeks[] = $weekDates;
        }
    }
}

// Build query with week filter
$where = ['1=1'];
$params = [];

if ($currentAcademicYear !== '') {
    $where[] = 'j.academic_year_id = ?';
    $params[] = (int)$currentAcademicYear;
}

if ($currentMinggu !== '') {
    $where[] = 'j.minggu_ke = ?';
    $params[] = (int)$currentMinggu;
}

$sql = "SELECT j.*, ay.nama_tahun_ajaran, p.nama_mitra, k.nama_kelas, u.nama as created_by_name
        FROM jadwal_pemakaian_ruang j
        LEFT JOIN academic_years ay ON ay.id = j.academic_year_id
        LEFT JOIN partners p ON p.id = j.partner_id
        LEFT JOIN kelas_detail k ON k.id = j.kelas_detail_id
        LEFT JOIN users u ON u.id = j.created_by
        WHERE " . implode(' AND ', $where) . "
        ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), k.nama_kelas, j.jam_mulai";

$st = db()->prepare($sql);
$st->execute($params);
$list = $st->fetchAll();

$PAGE_TITLE = 'Manajemen Jadwal Pemakaian Ruang';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Jadwal Pemakaian Ruang']];
include __DIR__ . '/../includes/header.php';
?>

<!-- Week Navigation -->
<div class="card mb-3">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-3">
        <label class="form-label">Tahun Ajaran</label>
        <select name="academic_year_id" class="form-select" onchange="location.href='?academic_year_id='+this.value+'&minggu_ke=1'">
          <?php foreach ($academicYears as $ay): ?>
            <option value="<?= $ay['id'] ?>" <?= $currentAcademicYear == $ay['id'] ? 'selected' : '' ?>>
              <?= e($ay['nama_tahun_ajaran']) ?> (<?= $ay['status'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Navigasi Minggu</label>
        <div class="d-flex align-items-center gap-2">
          <?php if ($currentMinggu > 1): ?>
            <a href="?academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu - 1 ?>" class="btn btn-outline-primary">
              <i class="fa-solid fa-chevron-left"></i> Minggu <?= $currentMinggu - 1 ?>
            </a>
          <?php endif; ?>
          
          <select class="form-select" onchange="location.href='?academic_year_id=<?= $currentAcademicYear ?>&minggu_ke='+this.value">
            <?php foreach ($availableWeeks as $week): ?>
              <option value="<?= $week['minggu_ke'] ?>" <?= $currentMinggu == $week['minggu_ke'] ? 'selected' : '' ?>>
                Minggu <?= $week['minggu_ke'] ?>: <?= $week['display'] ?>
              </option>
            <?php endforeach; ?>
          </select>
          
          <?php if ($currentMinggu < 52): ?>
            <a href="?academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu + 1 ?>" class="btn btn-outline-primary">
              Minggu <?= $currentMinggu + 1 ?> <i class="fa-solid fa-chevron-right"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Fitur</label>
        <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#copyModal">
          <i class="fa-solid fa-copy me-1"></i>Salin Jadwal Minggu
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Week Summary -->
<div class="row g-4 mb-4">
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Minggu Aktif</div>
        <div class="h4 mb-0">Minggu <?= $currentMinggu ?></div>
        <div class="text-muted small"><?= $currentWeekDates['display'] ?? '-' ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Total Jadwal Minggu Ini</div>
        <div class="h4 mb-0"><?= count($list) ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Total Fee Minggu Ini</div>
        <div class="h4 mb-0">Rp<?= number_format(count($list) * $tarifFee, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Tarif per Pemakaian</div>
        <div class="h4 mb-0">Rp<?= number_format($tarifFee, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
</div>

<?php if (($_GET['aksi'] ?? '') === 'tambah' || $row): ?>
<form method="post" class="card mb-3">
  <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
  <div class="card-header"><?= $row ? 'Edit Jadwal' : 'Tambah Jadwal' ?></div>
  <div class="card-body row g-3">
    <div class="col-md-3"><label class="form-label">Tahun Ajaran *</label>
      <select name="academic_year_id" class="form-select" required>
        <option value="">Pilih Tahun Ajaran</option>
        <?php foreach ($academicYears as $ay): ?>
          <option value="<?= $ay['id'] ?>" <?= ($row['academic_year_id'] ?? $currentAcademicYear) == $ay['id'] ? 'selected' : '' ?>>
            <?= e($ay['nama_tahun_ajaran']) ?> (<?= $ay['status'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><label class="form-label">Minggu ke- *</label>
      <input type="number" name="minggu_ke" class="form-control" min="1" max="52" required value="<?= e($row['minggu_ke'] ?? $currentMinggu) ?>">
    </div>
    <div class="col-md-3"><label class="form-label">Bimbingan Belajar *</label>
      <select name="partner_id" class="form-select" required>
        <option value="">Pilih Bimbingan Belajar</option>
        <?php foreach ($partners as $p): ?>
          <option value="<?= $p['id'] ?>" <?= ($row['partner_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
            <?= e($p['nama_mitra']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><label class="form-label">Hari *</label>
      <select name="hari" class="form-select" required>
        <option value="">Pilih Hari</option>
        <option value="Senin" <?= ($row['hari'] ?? '') === 'Senin' ? 'selected' : '' ?>>Senin</option>
        <option value="Selasa" <?= ($row['hari'] ?? '') === 'Selasa' ? 'selected' : '' ?>>Selasa</option>
        <option value="Rabu" <?= ($row['hari'] ?? '') === 'Rabu' ? 'selected' : '' ?>>Rabu</option>
        <option value="Kamis" <?= ($row['hari'] ?? '') === 'Kamis' ? 'selected' : '' ?>>Kamis</option>
        <option value="Jumat" <?= ($row['hari'] ?? '') === 'Jumat' ? 'selected' : '' ?>>Jumat</option>
        <option value="Sabtu" <?= ($row['hari'] ?? '') === 'Sabtu' ? 'selected' : '' ?>>Sabtu</option>
      </select>
    </div>
    <div class="col-md-2"><label class="form-label">Kelas *</label>
      <select name="kelas_detail_id" class="form-select" required>
        <option value="">Pilih Kelas</option>
        <?php foreach ($kelasSD as $k): ?>
          <option value="<?= $k['id'] ?>" <?= ($row['kelas_detail_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
            <?= e($k['nama_kelas']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3"><label class="form-label">Jam Mulai *</label>
      <input type="time" name="jam_mulai" class="form-control" required value="<?= e($row['jam_mulai'] ?? '') ?>">
    </div>
    <div class="col-md-3"><label class="form-label">Jam Selesai *</label>
      <input type="time" name="jam_selesai" class="form-control" required value="<?= e($row['jam_selesai'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Informasi</label>
      <div class="form-text">
        <small>1 jadwal = 1 pemakaian = Rp<?= number_format($tarifFee, 0, ',', '.') ?></small><br>
        <small>Maksimal durasi: 3 jam</small>
      </div>
    </div>
  </div>
  <div class="card-footer bg-white text-end">
    <a href="jadwal-pemakaian-ruang?academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button>
  </div>
</form>
<?php endif; ?>

<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr>
  <th>ID</th><th>Tahun Ajaran</th><th>Minggu</th><th>Periode</th><th>Bimbingan Belajar</th><th>Hari</th><th>Kelas</th><th>Jam</th><th>Durasi</th><th>Fee</th><th>Aksi</th>
</tr></thead><tbody>
<?php if (!$list): ?><tr><td colspan="11"><div class="empty-state"><i class="fa-solid fa-calendar-xmark"></i>Belum ada jadwal untuk minggu ini.</div></td></tr><?php endif; ?>
<?php foreach ($list as $r): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= e($r['nama_tahun_ajaran']) ?></td>
  <td>Minggu <?= $r['minggu_ke'] ?></td>
  <td><?= date('d M', strtotime($r['tanggal_mulai_minggu'])) ?> - <?= date('d M Y', strtotime($r['tanggal_selesai_minggu'])) ?></td>
  <td><?= e($r['nama_mitra']) ?></td>
  <td><?= e($r['hari']) ?></td>
  <td><?= e($r['nama_kelas']) ?></td>
  <td><?= e($r['jam_mulai']) ?> - <?= e($r['jam_selesai']) ?></td>
  <td><?= calculate_duration_hours($r['jam_mulai'], $r['jam_selesai']) ?> jam</td>
  <td>Rp<?= number_format($r['tarif_fee'], 0, ',', '.') ?></td>
  <td>
    <a href="?aksi=edit&id=<?= $r['id'] ?>&academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></a>
    <a href="?aksi=hapus&id=<?= $r['id'] ?>&academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal ini?')"><i class="fa-solid fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<!-- Copy Modal -->
<div class="modal fade" id="copyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Salin Jadwal Minggu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <?= csrf_field() ?><input type="hidden" name="aksi" value="copy_minggu">
        <input type="hidden" name="academic_year_id" value="<?= $currentAcademicYear ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Dari Minggu</label>
            <select name="source_minggu" class="form-select" required>
              <?php foreach ($availableWeeks as $week): ?>
                <option value="<?= $week['minggu_ke'] ?>" <?= $currentMinggu == $week['minggu_ke'] ? 'selected' : '' ?>>
                  Minggu <?= $week['minggu_ke'] ?>: <?= $week['display'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Ke Minggu</label>
            <select name="target_minggu" class="form-select" required>
              <?php foreach ($availableWeeks as $week): ?>
                <option value="<?= $week['minggu_ke'] ?>" <?= ($currentMinggu + 1) == $week['minggu_ke'] ? 'selected' : '' ?>>
                  Minggu <?= $week['minggu_ke'] ?>: <?= $week['display'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-copy me-1"></i>Salin Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="text-end mt-3">
  <a href="jadwal-grid-view?academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu ?>" class="btn btn-info">
    <i class="fa-solid fa-table-cells me-1"></i>Grid View
  </a>
  <a href="?aksi=tambah&academic_year_id=<?= $currentAcademicYear ?>&minggu_ke=<?= $currentMinggu ?>" class="btn btn-primary">
    <i class="fa-solid fa-plus me-1"></i>Tambah Jadwal
  </a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
