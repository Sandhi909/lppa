<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'mitra']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Check scope for mitra
if (role() === 'mitra') {
    $partnerId = my_partner_id();
    if ($id != $partnerId) {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
}

$partner = db()->prepare("SELECT id, nama_mitra, deskripsi_public, keunggulan_public, video_url, video_platform FROM partners WHERE id = ?");
$partner->execute([$id]);
$partner = $partner->fetch();

header('Content-Type: application/json');
echo json_encode($partner ?: []);
