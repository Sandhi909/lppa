<?php
/** Logika CRUD siswa yang dipakai Admin & Mitra. $SCOPE_PARTNER = kunci mitra (null = admin) */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/queries.php';
require_once __DIR__ . '/parent_account_helper.php';

$SCOPE_PARTNER = $SCOPE_PARTNER ?? null;
$SELF = basename($_SERVER['PHP_SELF']);

function student_scope_check(array $s, ?int $scope): void {
    if ($scope && (int)$s['partner_id'] !== $scope) { http_response_code(403); die('Akses ditolak.'); }
}

/**
 * Pencegah data siswa dobel (pemeriksaan PHP sebelum INSERT/UPDATE).
 * Kombinasi identitas: Mitra + Tahun Ajaran + Unit + Nama Siswa + Kelas.
 */
function cari_siswa_duplikat(int $partnerId, $taId, $unitId, string $nama, string $kelas, int $excludeId = 0): ?array {
    $sql = 'SELECT * FROM students WHERE partner_id = ?
              AND (academic_year_id <=> ?) AND (unit_id <=> ?)
              AND LOWER(TRIM(nama_siswa)) = LOWER(TRIM(?)) AND LOWER(TRIM(COALESCE(kelas,""))) = LOWER(TRIM(?))';
    $p = [$partnerId, $taId ?: null, $unitId ?: null, $nama, $kelas];
    if ($excludeId) { $sql .= ' AND id <> ?'; $p[] = $excludeId; }
    $st = db()->prepare($sql . ' LIMIT 1');
    $st->execute($p);
    $r = $st->fetch();
    return $r ?: null;
}

/* ------------------------------------------------------------------
 * SIMPAN (tambah / edit) data siswa
 * ------------------------------------------------------------------ */
// Skip logic CRUD jika ini adalah halaman import
$isImportPage = basename($_SERVER['PHP_SELF']) === 'import.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan' && !$isImportPage) {
    csrf_check();
    $id      = (int)($_POST['id'] ?? 0);
    $partner = $SCOPE_PARTNER ?: (int)($_POST['partner_id'] ?? 0);
    $nama    = trim($_POST['nama_siswa'] ?? '');
    $kelas   = trim($_POST['kelas'] ?? '');
    $tgl     = $_POST['tanggal_pendaftaran'] ?: date('Y-m-d');
    $taId    = $_POST['academic_year_id'] !== '' ? (int)$_POST['academic_year_id'] : null;
    $unitId  = $_POST['unit_id'] !== '' ? (int)$_POST['unit_id'] : null;
    $program = trim($_POST['program_bimbel'] ?? '');
    $biayaPendaftaran = (float)($_POST['biaya_pendaftaran'] ?? 0);
    $biayaProgram = (float)($_POST['biaya_program'] ?? 0);
    $totalBiaya = $biayaPendaftaran + $biayaProgram;
    $triwulan = $_POST['triwulan'] !== '' ? $_POST['triwulan'] : null;
    $isPsam = $_POST['is_psam'] ?? 'Tidak';
    $kategoriPeserta = $_POST['kategori_peserta'] ?? 'reguler';
    $status  = status_normal($_POST['status_pembayaran'] ?? 'angsuran');
    $wkId    = $_POST['wali_kelas_id'] !== '' ? (int)$_POST['wali_kelas_id'] : null;
    $wsInput = $_POST['wali_santri_id'] !== '' ? (int)$_POST['wali_santri_id'] : null;

    $errors = [];
    if ($nama === '') $errors[] = 'Nama siswa wajib diisi.';
    if (!$partner) $errors[] = 'Mitra wajib dipilih.';
    if (!$unitId) $errors[] = 'Unit wajib dipilih.';
    if (!$taId) $errors[] = 'Tahun ajaran wajib dipilih.';
    $errors = array_merge($errors, validasi_biaya($biayaPendaftaran, $biayaProgram), validasi_wali_santri($isPsam, $wsInput));
    $wsId = wali_santri_tersimpan($isPsam, $wsInput);

    // Pemeriksaan duplikasi sebelum INSERT/UPDATE
    if (!$errors && $partner && $nama !== '') {
        $dup = cari_siswa_duplikat($partner, $taId, $unitId, $nama, $kelas, $id);
        if ($dup) {
            $_SESSION['duplikat_siswa'] = ['id' => (int)$dup['id'], 'nama' => $dup['nama_siswa']];
            $_SESSION['form_old'] = $_POST;
            flash('warning', 'Data siswa ini sudah terdaftar.');
            redirect($SELF . ($id ? '?aksi=edit&id=' . $id : '?aksi=tambah'));
        }
    }
    if ($errors) {
        $_SESSION['form_old'] = $_POST;
        flash('danger', implode(' ', $errors));
        redirect($SELF . ($id ? '?aksi=edit&id=' . $id : '?aksi=tambah'));
    }

    $data = [
        'partner_id' => $partner,
        'academic_year_id' => $taId,
        'unit_id' => $unitId,
        'tanggal_pendaftaran' => $tgl,
        'nama_siswa' => $nama,
        'kelas' => $kelas,
        'program_bimbel' => $program,
        'kategori_peserta' => $kategoriPeserta,
        'biaya_pendaftaran' => $biayaPendaftaran,
        'biaya_program' => $biayaProgram,
        'total_biaya' => $totalBiaya,
        'triwulan' => $triwulan,
        'is_psam' => $isPsam,
        'status_pembayaran' => $status,
        'wali_kelas_id' => $wkId,
        'wali_santri_id' => $wsId,
        'keterangan' => trim($_POST['keterangan'] ?? ''),
    ];
    try {
        if ($id) {
            $old = db()->prepare('SELECT * FROM students WHERE id=?'); $old->execute([$id]); $old = $old->fetch();
            if (!$old) { flash('danger', 'Data siswa tidak ditemukan.'); redirect($SELF); }
            student_scope_check($old, $SCOPE_PARTNER);
            $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
            db()->prepare("UPDATE students SET $set WHERE id = :id")->execute($data + ['id' => $id]);
            audit('Mengubah data siswa: ' . $nama, 'students', $id, $old, $data);
            flash('success', 'Data siswa berhasil diperbarui. Status: ' . status_label($status) . '.');
        } else {
            $cols = implode(', ', array_keys($data));
            $ph   = ':' . implode(', :', array_keys($data));
            db()->prepare("INSERT INTO students ($cols) VALUES ($ph)")->execute($data);
            $newStudentId = (int)db()->lastInsertId();
            audit('Menambah data siswa: ' . $nama, 'students', $newStudentId, null, $data);

            // Auto create parent account
            $parentResult = createParentAccount($newStudentId, $nama);
            if ($parentResult['success']) {
                if ($parentResult['existing']) {
                    flash('success', 'Data siswa berhasil ditambahkan. Status: ' . status_label($status) . '. Akun orang tua dihubungkan ke akun yang sudah ada.');
                } else {
                    flash('success', 'Data siswa berhasil ditambahkan. Status: ' . status_label($status) . '. Akun orang tua otomatis dibuat. Username: ' . $parentResult['username'] . ', Password: 12345');
                }
            } else {
                flash('success', 'Data siswa berhasil ditambahkan. Status: ' . status_label($status) . '. Akun orang tua: ' . $parentResult['message']);
            }
        }
    } catch (PDOException $ex) {
        if ($ex->getCode() === '23000') {
            flash('warning', 'Data siswa ini sudah terdaftar.');
            redirect($SELF);
        }
        throw $ex;
    }
    unset($_SESSION['form_old'], $_SESSION['duplikat_siswa']);
    redirect($SELF);
}

/* ------------------------------------------------------------------
 * HAPUS
 * ------------------------------------------------------------------ */
if (($_GET['aksi'] ?? '') === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    $st = db()->prepare('SELECT * FROM students WHERE id=?'); $st->execute([$id]); $s = $st->fetch();
    if ($s) {
        student_scope_check($s, $SCOPE_PARTNER);
        if ((int)$s['academic_year_id'] !== tahun_ajaran_id_terbaru()) {
            flash('danger', 'Siswa pada tahun ajaran terdahulu tidak dapat dihapus. Hanya siswa tahun ajaran terbaru (' . tahun_ajaran_nama_terbaru() . ') yang bisa dihapus.');
        } else {
            db()->prepare('DELETE FROM students WHERE id=?')->execute([$id]);
            audit('Menghapus data siswa: ' . $s['nama_siswa'], 'students', $id, $s, null);
            flash('success', 'Data siswa berhasil dihapus.');
        }
    } else flash('danger', 'Data tidak ditemukan.');
    redirect($SELF);
}

/* ------------------------------------------------------------------
 * CREATE PARENT ACCOUNT
 * ------------------------------------------------------------------ */
if (($_GET['aksi'] ?? '') === 'create_parent') {
    $id = (int)($_GET['id'] ?? 0);
    $st = db()->prepare('SELECT * FROM students WHERE id=?'); $st->execute([$id]); $s = $st->fetch();
    if ($s) {
        student_scope_check($s, $SCOPE_PARTNER);
        if (hasParentAccount($id)) {
            flash('warning', 'Siswa ini sudah memiliki akun orang tua.');
        } else {
            $parentResult = createParentAccount($id, $s['nama_siswa']);
            if ($parentResult['success']) {
                flash('success', $parentResult['message']);
            } else {
                flash('danger', $parentResult['message']);
            }
        }
    } else flash('danger', 'Data siswa tidak ditemukan.');
    redirect($SELF . '?aksi=detail&id=' . $id);
}
