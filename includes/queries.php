<?php
/**
 * Query terpusat untuk data siswa + rekap rekomendator.
 * Semua query menggunakan prepared statement (PDO).
 * Konsep keuangan: Biaya Pendaftaran, Biaya Program, Total Biaya.
 */
require_once __DIR__ . '/auth.php';

/** ID tahun ajaran terbaru (yang masih bisa dihapus datanya). */
function tahun_ajaran_id_terbaru(): int {
    static $id = null;
    if ($id === null) {
        $id = (int)db()->query('SELECT id FROM academic_years ORDER BY id DESC LIMIT 1')->fetchColumn();
    }
    return $id;
}
/** Nama tahun ajaran terbaru. */
function tahun_ajaran_nama_terbaru(): string {
    static $nama = null;
    if ($nama === null) {
        $st = db()->prepare('SELECT nama_tahun_ajaran FROM academic_years ORDER BY id DESC LIMIT 1');
        $st->execute();
        $nama = (string)$st->fetchColumn();
    }
    return $nama !== '' ? $nama : '-';
}

/** Bangun WHERE + params dari filter GET. $force = paksaan scope (mis. partner_id mitra) */
function student_filters(array $g, array $force = []): array {
    $w = ['1=1']; $p = [];
    $map = [
        'partner_id' => 's.partner_id', 'academic_year_id' => 's.academic_year_id',
        'unit_id' => 's.unit_id',
        'wali_kelas_id' => 's.wali_kelas_id', 'wali_santri_id' => 's.wali_santri_id',
        'kategori_peserta' => 's.kategori_peserta',
    ];
    foreach ($map as $key => $col) {
        $val = $force[$key] ?? ($g[$key] ?? '');
        if ($val !== '' && $val !== null) { $w[] = "$col = ?"; $p[] = $val; }
    }
    $status = $force['status'] ?? ($g['status'] ?? '');
    if ($status !== '' && $status !== null) { $w[] = 's.status_pembayaran = ?'; $p[] = status_normal((string)$status); }

    $program = $force['program'] ?? ($g['program'] ?? '');
    if ($program !== '' && $program !== null) { $w[] = 's.program_bimbel LIKE ?'; $p[] = '%' . $program . '%'; }

    if (!empty($force['rekomendator_id'])) {
        $w[] = '(s.wali_kelas_id = ? OR s.wali_santri_id = ?)';
        $p[] = $force['rekomendator_id']; $p[] = $force['rekomendator_id'];
    } elseif (!empty($g['rekomendator_id'])) {
        $w[] = '(s.wali_kelas_id = ? OR s.wali_santri_id = ?)';
        $p[] = $g['rekomendator_id']; $p[] = $g['rekomendator_id'];
    }
    if (!empty($g['q'])) {
        $w[] = '(s.nama_siswa LIKE ? OR s.kelas LIKE ? OR s.program_bimbel LIKE ?)';
        $p[] = '%' . $g['q'] . '%'; $p[] = '%' . $g['q'] . '%'; $p[] = '%' . $g['q'] . '%';
    }
    if (!empty($g['dari'])) { $w[] = 's.tanggal_pendaftaran >= ?'; $p[] = $g['dari']; }
    if (!empty($g['sampai'])) { $w[] = 's.tanggal_pendaftaran <= ?'; $p[] = $g['sampai']; }
    if (!empty($g['bulan'])) { $w[] = 'DATE_FORMAT(s.tanggal_pendaftaran, "%Y-%m") = ?'; $p[] = $g['bulan']; }
    return [implode(' AND ', $w), $p];
}

const STUDENT_SELECT = "SELECT s.*, p.nama_mitra,
        a.nama_tahun_ajaran, u.nama_unit,
        wk.nama AS wali_kelas_nama, ws.nama AS wali_santri_nama
    FROM students s
    LEFT JOIN partners p ON p.id = s.partner_id
    LEFT JOIN academic_years a ON a.id = s.academic_year_id
    LEFT JOIN units u ON u.id = s.unit_id
    LEFT JOIN recommenders wk ON wk.id = s.wali_kelas_id
    LEFT JOIN recommenders ws ON ws.id = s.wali_santri_id";

function students_list(string $where, array $params, int $limit = 0, int $offset = 0): array {
    $sql = STUDENT_SELECT . " WHERE $where ORDER BY s.tanggal_pendaftaran DESC, s.id DESC";
    if ($limit > 0) $sql .= " LIMIT $limit OFFSET $offset";
    $st = db()->prepare($sql); $st->execute($params);
    return $st->fetchAll();
}
function student_by_id(int $id): ?array {
    $st = db()->prepare(STUDENT_SELECT . ' WHERE s.id = ?');
    $st->execute([$id]);
    $r = $st->fetch();
    return $r ?: null;
}
function students_count(string $where, array $params): int {
    $st = db()->prepare("SELECT COUNT(*) FROM students s WHERE $where");
    $st->execute($params); return (int)$st->fetchColumn();
}
/** Ringkasan: jumlah siswa, biaya pendaftaran, biaya program, total biaya */
function students_summary(string $where, array $params): array {
    $st = db()->prepare("SELECT COUNT(*) total,
        COALESCE(SUM(s.biaya_pendaftaran),0) biaya_pendaftaran,
        COALESCE(SUM(s.biaya_program),0) biaya_program,
        COALESCE(SUM(s.total_biaya),0) total_biaya
        FROM students s WHERE $where");
    $st->execute($params);
    return $st->fetch() ?: ['total' => 0, 'biaya_pendaftaran' => 0, 'biaya_program' => 0, 'total_biaya' => 0];
}
/** Rekap per Mitra lengkap dengan biaya pendaftaran, biaya program, total biaya. */
function rekap_per_mitra(string $where, array $params): array {
    $st = db()->prepare("SELECT pbh.id AS partner_id, COALESCE(pbh.nama_mitra,'(Tanpa Mitra)') AS nama_mitra,
        pbh.persentase_bagi_hasil,
        COUNT(*) jml,
        COALESCE(SUM(s.biaya_pendaftaran),0) biaya_pendaftaran,
        COALESCE(SUM(s.biaya_program),0) biaya_program,
        COALESCE(SUM(s.total_biaya),0) total_biaya
        FROM students s LEFT JOIN partners pbh ON pbh.id = s.partner_id
        WHERE $where GROUP BY pbh.id, nama_mitra, pbh.persentase_bagi_hasil ORDER BY total_biaya DESC");
    $st->execute($params);
    return $st->fetchAll();
}
function group_sum(string $where, array $params, string $joinCol, string $labelExpr, string $joinSql): array {
    $st = db()->prepare("SELECT $labelExpr AS label, COUNT(*) jml,
        COALESCE(SUM(s.biaya_pendaftaran),0) biaya_pendaftaran,
        COALESCE(SUM(s.biaya_program),0) biaya_program,
        COALESCE(SUM(s.total_biaya),0) total_biaya
        FROM students s $joinSql
        WHERE $where GROUP BY $joinCol, label ORDER BY jml DESC");
    $st->execute($params); return $st->fetchAll();
}
/** Data grafik: jumlah peserta pada 3 unit tetap (selalu lengkap walau 0) */
function chart_per_unit(string $where, array $params): array {
    $st = db()->prepare("SELECT u.nama_unit AS label, COUNT(*) jml
        FROM students s LEFT JOIN units u ON u.id = s.unit_id
        WHERE $where GROUP BY u.nama_unit");
    $st->execute($params);
    $found = [];
    foreach ($st->fetchAll() as $r) $found[(string)$r['label']] = (int)$r['jml'];
    $out = [];
    $unitTetap = ['SD', 'SMP', 'SMA'];
    foreach ($unitTetap as $u) $out[$u] = $found[$u] ?? 0;
    return $out;
}
/** Rekap per Program Bimbel (teks bebas) */
function group_program(string $where, array $params): array {
    $st = db()->prepare("SELECT COALESCE(NULLIF(s.program_bimbel,''),'(Tanpa Program)') AS label,
        COUNT(*) jml,
        COALESCE(SUM(s.biaya_pendaftaran),0) biaya_pendaftaran,
        COALESCE(SUM(s.biaya_program),0) biaya_program,
        COALESCE(SUM(s.total_biaya),0) total_biaya
        FROM students s
        WHERE $where GROUP BY label ORDER BY jml DESC");
    $st->execute($params); return $st->fetchAll();
}

/**
 * Rekap poin & fee rekomendator berdasarkan filter siswa.
 * Wali Kelas: 1 poin jika hanya wali kelas, 0,5 poin jika ada wali santri.
 * Wali Santri: 1 poin (hanya jika is_psam = 'Ya').
 * Poin dihitung per Tahun Ajaran secara terpisah.
 * KATEGORI KHUSUS: Siswa dengan kategori_peserta = 'khusus' TIDAK menghasilkan poin.
 */
function rekap_rekomendator(string $where, array $params, string $jenis = ''): array {
    $rows = students_list($where, $params);
    $agg = [];
    foreach ($rows as $r) {
        $academicYearId = $r['academic_year_id'] ?? null;
        $kategoriPeserta = $r['kategori_peserta'] ?? 'reguler';
        
        // Cek apakah siswa menghasilkan poin (hanya Reguler)
        $menghasilkanPoin = ($kategoriPeserta === 'reguler');
        
        if ($r['wali_kelas_id']) {
            $id = 'wk' . $r['wali_kelas_id'] . '_ay' . $academicYearId;
            $agg[$id] ??= ['id' => (int)$r['wali_kelas_id'], 'nama' => $r['wali_kelas_nama'], 'jenis' => 'wali_kelas', 
                          'siswa' => 0, 'siswa_reguler' => 0, 'siswa_khusus' => 0, 'poin' => 0.0, 'academic_year_id' => $academicYearId];
            $agg[$id]['siswa']++;
            
            // Hitung jumlah siswa per kategori
            if ($kategoriPeserta === 'reguler') {
                $agg[$id]['siswa_reguler']++;
            } else {
                $agg[$id]['siswa_khusus']++;
            }
            
            // Jika PSAM (ada wali santri), wali kelas dapat 0.5 poin, jika tidak 1 poin
            // HANYA jika siswa menghasilkan poin (Reguler)
            if ($menghasilkanPoin) {
                $agg[$id]['poin'] += ($r['is_psam'] === 'Ya' && $r['wali_santri_id']) ? 0.5 : 1.0;
            }
        }
        if ($r['wali_santri_id'] && $r['is_psam'] === 'Ya') {
            $id = 'ws' . $r['wali_santri_id'] . '_ay' . $academicYearId;
            $agg[$id] ??= ['id' => (int)$r['wali_santri_id'], 'nama' => $r['wali_santri_nama'], 'jenis' => 'wali_santri', 
                          'siswa' => 0, 'siswa_reguler' => 0, 'siswa_khusus' => 0, 'poin' => 0.0, 'academic_year_id' => $academicYearId];
            $agg[$id]['siswa']++;
            
            // Hitung jumlah siswa per kategori
            if ($kategoriPeserta === 'reguler') {
                $agg[$id]['siswa_reguler']++;
            } else {
                $agg[$id]['siswa_khusus']++;
            }
            
            // HANYA jika siswa menghasilkan poin (Reguler)
            if ($menghasilkanPoin) {
                $agg[$id]['poin'] += 1.0;
            }
        }
    }
    $units = db()->query("SELECT r.id, u.nama_unit FROM recommenders r LEFT JOIN units u ON u.id=r.unit_id")->fetchAll();
    $unitMap = []; foreach ($units as $u) $unitMap[$u['id']] = $u['nama_unit'];
    $out = [];
    foreach ($agg as $a) {
        if ($jenis && $a['jenis'] !== $jenis) continue;
        $fee = fee_dari_poin($a['poin'], $a['jenis'], null, $a['academic_year_id']);
        $a['unit'] = $unitMap[$a['id']] ?? '-';
        $a['kategori'] = $fee['kategori'];
        $a['nominal'] = $fee['nominal'];
        $a['fee'] = $fee['total'];
        $out[] = $a;
    }
    usort($out, fn($x, $y) => $y['poin'] <=> $x['poin']);
    return $out;
}

/** Data poin & fee untuk 1 rekomendator */
function rekap_satu(int $recId, string $jenis, string $where, array $params): array {
    $all = rekap_rekomendator($where, $params, $jenis);
    foreach ($all as $a) if ($a['id'] === $recId) return $a;
    return ['id' => $recId, 'nama' => '', 'jenis' => $jenis, 'siswa' => 0, 'siswa_reguler' => 0, 'siswa_khusus' => 0, 'poin' => 0.0, 'unit' => '-', 'kategori' => '-', 'nominal' => 0.0, 'fee' => 0.0];
}

/** Blok filter standar */
function render_filter_bar(array $g, array $show = ['q','ta','mitra','unit','program','status'], string $action = ''): void {
    $ta = opsi('academic_years', 'nama_tahun_ajaran', '');
    $mitra = opsi('partners', 'nama_mitra', '');
    echo '<form class="card mb-3" method="get" data-noload action="' . e($action) . '"><div class="card-body row g-2 align-items-end">';
    if (in_array('q', $show, true)) {
        echo '<div class="col-md-3"><label class="form-label">Cari Siswa</label>
        <input name="q" class="form-control" placeholder="Nama siswa / kelas / program" value="' . e($g['q'] ?? '') . '"></div>';
    }
    if (in_array('ta', $show, true)) echo '<div class="col-md-2"><label class="form-label">Tahun Ajaran</label><select name="academic_year_id" class="form-select">' . select_options($ta, $g['academic_year_id'] ?? '', 'Semua') . '</select></div>';
    if (in_array('mitra', $show, true)) echo '<div class="col-md-2"><label class="form-label">Mitra</label><select name="partner_id" class="form-select">' . select_options($mitra, $g['partner_id'] ?? '', 'Semua') . '</select></div>';
    if (in_array('unit', $show, true)) {
        echo '<div class="col-md-2"><label class="form-label">Unit</label><select name="unit_id" class="form-select"><option value="">Semua</option>';
        foreach (unit_map() as $id => $nama) {
            echo '<option value="' . $id . '"' . (((string)($g['unit_id'] ?? '') === (string)$id) ? ' selected' : '') . '>' . e($nama) . '</option>';
        }
        echo '</select></div>';
    }
    if (in_array('program', $show, true)) {
        $list = daftar_program_bimbel();
        echo '<div class="col-md-2"><label class="form-label">Program Bimbel</label>
          <input name="program" class="form-control" list="listProgramFilter" placeholder="Semua" value="' . e($g['program'] ?? '') . '">
          <datalist id="listProgramFilter">';
        foreach ($list as $pr) echo '<option value="' . e($pr) . '">';
        echo '</datalist></div>';
    }
    if (in_array('status', $show, true)) {
        echo '<div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua</option>';
        foreach (STATUS_LIST as $k => $lbl) echo '<option value="' . $k . '"' . ((($g['status'] ?? '') === $k) ? ' selected' : '') . '>' . $lbl . '</option>';
        echo '</select></div>';
    }
    if (in_array('kategori_peserta', $show, true)) {
        echo '<div class="col-md-2"><label class="form-label">Kategori Peserta</label><select name="kategori_peserta" class="form-select"><option value="">Semua</option>';
        echo '<option value="reguler"' . ((($g['kategori_peserta'] ?? '') === 'reguler') ? ' selected' : '') . '>Reguler</option>';
        echo '<option value="khusus"' . ((($g['kategori_peserta'] ?? '') === 'khusus') ? ' selected' : '') . '>Kategori Khusus</option>';
        echo '</select></div>';
    }
    if (in_array('wali_kelas', $show, true)) {
        $wk = db()->query("SELECT id, nama FROM recommenders WHERE jenis='wali_kelas' ORDER BY nama")->fetchAll();
        echo '<div class="col-md-2"><label class="form-label">Wali Kelas</label><select name="wali_kelas_id" class="form-select">' . select_options($wk, $g['wali_kelas_id'] ?? '', 'Semua') . '</select></div>';
    }
    if (in_array('wali_santri', $show, true)) {
        $ws = db()->query("SELECT id, nama FROM recommenders WHERE jenis='wali_santri' ORDER BY nama")->fetchAll();
        echo '<div class="col-md-2"><label class="form-label">Wali Santri</label><select name="wali_santri_id" class="form-select">' . select_options($ws, $g['wali_santri_id'] ?? '', 'Semua') . '</select></div>';
    }
    if (in_array('periode', $show, true)) {
        echo '<div class="col-md-2"><label class="form-label">Dari Tanggal</label><input type="date" name="dari" class="form-control" value="' . e($g['dari'] ?? '') . '"></div>';
        echo '<div class="col-md-2"><label class="form-label">Sampai Tanggal</label><input type="date" name="sampai" class="form-control" value="' . e($g['sampai'] ?? '') . '"></div>';
    }
    echo '<div class="col-md-2 d-flex gap-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
      <a href="?" class="btn btn-light" title="Reset"><i class="fa-solid fa-rotate-left"></i></a></div>';
    echo '</div></form>';
}

function pagination(int $total, int $per, int $page, array $g): void {
    $pages = max(1, (int)ceil($total / $per));
    if ($pages <= 1) return;
    unset($g['page']);
    $q = http_build_query($g);
    echo '<nav class="mt-3"><ul class="pagination pagination-sm justify-content-end mb-0">';
    for ($i = 1; $i <= $pages; $i++) {
        if ($i > 1 && $i < $pages && abs($i - $page) > 2) { if ($i === 2 || $i === $pages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>'; continue; }
        echo '<li class="page-item' . ($i === $page ? ' active' : '') . '"><a class="page-link" href="?' . e($q) . '&page=' . $i . '">' . $i . '</a></li>';
    }
    echo '</ul></nav>';
}
