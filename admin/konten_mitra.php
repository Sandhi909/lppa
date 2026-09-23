<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$partnerId = role() === 'mitra' ? my_partner_id() : (int)($_GET['partner_id'] ?? 0);
if (role() === 'mitra' && !$partnerId) {
    die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
}

// Get partners for admin
$partners = db()->query("SELECT * FROM partners WHERE status = 'aktif' ORDER BY nama_mitra")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $partnerIdInput = role() === 'mitra' ? $partnerId : (int)($_POST['partner_id'] ?? 0);
    $deskripsiPublic = trim($_POST['deskripsi_public'] ?? '');
    $keunggulanPublic = trim($_POST['keunggulan_public'] ?? '');
    $videoUrl = trim($_POST['video_url'] ?? '');
    $videoPlatform = $_POST['video_platform'] ?? 'youtube';
    
    if (!$partnerIdInput) {
        flash('danger', 'Mitra wajib dipilih.');
    } else {
        try {
            $stmt = db()->prepare("UPDATE partners SET deskripsi_public = ?, keunggulan_public = ?, video_url = ?, video_platform = ? WHERE id = ?");
            $stmt->execute([$deskripsiPublic, $keunggulanPublic, $videoUrl, $videoPlatform, $partnerIdInput]);
            
            audit('Mengubah konten public mitra', 'partners', $partnerIdInput);
            flash('success', 'Konten public berhasil diperbarui.');
        } catch (Exception $e) {
            flash('danger', 'Gagal memperbarui konten public: ' . $e->getMessage());
        }
    }
    
    if (role() === 'admin' && $partnerIdInput) {
        redirect(base_url('admin/konten_mitra.php?partner_id=' . $partnerIdInput));
    } else {
        redirect(base_url('admin/konten_mitra.php'));
    }
}

// Get current partner data
$partnerData = null;
if ($partnerId) {
    $partnerData = db()->prepare("SELECT * FROM partners WHERE id = ?");
    $partnerData->execute([$partnerId]);
    $partnerData = $partnerData->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Konten Public Mitra</h3>
        <a href="<?= base_url('admin/partners.php') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <?= csrf_field() ?>
                
                <?php if (role() === 'admin'): ?>
                <div class="mb-3">
                    <label class="form-label">Mitra <span class="text-danger">*</span></label>
                    <select name="partner_id" class="form-select" required id="partnerSelect" onchange="loadPartnerData()">
                        <option value="">Pilih Mitra</option>
                        <?php foreach ($partners as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $partnerId == $p['id'] ? 'selected' : '' ?>>
                                <?= e($p['nama_mitra']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <input type="hidden" name="partner_id" value="<?= $partnerId ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi Public</label>
                    <textarea name="deskripsi_public" class="form-control" rows="5" id="deskripsiPublic"><?= e($partnerData['deskripsi_public'] ?? '') ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Keunggulan</label>
                    <textarea name="keunggulan_public" class="form-control" rows="5" id="keunggulanPublic"><?= e($partnerData['keunggulan_public'] ?? '') ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">URL Video</label>
                            <input type="text" name="video_url" class="form-control" id="videoUrl" value="<?= e($partnerData['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Platform Video</label>
                            <select name="video_platform" class="form-select" id="videoPlatform">
                                <option value="youtube" <?= ($partnerData['video_platform'] ?? 'youtube') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                <option value="drive" <?= ($partnerData['video_platform'] ?? 'youtube') === 'drive' ? 'selected' : '' ?>>Google Drive</option>
                                <option value="other" <?= ($partnerData['video_platform'] ?? 'youtube') === 'other' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Preview Video</label>
                    <div id="videoPreview" class="ratio ratio-16x9 bg-light">
                        <div class="d-flex align-items-center justify-content-center text-muted">
                            <i class="fa-solid fa-video fa-2x me-2"></i>
                            <span>Preview akan muncul di sini</span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function loadPartnerData() {
    const partnerId = document.getElementById('partnerSelect').value;
    if (!partnerId) return;
    
    fetch('<?= base_url('admin/api_partner_data.php') ?>?id=' + partnerId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('deskripsiPublic').value = data.deskripsi_public || '';
            document.getElementById('keunggulanPublic').value = data.keunggulan_public || '';
            document.getElementById('videoUrl').value = data.video_url || '';
            document.getElementById('videoPlatform').value = data.video_platform || 'youtube';
            updateVideoPreview();
        })
        .catch(error => console.error('Error:', error));
}

function updateVideoPreview() {
    const url = document.getElementById('videoUrl').value;
    const platform = document.getElementById('videoPlatform').value;
    const preview = document.getElementById('videoPreview');
    
    if (!url) {
        preview.innerHTML = '<div class="d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-video fa-2x me-2"></i><span>Preview akan muncul di sini</span></div>';
        return;
    }
    
    let embedUrl = '';
    if (platform === 'youtube') {
        const videoId = extractYouTubeId(url);
        if (videoId) {
            embedUrl = 'https://www.youtube.com/embed/' + videoId;
        }
    } else if (platform === 'drive') {
        const fileId = extractDriveId(url);
        if (fileId) {
            embedUrl = 'https://drive.google.com/file/d/' + fileId + '/preview';
        }
    }
    
    if (embedUrl) {
        preview.innerHTML = '<iframe src="' + embedUrl + '" frameborder="0" allowfullscreen></iframe>';
    } else {
        preview.innerHTML = '<div class="d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-exclamation-triangle fa-2x me-2"></i><span>URL tidak valid untuk platform ini</span></div>';
    }
}

function extractYouTubeId(url) {
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const match = url.match(regex);
    return match ? match[1] : null;
}

function extractDriveId(url) {
    const regex = /drive\.google\.com\/file\/d\/([^\/]+)/;
    const match = url.match(regex);
    return match ? match[1] : null;
}

document.getElementById('videoUrl').addEventListener('input', updateVideoPreview);
document.getElementById('videoPlatform').addEventListener('change', updateVideoPreview);

<?php if (role() === 'mitra' && $partnerData): ?>
updateVideoPreview();
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
