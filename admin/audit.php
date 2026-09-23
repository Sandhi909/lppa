<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin']);
require_once __DIR__ . '/../includes/queries.php';

$per = 25; $page = max(1, (int)($_GET['page'] ?? 1));
$q = trim($_GET['q'] ?? '');
$w = ['1=1']; $p = [];
if ($q !== '') { $w[] = '(a.aktivitas LIKE ? OR u.nama LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
$where = implode(' AND ', $w);
$c = db()->prepare("SELECT COUNT(*) FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id WHERE $where");
$c->execute($p); $total = (int)$c->fetchColumn();
$st = db()->prepare("SELECT a.*, u.nama, u.role FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id
  WHERE $where ORDER BY a.created_at DESC, a.id DESC LIMIT $per OFFSET " . (($page - 1) * $per));
$st->execute($p); $list = $st->fetchAll();

$PAGE_TITLE = 'Riwayat Aktivitas';
$BREADCRUMB = [['label' => 'Dashboard', 'url' => base_url('admin/index.php')], ['label' => 'Riwayat Aktivitas']];
include __DIR__ . '/../includes/header.php';
?>
<form class="card mb-3" method="get" data-noload><div class="card-body row g-2 align-items-end">
  <div class="col-md-6"><label class="form-label">Cari Aktivitas</label><input name="q" class="form-control" value="<?= e($q) ?>"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Cari</button></div>
</div></form>
<div class="card"><div class="card-body p-0"><div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle"><thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Tabel</th><th>ID Data</th></tr></thead><tbody>
<?php if (!$list): ?><tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i>Belum ada aktivitas.</div></td></tr><?php endif; ?>
<?php foreach ($list as $r): ?>
  <tr><td class="text-nowrap"><small><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small></td>
  <td><?= e($r['nama'] ?: 'Sistem') ?><br><small class="text-muted"><?= e(str_replace('_', ' ', (string)$r['role'])) ?></small></td>
  <td><?= e($r['aktivitas']) ?></td><td><code><?= e($r['tabel'] ?: '-') ?></code></td><td><?= e($r['data_id'] ?: '-') ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php pagination($total, $per, $page, $_GET); ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
