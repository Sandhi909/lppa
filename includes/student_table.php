<?php /** Tabel siswa. Variabel: $rows, $canEdit(bool), $showMitra(bool), $offsetNo */
$latestAyId = tahun_ajaran_id_terbaru(); ?>
<div class="table-responsive-wrapper mobile-table-wrapper">
<table class="table table-hover align-middle">
  <thead><tr>
    <th>No</th><th>Tanggal</th><th>Nama Siswa</th><?php if ($showMitra): ?><th>Mitra</th><?php endif; ?>
    <th>Unit</th><th>Kelas</th><th>Program Bimbel</th><th>Kategori</th><th>Triwulan</th><th>PSAM</th>
    <th class="text-end">Biaya Pendaftaran</th><th class="text-end">Biaya Program</th><th class="text-end">Total Biaya</th>
    <th>Status</th><th>Akun Orang Tua</th><th>Wali Kelas</th><th>Wali Santri</th>
    <?php if ($canEdit): ?><th class="text-end no-print">Aksi</th><?php endif; ?>
  </tr></thead>
  <tbody>
  <?php if (!$rows): ?>
    <tr><td colspan="18"><div class="empty-state"><i class="fa-solid fa-folder-open"></i>Belum ada data siswa yang sesuai.</div></td></tr>
  <?php endif; ?>
  <?php foreach ($rows as $i => $r): ?>
    <tr>
      <td><?= $i + 1 + ($offsetNo ?? 0) ?></td>
      <td class="text-nowrap"><?= tgl_id($r['tanggal_pendaftaran']) ?></td>
      <td><strong><?= e($r['nama_siswa']) ?></strong><br><small class="text-muted"><?= e($r['nama_tahun_ajaran'] ?? '-') ?></small></td>
      <?php if ($showMitra): ?><td><?= e($r['nama_mitra'] ?? '-') ?></td><?php endif; ?>
      <td><?= e($r['nama_unit'] ?? '-') ?></td>
      <td><?= e($r['kelas'] ?: '-') ?></td>
      <td><?= e($r['program_bimbel'] ?: '-') ?></td>
      <td>
        <?php 
        $kategori = $r['kategori_peserta'] ?? 'reguler';
        if ($kategori === 'khusus'): 
        ?>
        <span class="badge bg-warning text-dark"><i class="fa-solid fa-star me-1"></i>Kategori Khusus</span>
        <?php else: ?>
        <span class="badge bg-success">Reguler</span>
        <?php endif; ?>
      </td>
      <td><?= e($r['triwulan'] ?: '-') ?></td>
      <td><?= e($r['is_psam'] ?: '-') ?></td>
      <td class="text-end"><?= rupiah($r['biaya_pendaftaran']) ?></td>
      <td class="text-end"><?= rupiah($r['biaya_program']) ?></td>
      <td class="text-end fw-semibold"><?= rupiah($r['total_biaya']) ?></td>
      <td><?= badge_status($r['status_pembayaran']) ?></td>
      <td>
        <?php 
        $hasParent = false;
        if (isset($r['id'])) {
            try {
                $check = db()->prepare("SELECT COUNT(*) FROM siswa_orang_tua WHERE student_id = ?");
                $check->execute([(int)$r['id']]);
                $count = $check->fetchColumn();
                $hasParent = $count !== false && (int)$count > 0;
            } catch (Exception $e) {
                $hasParent = false;
            }
        }
        if ($hasParent): 
        ?>
        <span class="badge bg-success"><i class="fa-solid fa-check"></i> Ada</span>
        <?php else: ?>
        <span class="badge bg-secondary"><i class="fa-solid fa-times"></i> Belum</span>
        <?php endif; ?>
      </td>
      <td><small><?= e($r['wali_kelas_nama'] ?: '-') ?></small></td>
      <td><small><?= e($r['wali_santri_nama'] ?: '-') ?></small></td>
<?php if ($canEdit): ?>
    <?php $deletable = (int)($r['academic_year_id'] ?? 0) === $latestAyId; ?>
    <td class="text-end text-nowrap no-print">
        <a class="btn btn-sm btn-light" href="?aksi=detail&id=<?= (int)$r['id'] ?>" title="Detail"><i class="fa-solid fa-eye"></i></a>
        <a class="btn btn-sm btn-light" href="?aksi=edit&id=<?= (int)$r['id'] ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
        <?php if ($deletable): ?>
        <a class="btn btn-sm btn-light text-danger" href="?aksi=hapus&id=<?= (int)$r['id'] ?>"
           data-confirm="Hapus data siswa <?= e($r['nama_siswa']) ?>?" title="Hapus"><i class="fa-solid fa-trash"></i></a>
        <?php else: ?>
        <span class="btn btn-sm btn-light text-muted opacity-50" title="Hapus hanya berlaku untuk tahun ajaran terbaru"
              style="cursor:not-allowed"><i class="fa-solid fa-trash"></i></span>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
