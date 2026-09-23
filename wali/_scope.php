<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['wali_kelas', 'wali_santri']);
require_once __DIR__ . '/../includes/queries.php';

$JENIS = role(); // wali_kelas | wali_santri
$st = db()->prepare('SELECT r.*, u.nama_unit FROM recommenders r LEFT JOIN units u ON u.id=r.unit_id WHERE r.user_id=? AND r.jenis=? LIMIT 1');
$st->execute([user()['id'], $JENIS]);
$REC = $st->fetch();
if (!$REC) die('Akun Anda belum terhubung dengan data rekomendator. Hubungi Admin LPPA.');
$REC_ID = (int)$REC['id'];

$force = $JENIS === 'wali_kelas' ? ['wali_kelas_id' => $REC_ID] : ['wali_santri_id' => $REC_ID];
[$WHERE, $PARAMS] = student_filters($_GET, $force);
$REKAP = rekap_satu($REC_ID, $JENIS, $WHERE, $PARAMS);
$JENIS_LABEL = $JENIS === 'wali_kelas' ? 'Wali Kelas' : 'Wali Santri';

// Get active academic year for filtering
$activeTA = db()->query("SELECT id FROM academic_years WHERE status='aktif' ORDER BY id DESC LIMIT 1")->fetch();
$activeTAId = $activeTA ? (int)$activeTA['id'] : null;

// Set default filter to active academic year if not specified
if (empty($_GET['academic_year_id']) && $activeTAId) {
    $_GET['academic_year_id'] = $activeTAId;
}

// Re-run filters with academic year
[$WHERE, $PARAMS] = student_filters($_GET, $force);
$REKAP = rekap_satu($REC_ID, $JENIS, $WHERE, $PARAMS);

/** poin per siswa untuk rekomendator ini */
function poin_baris(array $r, string $jenis): float {
    $kategoriPeserta = $r['kategori_peserta'] ?? 'reguler';
    
    // Kategori Khusus tidak menghasilkan poin
    if ($kategoriPeserta === 'khusus') {
        return 0.0;
    }
    
    return $jenis === 'wali_kelas'
        ? poin_wali_kelas($r['wali_kelas_id'] ? (int)$r['wali_kelas_id'] : null, $r['wali_santri_id'] ? (int)$r['wali_santri_id'] : null)
        : 1.0;
}
