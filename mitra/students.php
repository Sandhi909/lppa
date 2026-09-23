<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra']);
$SCOPE_PARTNER = my_partner_id();
if (!$SCOPE_PARTNER) die('Akun mitra belum terhubung dengan data Mitra. Hubungi Admin LPPA.');
$PAGE_TITLE = 'Data Siswa Saya';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('mitra/index.php')], ['label' => 'Data Siswa']];
require __DIR__ . '/../includes/students_page.php';
