<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'direksi']);
$SCOPE_PARTNER = null;
$PAGE_TITLE = 'Data Siswa';
$BREADCRUMB = role() === 'direksi'
    ? [['label' => 'Dashboard', 'url' => base_url('admin/direksi_dashboard.php')], ['label' => 'Data Siswa']]
    : [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Data Siswa']];
require __DIR__ . '/../includes/students_page.php';
