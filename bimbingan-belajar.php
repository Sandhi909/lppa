<?php
require_once __DIR__ . '/config/database.php';

// Helper function untuk base_url
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
}

// SEO & Open Graph
$pageTitle = "Program Bimbingan Belajar LPPA | Yayasan Al Ma'soem Bandung";
$pageDescription = "Temukan berbagai pilihan program bimbingan belajar yang diselenggarakan di lingkungan Yayasan Al Ma'soem Bandung.";
$pageImage = base_url('assets/images/LPPA.png');

// Get filter
$filterLevel = $_GET['level'] ?? 'all';
$search = $_GET['search'] ?? '';

// Get public content
$kontenLppa = db()->query("SELECT * FROM konten_lppa")->fetchAll();
$kontenArray = [];
foreach ($kontenLppa as $k) {
    $kontenArray[$k['bagian']] = $k;
}

// Build query
$where = "p.is_public = 1 AND p.status = 'aktif'";
$params = [];

if ($filterLevel !== 'all') {
    $where .= " AND (p.public_levels LIKE ? OR p.public_levels IS NULL)";
    $params[] = "%$filterLevel%";
}

if (!empty($search)) {
    $where .= " AND (p.nama_mitra LIKE ? OR p.public_description LIKE ? OR p.public_program_unggulan LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$lembaga = db()->prepare(
    "SELECT p.*, 
            CASE 
                WHEN p.public_logo IS NOT NULL AND p.public_logo != '' THEN p.public_logo
                ELSE NULL
            END as logo_url
     FROM partners p 
     WHERE $where 
     ORDER BY p.display_order ASC, p.nama_mitra ASC"
);
$lembaga->execute($params);
$lembaga = $lembaga->fetchAll();
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
    <meta property="og:url" content="<?= base_url('mitra-bimbel') ?>">
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
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-identity {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .lppa-logo {
            width: 100px;
            height: auto;
            max-height: 100px;
            object-fit: contain;
        }
        
        .lppa-name {
            color: white;
        }
        
        .lppa-acronym {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 2px;
            line-height: 1;
        }
        
        .lppa-fullname {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -1px;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            max-width: 600px;
        }
        
        .hero-cta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .hero-visual {
            position: relative;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-icon-wrapper {
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            border: 2px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 2;
        }
        
        .hero-decoration {
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            z-index: 1;
        }
        
        .hero-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
        }
        
        .btn-primary {
            background: white;
            color: var(--primary);
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--primary);
        }
        
        .btn-outline-light {
            background: transparent;
            color: white;
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            border: 2px solid white;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-outline-light:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        /* Filters */
        .filters {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .filter-btn {
            padding: 8px 20px;
            border: 2px solid var(--primary);
            background: white;
            color: var(--primary);
            border-radius: 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--primary);
            color: white;
        }
        
        .search-box {
            max-width: 300px;
        }
        
        /* Bimbingan Belajar Section */
        .bimbingan-belajar-section {
            padding: 80px 0;
        }
        
        .section-header {
            margin-bottom: 50px;
        }
        
        .section-badge {
            display: inline-flex;
            align-items: center;
            background: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 15px;
            letter-spacing: -1px;
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .partner-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }
        
        .partner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .partner-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }
        
        .partner-card:hover::before {
            opacity: 1;
        }
        
        .partner-logo-wrapper {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .partner-logo-wrapper::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(30,111,217,0.05);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }
        
        .partner-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            position: relative;
            z-index: 2;
            transition: transform 0.3s;
        }
        
        .partner-card:hover .partner-logo {
            transform: scale(1.05);
        }
        
        .partner-card-body {
            padding: 25px;
        }
        
        .partner-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 12px;
            line-height: 1.3;
        }
        
        .partner-desc {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.6;
        }
        
        .partner-levels {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .partner-level {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .partner-highlight {
            background: linear-gradient(135deg, rgba(22,163,74,0.1), rgba(22,163,74,0.05));
            border: 1px solid rgba(22,163,74,0.2);
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.9rem;
            color: var(--accent);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .partner-highlight i {
            font-size: 1.1rem;
        }
        
        .btn-explore {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1rem;
        }
        
        .btn-explore:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30,111,217,0.3);
            color: white;
        }
        
        /* Features Section */
        .features-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 100px 0;
            position: relative;
        }
        
        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.05);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        
        .feature-icon-wrapper {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 25px;
            position: relative;
        }
        
        .feature-icon-wrapper::after {
            content: '';
            position: absolute;
            width: 110px;
            height: 110px;
            background: rgba(30,111,217,0.1);
            border-radius: 50%;
            top: -10px;
            left: -10px;
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--secondary);
            line-height: 1.3;
        }
        
        .feature-desc {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            padding: 80px 0;
            color: white;
        }
        
        .cta-content h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .cta-content p {
            font-size: 1.1rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        .cta-action {
            text-align: center;
        }
        
        .btn-cta-large {
            background: white;
            color: var(--primary);
            padding: 18px 40px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            font-size: 1.1rem;
        }
        
        .btn-cta-large:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            color: var(--primary);
        }
        
        /* Yayasan Section */
        .yayasan-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
        }
        
        .yayasan-content h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .yayasan-content p {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.8;
        }
        
        .yayasan-visual {
            text-align: center;
        }
        
        .yayasan-icon-wrapper {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto;
            position: relative;
        }
        
        .yayasan-icon-wrapper::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            background: rgba(30,111,217,0.1);
            border-radius: 50%;
            top: -20px;
            left: -20px;
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--secondary) 0%, #0a1f3d 100%);
            color: white;
            padding: 80px 0 30px;
            position: relative;
        }
        
        .footer-brand {
            margin-bottom: 30px;
        }
        
        .footer-logo {
            width: 80px;
            height: auto;
            max-height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
        }
        
        .footer h5 {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        
        .footer-desc {
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        .footer-section h5 {
            margin-bottom: 25px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .footer-links a[href="tentang-lppa"] {
            color: white;
            font-weight: 600;
        }
        
        .footer-contact {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-contact li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            color: rgba(255,255,255,0.7);
        }
        
        .footer-contact i {
            margin-top: 3px;
            opacity: 0.8;
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .social-link:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-divider {
            border-color: rgba(255,255,255,0.1);
            margin: 40px 0;
        }
        
        .footer-copyright {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Modal */
        .modal-header {
            background: var(--primary);
            color: white;
        }
        
        .modal-title {
            font-weight: 700;
        }
        
        .detail-logo {
            max-width: 200px;
            max-height: 150px;
            object-fit: contain;
            margin: 0 auto 20px;
            display: block;
        }
        
        .detail-section {
            margin-bottom: 20px;
        }
        
        .detail-label {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                padding: 60px 0 40px;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-cta {
                flex-direction: column;
                gap: 10px;
            }
            
            .hero-visual {
                height: 250px;
            }
            
            .hero-icon-wrapper {
                width: 180px;
                height: 180px;
                font-size: 3.5rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .section-subtitle {
                font-size: 1rem;
            }
            
            .search-box {
                max-width: 100%;
                margin-top: 15px;
            }
            
            .filter-btn {
                margin-right: 5px;
                margin-bottom: 5px;
                padding: 8px 16px;
                font-size: 0.9rem;
            }
            
            .partner-card {
                margin-bottom: 20px;
            }
            
            .partner-logo-wrapper {
                height: 150px;
            }
            
            .cta-content h2 {
                font-size: 1.5rem;
            }
            
            .cta-content p {
                font-size: 0.95rem;
            }
            
            .btn-cta-large {
                padding: 15px 30px;
                font-size: 1rem;
            }
            
            .footer {
                padding: 50px 0 20px;
            }
            
            .footer-brand {
                margin-bottom: 25px;
            }
            
            .footer-social {
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .hero h1 {
                font-size: 1.6rem;
            }
            
            .hero-badge {
                font-size: 0.8rem;
                padding: 6px 15px;
            }
            
            .partner-name {
                font-size: 1.2rem;
            }
            
            .feature-card {
                padding: 30px 20px;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
        }
        
        /* Loading */
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        /* About Section */
        .about-section {
            padding: 100px 0;
            background: #fdfdfd;
            position: relative;
            overflow: hidden;
        }
        
        .about-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 400px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            z-index: 1;
            border-radius: 0 0 50% 50% / 0 0 20px 20px;
        }
        
        .about-section .container {
            position: relative;
            z-index: 2;
        }

        .about-header {
            margin-bottom: 60px;
        }

        .about-header .section-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .about-header .section-title {
            color: white;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .about-header .section-subtitle {
            color: rgba(255, 255, 255, 0.9);
        }

        .about-main-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            border: none;
            display: flex;
            flex-direction: row;
        }

        .about-img-side {
            flex: 0 0 30%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .about-logo-img {
            max-width: 220px;
            height: auto;
            object-fit: contain;
            transition: all 0.4s ease;
        }

        .about-main-card:hover .about-logo-img {
            transform: scale(1.05);
        }
        
        .about-img-side::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, transparent, rgba(0,0,0,0.02));
        }

        .about-content-side {
            flex: 1;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .about-vmt-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .vmt-card {
            background: white;
            padding: 40px 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.03);
            text-align: center;
        }

        .vmt-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(30,111,217,0.15);
            border-color: var(--primary);
        }

        .vmt-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 25px;
            transform: rotate(-5deg);
            transition: all 0.4s ease;
        }

        .vmt-card:hover .vmt-icon {
            transform: rotate(0deg) scale(1.1);
        }

        .vmt-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .vmt-text {
            color: #666;
            line-height: 1.7;
            font-size: 1rem;
        }

        @media (max-width: 992px) {
            .about-main-card {
                flex-direction: column;
            }
            .about-img-side {
                height: 300px;
            }
            .about-vmt-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="hero-identity">
                            <img src="<?= base_url('assets/images/LPPA.png') ?>" alt="LPPA Logo" class="lppa-logo">
                            <div class="lppa-name">
                                <div class="lppa-acronym">LPPA</div>
                                <div class="lppa-fullname">Lembaga Pengembangan Potensi Akademik</div>
                            </div>
                        </div>
                        <h1>Pilihan Bimbingan Belajar untuk Mendukung Prestasi Akademik</h1>
                        <p class="hero-subtitle">
                            Beragam program bimbingan belajar diselenggarakan di lingkungan Yayasan Al Ma'soem Bandung 
                            untuk mendukung kebutuhan belajar dan pengembangan potensi akademik siswa.
                        </p>
                        <div class="hero-cta">
                            <a href="#bimbingan-belajar" class="btn btn-primary btn-lg">
                                <i class="fas fa-compass me-2"></i>Lihat Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-visual">
                        <div class="hero-icon-wrapper">
                            <i class="fas fa-school"></i>
                        </div>
                        <div class="hero-decoration"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-wave">
            <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 50L48 41.7C96 33.3 192 16.7 288 16.7C384 16.7 480 33.3 576 41.7C672 50 768 50 864 41.7C960 33.3 1056 16.7 1152 16.7C1248 16.7 1344 33.3 1392 41.7L1440 50V100H1392C1344 100 1248 100 1152 100C1056 100 960 100 864 100C768 100 672 100 576 100C480 100 384 100 288 100C192 100 96 100 48 100H0V50Z" fill="white"/>
            </svg>
        </div>
    </section>
    
    <!-- Filters -->
    <section class="filters" id="bimbingan-belajar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap">
                        <a href="?level=all" class="filter-btn <?= $filterLevel === 'all' ? 'active' : '' ?>">
                            <i class="fas fa-th-large me-1"></i>Semua
                        </a>
                        <a href="?level=SD" class="filter-btn <?= $filterLevel === 'SD' ? 'active' : '' ?>">
                            <i class="fas fa-child me-1"></i>SD
                        </a>
                        <a href="?level=SMP" class="filter-btn <?= $filterLevel === 'SMP' ? 'active' : '' ?>">
                            <i class="fas fa-user-graduate me-1"></i>SMP
                        </a>
                        <a href="?level=SMA" class="filter-btn <?= $filterLevel === 'SMA' ? 'active' : '' ?>">
                            <i class="fas fa-graduation-cap me-1"></i>SMA
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <form action="" method="GET" class="search-box">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari bimbingan belajar..." value="<?= htmlspecialchars($search) ?>">
                            <input type="hidden" name="level" value="<?= $filterLevel ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Bimbingan Belajar Section -->
    <section class="bimbingan-belajar-section" id="bimbingan-belajar">
        <div class="container">
            <div class="section-header text-center mb-5">
                <div class="section-badge">
                    <i class="fas fa-book-open me-2"></i>Program Bimbingan Belajar
                </div>
                <h2 class="section-title">Pilihan Bimbingan Belajar</h2>
                <p class="section-subtitle">
                    Kenali berbagai lembaga bimbingan belajar dan program yang tersedia di lingkungan Yayasan Al Ma'soem Bandung.
                </p>
            </div>
            
            <?php if (empty($lembaga)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Tidak ada bimbingan belajar ditemukan</h3>
                    <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($lembaga as $item): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="partner-card">
                                <div class="partner-logo-wrapper">
                                    <?php if (!empty($item['logo_url'])): ?>
                                        <img src="<?= base_url('uploads/partners/' . $item['logo_url']) ?>" 
                                             alt="<?= htmlspecialchars($item['nama_mitra']) ?>" 
                                             class="partner-logo"
                                             onerror="this.style.display='none'">
                                    <?php else: ?>
                                        <i class="fas fa-building fa-4x text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="partner-card-body">
                                    <h3 class="partner-name"><?= htmlspecialchars($item['nama_mitra']) ?></h3>
                                    
                                    <?php if (!empty($item['public_description'])): ?>
                                        <p class="partner-desc"><?= htmlspecialchars($item['public_description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($item['public_levels'])): ?>
                                        <div class="partner-levels">
                                            <?php 
                                            $levels = explode(',', $item['public_levels']);
                                            foreach ($levels as $level): 
                                                $level = trim($level);
                                                if (!empty($level)):
                                            ?>
                                                <span class="partner-level"><?= htmlspecialchars($level) ?></span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($item['public_program_unggulan'])): ?>
                                        <div class="partner-highlight">
                                            <i class="fas fa-star"></i>
                                            <?= htmlspecialchars($item['public_program_unggulan']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="bimbingan-belajar-detail?id=<?= $item['id'] ?>" class="btn btn-explore">
                                        <i class="fas fa-compass me-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section" id="why-choose">
        <div class="container">
            <div class="section-header text-center mb-5">
                <div class="section-badge">
                    <i class="fas fa-star me-2"></i>Keunggulan
                </div>
                <h2 class="section-title">Kenapa Memilih Program Bimbingan Belajar LPPA?</h2>
                <p class="section-subtitle">
                    Temukan program yang tepat untuk perjalanan belajarmu di lingkungan Yayasan Al Ma'soem Bandung
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3 class="feature-title">Pilihan Program Beragam</h3>
                        <p class="feature-desc">
                            Berbagai program bimbingan belajar yang disesuaikan dengan kebutuhan dan target akademik siswa
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="feature-title">Jenjang Pendidikan Lengkap</h3>
                        <p class="feature-desc">
                            Program komprehensif untuk SD, SMP, hingga SMA dalam satu ekosistem pendidikan terpadu
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="feature-title">Lembaga Terpercaya</h3>
                        <p class="feature-desc">
                            Bekerja sama dengan lembaga bimbingan belajar terpercaya dan berpengalaman
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Mendukung Prestasi Siswa</h3>
                        <p class="feature-desc">
                            Fokus pada peningkatan prestasi akademik dan pengembangan potensi siswa secara maksimal
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Yayasan Info Section -->
    <section class="yayasan-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="yayasan-content">
                        <h2>Belajar dan Berkembang di Lingkungan Yayasan Al Ma'soem Bandung</h2>
                        <p>
                            Seluruh program bimbingan belajar diselenggarakan di lingkungan Yayasan Al Ma'soem Bandung, 
                            menyediakan fasilitas dan lingkungan belajar yang kondusif untuk mendukung perkembangan akademik siswa.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="yayasan-visual">
                        <div class="yayasan-icon-wrapper">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About LPPA Section -->
    <section class="about-section" id="tentang-lppa">
        <div class="container">
            <div class="about-header text-center">
                <div class="section-badge">
                    <i class="fas fa-info-circle me-2"></i>Tentang LPPA
                </div>
                <h2 class="section-title">Kenali LPPA Lebih Dekat</h2>
                <p class="section-subtitle">
                    Lembaga Pengembangan Potensi Akademik yang berkomitmen menghasilkan generasi berkualitas
                </p>
            </div>
            
            <?php if (isset($kontenArray['tentang'])): ?>
                <div class="about-main-card">
                    <div class="about-img-side">
                        <img src="<?= $kontenArray['tentang']['gambar'] ? base_url('uploads/konten/' . htmlspecialchars($kontenArray['tentang']['gambar'])) : base_url('assets/images/LPPA.png') ?>" 
                             alt="<?= htmlspecialchars($kontenArray['tentang']['judul']) ?>" 
                             class="about-logo-img">
                    </div>
                    <div class="about-content-side">
                        <h3 class="vmt-title text-start mb-4" style="font-size: 1.8rem;">
                            <?= htmlspecialchars($kontenArray['tentang']['judul']) ?>
                        </h3>
                        <div class="vmt-text text-start"><?= $kontenArray['tentang']['konten'] ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="about-vmt-grid">
                <?php if (isset($kontenArray['visi'])): ?>
                    <div class="vmt-card">
                        <div class="vmt-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="vmt-title"><?= htmlspecialchars($kontenArray['visi']['judul']) ?></h3>
                        <div class="vmt-text"><?= $kontenArray['visi']['konten'] ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($kontenArray['misi'])): ?>
                    <div class="vmt-card">
                        <div class="vmt-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="vmt-title"><?= htmlspecialchars($kontenArray['misi']['judul']) ?></h3>
                        <div class="vmt-text"><?= $kontenArray['misi']['konten'] ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($kontenArray['tujuan'])): ?>
                    <div class="vmt-card">
                        <div class="vmt-icon">
                            <i class="fas fa-flag"></i>
                        </div>
                        <h3 class="vmt-title"><?= htmlspecialchars($kontenArray['tujuan']['judul']) ?></h3>
                        <div class="vmt-text"><?= $kontenArray['tujuan']['konten'] ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">
                        <img src="<?= base_url('assets/images/LPPA.png') ?>" alt="LPPA Logo" class="footer-logo">
                        <h5>LPPA Yayasan Al Ma'soem</h5>
                        <p class="footer-desc">
                            Lembaga Pengembangan Potensi Akademik yang berkomitmen 
                            meningkatkan kualitas pendidikan melalui program bimbingan belajar di lingkungan Yayasan Al Ma'soem Bandung.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="footer-section">
                        <h5>Navigasi</h5>
                        <ul class="footer-links">
                            <li>
                                <a href="#bimbingan-belajar"><i class="fas fa-book-open me-2"></i>Program Bimbingan Belajar</a>
                            </li>
                            <li>
                                <a href="#tentang-lppa"><i class="fas fa-info-circle me-2"></i>Tentang LPPA</a>
                            </li>
                            <li>
                                <a href="#why-choose"><i class="fas fa-star me-2"></i>Keunggulan</a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="footer-section">
                        <h5>Hubungi Kami</h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span>Bandung, Indonesia</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope me-2"></i>
                                <span>info@almasoem.sch.id</span>
                            </li>
                            <li>
                                <i class="fas fa-phone me-2"></i>
                                <span>+62 22 XXX XXXX</span>
                            </li>
                        </ul>
                        <div class="footer-social">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="footer-divider">
            
            <div class="row">
                <div class="col-12 text-center">
                    <p class="footer-copyright">
                        &copy; <?= date('Y') ?> LPPA Yayasan Al Ma'soem. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
