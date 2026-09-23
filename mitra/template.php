<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['mitra', 'admin']);
$file = 'template_import_siswa.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $file . '"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
$head = ['Nama Siswa','Unit','Kelas','PSAM','Program Bimbel','Kategori Peserta','Biaya Pendaftaran','Biaya Program',
         'Status','Triwulan','Tahun Ajaran','Wali Kelas','Wali Santri'];
fputcsv($out, $head, ';');
fputcsv($out, ['Contoh Siswa PSAM','SMP','7A','YA','Neutron','reguler','500000','2000000','Angsuran','I','2025/2026','Ustadz Ahmad Fauzi','Ustadzah Siti Aminah'], ';');
fputcsv($out, ['Contoh Siswa Regular','SMA','11 IPA','TIDAK','Persiapan UTBK','reguler','1000000','3000000','Lunas','II','2025/2026','Ustadz Rahmat Hidayat',''], ';');
fputcsv($out, ['Contoh Siswa Khusus','SD','5','TIDAK','Tambahan','khusus','500000','1500000','Angsuran','III','2025/2026','Ustadzah Fatimah',''], ';');
fclose($out);
