<?php
require_once __DIR__ . '/auth.php';
require_login();
$PAGE_TITLE = $PAGE_TITLE ?? 'Dashboard';
$BREADCRUMB = $BREADCRUMB ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?= e($PAGE_TITLE) ?> &middot; <?= e(APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
<link rel="icon" href="<?= base_url('assets/images/LPPA.png') ?>" type="image/png">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
</head>
<body>
<div class="app-wrapper">
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="app-main">
<?php include __DIR__ . '/navbar.php'; ?>
<main class="app-content">
  <div class="page-head">
    <div>
      <h1 class="page-title"><?= e($PAGE_TITLE) ?></h1>
      <?php if ($BREADCRUMB): ?>
      <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <?php foreach ($BREADCRUMB as $i => $b): $last = $i === array_key_last($BREADCRUMB); ?>
          <li class="breadcrumb-item <?= $last ? 'active' : '' ?>">
            <?= $last || empty($b['url']) ? e($b['label']) : '<a href="' . e($b['url']) . '">' . e($b['label']) . '</a>' ?>
          </li>
        <?php endforeach; ?>
      </ol></nav>
      <?php endif; ?>
    </div>
  </div>
  <?php foreach (get_flash() as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
      <i class="fa-solid <?= $f['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?> me-2"></i><?= e($f['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>
