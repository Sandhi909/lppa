<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);
require_once __DIR__ . '/../includes/queries.php';

$SCOPE_PARTNER = role() === 'mitra' ? my_partner_id() : null;
if (role() === 'mitra' && !$SCOPE_PARTNER) die('Akun mitra belum terhubung dengan data Mitra.');
// Default filter tahun ajaran ke yang paling terbaru (id DESC) jika belum dipilih
if (!isset($_GET['academic_year_id'])) {
    $latestAy = db()->query('SELECT id FROM academic_years ORDER BY id DESC LIMIT 1')->fetchColumn();
    if ($latestAy) $_GET['academic_year_id'] = $latestAy;
}
$force = $SCOPE_PARTNER ? ['partner_id' => $SCOPE_PARTNER] : [];
[$WHERE, $PARAMS] = student_filters($_GET, $force);

$JENIS_LAPORAN = [
  'siswa'        => 'Data Seluruh Siswa',
  'mitra'        => 'Rekap per Mitra (Peserta, Biaya)',
  'unit'         => 'Rekap per Unit',
  'tahun_ajaran' => 'Rekap per Tahun Ajaran',
  'angsuran'     => 'Siswa Status Angsuran',
  'lunas'        => 'Siswa Status Lunas',
  'rekap'        => 'Rekap Seluruh Rekomendator',
  'wali_kelas'   => 'Rekap Wali Kelas',
  'wali_santri'  => 'Rekap Wali Santri',
];
$jl = $_GET['jenis_laporan'] ?? 'siswa';
if (!isset($JENIS_LAPORAN[$jl])) $jl = 'siswa';

/** Bangun data laporan: [header[], rows[][], money[]] */
function build_report(string $jl, string $where, array $params): array {
    switch ($jl) {
        case 'angsuran': case 'lunas':
            $where .= ' AND s.status_pembayaran = ?'; $params[] = $jl;
            // lanjut ke daftar siswa
        case 'siswa':
            $rows = students_list($where, $params);
            $out = [];
            foreach ($rows as $i => $r) $out[] = [
                $i + 1, tgl_id($r['tanggal_pendaftaran']), $r['nama_siswa'], $r['nama_mitra'], $r['nama_tahun_ajaran'] ?: '-',
                $r['nama_unit'] ?: '-', $r['kelas'] ?: '-', $r['program_bimbel'] ?: '-',
                $r['triwulan'] ?: '-', $r['is_psam'] ?: '-',
                (float)$r['biaya_pendaftaran'], (float)$r['biaya_program'], (float)$r['total_biaya'],
                status_label($r['status_pembayaran']),
                $r['wali_kelas_nama'] ?: '-', $r['wali_santri_nama'] ?: '-',
            ];
            return ['header' => ['No','Tanggal','Nama Siswa','Mitra','Tahun Ajaran','Unit','Kelas','Program Bimbel','Triwulan','PSAM',
                'Biaya Pendaftaran','Biaya Program','Total Biaya','Status','Wali Kelas','Wali Santri'],
                'rows' => $out, 'money' => [10,11,12]];
        case 'mitra':
            // Rekap per Mitra: setiap Mitra memakai persentase bagi hasilnya sendiri + pajak per tahun ajaran
            $g = rekap_per_mitra($where, $params);
            $out = [];
            $academicYearId = null;
            // Extract academic_year_id from params if present
            if (strpos($where, 's.academic_year_id = ?') !== false) {
                $ayParams = array_values(array_filter($params, function($p) { return is_numeric($p) && $p > 0; }));
                $academicYearId = $ayParams[0] ?? null;
            }
            foreach ($g as $i => $r) {
                $bagiHasil = (float)$r['total_biaya'] * (float)$r['persentase_bagi_hasil'] / 100;
                $setoranBersih = hitung_total_setoran($bagiHasil, $r['id'], $academicYearId);
                $pajak = $bagiHasil - $setoranBersih;
                $out[] = [$i + 1, $r['nama_mitra'],
                    persen_fmt($r['persentase_bagi_hasil']) . '%', (int)$r['jml'],
                    (float)$r['biaya_pendaftaran'], (float)$r['biaya_program'], (float)$r['total_biaya'],
                    $bagiHasil, $pajak, $setoranBersih];
            }
            return ['header' => ['No', 'Mitra', 'Persentase Bagi Hasil', 'Total Data Siswa',
                'Biaya Pendaftaran', 'Biaya Program', 'Total Biaya', 'Bagi Hasil Kotor', 'Pajak', 'Setoran Bersih'],
                'rows' => $out, 'money' => [4,5,6,7,8,9]];
        case 'unit': case 'tahun_ajaran':
            $cfg = [
              'unit' => ['u.id', 'u.nama_unit', 'LEFT JOIN units u ON u.id=s.unit_id', 'Unit'],
              'tahun_ajaran' => ['a.id', 'a.nama_tahun_ajaran', 'LEFT JOIN academic_years a ON a.id=s.academic_year_id', 'Tahun Ajaran'],
            ][$jl];
            $g = group_sum($where, $params, $cfg[0], $cfg[1], $cfg[2]);
            $out = [];
            foreach ($g as $i => $r) $out[] = [$i + 1, $r['label'] ?: '-', (int)$r['jml'],
                (float)$r['biaya_pendaftaran'], (float)$r['biaya_program'], (float)$r['total_biaya']];
            return ['header' => ['No', $cfg[3], 'Jumlah Siswa', 'Biaya Pendaftaran', 'Biaya Program', 'Total Biaya'],
                'rows' => $out, 'money' => [3,4,5]];
        default:
            $jenis = $jl === 'wali_kelas' ? 'wali_kelas' : ($jl === 'wali_santri' ? 'wali_santri' : '');
            $rk = rekap_rekomendator($where, $params, $jenis);
            $out = [];
            foreach ($rk as $i => $r) {
                // Get academic year name
                $ayName = '';
                if ($r['academic_year_id']) {
                    $ay = db()->prepare('SELECT nama_tahun_ajaran FROM academic_years WHERE id = ?');
                    $ay->execute([$r['academic_year_id']]);
                    $ayName = $ay->fetchColumn() ?: '-';
                } else {
                    $ayName = '-';
                }
                
                $out[] = [$i + 1, $r['nama'],
                    $r['jenis'] === 'wali_kelas' ? 'Wali Kelas' : 'Wali Santri', $ayName, $r['unit'],
                    $r['siswa'], $r['poin'], $r['kategori'], (float)($r['nominal'] ?? 0), (float)$r['fee']];
            }
            return ['header' => ['No','Nama Rekomendator','Jenis','Tahun Ajaran','Unit','Jumlah Siswa','Total Poin','Kategori','Nominal Fee per Poin','Total Fee'],
                'rows' => $out, 'money' => [6,8,9]];
    }
}
