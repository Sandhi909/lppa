<?php
/** Logika CRUD siswa dengan support multi-program */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/queries.php';
require_once __DIR__ . '/parent_account_helper.php';
require_once __DIR__ . '/enrollment_helper.php';

$SCOPE_PARTNER = $SCOPE_PARTNER ?? null;
$SELF = basename($_SERVER['PHP_SELF']);

function student_scope_check(array $s, ?int $scope): void {
    if ($scope && (int)$s['partner_id'] !== $scope) { http_response_code(403); die('Akses ditolak.'); }
}

/* ------------------------------------------------------------------
 * SIMPAN (tambah / edit) data siswa dengan multi-program support
 * ------------------------------------------------------------------ */
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
    $status  = status_normal($_POST['status_pembayaran'] ?? 'angsuran');
    $wkId    = $_POST['wali_kelas_id'] !== '' ? (int)$_POST['wali_kelas_id'] : null;
    $wsInput = $_POST['wali_santri_id'] !== '' ? (int)$_POST['wali_santri_id'] : null;
    $nisNisn = trim($_POST['nis_nisn'] ?? '');

    $errors = [];
    if ($nama === '') $errors[] = 'Nama siswa wajib diisi.';
    if (!$partner) $errors[] = 'Mitra wajib dipilih.';
    if (!$unitId) $errors[] = 'Unit wajib dipilih.';
    $errors = array_merge($errors, validasi_biaya($biayaPendaftaran, $biayaProgram), validasi_wali_santri($isPsam, $wsInput));
    $wsId = wali_santri_tersimpan($isPsam, $wsInput);

    if ($errors) {
        $_SESSION['form_old'] = $_POST;
        flash('danger', implode(' ', $errors));
        redirect($SELF . ($id ? '?aksi=edit&id=' . $id : '?aksi=tambah'));
    }

    try {
        if ($id) {
            // UPDATE EXISTING STUDENT
            $old = db()->prepare('SELECT * FROM students WHERE id=?'); $old->execute([$id]); $old = $old->fetch();
            if (!$old) { flash('danger', 'Data siswa tidak ditemukan.'); redirect($SELF); }
            student_scope_check($old, $SCOPE_PARTNER);
            
            $data = [
                'nama_siswa' => $nama,
                'kelas' => $kelas,
                'nis_nisn' => $nisNisn,
                'keterangan' => trim($_POST['keterangan'] ?? ''),
            ];
            
            $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
            db()->prepare("UPDATE students SET $set WHERE id = :id")->execute($data + ['id' => $id]);
            
            // Update enrollment jika ada enrollment aktif untuk partner ini
            $enrollment = db()->prepare("SELECT * FROM student_enrollments 
                                      WHERE student_id = ? AND partner_id = ? AND status = 'aktif'");
            $enrollment->execute([$id, $partner]);
            $enrollment = $enrollment->fetch();
            
            if ($enrollment) {
                $enrollData = [
                    'academic_year_id' => $taId,
                    'unit_id' => $unitId,
                    'program_bimbel' => $program,
                    'biaya_pendaftaran' => $biayaPendaftaran,
                    'biaya_program' => $biayaProgram,
                    'total_biaya' => $totalBiaya,
                    'triwulan' => $triwulan,
                    'is_psam' => $isPsam,
                    'status_pembayaran' => $status,
                    'wali_kelas_id' => $wkId,
                    'wali_santri_id' => $wsId,
                ];
                
                $enrollSet = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($enrollData)));
                db()->prepare("UPDATE student_enrollments SET $enrollSet WHERE id = :id")
                    ->execute($enrollData + ['id' => $enrollment['id']]);
            }
            
            audit('Mengubah data siswa: ' . $nama, 'students', $id, $old, $data);
            flash('success', 'Data siswa berhasil diperbarui.');
            
        } else {
            // ADD NEW STUDENT OR ADD ENROLLMENT TO EXISTING STUDENT
            $existingStudent = findStudentByName($nama, $nisNisn);
            
            if ($existingStudent) {
                // Siswa sudah ada, tambahkan enrollment baru
                $studentId = (int)$existingStudent['id'];
                
                // Cek apakah sudah ada enrollment di partner/program yang sama
                if (hasEnrollment($studentId, $partner, $taId, $program)) {
                    flash('warning', 'Siswa sudah terdaftar di program ini. Gunakan menu Edit untuk memperbarui data.');
                    redirect($SELF);
                }
                
                // Tambah enrollment baru
                $enrollmentData = [
                    'student_id' => $studentId,
                    'partner_id' => $partner,
                    'academic_year_id' => $taId,
                    'unit_id' => $unitId,
                    'program_bimbel' => $program,
                    'biaya_pendaftaran' => $biayaPendaftaran,
                    'biaya_program' => $biayaProgram,
                    'total_biaya' => $totalBiaya,
                    'triwulan' => $triwulan,
                    'is_psam' => $isPsam,
                    'status_pembayaran' => $status,
                    'wali_kelas_id' => $wkId,
                    'wali_santri_id' => $wsId,
                ];
                
                $enrollmentId = addEnrollment($enrollmentData);
                audit('Menambah enrollment untuk siswa: ' . $nama, 'student_enrollments', $enrollmentId);
                flash('success', 'Enrollment berhasil ditambahkan untuk siswa yang sudah terdaftar.');
                
            } else {
                // Siswa baru, buat siswa + enrollment + akun orang tua
                $studentData = [
                    'nama_siswa' => $nama,
                    'kelas' => $kelas,
                    'nis_nisn' => $nisNisn,
                    'keterangan' => trim($_POST['keterangan'] ?? ''),
                ];
                
                $cols = implode(', ', array_keys($studentData));
                $ph   = ':' . implode(', :', array_keys($studentData));
                db()->prepare("INSERT INTO students ($cols) VALUES ($ph)")->execute($studentData);
                $newStudentId = (int)db()->lastInsertId();
                
                // Tambah enrollment
                $enrollmentData = [
                    'student_id' => $newStudentId,
                    'partner_id' => $partner,
                    'academic_year_id' => $taId,
                    'unit_id' => $unitId,
                    'program_bimbel' => $program,
                    'biaya_pendaftaran' => $biayaPendaftaran,
                    'biaya_program' => $biayaProgram,
                    'total_biaya' => $totalBiaya,
                    'triwulan' => $triwulan,
                    'is_psam' => $isPsam,
                    'status_pembayaran' => $status,
                    'wali_kelas_id' => $wkId,
                    'wali_santri_id' => $wsId,
                ];
                
                $enrollmentId = addEnrollment($enrollmentData);

                // Buat akun orang tua hanya jika tahun ajaran aktif
                $parentResult = null;
                if ($taId) {
                    $activeCheck = db()->prepare("SELECT status FROM academic_years WHERE id = ?");
                    $activeCheck->execute([$taId]);
                    $taStatus = $activeCheck->fetchColumn();

                    if ($taStatus === 'aktif') {
                        $parentResult = createParentAccount($newStudentId, $nama);
                    }
                }

                audit('Menambah data siswa baru: ' . $nama, 'students', $newStudentId);

                if ($parentResult && $parentResult['success']) {
                    flash('success', 'Data siswa baru berhasil ditambahkan. ' . $parentResult['message']);
                } else {
                    flash('success', 'Data siswa baru berhasil ditambahkan.' . ($taId ? ' (Tahun ajaran tidak aktif, akun orang tua tidak dibuat otomatis.)' : ''));
                }
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
        
        // Hapus semua enrollment siswa
        db()->prepare('DELETE FROM student_enrollments WHERE student_id=?')->execute([$id]);
        
        // Hapus siswa
        db()->prepare('DELETE FROM students WHERE id=?')->execute([$id]);
        audit('Menghapus data siswa: ' . $s['nama_siswa'], 'students', $id, $s, null);
        flash('success', 'Data siswa berhasil dihapus.');
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
