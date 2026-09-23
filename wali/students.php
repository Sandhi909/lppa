<?php
require __DIR__ . '/_scope.php';
$per = 20; $page = max(1, (int)($_GET['page'] ?? 1));
$total = students_count($WHERE, $PARAMS);
$rows = students_list($WHERE, $PARAMS, $per, ($page - 1) * $per);
$PAGE_TITLE = 'Siswa Rekomendasi Saya';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('wali/index.php')], ['label' => 'Siswa Rekomendasi']];
include __DIR__ . '/../includes/header.php';
?>
<?php render_filter_bar($_GET, ['q','ta','mitra','unit','program','status']); ?>
<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr>
  <th>#</th><th>Nama Siswa</th><th>Unit</th><th>Kelas</th><th>Mitra Bimbel</th><th>Program Bimbel</th>
  <th>Tanggal Daftar</th><th>Status Pembayaran</th><th class="text-end">Poin</th></tr></thead><tbody>
<?php if (!$rows): ?><tr><td colspan="9"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada siswa rekomendasi.</div></td></tr><?php endif; ?>
<?php foreach ($rows as $i => $r): ?>
  <tr><td><?= $i + 1 + ($page - 1) * $per ?></td><td><strong><?= e($r['nama_siswa']) ?></strong></td>
  <td><?= e($r['nama_unit'] ?: '-') ?></td><td><?= e($r['kelas'] ?: '-') ?></td><td><?= e($r['nama_mitra']) ?></td>
  <td><?= e($r['program_bimbel'] ?: '-') ?></td><td><?= tgl_id($r['tanggal_pendaftaran']) ?></td>

  <td><?= badge_status($r['status_pembayaran']) ?></td><td class="text-end"><?= poin_fmt(poin_baris($r, $JENIS)) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php pagination($total, $per, $page, $_GET); ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
