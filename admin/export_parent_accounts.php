<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);
require_once __DIR__ . '/../includes/queries.php';

// Filter scope for mitra
$partnerId = role() === 'mitra' ? my_partner_id() : null;
if (role() === 'mitra' && !$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get default academic year (most recent)
$defaultTA = db()->query("SELECT id FROM academic_years ORDER BY id DESC LIMIT 1")->fetch();
$defaultTAId = $defaultTA ? (int)$defaultTA['id'] : null;

// Set default filter if not specified
if (empty($_GET['academic_year_id']) && $defaultTAId) {
    $_GET['academic_year_id'] = $defaultTAId;
}

// Get filters
$academicYearId = $_GET['academic_year_id'] ?? '';
$partnerFilterId = $_GET['partner_id'] ?? '';
$unitId = $_GET['unit_id'] ?? '';
$kelas = $_GET['kelas'] ?? '';

// Build query
$where = ["1=1"];
$params = [];

if ($partnerId) {
    $where[] = "s.partner_id = ?";
    $params[] = $partnerId;
}

if ($academicYearId) {
    $where[] = "s.academic_year_id = ?";
    $params[] = $academicYearId;
}

if ($partnerFilterId) {
    $where[] = "s.partner_id = ?";
    $params[] = $partnerFilterId;
}

if ($unitId) {
    $where[] = "s.unit_id = ?";
    $params[] = $unitId;
}

if ($kelas) {
    $where[] = "s.kelas LIKE ?";
    $params[] = "%$kelas%";
}

$whereClause = implode(' AND ', $where);

// Get students with parent accounts
$students = db()->prepare("SELECT s.*, p.nama_mitra, ay.nama_tahun_ajaran, u.nama_unit,
    u_parent.username, u_parent.nama as nama_orang_tua, u_parent.status as user_status
FROM students s
LEFT JOIN partners p ON s.partner_id = p.id
LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
LEFT JOIN units u ON s.unit_id = u.id
LEFT JOIN siswa_orang_tua sot ON s.id = sot.student_id
LEFT JOIN users u_parent ON sot.user_id = u_parent.id
WHERE $whereClause
ORDER BY p.nama_mitra, s.kelas, s.nama_siswa ASC");
$students->execute($params);
$students = $students->fetchAll();

// Get filter options
$academicYears = db()->query("SELECT * FROM academic_years WHERE status = 'aktif' ORDER BY nama_tahun_ajaran DESC")->fetchAll();
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();
$units = db()->query("SELECT * FROM units WHERE status = 'aktif' ORDER BY nama_unit")->fetchAll();

$PAGE_TITLE = 'Export Akun Orang Tua';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Export Akun Orang Tua']];
include __DIR__ . '/../includes/header.php';
?>

<style>
.print-only { display: none; }
.print-sheet { background:#fff; }
@media print {
    @page { size: A4 landscape; margin: 8mm 8mm 10mm 8mm; }
    html, body { background:#fff !important; font-family: 'Inter','Segoe UI',Arial,sans-serif; font-size:8pt; color:#1e293b; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .app-wrapper, .app-main, .app-content, .container-fluid { margin:0 !important; padding:0 !important; max-width:none !important; }
    .page-head, .breadcrumb, .sidebar, .navbar, footer, .app-footer { display:none !important; }
    .no-print { display:none !important; }
    .print-only { display:block !important; }
    .card { border:none !important; box-shadow:none !important; border-radius:0 !important; }
    .card-body { padding:0 !important; }
    .print-sheet { border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; }
    .print-header { display:flex; align-items:center; gap:14px; border-bottom:3px solid #0f2a4d; padding:10px 14px 9px; background:#f8fafc; }
    .print-header .ph-logo img { height:36px; width:auto; object-fit:contain; }
    .print-header .ph-title { flex:1; }
    .print-header .ph-title h2 { font-size:12.5pt; font-weight:800; color:#0f2a4d; margin:0; line-height:1.1; letter-spacing:.1px; }
    .print-header .ph-title .ph-sub { font-size:7pt; color:#64748b; margin-top:2px; font-weight:500; letter-spacing:.2px; text-transform:uppercase; }
    .print-header .ph-datebox { text-align:right; font-size:6.8pt; color:#475569; line-height:1.35; }
    .print-header .ph-datebox strong { color:#0f2a4d; }
    .print-meta { display:flex; gap:6px; flex-wrap:wrap; padding:7px 12px; background:#fff; border-bottom:1px solid #e2e8f0; font-size:7pt; }
    .print-meta .pm-chip { background:#f1f5f9; border:1px solid #e2e8f0; border-radius:4px; padding:3px 7px; color:#334155; }
    .print-meta .pm-chip b { color:#0f2a4d; font-weight:700; }
    .table-wrap, .table-responsive { overflow:visible !important; display:block !important; }
    .table { font-size:8pt; border-collapse:collapse; width:100% !important; table-layout:fixed !important; }
    .table th { background:#0f2a4d !important; color:#fff !important; font-weight:700; text-transform:uppercase; letter-spacing:.35px; font-size:6.6pt; padding:7px 5px; border:1px solid #0f2a4d; white-space:nowrap; }
    .table td { padding:6px 5px; border:1px solid #e2e8f0; vertical-align:middle; line-height:1.35; word-break:normal; overflow-wrap:break-word; }
    .table thead { display:table-header-group; }
    .table tfoot { display:table-footer-group; }
    .table tbody tr { page-break-inside:avoid; }
    .table tbody tr:nth-child(even) { background:#f8fafc !important; }
    .table tbody td:first-child { text-align:center; color:#64748b; font-weight:600; }
    .table .td-nama { font-weight:700; color:#0f172a; font-size:8.8pt; white-space:normal; line-height:1.35; min-width:190px; }
    .table .td-mono { font-family:Consolas,'Courier New',monospace; font-size:7pt; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 5px; border-radius:3px; display:inline-block; min-width:62px; text-align:center; }
    .table .td-muted { color:#94a3b8; font-style:italic; font-size:7pt; }
    .badge-print { display:inline-block; font-size:6.5pt; font-weight:700; letter-spacing:.2px; padding:2px 7px; border-radius:999px; border:1px solid #cbd5e1; color:#334155; background:#f8fafc; text-transform:uppercase; }
    .badge-print.is-aktif { border-color:#16a34a; color:#15803d; background:#f0fdf4; }
    .print-footer { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; padding:8px 12px 10px; font-size:6.5pt; color:#64748b; border-top:1px solid #e2e8f0; background:#f8fafc; }
    .print-footer .pf-left { line-height:1.4; }
    .print-footer .pf-right { text-align:right; white-space:nowrap; }
    .print-sign { display:flex; justify-content:flex-end; padding:18px 14px 8px; gap:48px; font-size:7.5pt; color:#334155; }
    .print-sign .sig-box { text-align:center; min-width:150px; }
    .print-sign .sig-line { margin-top:44px; border-top:1px solid #334155; padding-top:4px; font-weight:600; }
}
@media screen {
    .print-sheet { border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
}
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3>Export Akun Orang Tua</h3>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Cetak PDF
            </button>
            <a href="<?= base_url('admin/absensi.php') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($academicYears as $ay): ?>
                            <option value="<?= $ay['id'] ?>" <?= $academicYearId == $ay['id'] ? 'selected' : '' ?>>
                                <?= e($ay['nama_tahun_ajaran']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (role() === 'admin'): ?>
                <div class="col-md-2">
                    <label class="form-label">Mitra</label>
                    <select name="partner_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($partners as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $partnerFilterId == $p['id'] ? 'selected' : '' ?>>
                                <?= e($p['nama_mitra']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-2">
                    <label class="form-label">Unit</label>
                    <select name="unit_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $unitId == $u['id'] ? 'selected' : '' ?>>
                                <?= e($u['nama_unit']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-control" value="<?= e($kelas) ?>" placeholder="Cari kelas...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Print Header -->
    <?php
    $phTA = 'Semua'; if ($academicYearId) { foreach($academicYears as $ay) if((int)$ay['id']==(int)$academicYearId){ $phTA=e($ay['nama_tahun_ajaran']); break; } }
    $phMitra = 'Semua'; if ($partnerFilterId) { foreach($partners as $p) if((int)$p['id']==(int)$partnerFilterId){ $phMitra=e($p['nama_mitra']); break; } }
    $phUnit = 'Semua'; if ($unitId) { foreach($units as $u) if((int)$u['id']==(int)$unitId){ $phUnit=e($u['nama_unit']); break; } }
    ?>
    <div class="print-only print-header">
        <div class="ph-logo"><img src="<?= base_url('assets/images/LPPA.png') ?>" alt="LPPA"></div>
        <div class="ph-title">
            <h2>Daftar Akun Orang Tua</h2>
            <div class="ph-sub">Lembaga Pengembangan Potensi Akademik &bull; Sistem Informasi LPPA Bimbel</div>
        </div>
        <div class="ph-datebox">
            <div><strong>Tanggal Cetak</strong><br><?= date('d/m/Y H:i') ?> WIB</div>
            <div style="margin-top:4px">Oleh: <?= e($_SESSION['user']['nama'] ?? '-') ?></div>
        </div>
    </div>
    <div class="print-only print-meta">
        <span class="pm-chip"><b>Tahun Ajaran:</b> <?= $phTA ?></span>
        <?php if(role()==='admin'): ?><span class="pm-chip"><b>Mitra:</b> <?= $phMitra ?></span><?php endif; ?>
        <span class="pm-chip"><b>Unit:</b> <?= $phUnit ?></span>
        <?php if($kelas): ?><span class="pm-chip"><b>Kelas:</b> <?= e($kelas) ?></span><?php endif; ?>
        <span class="pm-chip"><b>Total:</b> <?= count($students) ?> siswa</span>
        <?php if($partnerId): ?><span class="pm-chip"><b>Scope Mitra:</b> <?= e($namaMitra ?? '') ?></span><?php endif; ?>
    </div>

    <!-- Accounts Table -->
    <div class="card print-sheet">
        <div class="card-body">
            <?php if (empty($students)): ?>
                <div class="alert alert-warning no-print">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    Tidak ada data siswa yang ditemukan dengan filter ini.
                </div>
            <?php else: ?>
                <div class="alert alert-info no-print">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Total <strong><?= count($students) ?></strong> siswa ditampilkan. Klik "Cetak PDF" untuk mencetak atau simpan sebagai PDF.
                </div>

                <div class="table-responsive table-wrap">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="no-print" style="width:28px">No</th>
                                <th style="width:34% !important; min-width:190px">Nama Siswa</th>
                                <th style="width:5% !important">Kelas</th>
                                <th style="width:10% !important">Program</th>
                                <th style="width:5% !important">Unit</th>
                                <th style="width:10% !important">Mitra</th>
                                <th style="width:6% !important">TA</th>
                                <th style="width:9% !important">Username</th>
                                <th style="width:6% !important">Password</th>
                                <th style="width:7% !important">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $s): ?>
                                <tr>
                                    <td class="no-print"><?= $index + 1 ?></td>
                                    <td class="td-nama"><?= e($s['nama_siswa']) ?></td>
                                    <td style="text-align:center"><?= e($s['kelas'] ?: '-') ?></td>
                                    <td><?= e($s['program_bimbel'] ?: '-') ?></td>
                                    <td style="text-align:center"><?= e($s['nama_unit'] ?: '-') ?></td>
                                    <td><?= e($s['nama_mitra'] ?: '-') ?></td>
                                    <td style="text-align:center"><?= e($s['nama_tahun_ajaran'] ?: '-') ?></td>
                                    <td style="text-align:center">
                                        <?php if ($s['username']): ?>
                                            <span class="td-mono"><?= e($s['username']) ?></span>
                                        <?php else: ?>
                                            <span class="td-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center">
                                        <?php if ($s['username']): ?>
                                            <span class="td-mono">12345</span>
                                        <?php else: ?>
                                            <span class="td-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center">
                                        <?php if ($s['username']): ?>
                                            <span class="badge-print is-aktif">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge-print">Belum Ada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-3 no-print">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <strong>Catatan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Password default untuk semua akun adalah <strong>12345</strong></li>
                        <li>Siswa yang belum memiliki akun orang tua akan ditampilkan dengan status "Belum Ada"</li>
                        <li>Orang tua dapat mengubah password setelah login pertama kali</li>
                        <li>Gunakan tombol "Cetak PDF" lalu pilih "Save as PDF" di dialog print browser</li>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="print-only print-footer">
                <div class="pf-left">
                    <div>Password default <b>12345</b> • Harap ganti setelah login pertama • Dokumen rahasia</div>
                    <div style="margin-top:2px">Dicetak otomatis oleh sistem LPPA — <?= date('d F Y, H:i') ?> WIB</div>
                </div>
                <div class="pf-right">
                    <div>Halaman cetak landscape A4</div>
                    <div>© <?= date('Y') ?> LPPA</div>
                </div>
            </div>
            <div class="print-only print-sign">
                <div class="sig-box">
                    <div>Mengetahui,</div>
                    <div class="sig-line">Admin LPPA</div>
                </div>
                <div class="sig-box">
                    <div>Petugas Cetak</div>
                    <div class="sig-line"><?= e($_SESSION['user']['nama'] ?? '—') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
