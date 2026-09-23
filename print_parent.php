<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['admin', 'mitra']);
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/parent_account_helper.php';
require_once __DIR__ . '/config/app.php';

$aksi = $_GET['aksi'] ?? '';
if ($aksi !== 'print_parent') {
    die('Akses tidak valid.');
}

$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT * FROM students WHERE id=?');
$st->execute([$id]);
$student = $st->fetch();

if (!$student) {
    die('Data siswa tidak ditemukan.');
}

// Check scope for mitra
if (role() === 'mitra') {
    $partnerId = my_partner_id();
    if (!$partnerId || (int)$student['partner_id'] !== $partnerId) {
        die('Akses ditolak.');
    }
}

$parentAccount = getParentAccount($id);
if (!$parentAccount) {
    die('Siswa ini belum memiliki akun orang tua.');
}

// Get partner info
$partner = db()->prepare('SELECT * FROM partners WHERE id=?');
$partner->execute([$student['partner_id']]);
$partner = $partner->fetch();

// Get academic year info
$ta = db()->prepare('SELECT * FROM academic_years WHERE id=?');
$ta->execute([$student['academic_year_id']]);
$ta = $ta->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Akun Orang Tua - <?= htmlspecialchars($student['nama_siswa']) ?></title>
    <style>
        @media print {
            body { font-family: Arial, sans-serif; }
            .no-print { display: none !important; }
            .print-container { 
                border: 2px solid #000; 
                padding: 20px; 
                max-width: 600px; 
                margin: 20px auto;
            }
            .header { 
                text-align: center; 
                border-bottom: 2px solid #000; 
                padding-bottom: 15px; 
                margin-bottom: 20px;
            }
            .logo { text-align:center; margin-bottom:4px; }
            .logo img { max-height:60px; width:auto; }
            .subtitle { font-size: 14px; color: #666; }
            .credentials { 
                background: #f0f0f0; 
                padding: 15px; 
                border: 1px solid #000; 
                margin: 20px 0;
            }
            .credential-item { 
                margin: 10px 0; 
                font-size: 16px;
            }
            .credential-label { 
                font-weight: bold; 
                display: inline-block; 
                width: 120px;
            }
            .credential-value { 
                font-family: monospace; 
                font-size: 18px; 
                font-weight: bold;
            }
            .footer { 
                text-align: center; 
                margin-top: 30px; 
                font-size: 12px; 
                color: #666;
            }
        }
        @media screen {
            body { 
                font-family: Arial, sans-serif; 
                background: #f5f5f5; 
                padding: 20px;
            }
            .print-container { 
                background: white; 
                border: 1px solid #ddd; 
                padding: 20px; 
                max-width: 600px; 
                margin: 20px auto;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .header { 
                text-align: center; 
                border-bottom: 2px solid #333; 
                padding-bottom: 15px; 
                margin-bottom: 20px;
            }
            .logo { font-size: 24px; font-weight: bold; color: #1e6fd9; }
            .logo img { max-height:70px; width:auto; }
            .subtitle { font-size: 14px; color: #666; }
            .credentials { 
                background: #e8f4e8; 
                padding: 15px; 
                border: 1px solid #4caf50; 
                margin: 20px 0;
                border-radius: 5px;
            }
            .credential-item { 
                margin: 10px 0; 
                font-size: 16px;
            }
            .credential-label { 
                font-weight: bold; 
                display: inline-block; 
                width: 120px;
            }
            .credential-value { 
                font-family: monospace; 
                font-size: 18px; 
                font-weight: bold;
                color: #1e6fd9;
            }
            .footer { 
                text-align: center; 
                margin-top: 30px; 
                font-size: 12px; 
                color: #666;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">
            <div class="logo"><img src="<?= base_url('assets/images/LPPA.png') ?>" alt="LPPA Logo"></div>
            <div class="subtitle">Lembaga Pengembangan Potensi Akademik</div>
        </div>
        
        <h3 style="text-align: center; margin: 20px 0;">Informasi Akun Orang Tua</h3>
        
        <div style="margin: 20px 0;">
            <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($student['nama_siswa']) ?></p>
            <p><strong>Kelas:</strong> <?= htmlspecialchars($student['kelas']) ?></p>
            <p><strong>Mitra:</strong> <?= htmlspecialchars($partner['nama_mitra'] ?? '-') ?></p>
            <p><strong>Tahun Ajaran:</strong> <?= htmlspecialchars($ta['nama_tahun_ajaran'] ?? '-') ?></p>
        </div>
        
        <div class="credentials">
            <h4 style="margin-top: 0; text-align: center;">Login Credential</h4>
            <div class="credential-item">
                <span class="credential-label">Username:</span>
                <span class="credential-value"><?= htmlspecialchars($parentAccount['username']) ?></span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Password:</span>
                <span class="credential-value">12345</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Login URL:</span>
                <span class="credential-value" style="font-size: 14px;"><?= base_url('auth/login.php') ?></span>
            </div>
        </div>
        
        <div style="margin: 20px 0; font-size: 14px;">
            <p><strong>Catatan:</strong></p>
            <ul>
                <li>Password default adalah <strong>12345</strong></li>
                <li>Silakan ganti password setelah login pertama untuk keamanan</li>
                <li>Dengan akun ini, orang tua dapat memantau absensi dan pencapaian anak</li>
                <li>Jika lupa password, hubungi admin/mitra untuk reset</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
            <p>© <?= date('Y') ?> <?= e(COPYRIGHT_HOLDER) ?></p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            <i class="fa-solid fa-print"></i> Cetak Halaman Ini
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>
</body>
</html>
