<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$partnerId = (int)($_GET['partner_id'] ?? 0);
$academicYearId = isset($_GET['academic_year_id']) ? (int)$_GET['academic_year_id'] : null;

if ($partnerId === 0) {
    echo json_encode(['enabled' => false, 'percentage' => 0, 'method' => 'none', 'manual_nominal' => 0]);
    exit;
}

$tax = get_tax_config($partnerId, $academicYearId);
echo json_encode($tax);
