<?php
/**
 * Helper untuk pembuatan akun orang tua otomatis
 * Mendukung multi-program: 1 anak = 1 akun orang tua (meskipun ikut banyak mitra)
 * @param int $studentId ID siswa
 * @param string $studentName Nama siswa
 * @return array [success: bool, message: string, userId: int|null, username: string|null, password: string|null, existing: bool]
 */
function createParentAccount(int $studentId, string $studentName): array {
    // Cek apakah sudah ada
    if (hasParentAccount($studentId)) {
        return [
            'success' => false,
            'message' => 'Akun orang tua sudah ada untuk siswa ini',
            'userId' => null,
            'username' => null,
            'password' => null,
            'existing' => false
        ];
    }

    try {
        db()->beginTransaction();

        // Cek apakah ada siswa dengan nama sama yang sudah punya parent account
        // Ini untuk mendukung 1 anak = 1 akun orang tua meskipun ikut banyak mitra
        $namaSiswaClean = strtolower(trim($studentName));
        $dupCheck = db()->prepare("SELECT id, nama_siswa FROM students WHERE LOWER(TRIM(nama_siswa)) = ?");
        $dupCheck->execute([$namaSiswaClean]);
        $duplicates = $dupCheck->fetchAll();

        $existingParent = null;
        if ($duplicates) {
            foreach ($duplicates as $dup) {
                $parent = getParentAccount($dup['id']);
                if ($parent) {
                    $existingParent = $parent;
                    break;
                }
            }
        }

        // Jika ada parent account dari duplikat, gunakan parent account yang sama
        if ($existingParent) {
            // Hubungkan student_id ini ke user_id yang sudah ada
            $linkStmt = db()->prepare("INSERT INTO siswa_orang_tua (student_id, user_id, hubungan) VALUES (?, ?, 'Orang Tua')");
            $linkStmt->execute([$studentId, $existingParent['id']]);

            db()->commit();
            return [
                'success' => true,
                'message' => 'Siswa dihubungkan ke akun orang tua yang sudah ada (kemungkinan anak yang sama di mitra berbeda)',
                'userId' => $existingParent['id'],
                'username' => $existingParent['username'],
                'password' => null, // Password tidak ditampilkan karena sudah ada
                'existing' => true
            ];
        }

        // Generate username dari nama siswa
        // 1. Hapus karakter khusus kecuali huruf, angka, dan spasi
        $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $studentName);

        // 2. Trim spasi berlebihan dan ganti spasi dengan titik
        $cleanName = trim($cleanName);
        $cleanName = preg_replace('/\s+/', '.', $cleanName);

        // 3. Convert ke lowercase
        $baseUsername = strtolower($cleanName);

        // 4. Hapus titik berlebihan
        $baseUsername = preg_replace('/\.+/', '.', $baseUsername);
        $baseUsername = trim($baseUsername, '.');

        // 5. Jika kosong atau hanya titik, gunakan default
        if (empty($baseUsername) || $baseUsername === '.') {
            $baseUsername = 'orangtua' . $studentId;
        }

        // 6. Batasi panjang username (max 30 karakter)
        if (strlen($baseUsername) > 30) {
            $baseUsername = substr($baseUsername, 0, 30);
        }

        $username = $baseUsername;
        $counter = 1;

        // Cek jika username sudah ada
        while (true) {
            $check = db()->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);
            if (!$check->fetch()) {
                break;
            }
            $username = $baseUsername . $counter;
            $counter++;
        }

        // Generate password default
        $defaultPassword = '12345';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        // Insert user baru
        $stmt = db()->prepare("INSERT INTO users (nama, username, email, password, role, status) VALUES (?, ?, ?, ?, 'orang_tua', 'aktif')");
        $email = $username . '@lppa.local'; // Email placeholder
        $stmt->execute([$studentName, $username, $email, $hashedPassword]);
        $userId = db()->lastInsertId();

        // Hubungkan user dengan siswa
        $linkStmt = db()->prepare("INSERT INTO siswa_orang_tua (student_id, user_id, hubungan) VALUES (?, ?, 'Orang Tua')");
        $linkStmt->execute([$studentId, $userId]);

        db()->commit();

        return [
            'success' => true,
            'message' => "Akun orang tua berhasil dibuat. Username: $username, Password: $defaultPassword",
            'userId' => $userId,
            'username' => $username,
            'password' => $defaultPassword,
            'existing' => false
        ];
    } catch (Exception $e) {
        db()->rollBack();
        return [
            'success' => false,
            'message' => "Gagal membuat akun orang tua: " . $e->getMessage(),
            'userId' => null,
            'username' => null,
            'password' => null,
            'existing' => false
        ];
    }
}

/**
 * Cek apakah siswa sudah punya akun orang tua
 * @param int $studentId ID siswa
 * @return bool
 */
function hasParentAccount(int $studentId): bool {
    try {
        $check = db()->prepare("SELECT COUNT(*) FROM siswa_orang_tua WHERE student_id = ?");
        $check->execute([$studentId]);
        $count = $check->fetchColumn();
        return $count !== false && (int)$count > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get parent account data untuk siswa
 * @param int $studentId ID siswa
 * @return array|null
 */
function getParentAccount(int $studentId): ?array {
    try {
        $stmt = db()->prepare("SELECT u.*, sot.hubungan FROM users u
        JOIN siswa_orang_tua sot ON u.id = sot.user_id
        WHERE sot.student_id = ?");
        $stmt->execute([$studentId]);
        $result = $stmt->fetch();
        return $result ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Update session user dengan student_id untuk role orang_tua
 * @param int $userId ID user
 */
function updateParentSession(int $userId): void {
    $stmt = db()->prepare("SELECT student_id FROM siswa_orang_tua WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();

    if ($result && isset($result['student_id'])) {
        $_SESSION['user']['student_id'] = $result['student_id'];
    }
}

/**
 * Ambil semua student_id yang terhubung ke parent account yang sama
 * Digunakan untuk menampilkan semua enrollment anak yang sama di mitra berbeda
 * @param int $userId ID user
 * @return array Array of student_id
 */
function getLinkedStudentIds(int $userId): array {
    $stmt = db()->prepare("SELECT student_id FROM siswa_orang_tua WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
