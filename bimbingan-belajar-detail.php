<?php
require_once __DIR__ . '/config/database.php';

// Helper function untuk base_url
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
}

// Get partner ID
$partnerId = (int)($_GET['id'] ?? 0);

if ($partnerId === 0) {
    header('Location: mitra-bimbel');
    exit;
}

// Get lembaga data
$lembaga = db()->prepare(
    "SELECT p.*, 
            CASE 
                WHEN p.public_logo IS NOT NULL AND p.public_logo != '' THEN p.public_logo
                ELSE NULL
            END as logo_url,
            CASE 
                WHEN p.public_cover IS NOT NULL AND p.public_cover != '' THEN p.public_cover
                ELSE NULL
            END as cover_url
     FROM partners p 
     WHERE p.id = ? AND p.is_public = 1 AND p.status = 'aktif'"
);
$lembaga->execute([$partnerId]);
$lembaga = $lembaga->fetch();

if (!$lembaga) {
    header('Location: bimbingan-belajar');
    exit;
}

// Get lembaga PDFs
$pdfs = db()->prepare(
    "SELECT * FROM partner_programs_pdfs 
     WHERE partner_id = ? AND is_public = 1 
     ORDER BY uploaded_at DESC"
);
$pdfs->execute([$partnerId]);
$pdfs = $pdfs->fetchAll();

// SEO & Open Graph
$pageTitle = $lembaga['nama_mitra'] . " - Program Bimbingan Belajar LPPA";
$pageDescription = $lembaga['public_description'] ?? "Program bimbingan belajar berkualitas dari " . $lembaga['nama_mitra'];
$pageImage = !empty($lembaga['logo_url']) ? base_url('uploads/partners/' . $lembaga['logo_url']) : base_url('assets/images/LPPA.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDescription ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDescription ?>">
    <meta property="og:image" content="<?= $pageImage ?>">
    <meta property="og:url" content="<?= base_url('bimbingan-belajar-detail.php?id=' . $partnerId) ?>">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDescription ?>">
    <meta name="twitter:image" content="<?= $pageImage ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e6fd9;
            --secondary: #0f2f5f;
            --accent: #16a34a;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: #f8f9fa;
        }
        
        /* Header */
        .partner-header {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: white;
            padding: 60px 0;
            position: relative;
            overflow: hidden;
        }
        
        .partner-header::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }
        
        .partner-header::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }
        
        .partner-cover {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        
        .partner-logo-large {
            max-width: 250px;
            max-height: 200px;
            object-fit: contain;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin: 0 auto 30px;
        }
        
        .partner-name {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .partner-tagline {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Content Section */
        .content-section {
            padding: 60px 0;
        }
        
        .section-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary);
            display: inline-block;
        }
        
        .partner-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }
        
        /* Programs */
        .program-highlight {
            background: linear-gradient(135deg, rgba(22,163,74,0.1), rgba(22,163,74,0.05));
            border: 2px solid rgba(22,163,74,0.2);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        
        .program-highlight-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .program-highlight-content {
            font-size: 1.1rem;
            color: #555;
        }
        
        /* PDF Section */
        .pdf-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }
        
        .pdf-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        
        .pdf-description {
            color: #666;
            margin-bottom: 20px;
        }
        
        .pdf-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-view-pdf {
            background: var(--primary);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-view-pdf:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-download-pdf {
            background: var(--accent);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-download-pdf:hover {
            background: #15803d;
            color: white;
            transform: translateY(-2px);
        }
        
        .no-pdf {
            text-align: center;
            padding: 40px;
            background: var(--light);
            border-radius: 15px;
            color: #666;
        }
        
        .no-pdf i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        /* Gallery */
        .gallery-item {
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
        }
        
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        /* Contact Info */
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: var(--light);
            border-radius: 10px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .contact-label {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .contact-value {
            color: #555;
        }
        
        /* Back Button */
        .btn-back {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .partner-header {
                padding: 40px 0;
            }
            
            .partner-name {
                font-size: 1.8rem;
            }
            
            .partner-tagline {
                font-size: 1rem;
            }
            
            .section-card {
                padding: 25px;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .pdf-actions {
                flex-direction: column;
            }
            
            .btn-view-pdf,
            .btn-download-pdf {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <section class="partner-header">
        <div class="container">
            <a href="bimbingan-belajar" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Program Bimbingan Belajar
            </a>
            
            <div class="text-center">
                <?php if (!empty($lembaga['cover_url'])): ?>
                    <img src="<?= base_url('uploads/partners/' . $lembaga['cover_url']) ?>" 
                         alt="<?= htmlspecialchars($lembaga['nama_mitra']) ?> Cover" 
                         class="partner-cover"
                         onerror="this.style.display='none'">
                <?php endif; ?>
                
                <?php if (!empty($lembaga['logo_url'])): ?>
                    <img src="<?= base_url('uploads/partners/' . $lembaga['logo_url']) ?>" 
                         alt="<?= htmlspecialchars($lembaga['nama_mitra']) ?>" 
                         class="partner-logo-large"
                         onerror="this.style.display='none'">
                <?php else: ?>
                    <div class="partner-logo-large d-flex align-items-center justify-content-center">
                        <i class="fas fa-building fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <h1 class="partner-name"><?= htmlspecialchars($lembaga['nama_mitra']) ?></h1>
                
                <?php if (!empty($lembaga['public_description'])): ?>
                    <p class="partner-tagline"><?= htmlspecialchars($lembaga['public_description']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($lembaga['public_levels'])): ?>
                    <div class="mt-4">
                        <?php 
                        $levels = explode(',', $lembaga['public_levels']);
                        foreach ($levels as $level): 
                            $level = trim($level);
                            if (!empty($level)):
                        ?>
                            <span class="badge bg-light text-dark fs-6 me-2"><?= htmlspecialchars($level) ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Content -->
    <section class="content-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Description -->
                    <?php if (!empty($lembaga['deskripsi_public'])): ?>
                    <div class="section-card">
                        <h2 class="section-title">Tentang Kami</h2>
                        <div class="partner-description">
                            <?= nl2br(htmlspecialchars($lembaga['deskripsi_public'])) ?>
                        </div>
                    </div>
                    <?php elseif (!empty($lembaga['public_description'])): ?>
                    <div class="section-card">
                        <h2 class="section-title">Tentang Kami</h2>
                        <div class="partner-description">
                            <?= nl2br(htmlspecialchars($lembaga['public_description'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Program Unggulan -->
                    <?php if (!empty($lembaga['public_program_unggulan'])): ?>
                    <div class="section-card">
                        <h2 class="section-title">Program Unggulan</h2>
                        <div class="program-highlight">
                            <div class="program-highlight-title">
                                <i class="fas fa-star"></i>
                                Program Unggulan
                            </div>
                            <div class="program-highlight-content">
                                <?= htmlspecialchars($lembaga['public_program_unggulan']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Keunggulan -->
                    <?php if (!empty($lembaga['keunggulan_public'])): ?>
                    <div class="section-card">
                        <h2 class="section-title">Keunggulan</h2>
                        <p class="partner-description">
                            <?= nl2br(htmlspecialchars($lembaga['keunggulan_public'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Video -->
                    <?php if (!empty($lembaga['video_url'])): ?>
                    <div class="section-card">
                        <h2 class="section-title">Video Profil</h2>
                        <div class="ratio ratio-16x9">
                            <?php if ($lembaga['video_platform'] === 'youtube'): ?>
                                <?php
                                $videoId = '';
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $lembaga['video_url'], $matches)) {
                                    $videoId = $matches[1];
                                }
                                ?>
                                <?php if ($videoId): ?>
                                    <iframe src="https://www.youtube.com/embed/<?= $videoId ?>" frameborder="0" allowfullscreen></iframe>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($lembaga['video_url']) ?>" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt"></i> Tonton Video
                                    </a>
                                <?php endif; ?>
                            <?php elseif ($lembaga['video_platform'] === 'drive'): ?>
                                <?php
                                $fileId = '';
                                if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/', $lembaga['video_url'], $matches)) {
                                    $fileId = $matches[1];
                                }
                                ?>
                                <?php if ($fileId): ?>
                                    <iframe src="https://drive.google.com/file/d/<?= $fileId ?>/preview" frameborder="0" allowfullscreen></iframe>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($lembaga['video_url']) ?>" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt"></i> Tonton Video
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars($lembaga['video_url']) ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Tonton Video
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Galeri -->
                    <?php
                    $gallery = db()->prepare("SELECT * FROM galeri_mitra WHERE partner_id = ? AND is_aktif = 1 ORDER BY urutan ASC");
                    $gallery->execute([$partnerId]);
                    $gallery = $gallery->fetchAll();
                    ?>
                    <?php if (!empty($gallery)): ?>
                    <div class="section-card">
                        <h2 class="section-title">Galeri</h2>
                        <div class="row g-3">
                            <?php foreach ($gallery as $photo): ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="gallery-item">
                                        <img src="<?= base_url('uploads/galeri/' . $photo['file_gambar']) ?>" 
                                             alt="<?= htmlspecialchars($photo['judul'] ?? 'Foto') ?>" 
                                             class="img-fluid rounded"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal" 
                                             data-bs-image="<?= base_url('uploads/galeri/' . $photo['file_gambar']) ?>"
                                             data-bs-title="<?= htmlspecialchars($photo['judul'] ?? 'Foto') ?>">
                                        <?php if ($photo['judul']): ?>
                                            <p class="small text-muted mt-2"><?= htmlspecialchars($photo['judul']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Programs -->
                    <?php if (!empty($lembaga['public_programs'])): ?>
                    <div class="section-card">
                        <h2 class="section-title">Program Bimbel</h2>
                        <p class="partner-description">
                            <?= nl2br(htmlspecialchars($lembaga['public_programs'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- PDF Programs -->
                    <div class="section-card">
                        <h2 class="section-title">Program Lengkap</h2>
                        <p class="mb-4">
                            Pelajari informasi lengkap mengenai program bimbingan belajar, layanan, dan program yang tersedia.
                        </p>
                        
                        <?php if (empty($pdfs)): ?>
                            <div class="no-pdf">
                                <i class="fas fa-file-pdf"></i>
                                <h4>Informasi program lengkap belum tersedia</h4>
                                <p>Silakan cek kembali nanti untuk mendapatkan informasi program lengkap.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pdfs as $pdf): ?>
                                <div class="pdf-card">
                                    <h3 class="pdf-title"><?= htmlspecialchars($pdf['pdf_title']) ?></h3>
                                    <?php if (!empty($pdf['pdf_description'])): ?>
                                        <p class="pdf-description"><?= htmlspecialchars($pdf['pdf_description']) ?></p>
                                    <?php endif; ?>
                                    <div class="pdf-actions">
                                        <a href="<?= base_url('uploads/partner_pdfs/' . $pdf['pdf_file']) ?>" 
                                           target="_blank" 
                                           class="btn-view-pdf">
                                            <i class="fas fa-book-open"></i>
                                            Baca Program Lengkap
                                        </a>
                                        <a href="<?= base_url('uploads/partner_pdfs/' . $pdf['pdf_file']) ?>" 
                                           download 
                                           class="btn-download-pdf">
                                            <i class="fas fa-download"></i>
                                            Download PDF
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Contact Info -->
                    <div class="section-card">
                        <h2 class="section-title">Kontak</h2>
                        
                        <?php if (!empty($lembaga['public_whatsapp'])): ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <div class="contact-label">WhatsApp</div>
                                    <div class="contact-value">
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $lembaga['public_whatsapp']) ?>" 
                                           target="_blank" 
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($lembaga['public_whatsapp']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($lembaga['public_website'])): ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div>
                                    <div class="contact-label">Website</div>
                                    <div class="contact-value">
                                        <a href="<?= htmlspecialchars($lembaga['public_website']) ?>" 
                                           target="_blank" 
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($lembaga['public_website']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($lembaga['public_address'])): ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <div class="contact-label">Alamat</div>
                                    <div class="contact-value">
                                        <?= nl2br(htmlspecialchars($lembaga['public_address'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="" id="imageModalImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image modal
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imageSrc = button.getAttribute('data-bs-image');
                const imageTitle = button.getAttribute('data-bs-title');
                
                document.getElementById('imageModalImage').src = imageSrc;
                document.getElementById('imageModalTitle').textContent = imageTitle;
            });
        }
    </script>
</body>
</html>
