<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/queries.php';

$partnerId = (int)($_GET['partner_id'] ?? 0);
$academicYearId = isset($_GET['academic_year_id']) ? (int)$_GET['academic_year_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $partnerId = (int)($_POST['partner_id'] ?? 0);
    $academicYearId = (int)($_POST['academic_year_id'] ?? 0);
    
    $tax_enabled = isset($_POST['tax_enabled']) ? 1 : 0;
    $tax_method = $_POST['tax_method'] ?? 'none';
    $tax_percentage = round((float)str_replace(',', '.', (string)($_POST['tax_percentage'] ?? 0)), 2);
    $tax_manual_nominal = round((float)str_replace(',', '.', (string)($_POST['tax_manual_nominal'] ?? 0)), 2);
    
    // Validation
    if ($tax_enabled) {
        if ($tax_method === 'percentage' && $tax_percentage <= 0) {
            flash('danger', 'Persentase pajak wajib diisi untuk metode persentase.'); redirect('tax_per_mitra.php');
        }
        if ($tax_method === 'manual' && $tax_manual_nominal <= 0) {
            flash('danger', 'Nominal pajak wajib diisi untuk metode manual.'); redirect('tax_per_mitra.php');
        }
    }
    
    // Check if config already exists for this partner and academic year
    $cek = db()->prepare('SELECT id FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
    $cek->execute([$partnerId, $academicYearId]);
    $exists = $cek->fetch();
    
    if ($exists) {
        // Update existing config
        db()->prepare('UPDATE partner_tax_config SET tax_enabled = ?, tax_percentage = ?, tax_method = ?, tax_manual_nominal = ? WHERE partner_id = ? AND academic_year_id = ?')
            ->execute([$tax_enabled, $tax_percentage, $tax_method, $tax_manual_nominal, $partnerId, $academicYearId]);
        audit('Memperbarui konfigurasi pajak mitra: partner_id=' . $partnerId . ', tahun_ajaran=' . $academicYearId, 'partner_tax_config', $exists['id'], null, $_POST);
        flash('success', 'Konfigurasi pajak diperbarui.');
    } else {
        // Insert new config
        db()->prepare('INSERT INTO partner_tax_config (partner_id, academic_year_id, tax_enabled, tax_percentage, tax_method, tax_manual_nominal) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$partnerId, $academicYearId, $tax_enabled, $tax_percentage, $tax_method, $tax_manual_nominal]);
        $newId = (int)db()->lastInsertId();
        audit('Membuat konfigurasi pajak baru: partner_id=' . $partnerId . ', tahun_ajaran=' . $academicYearId, 'partner_tax_config', $newId, null, $_POST);
        flash('success', 'Konfigurasi pajak baru dibuat.');
    }
    
    redirect('tax_per_mitra.php?partner_id=' . $partnerId . '&academic_year_id=' . $academicYearId);
}

// Get partner info
$partner = null;
if ($partnerId) {
    $st = db()->prepare('SELECT * FROM partners WHERE id = ?');
    $st->execute([$partnerId]);
    $partner = $st->fetch();
}

// Get available academic years
$academicYears = db()->query('SELECT id, nama_tahun_ajaran FROM academic_years ORDER BY nama_tahun_ajaran')->fetchAll();

// Get current tax config
$taxConfig = null;
if ($academicYearId) {
    $st = db()->prepare('SELECT * FROM partner_tax_config WHERE partner_id = ? AND academic_year_id = ?');
    $st->execute([$partnerId, $academicYearId]);
    $taxConfig = $st->fetch();
}

$PAGE_TITLE = 'Kelola Pajak per Mitra per Tahun Ajaran';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Kelola Pajak per Mitra']];
include __DIR__ . '/../includes/header.php';
?>
<div class="card mb-3">
  <div class="card-header">Pilih Mitra dan Tahun Ajaran</div>
  <div class="card-body row g-3">
    <div class="col-md-6">
      <label class="form-label">Mitra</label>
      <select class="form-select" id="partnerSelect" onchange="updateUrl()">
        <option value="">Pilih Mitra</option>
        <?php 
        $partners = db()->query('SELECT id, nama_mitra FROM partners ORDER BY nama_mitra')->fetchAll();
        foreach ($partners as $p) {
            echo '<option value="' . (int)$p['id'] . '" ' . ($partnerId === (int)$p['id'] ? 'selected' : '') . '>' . e($p['nama_mitra']) . '</option>';
        }
        ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Tahun Ajaran</label>
      <select class="form-select" id="academicYearSelect" onchange="updateUrl()">
        <option value="">Pilih Tahun Ajaran</option>
        <?php
        foreach ($academicYears as $ay) {
            echo '<option value="' . (int)$ay['id'] . '" ' . ($academicYearId === (int)$ay['id'] ? 'selected' : '') . '>' . e($ay['nama_tahun_ajaran']) . '</option>';
        }
        ?>
      </select>
    </div>
  </div>
</div>

<?php if ($partnerId && $partner && $academicYearId): ?>
<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title">Kelola Pajak: <?= e($partner['nama_mitra']) ?></h5>
    <p class="card-text">Tahun Ajaran: <strong><?= e($academicYears[array_search($academicYearId, array_column($academicYears, 'id'), true)]['nama_tahun_ajaran'] ?? '') ?></strong></p>
  </div>
</div>

<form method="post" class="card mb-3">
  <?= csrf_field() ?>
  <input type="hidden" name="partner_id" value="<?= (int)$partnerId ?>">
  <input type="hidden" name="academic_year_id" value="<?= (int)$academicYearId ?>">
  <div class="card-header">Konfigurasi Pajak</div>
  <div class="card-body row g-3">
    <div class="col-md-12">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="tax_enabled" name="tax_enabled" <?= ($taxConfig && $taxConfig['tax_enabled']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="tax_enabled">Gunakan Pajak</label>
      </div>
    </div>
    <div class="col-md-12 tax-field" style="<?= ($taxConfig && $taxConfig['tax_enabled']) ? 'display:block;' : 'display:none;' ?>">
      <label class="form-label">Metode Pajak</label>
      <select name="tax_method" class="form-select" id="tax_method">
        <option value="none" <?= (!$taxConfig || !in_array($taxConfig['tax_method'], ['percentage', 'manual'])) ? 'selected' : '' ?>>Tidak Ada</option>
        <option value="percentage" <?= ($taxConfig && $taxConfig['tax_method'] === 'percentage') ? 'selected' : '' ?>>Persentase</option>
        <option value="manual" <?= ($taxConfig && $taxConfig['tax_method'] === 'manual') ? 'selected' : '' ?>>Manual</option>
      </select>
    </div>
    <div class="col-md-6 tax-field tax-percentage-field" style="<?= ($taxConfig && $taxConfig['tax_enabled'] && $taxConfig['tax_method'] === 'percentage') ? 'display:block;' : 'display:none;' ?>">
      <label class="form-label">Persentase Pajak (%)</label>
      <input type="number" name="tax_percentage" class="form-control" step="0.01" min="0" max="100" value="<?= ($taxConfig ? (float)$taxConfig['tax_percentage'] : 0) ?>">
    </div>
    <div class="col-md-6 tax-field tax-manual-field" style="<?= ($taxConfig && $taxConfig['tax_enabled'] && $taxConfig['tax_method'] === 'manual') ? 'display:block;' : 'display:none;' ?>">
      <label class="form-label">Nominal Pajak (Rp)</label>
      <input type="number" name="tax_manual_nominal" class="form-control" step="1" min="0" value="<?= ($taxConfig ? (float)$taxConfig['tax_manual_nominal'] : 0) ?>">
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Simpan Konfigurasi</button>
    </div>
  </div>
</form>
<?php elseif ($partnerId && !$academicYearId): ?>
<div class="alert alert-info">Silakan pilih Tahun Ajaran terlebih dahulu.</div>
<?php else: ?>
<div class="alert alert-info">Silakan pilih Mitra dan Tahun Ajaran untuk mengelola konfigurasi pajak.</div>
<?php endif; ?>

<script>
function updateUrl() {
  const partnerId = document.getElementById('partnerSelect').value;
  const academicYearId = document.getElementById('academicYearSelect').value;
  let url = '?';
  if (partnerId) url += 'partner_id=' + partnerId;
  if (academicYearId) url += (partnerId ? '&' : '') + 'academic_year_id=' + academicYearId;
  window.location.href = url || '?';
}

const taxEnabled = document.getElementById('tax_enabled');
const taxFields = document.querySelectorAll('.tax-field');
const taxMethod = document.getElementById('tax_method');
const taxPercentageField = document.querySelector('.tax-percentage-field');
const taxManualField = document.querySelector('.tax-manual-field');

function toggleTaxFields() {
  const show = taxEnabled && taxEnabled.checked;
  if (!taxFields.length) return;
  
  taxFields.forEach(field => {
    if (!field.classList.contains('tax-percentage-field') && !field.classList.contains('tax-manual-field')) {
      field.style.display = show ? 'block' : 'none';
    }
  });
  
  if (show) {
    toggleTaxMethodFields();
  } else {
    if (taxPercentageField) taxPercentageField.style.display = 'none';
    if (taxManualField) taxManualField.style.display = 'none';
  }
}

function toggleTaxMethodFields() {
  if (!taxMethod) return;
  const method = taxMethod.value;
  
  if (method === 'percentage') {
    if (taxPercentageField) taxPercentageField.style.display = 'block';
    if (taxManualField) taxManualField.style.display = 'none';
  } else if (method === 'manual') {
    if (taxPercentageField) taxPercentageField.style.display = 'none';
    if (taxManualField) taxManualField.style.display = 'block';
  } else {
    if (taxPercentageField) taxPercentageField.style.display = 'none';
    if (taxManualField) taxManualField.style.display = 'none';
  }
}

if (taxEnabled) {
  taxEnabled.addEventListener('change', toggleTaxFields);
  toggleTaxFields();
}

if (taxMethod) {
  taxMethod.addEventListener('change', toggleTaxMethodFields);
  toggleTaxMethodFields();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>