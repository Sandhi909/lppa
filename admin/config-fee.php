<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['aksi'] ?? '';
    
    if ($act === 'simpan') {
        $jenisFee = trim($_POST['jenis_fee'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tarifDefault = (float)str_replace(',', '.', $_POST['tarif_default'] ?? 0);
        $satuan = trim($_POST['satuan'] ?? 'per_pemakaian');
        $status = ($_POST['status'] ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif';
        
        if ($jenisFee === '') { flash('danger', 'Jenis fee wajib diisi.'); redirect('config-fee.php'); }
        if ($tarifDefault <= 0) { flash('danger', 'Tarif harus lebih dari 0.'); redirect('config-fee.php'); }
        
        $st = db()->prepare("INSERT INTO config_fee_settings (jenis_fee, deskripsi, tarif_default, satuan, status) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE deskripsi = ?, tarif_default = ?, satuan = ?, status = ?");
        $st->execute([$jenisFee, $deskripsi, $tarifDefault, $satuan, $status, $deskripsi, $tarifDefault, $satuan, $status]);
        
        audit('Mengubah konfigurasi fee', 'config_fee_settings');
        flash('success', 'Konfigurasi fee diperbarui.');
        redirect('config-fee.php');
    }
}

$config = db()->query("SELECT * FROM config_fee_settings")->fetchAll();

$PAGE_TITLE = 'Konfigurasi Tarif Fee';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Konfigurasi Fee']];
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-header">Form Konfigurasi</div>
      <div class="card-body">
        <form method="post">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Fee</label>
              <input type="text" name="jenis_fee" class="form-control" value="fee_wali_kelas_sd" readonly>
              <div class="form-text">Jenis fee yang akan dikonfigurasi</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Deskripsi</label>
              <input type="text" name="deskripsi" class="form-control" value="Fee Wali Kelas SD per pemakaian ruang bimbel">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tarif Default (Rp)</label>
              <input type="number" name="tarif_default" class="form-control" step="100" min="0" 
                     value="<?= $config[0]['tarif_default'] ?? 5000 ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Satuan</label>
              <select name="satuan" class="form-select">
                <option value="per_pemakaian" selected>Per Pemakaian</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="aktif" selected>Aktif</option>
                <option value="nonaktif">Nonaktif</option>
              </select>
            </div>
            <div class="col-12">
              <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">Informasi</div>
      <div class="card-body">
        <div class="alert alert-info">
          <strong>Catatan Penting:</strong><br>
          <small>
            • Fee dihitung berdasarkan JUMLAH PEMAKAIAN/JADWAL<br>
            • 1 jadwal = 1 pemakaian<br>
            • Durasi jam tidak mempengaruhi fee<br>
            • Perubahan tarif akan berlaku untuk jadwal baru
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">Konfigurasi Fee Saat Ini</div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Jenis Fee</th><th>Deskripsi</th><th>Tarif</th><th>Satuan</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($config as $c): ?>
        <tr>
          <td><?= e($c['jenis_fee']) ?></td>
          <td><?= e($c['deskripsi']) ?></td>
          <td>Rp<?= number_format($c['tarif_default'], 0, ',', '.') ?></td>
          <td><?= e($c['satuan']) ?></td>
          <td><span class="badge bg-<?= $c['status'] === 'aktif' ? 'success' : 'secondary' ?>"><?= e($c['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
