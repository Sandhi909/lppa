<?php
/**
 * Helper & query modul Setoran Bagi Hasil.
 * Tidak mengubah tabel siswa maupun transaksi lama.
 */
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

const SETORAN_STATUS = [
    'pending'    => 'Pending',
    'disetujui'  => 'Disetujui',
    'ditolak'    => 'Ditolak',
];

const SETORAN_BUKTI_EXT = ['jpg', 'jpeg', 'png', 'pdf'];
const SETORAN_BUKTI_MAX = 2 * 1024 * 1024; // 2 MB

function setoran_status_label(?string $s): string {
    $k = strtolower(trim((string)$s));
    return SETORAN_STATUS[$k] ?? $s ?? '-';
}

function badge_setoran_status(string $s): string {
    $map = ['pending' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger'];
    $k = strtolower($s);
    return '<span class="badge bg-' . ($map[$k] ?? 'secondary') . '-soft">' . e(setoran_status_label($k)) . '</span>';
}

/** Tagihan bagi hasil Yayasan = total biaya siswa x persentase mitra */
function tagihan_bagi_hasil_mitra(int $partnerId, ?int $academicYearId = null): float {
    $sql = 'SELECT COALESCE(SUM(s.total_biaya * COALESCE(p.persentase_bagi_hasil, 0) / 100), 0)
            FROM students s
            JOIN partners p ON p.id = s.partner_id
            WHERE s.partner_id = ?';
    $params = [$partnerId];
    
    if ($academicYearId) {
        $sql .= ' AND s.academic_year_id = ?';
        $params[] = $academicYearId;
    }
    
    $st = db()->prepare($sql);
    $st->execute($params);
    $bagiHasil = (float)$st->fetchColumn();
    
    // Apply tax deduction if enabled
    return hitung_total_setoran($bagiHasil, $partnerId, $academicYearId);
}

/** Total setoran yang sudah disetujui */
function total_setoran_disetujui(int $partnerId, ?int $academicYearId = null): float {
    $st = db()->prepare(
        "SELECT COALESCE(SUM(nominal), 0) FROM setoran_bagi_hasil
         WHERE partner_id = ? AND status = 'disetujui'" . ($academicYearId ? ' AND academic_year_id = ?' : '') . ""
    );
    if ($academicYearId) {
        $st->execute([$partnerId, $academicYearId]);
    } else {
        $st->execute([$partnerId]);
    }
    return (float)$st->fetchColumn();
}

/** Sisa setoran = tagihan bagi hasil (net) - total setoran disetujui */
function sisa_setoran_mitra(int $partnerId, ?int $academicYearId = null): float {
    $tagihan = tagihan_bagi_hasil_mitra($partnerId, $academicYearId);
    $disetujui = total_setoran_disetujui($partnerId, $academicYearId);
    $sisa = $tagihan - $disetujui;
    return max(0, $sisa); // Tidak boleh negatif
}

/** Get tax info for display (bagi hasil kotor, pajak, total bersih) */
function info_pajak_mitra(int $partnerId, string $where = '1=1', array $params = [], ?int $academicYearId = null): array {
    // If academicYearId filter provided, add to where clause
    $sqlWhere = $where;
    if ($academicYearId) {
        $sqlWhere .= ' AND s.academic_year_id = ?';
        $params[] = $academicYearId;
    }
    
    $sql = 'SELECT COALESCE(SUM(s.total_biaya * COALESCE(p.persentase_bagi_hasil, 0) / 100), 0) as bagi_hasil_kotor
         FROM students s
         JOIN partners p ON p.id = s.partner_id
         WHERE s.partner_id = ? AND ' . $sqlWhere;
    $st = db()->prepare($sql);
    $st->execute(array_merge([$partnerId], $params));
    $bagiHasilKotor = (float)$st->fetchColumn();
    
    $tax = get_tax_config($partnerId, $academicYearId);
    $pajak = 0;
    $pajakMethod = $tax['method'];
    
    if ($bagiHasilKotor <= 0) {
        $pajak = 0;
    } elseif ($tax['enabled']) {
        if ($tax['method'] === 'percentage') {
            $pajak = $bagiHasilKotor * $tax['percentage'] / 100;
        } elseif ($tax['method'] === 'manual') {
            $pajak = min($tax['manual_nominal'], $bagiHasilKotor);
        }
    }
    
    return [
        'bagi_hasil_kotor' => $bagiHasilKotor,
        'pajak_method' => $pajakMethod,
        'pajak_percentage' => $tax['percentage'],
        'pajak_nominal' => $pajak,
        'total_bersih' => $bagiHasilKotor - $pajak,
    ];
}

function ringkasan_setoran_mitra(int $partnerId, ?int $academicYearId = null): array {
    $whereClause = 's.partner_id = ?';
    $params = [$partnerId];
    
    if ($academicYearId) {
        $whereClause .= ' AND s.academic_year_id = ?';
        $params[] = $academicYearId;
    }
    
    // Hitung tagihan (kotor -> potong pajak jadi bersih)
    $st = db()->prepare(
        "SELECT COALESCE(SUM(s.total_biaya * COALESCE(p.persentase_bagi_hasil, 0) / 100), 0)
         FROM students s
         JOIN partners p ON p.id = s.partner_id
         WHERE $whereClause"
    );
    $st->execute($params);
    $tagihanKotor = (float)$st->fetchColumn();
    $tagihan = hitung_total_setoran($tagihanKotor, $partnerId, $academicYearId);
    
    // Hitung total disetujui
    $st = db()->prepare(
        "SELECT COALESCE(SUM(nominal), 0) FROM setoran_bagi_hasil
         WHERE partner_id = ? AND status = 'disetujui'"
        . ($academicYearId ? " AND academic_year_id = ?" : "")
    );
    $disetujuiParams = [$partnerId];
    if ($academicYearId) {
        $disetujuiParams[] = $academicYearId;
    }
    $st->execute($disetujuiParams);
    $disetujui = (float)$st->fetchColumn();
    
    // Hitung sisa
    $sisa = $tagihan - $disetujui;
    
    return [
        'tagihan'   => $tagihan,
        'disetujui' => $disetujui,
        'sisa'      => max(0, $sisa),
    ];
}

const SETORAN_SELECT = "SELECT sb.*, p.nama_mitra, ay.nama_tahun_ajaran,
        uc.nama AS created_nama, ur.nama AS reviewed_nama
    FROM setoran_bagi_hasil sb
    JOIN partners p ON p.id = sb.partner_id
    LEFT JOIN academic_years ay ON ay.id = sb.academic_year_id
    LEFT JOIN users uc ON uc.id = sb.created_by
    LEFT JOIN users ur ON ur.id = sb.reviewed_by";

function setoran_by_id(int $id): ?array {
    $st = db()->prepare(SETORAN_SELECT . ' WHERE sb.id = ?');
    $st->execute([$id]);
    $r = $st->fetch();
    return $r ?: null;
}

function setoran_list(array $filters = [], int $limit = 0, int $offset = 0): array {
    [$where, $params] = setoran_filters($filters);
    $sql = SETORAN_SELECT . " WHERE $where ORDER BY sb.tanggal_setoran DESC, sb.id DESC";
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
    }
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function setoran_count(array $filters = []): int {
    [$where, $params] = setoran_filters($filters);
    $st = db()->prepare("SELECT COUNT(*) FROM setoran_bagi_hasil sb JOIN partners p ON p.id = sb.partner_id WHERE $where");
    $st->execute($params);
    return (int)$st->fetchColumn();
}

function setoran_filters(array $g): array {
    $w = ['1=1'];
    $p = [];
    if (!empty($g['partner_id'])) {
        $w[] = 'sb.partner_id = ?';
        $p[] = (int)$g['partner_id'];
    }
    if (!empty($g['academic_year_id'])) {
        $w[] = 'sb.academic_year_id = ?';
        $p[] = (int)$g['academic_year_id'];
    }
    if (!empty($g['status']) && isset(SETORAN_STATUS[$g['status']])) {
        $w[] = 'sb.status = ?';
        $p[] = $g['status'];
    }
    if (!empty($g['dari'])) {
        $w[] = 'sb.tanggal_setoran >= ?';
        $p[] = $g['dari'];
    }
    if (!empty($g['sampai'])) {
        $w[] = 'sb.tanggal_setoran <= ?';
        $p[] = $g['sampai'];
    }
    return [implode(' AND ', $w), $p];
}

function setoran_upload_dir(): string {
    $dir = __DIR__ . '/../uploads/setoran';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (is_dir($dir)) {
        @chmod($dir, 0777);
    }
    clearstatcache(true, $dir);
    return $dir;
}

/** Direktori fallback bila uploads/setoran tidak writable (biasanya permission di server shared hosting). */
function setoran_upload_dir_fallback(): string {
    $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . '/lppa_setoran';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (is_dir($dir)) {
        @chmod($dir, 0777);
    }
    clearstatcache(true, $dir);
    return $dir;
}

/** Unggah bukti transfer. Mengembalikan nama file relatif atau null + pesan error via reference. */
function setoran_unggah_bukti(array $file, ?string $existing = null): ?string {
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Gagal mengunggah bukti transfer.');
    }
    if (($file['size'] ?? 0) > SETORAN_BUKTI_MAX) {
        throw new RuntimeException('Ukuran bukti transfer maksimal 2 MB.');
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, SETORAN_BUKTI_EXT, true)) {
        throw new RuntimeException('Bukti transfer harus berformat JPG, PNG, atau PDF.');
    }

    $destDir = setoran_upload_dir();
    $writableDir = is_writable($destDir) ? $destDir : setoran_upload_dir_fallback();

    $nama = 'setoran_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = $writableDir . '/' . $nama;

    error_clear_last();
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $err = error_get_last();
        throw new RuntimeException('Gagal menyimpan bukti transfer: ' . ($err['message'] ?? 'permission atau path tidak valid.'));
    }

    if ($existing) {
        $oldPrimary = setoran_upload_dir() . '/' . basename($existing);
        $oldFallback = setoran_upload_dir_fallback() . '/' . basename($existing);
        foreach ([$oldPrimary, $oldFallback] as $old) {
            if (is_file($old)) {
                @unlink($old);
            }
        }
    }
    return $nama;
}

function setoran_bukti_path(string $file): string {
    $primary = setoran_upload_dir() . '/' . basename($file);
    if (is_file($primary)) {
        return $primary;
    }
    return setoran_upload_dir_fallback() . '/' . basename($file);
}

function setoran_bukti_url(string $self, int $id): string {
    return $self . '?aksi=bukti&id=' . $id;
}

function setoran_kirim_bukti(int $id, ?int $scopePartner = null): void {
    $row = setoran_by_id($id);
    if (!$row || empty($row['bukti_file'])) {
        http_response_code(404);
        die('Bukti transfer tidak ditemukan.');
    }
    if ($scopePartner !== null && (int)$row['partner_id'] !== $scopePartner) {
        http_response_code(403);
        die('Akses ditolak.');
    }
    $path = setoran_bukti_path($row['bukti_file']);
    if (!is_file($path)) {
        http_response_code(404);
        die('Berkas bukti tidak ditemukan di server.');
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mime = match ($ext) {
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        default => 'application/octet-stream',
    };
    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . basename($path) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

function setoran_validasi_nominal(float $nominal): array {
    $err = [];
    if ($nominal <= 0) {
        $err[] = 'Nominal setoran harus lebih besar dari Rp0.';
    }
    return $err;
}

function setoran_insert(array $data): int {
    $cols = implode(', ', array_keys($data));
    $ph = ':' . implode(', :', array_keys($data));
    db()->prepare("INSERT INTO setoran_bagi_hasil ($cols) VALUES ($ph)")->execute($data);
    return (int)db()->lastInsertId();
}

function setoran_update(int $id, array $data): int {
    $cols = [];
    $params = [];
    foreach ($data as $k => $v) {
        $cols[] = $k . ' = ?';
        $params[] = $v;
    }
    $params[] = $id;
    db()->prepare('UPDATE setoran_bagi_hasil SET ' . implode(', ', $cols) . ' WHERE id = ?')->execute($params);
    return (int)db()->lastInsertId(); // no-op for update; returns 0
}

function setoran_delete(int $id): bool {
    $row = setoran_by_id($id);
    if (!$row) {
        return false;
    }
    if (!empty($row['bukti_file'])) {
        foreach ([setoran_upload_dir() . '/' . basename($row['bukti_file']), setoran_upload_dir_fallback() . '/' . basename($row['bukti_file'])] as $old) {
            if (is_file($old)) {
                @unlink($old);
            }
        }
    }
    db()->prepare('DELETE FROM setoran_bagi_hasil WHERE id = ?')->execute([$id]);
    return true;
}

function setoran_update_status(int $id, string $status, ?string $catatan = null): void {
    db()->prepare(
        'UPDATE setoran_bagi_hasil SET status = ?, reviewed_by = ?, reviewed_at = NOW(), catatan_review = ? WHERE id = ?'
    )->execute([$status, $_SESSION['user']['id'] ?? null, $catatan, $id]);
}
