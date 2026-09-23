<?php /** Form tambah/edit siswa. Variabel: $row (array|null), $SCOPE_PARTNER */
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_old']);
$row = array_merge($row ?? [], $old ? array_intersect_key($old, array_flip([
    'tanggal_pendaftaran','academic_year_id','partner_id','unit_id','nama_siswa','kelas','program_bimbel','kategori_peserta',
    'biaya_pendaftaran','biaya_program','total_biaya','triwulan','is_psam',
    'status_pembayaran','wali_kelas_id','wali_santri_id','keterangan','id',
])) : []);
$val = fn(string $k, $d = '') => e($row[$k] ?? $d);
$listMitra = opsi('partners', 'nama_mitra', '');
$listTA    = opsi('academic_years', 'nama_tahun_ajaran', '');
$listProgram = daftar_program_bimbel($SCOPE_PARTNER);
$statusNow = status_normal($row['status_pembayaran'] ?? 'angsuran');
$isPsamNow = $row['is_psam'] ?? 'Tidak';
$unitIdNow = (int)($row['unit_id'] ?? 0);

// Handle unit_id dari URL untuk filter wali kelas
if (isset($_GET['unit_id']) && $_GET['unit_id'] !== '' && !$row['unit_id']) {
    $unitIdNow = (int)$_GET['unit_id'];
    $row['unit_id'] = $unitIdNow;
}

// Filter Wali Kelas berdasarkan Unit yang dipilih
if ($unitIdNow) {
    $wk = db()->prepare("SELECT id, nama FROM recommenders WHERE jenis='wali_kelas' AND unit_id = ? AND status='aktif' ORDER BY nama");
    $wk->execute([$unitIdNow]);
    $wk = $wk->fetchAll();
} else {
    $wk = db()->query("SELECT id, nama FROM recommenders WHERE jenis='wali_kelas' AND status='aktif' ORDER BY nama")->fetchAll();
}
$ws = db()->query("SELECT id, nama FROM recommenders WHERE jenis='wali_santri' AND status='aktif' ORDER BY nama")->fetchAll();
?>
<form method="post" id="formSiswa" class="card">
  <?= csrf_field() ?>
  <input type="hidden" name="aksi" value="simpan">
  <input type="hidden" name="id" value="<?= $val('id') ?>">
  <div class="card-header"><i class="fa-solid fa-user-pen me-2"></i><?= empty($row['id']) ? 'Tambah Siswa Baru' : 'Edit Data Siswa' ?>
    <?php if (!empty($row['id'])): ?><span class="badge bg-secondary-soft ms-2">ID Siswa: <?= (int)$row['id'] ?></span><?php endif; ?>
  </div>
  <div class="card-body row g-3">
    <div class="col-md-3"><label class="form-label">Tanggal Pendaftaran *</label>
      <input type="date" name="tanggal_pendaftaran" class="form-control" required value="<?= $val('tanggal_pendaftaran', date('Y-m-d')) ?>"></div>
    <div class="col-md-3"><label class="form-label">Tahun Ajaran *</label>
      <select name="academic_year_id" class="form-select" required><?= select_options($listTA, $row['academic_year_id'] ?? '') ?></select></div>
    <?php if (!$SCOPE_PARTNER): ?>
    <div class="col-md-3"><label class="form-label">Mitra Bimbel *</label>
      <select name="partner_id" id="partner_id" class="form-select" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($listMitra as $m): ?>
          <option value="<?= (int)$m['id'] ?>"
            <?= (string)($row['partner_id'] ?? '') === (string)$m['id'] ? 'selected' : '' ?>>
            <?= e($m['nama']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <?php endif; ?>
    <div class="col-md-3"><label class="form-label">Unit *</label>
      <select name="unit_id" id="unit_id" class="form-select" required onchange="loadWaliKelasByUnit(this.value)"><?= unit_options(isset($row['unit_id']) && $row['unit_id'] !== '' ? (int)$row['unit_id'] : null) ?></select></div>
    <div class="col-md-<?= $SCOPE_PARTNER ? 6 : 3 ?>"><label class="form-label">Nama Siswa *</label>
      <input name="nama_siswa" class="form-control" required maxlength="150" value="<?= $val('nama_siswa') ?>"></div>
    <div class="col-md-3"><label class="form-label">Kelas</label>
      <input name="kelas" class="form-control" maxlength="50" value="<?= $val('kelas') ?>"></div>
    <div class="col-md-3"><label class="form-label">Program Bimbel</label>
      <input name="program_bimbel" class="form-control" maxlength="150" list="listProgramBimbel"
             placeholder="Contoh: Neutron, Primagama, Persiapan UTBK" value="<?= $val('program_bimbel') ?>">
      <datalist id="listProgramBimbel"><?php foreach ($listProgram as $p) echo '<option value="' . e($p) . '">'; ?></datalist>
      <div class="form-text">Ketik langsung nama Program Bimbel.</div></div>
    <div class="col-md-3"><label class="form-label">Kategori Peserta *</label>
      <select name="kategori_peserta" class="form-select" required>
        <option value="reguler" <?= ($row['kategori_peserta'] ?? 'reguler') === 'reguler' ? 'selected' : '' ?>>Reguler</option>
        <option value="khusus" <?= ($row['kategori_peserta'] ?? 'reguler') === 'khusus' ? 'selected' : '' ?>>Kategori Khusus</option>
      </select>
      <div class="form-text text-muted">Kategori Khusus tidak menghasilkan poin/fee rekomendator.</div></div>
    <div class="col-md-3"><label class="form-label">Triwulan</label>
      <select name="triwulan" class="form-select"><?= triwulan_options($row['triwulan'] ?? '') ?></select></div>
    <div class="col-md-3"><label class="form-label">Apakah siswa PSAM?</label>
      <select name="is_psam" id="is_psam" class="form-select" onchange="toggleWaliSantri(this.value)">
        <option value="Tidak" <?= $isPsamNow === 'Tidak' ? 'selected' : '' ?>>Tidak</option>
        <option value="Ya" <?= $isPsamNow === 'Ya' ? 'selected' : '' ?>>Ya</option>
      </select></div>
    <div class="col-12"><hr class="my-1"></div>
    <div class="col-md-3"><label class="form-label">Biaya Pendaftaran (Rp) *</label>
      <input type="number" min="0" step="1000" id="biaya_pendaftaran" name="biaya_pendaftaran" class="form-control" required value="<?= $val('biaya_pendaftaran', '0') ?>" onchange="hitungTotalBiaya()">
      <div class="form-text" id="prevBiayaPendaftaran">Rp0</div></div>
    <div class="col-md-3"><label class="form-label">Biaya Program (Rp) *</label>
      <input type="number" min="0" step="1000" id="biaya_program" name="biaya_program" class="form-control" required value="<?= $val('biaya_program', '0') ?>" onchange="hitungTotalBiaya()">
      <div class="form-text" id="prevBiayaProgram">Rp0</div></div>
    <div class="col-md-3"><label class="form-label">Total Biaya (Rp)</label>
      <input type="text" id="total_biaya" class="form-control" readonly value="<?= rupiah($val('total_biaya', '0')) ?>">
      <div class="form-text text-muted">Otomatis = Biaya Pendaftaran + Biaya Program</div></div>
    <div class="col-md-3"><label class="form-label">Status</label>
      <select name="status_pembayaran" id="status_pembayaran" class="form-select">
        <?php foreach (STATUS_LIST as $k => $lbl): ?>
          <option value="<?= $k ?>" <?= $statusNow === $k ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
      <div class="form-text text-muted">Hanya informasi</div></div>
    <div class="col-md-4"><label class="form-label">Wali Kelas Perekomendasi</label>
      <select name="wali_kelas_id" id="wali_kelas_id" class="form-select"><?= select_options($wk, $row['wali_kelas_id'] ?? '', '-- Tidak Ada --') ?></select>
      <div class="form-text">Wajib diisi untuk semua kategori peserta (Reguler/Kategori Khusus).</div></div>
    <div class="col-md-4"><label class="form-label" id="labelWaliSantri">Wali Santri Perekomendasi</label>
      <select name="wali_santri_id" id="wali_santri_id" class="form-select" <?= $isPsamNow !== 'Ya' ? 'disabled' : '' ?>><?= select_options($ws, $row['wali_santri_id'] ?? '', '-- Tidak Ada --') ?></select>
      <div class="form-text" id="infoWaliSantri">Wajib diisi jika siswa PSAM (untuk semua kategori).</div></div>
    <div class="col-md-4"><label class="form-label">Keterangan</label>
      <input name="keterangan" class="form-control" maxlength="255" value="<?= $val('keterangan') ?>"></div>
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a href="<?= e(basename($_SERVER['PHP_SELF'])) ?>" class="btn btn-light">Batal</a>
    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button>
  </div>
</form>
<script>
function hitungTotalBiaya() {
    const biayaPendaftaran = parseFloat(document.getElementById('biaya_pendaftaran').value) || 0;
    const biayaProgram = parseFloat(document.getElementById('biaya_program').value) || 0;
    const total = biayaPendaftaran + biayaProgram;
    document.getElementById('total_biaya').value = 'Rp' + total.toLocaleString('id-ID');
    document.getElementById('prevBiayaPendaftaran').textContent = 'Rp' + biayaPendaftaran.toLocaleString('id-ID');
    document.getElementById('prevBiayaProgram').textContent = 'Rp' + biayaProgram.toLocaleString('id-ID');
}

function toggleWaliSantri(isPsam) {
    const waliSantriSelect = document.getElementById('wali_santri_id');
    const infoWaliSantri = document.getElementById('infoWaliSantri');
    if (isPsam === 'Ya') {
        waliSantriSelect.disabled = false;
        infoWaliSantri.textContent = 'Wajib diisi jika siswa PSAM.';
    } else {
        waliSantriSelect.disabled = true;
        waliSantriSelect.value = '';
        infoWaliSantri.textContent = 'Tidak diperlukan untuk non-PSAM.';
    }
}

function loadWaliKelasByUnit(unitId) {
    if (!unitId) return;
    const url = new URL(window.location.href);
    url.searchParams.set('unit_id', unitId);
    if (url.searchParams.has('id')) {
        url.searchParams.set('aksi', 'edit');
    } else {
        url.searchParams.set('aksi', 'tambah');
    }
    window.location.href = url.toString();
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    hitungTotalBiaya();
    toggleWaliSantri(document.getElementById('is_psam').value);
});
</script>
