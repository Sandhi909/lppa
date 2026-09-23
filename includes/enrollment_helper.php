<?php
/** Helper untuk manajemen student_enrollments (multi-program support) */

/**
 * Cek apakah siswa sudah punya enrollment di mitra/program tertentu
 */
function hasEnrollment(int $studentId, int $partnerId, ?int $academicYearId, ?string $program = null): bool {
    $sql = "SELECT COUNT(*) FROM student_enrollments 
            WHERE student_id = ? AND partner_id = ? AND status = 'aktif'";
    $params = [$studentId, $partnerId];
    
    if ($academicYearId !== null) {
        $sql .= " AND academic_year_id = ?";
        $params[] = $academicYearId;
    }
    
    if ($program !== null) {
        $sql .= " AND program_bimbel = ?";
        $params[] = $program;
    }
    
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Tambah enrollment baru untuk siswa
 */
function addEnrollment(array $data): int {
    $sql = "INSERT INTO student_enrollments 
            (student_id, partner_id, academic_year_id, unit_id, program_bimbel, 
             biaya_pendaftaran, biaya_program, total_biaya, triwulan, is_psam, 
             status_pembayaran, wali_kelas_id, wali_santri_id, tanggal_enroll, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE, 'aktif')";
    
    $stmt = db()->prepare($sql);
    $stmt->execute([
        $data['student_id'],
        $data['partner_id'],
        $data['academic_year_id'],
        $data['unit_id'],
        $data['program_bimbel'],
        $data['biaya_pendaftaran'],
        $data['biaya_program'],
        $data['total_biaya'],
        $data['triwulan'],
        $data['is_psam'],
        $data['status_pembayaran'],
        $data['wali_kelas_id'],
        $data['wali_santri_id']
    ]);
    
    return (int)db()->lastInsertId();
}

/**
 * Get semua enrollment aktif untuk siswa
 */
function getStudentEnrollments(int $studentId): array {
    $stmt = db()->prepare("SELECT se.*, p.nama_mitra, ay.nama_tahun_ajaran, u.nama_unit
    FROM student_enrollments se
    LEFT JOIN partners p ON se.partner_id = p.id
    LEFT JOIN academic_years ay ON se.academic_year_id = ay.id
    LEFT JOIN units u ON se.unit_id = u.id
    WHERE se.student_id = ? AND se.status = 'aktif'
    ORDER BY se.tanggal_enroll DESC");
    $stmt->execute([$studentId]);
    return $stmt->fetchAll();
}

/**
 * Get enrollment berdasarkan ID
 */
function getEnrollmentById(int $enrollmentId): ?array {
    $stmt = db()->prepare("SELECT * FROM student_enrollments WHERE id = ?");
    $stmt->execute([$enrollmentId]);
    return $stmt->fetch() ?: null;
}

/**
 * Nonaktifkan enrollment
 */
function deactivateEnrollment(int $enrollmentId): bool {
    $stmt = db()->prepare("UPDATE student_enrollments SET status = 'nonaktif' WHERE id = ?");
    return $stmt->execute([$enrollmentId]);
}

/**
 * Cari siswa berdasarkan nama (untuk cek duplikasi)
 */
function findStudentByName(string $nama, ?string $nisNisn = null): ?array {
    if ($nisNisn) {
        $stmt = db()->prepare("SELECT * FROM students WHERE nis_nisn = ? LIMIT 1");
        $stmt->execute([$nisNisn]);
        return $stmt->fetch() ?: null;
    }
    
    // Jika tidak ada NIS, cari berdasarkan nama (fallback)
    $stmt = db()->prepare("SELECT * FROM students WHERE nama_siswa LIKE ? LIMIT 5");
    $stmt->execute(["%$nama%"]);
    return $stmt->fetchAll();
}
