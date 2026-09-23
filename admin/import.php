<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';
require_once __DIR__ . '/../includes/student_crud.php';
require_once __DIR__ . '/../includes/parent_account_helper.php';

const IMPORT_HEADER = ['Nama Siswa','Unit','Kelas','PSAM','Program Bimbel','Biaya Pendaftaran','Biaya Program',
    'Status','Triwulan','Tahun Ajaran','Wali Kelas','Wali Santri','Mitra'];

function lookup_id(string $table, string $col, string $val): ?int {
    if (trim($val) === '') return null;
    $st = db()->prepare("SELECT id FROM $table WHERE LOWER($col)=LOWER(?) LIMIT 1");
    $st->execute([trim($val)]);
    $id = $st->fetchColumn();
    return $id ? (int)$id : null;
}
function lookup_rec(string $jenis, string $nama): ?int {
    if (trim($nama) === '') return null;
    $st = db()->prepare('SELECT id FROM recommenders WHERE LOWER(nama)=LOWER(?) AND jenis=? LIMIT 1');
    $st->execute([trim($nama), $jenis]);
    $id = $st->fetchColumn();
    return $id ? (int)$id : null;
}
function to_num($v): float { return (float)preg_replace('/[^0-9.\-]/', '', str_replace(',', '.', (string)$v)); }
function parse_tanggal(string $v): ?string {
    $v = trim($v); if ($v === '') return null;
    foreach (['Y-m-d','d/m/Y','d-m-Y','Y/m/d'] as $f) {
        $d = DateTime::createFromFormat($f, $v);
        if ($d && $d->format($f) === $v) return $d->format('Y-m-d');
    }
    $ts = strtotime($v);
    return $ts ? date('Y-m-d', $ts) : null;
}

$preview = null;
$ringkasan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'upload') {
    csrf_check();
    $f = $_FILES['berkas'] ?? null;
    if (!$f || $f['error'] !== UPLOAD_ERR_OK) { flash('danger', 'Gagal mengunggah berkas.'); redirect('import.php'); }
    if ($f['size'] > 2 * 1024 * 1024) { flash('danger', 'Ukuran berkas maksimal 2 MB.'); redirect('import.php'); }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['csv', 'xlsx'], true)) { flash('danger', 'Format berkas harus CSV atau XLSX.'); redirect('import.php'); }

    $rows = [];
    if ($ext === 'csv') {
        $h = fopen($f['tmp_name'], 'r');
        $first = true;
        while (($line = fgets($h)) !== false) {
            if ($first) { $line = preg_replace('/^\xEF\xBB\xBF/', '', $line); }
            $sep = substr_count($line, ';') >= substr_count($line, ',') ? ';' : ',';
            $r = str_getcsv(rtrim($line, "\r\n"), $sep);
            if ($first) { $first = false; continue; } // lewati header
            if (count(array_filter($r, fn($x) => trim((string)$x) !== '')) === 0) continue;
            $rows[] = $r;
        }
        fclose($h);
    } else {
        $auto = __DIR__ . '/../vendor/autoload.php';
        if (!file_exists($auto)) { flash('danger', 'Import XLSX memerlukan PhpSpreadsheet (composer require phpoffice/phpspreadsheet). Gunakan format CSV.'); redirect('import.php'); }
        require_once $auto;
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($f['tmp_name'])->getActiveSheet()->toArray(null, true, true, false);
        array_shift($sheet);
        foreach ($sheet as $r) { if (count(array_filter($r, fn($x) => trim((string)$x) !== '')) === 0) continue; $rows[] = $r; }
    }

    $hasil = []; $seen = [];
    foreach ($rows as $i => $r) {
        $r = array_pad($r, 14, '');
        $item = [
            'baris' => $i + 2,
            'nama' => trim((string)$r[0]),
            'unit' => trim((string)$r[1]),
            'kelas' => trim((string)$r[2]),
            'psam' => strtoupper(trim((string)$r[3])),
            'program' => trim((string)$r[4]),
            'kategori_peserta' => strtolower(trim((string)$r[5])) === 'khusus' ? 'khusus' : 'reguler',
            'biaya_pendaftaran' => to_num($r[6]),
            'biaya_program' => to_num($r[7]),
            'status' => status_normal((string)$r[8]),
            'triwulan' => trim((string)$r[9]),
            'ta' => trim((string)$r[10]),
            'wk' => trim((string)$r[11]),
            'ws' => trim((string)$r[12]),
            'mitra' => trim((string)$r[13]),
        ];
        $item['total_biaya'] = $item['biaya_pendaftaran'] + $item['biaya_program'];
        $err = [];
        if ($item['nama'] === '') $err[] = 'Nama siswa kosong';

        $item['partner_id'] = lookup_id('partners', 'nama_mitra', $item['mitra']);
        $item['ta_id'] = lookup_id('academic_years', 'nama_tahun_ajaran', $item['ta']);
        $item['unit_id'] = $item['unit'] !== '' ? unit_id_dari_nama($item['unit']) : null;
        $item['wk_id'] = lookup_rec('wali_kelas', $item['wk']);
        $item['ws_id'] = lookup_rec('wali_santri', $item['ws']);
        
        if ($item['mitra'] === '') $err[] = 'Mitra wajib diisi';
        elseif (!$item['partner_id']) $err[] = 'Mitra "' . $item['mitra'] . '" tidak dikenal';
        
        if ($item['ta'] !== '' && !$item['ta_id']) $err[] = 'Tahun ajaran "' . $item['ta'] . '" tidak dikenal';
        if ($item['unit'] === '') $err[] = 'Unit wajib diisi';
        elseif (!$item['unit_id']) $err[] = 'Unit "' . $item['unit'] . '" tidak dikenal. Gunakan: SD, SMP, SMA';
        
        // Validasi Unit hanya SD, SMP, SMA
        $unitValid = ['SD', 'SMP', 'SMA'];
        if ($item['unit'] !== '' && !in_array(strtoupper($item['unit']), $unitValid, true)) {
            $err[] = 'Unit hanya boleh: SD, SMP, SMA';
        }
        
        // Validasi PSAM
        $psamValid = ['YA', 'TIDAK'];
        if ($item['psam'] !== '' && !in_array($item['psam'], $psamValid, true)) {
            $err[] = 'PSAM hanya boleh: YA atau TIDAK';
        }
        
        // Validasi Wali Santri berdasarkan PSAM
        if ($item['psam'] === 'YA') {
            if ($item['ws'] === '') $err[] = 'Untuk PSAM YA, data Wali Santri wajib diisi';
            elseif (!$item['ws_id']) $err[] = 'Wali Santri "' . $item['ws'] . '" tidak terdaftar';
        } else {
            if ($item['ws'] !== '') $err[] = 'Wali Santri hanya untuk PSAM YA. Kosongkan kolom Wali Santri';
            $item['ws_id'] = null; // tidak disimpan untuk PSAM TIDAK
        }
        
        if ($item['wk'] !== '' && !$item['wk_id']) $err[] = 'Wali Kelas "' . $item['wk'] . '" tidak terdaftar';

        // Validasi biaya tidak negatif
        if ($item['biaya_pendaftaran'] < 0) $err[] = 'Biaya Pendaftaran tidak boleh negatif';
        if ($item['biaya_program'] < 0) $err[] = 'Biaya Program tidak boleh negatif';

        // Duplikat dalam berkas
        $key = strtolower($item['nama']) . '|' . $item['ta_id'] . '|' . strtolower($item['program']);
        if (isset($seen[$key])) $err[] = 'Duplikat dengan baris ' . $seen[$key];
        else $seen[$key] = $item['baris'];

        // Duplikat di database: Nama + Program + Tahun Ajaran
        $item['existing_id'] = null;
        if ($item['nama'] !== '') {
            $st = db()->prepare('SELECT id, status_pembayaran, biaya_pendaftaran, biaya_program, total_biaya, is_psam, wali_santri_id, kategori_peserta
                FROM students WHERE partner_id = ? AND nama_siswa = ? AND program_bimbel = ? AND academic_year_id = ?');
            $st->execute([$item['partner_id'], $item['nama'], $item['program'], $item['ta_id']]);
            $dup = $st->fetch();
            if ($dup) {
                $item['existing_id'] = (int)$dup['id'];
                $item['existing_status'] = status_normal($dup['status_pembayaran']);
                $item['existing_biaya_pendaftaran'] = (float)$dup['biaya_pendaftaran'];
                $item['existing_biaya_program'] = (float)$dup['biaya_program'];
                $item['existing_total_biaya'] = (float)$dup['total_biaya'];
                $item['existing_is_psam'] = $dup['is_psam'];
                $item['existing_ws_id'] = $dup['wali_santri_id'];
                $item['existing_kategori_peserta'] = $dup['kategori_peserta'];
            }
        }
        $item['error'] = $err;
        $hasil[] = $item;
    }
    if (!$hasil) { flash('danger', 'Berkas tidak berisi data.'); redirect('import.php'); }
    $_SESSION['import_preview'] = $hasil;
    $preview = $hasil;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
    csrf_check();
    $data = $_SESSION['import_preview'] ?? [];
    if (!$data) { flash('danger', 'Tidak ada data pratinjau. Unggah ulang berkas.'); redirect('import.php'); }
    // Blok total jika ada 1 baris merah
    $hasError = count(array_filter($data, fn($d) => !empty($d['error'])) ) > 0;
    if ($hasError) {
        $gagalList = array_filter($data, fn($d) => !empty($d['error']));
        $alasan = array_map(fn($d) => 'Baris ' . $d['baris'] . ': ' . implode('; ', $d['error']), $gagalList);
        $_SESSION['import_ringkasan'] = ['baru' => 0, 'diperbarui' => 0, 'dilewati' => 0, 'gagal' => count($gagalList), 'alasan' => $alasan, 'blocked' => true];
        flash('danger', 'Import diblokir: ada ' . count($gagalList) . ' baris merah (error). Perbaiki dulu, tidak ada data yang disimpan.');
        redirect('import.php');
    }
    $modeDuplikat = ($_POST['mode_duplikat'] ?? 'update') === 'lewati' ? 'lewati' : 'update';

    $baru = 0; $diperbarui = 0; $dilewati = 0; $gagal = 0; $alasan = [];
    $ins = db()->prepare('INSERT INTO students (partner_id, academic_year_id, unit_id, tanggal_pendaftaran,
        nama_siswa, kelas, program_bimbel, kategori_peserta, biaya_pendaftaran, biaya_program, total_biaya, triwulan, is_psam,
        status_pembayaran, wali_kelas_id, wali_santri_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $upd = db()->prepare('UPDATE students SET kelas=?, program_bimbel=?, kategori_peserta=?, biaya_pendaftaran=?, biaya_program=?, total_biaya=?, 
        triwulan=?, is_psam=?, status_pembayaran=?, wali_kelas_id=?, wali_santri_id=? WHERE id=?');

    foreach ($data as $d) {
        if ($d['error']) { $gagal++; $alasan[] = 'Baris ' . $d['baris'] . ': ' . implode('; ', $d['error']); continue; }
        try {
            if ($d['existing_id']) {
                if ($modeDuplikat === 'lewati') { $dilewati++; continue; }
                // Data yang sama diperbarui (Pendapatan terbaru), tidak menambah baris siswa baru
                $upd->execute([$d['kelas'], $d['program'], $d['kategori_peserta'], $d['biaya_pendaftaran'], $d['biaya_program'], $d['total_biaya'],
                    $d['triwulan'], $d['psam'], $d['status'], $d['wk_id'], $d['ws_id'], $d['existing_id']]);
                audit('Import: memperbarui data siswa ' . $d['nama'], 'students', $d['existing_id'], 
                    ['biaya_pendaftaran' => $d['existing_biaya_pendaftaran'] ?? null, 'biaya_program' => $d['existing_biaya_program'] ?? null, 
                     'total_biaya' => $d['existing_total_biaya'] ?? null], 
                    ['biaya_pendaftaran' => $d['biaya_pendaftaran'], 'biaya_program' => $d['biaya_program'], 
                     'total_biaya' => $d['total_biaya'], 'is_psam' => $d['psam']]);
                $diperbarui++;
            } else {
                $tanggal = date('Y-m-d'); // Gunakan tanggal hari ini untuk insert baru
                $ins->execute([$d['partner_id'], $d['ta_id'], $d['unit_id'], $tanggal, $d['nama'], $d['kelas'], $d['program'], $d['kategori_peserta'],
                    $d['biaya_pendaftaran'], $d['biaya_program'], $d['total_biaya'], $d['triwulan'], $d['psam'],
                    $d['status'], $d['wk_id'], $d['ws_id']]);
                $newStudentId = (int)db()->lastInsertId();
                audit('Import: menambah data siswa ' . $d['nama'], 'students', $newStudentId, null,
                    ['biaya_pendaftaran' => $d['biaya_pendaftaran'], 'biaya_program' => $d['biaya_program'], 
                     'total_biaya' => $d['total_biaya'], 'is_psam' => $d['psam']]);
                
                // Otomatis buat akun orang tua
                $parentResult = createParentAccount($newStudentId, $d['nama']);
                if (!$parentResult['success']) {
                    $alasan[] = 'Baris ' . $d['baris'] . ': Gagal membuat akun orang tua - ' . $parentResult['message'];
                }
                
                $baru++;
            }
        } catch (PDOException $ex) {
            $gagal++;
            $alasan[] = 'Baris ' . $d['baris'] . ': ' . ($ex->getCode() === '23000' ? 'Data siswa sudah terdaftar (duplikat)' : 'Gagal disimpan');
        }
    }
    unset($_SESSION['import_preview']);
    $_SESSION['import_ringkasan'] = ['baru' => $baru, 'diperbarui' => $diperbarui, 'dilewati' => $dilewati, 'gagal' => $gagal, 'alasan' => $alasan];
    audit("Import data siswa: $baru baru, $diperbarui diperbarui, $dilewati dilewati, $gagal gagal", 'students');
    flash('success', "Import selesai. $baru data baru, $diperbarui data diperbarui, $dilewati dilewati, $gagal gagal.");
    redirect('import.php');
}

$ringkasan = $_SESSION['import_ringkasan'] ?? null;
unset($_SESSION['import_ringkasan']);

$PAGE_TITLE = 'Import Data Siswa';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Import Data Siswa']];
include __DIR__ . '/../includes/header.php';
$valid = $preview ? count(array_filter($preview, fn($r) => !$r['error'])) : 0;
$gagalPrev = $preview ? count($preview) - $valid : 0;
$dupPrev = $preview ? count(array_filter($preview, fn($r) => !$r['error'] && $r['existing_id'])) : 0;
?>
<?php if ($ringkasan): ?>
<div class="card mb-3"><div class="card-header">Ringkasan Hasil Import</div><div class="card-body">
  <div class="row g-3">
    <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Data baru ditambahkan</div><div class="value"><?= (int)$ringkasan['baru'] ?></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Data lama diperbarui</div><div class="value"><?= (int)$ringkasan['diperbarui'] ?></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Data dilewati</div><div class="value"><?= (int)$ringkasan['dilewati'] ?></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="label">Data gagal</div><div class="value"><?= (int)$ringkasan['gagal'] ?></div></div></div>
  </div>
  <?php if (!empty($ringkasan['alasan'])): ?>
    <div class="mt-3"><div class="fw-semibold small mb-1">Alasan kegagalan:</div>
      <ul class="small text-danger mb-0"><?php foreach ($ringkasan['alasan'] as $a) echo '<li>' . e($a) . '</li>'; ?></ul></div>
  <?php endif; ?>
</div></div>
<?php endif; ?>
<div class="row g-3">
  <div class="col-lg-5">
    <form method="post" enctype="multipart/form-data" class="card"><?= csrf_field() ?>
      <input type="hidden" name="aksi" value="upload">
      <div class="card-header"><i class="fa-solid fa-file-import me-2"></i>Unggah Berkas</div>
      <div class="card-body">
        <p class="small text-muted">Unduh template terlebih dahulu, isi datanya, lalu unggah kembali. Format didukung: <strong>CSV</strong> (pemisah titik koma) dan <strong>XLSX</strong> (bila PhpSpreadsheet terpasang). Maksimal 2 MB.</p>
        <a href="template" class="btn btn-light w-100 mb-3"><i class="fa-solid fa-download me-1"></i>Download Template</a>
        <input type="file" name="berkas" class="form-control" accept=".csv,.xlsx" required>
        <button class="btn btn-primary w-100 mt-3"><i class="fa-solid fa-eye me-1"></i>Pratinjau Data</button>
      </div>
    </form>
  </div>
  <div class="col-lg-7">
    <?php if ($preview): ?>
    <form method="post" class="card mb-3"><?= csrf_field() ?>
      <input type="hidden" name="aksi" value="simpan">
      <div class="card-header">Konfirmasi Import</div>
      <div class="card-body">
        <?php if ($gagalPrev > 0): ?>
        <div class="alert alert-danger">⛔ Ada <strong><?= $gagalPrev ?></strong> baris merah (error) — import diblokir total. Perbaiki berkas dulu, tidak ada data yang akan disimpan.</div>
        <?php endif; ?>
        <div class="alert alert-info">
          <div>✅ Data valid: <strong><?= $valid ?></strong> baris</div>
          <div>❌ Data gagal: <strong><?= $gagalPrev ?></strong> baris</div>
          <div>⚠️ Data duplikat: <strong><?= $dupPrev ?></strong> baris</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Mode duplikat:</label>
          <select name="mode_duplikat" class="form-select" <?= $gagalPrev > 0 ? 'disabled' : '' ?>>
            <option value="update">Update data yang sudah ada</option>
            <option value="lewati">Lewati data yang sudah ada</option>
          </select>
        </div>
        <button class="btn btn-success w-100" <?= $gagalPrev > 0 ? 'disabled' : '' ?>><i class="fa-solid fa-floppy-disk me-1"></i><?= $gagalPrev > 0 ? 'Perbaiki dulu ('.$gagalPrev.' error)' : 'Simpan Data' ?></button>
        <a href="import" class="btn btn-light w-100 mt-2">Batal</a>
      </div>
    </form>
    <div class="card"><div class="card-header">Pratinjau Data</div><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
    <table class="table table-sm table-striped mb-0"><thead><tr><th>Baris</th><th>Nama</th><th>Unit</th><th>Kelas</th><th>PSAM</th><th>Program</th><th>Biaya Daftar</th><th>Biaya Program</th><th>Status</th><th>TA</th><th>Mitra</th></tr></thead><tbody>
    <?php foreach ($preview as $r): ?>
      <tr class="<?= $r['error'] ? 'table-danger' : ($r['existing_id'] ? 'table-warning' : '') ?>">
        <td><?= $r['baris'] ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['unit']) ?></td>
        <td><?= e($r['kelas']) ?></td>
        <td><?= e($r['psam']) ?></td>
        <td><?= e($r['program']) ?></td>
        <td><?= number_format($r['biaya_pendaftaran'], 0, ',', '.') ?></td>
        <td><?= number_format($r['biaya_program'], 0, ',', '.') ?></td>
        <td><?= badge_status($r['status']) ?></td>
        <td><?= e($r['ta']) ?></td>
        <td><?= e($r['mitra']) ?></td>
      </tr>
      <?php if ($r['error']): ?>
        <tr class="table-danger"><td colspan="11" class="small text-danger"><?= implode('; ', $r['error']) ?></td></tr>
      <?php elseif ($r['existing_id']): ?>
        <tr class="table-warning"><td colspan="11" class="small text-warning">Duplikat dengan ID #<?= $r['existing_id'] ?> (status: <?= status_label($r['existing_status']) ?>)</td></tr>
      <?php endif; ?>
    <?php endforeach; ?>
    </tbody></table></div></div></div>
    <?php else: ?>
    <div class="card"><div class="card-body text-center text-muted py-5">
      <i class="fa-solid fa-file-csv fa-3x mb-3"></i>
      <p>Unggah berkas untuk memulai import data siswa.</p>
    </div></div>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
