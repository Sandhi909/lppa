<?php
require_once __DIR__ . '/_scope.php';
$rep = build_report($jl, $WHERE, $PARAMS);
$format = ($_GET['format'] ?? 'csv') === 'excel' ? 'excel' : 'csv';
$nama = 'laporan_' . $jl . '_' . date('Ymd_His');
audit('Export laporan ' . $jl . ' (' . strtoupper($format) . ')', 'students');

if ($format === 'excel') {
    // Excel-compatible XML/HTML table (dapat dibuka Microsoft Excel & LibreOffice)
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nama . '.xls"');
    echo "\xEF\xBB\xBF";
    echo '<html><head><meta charset="utf-8"></head><body>';
    echo '<h3>' . e(APP_NAME) . ' - ' . e($JENIS_LAPORAN[$jl]) . '</h3>';
    echo '<p>Dicetak: ' . date('d/m/Y H:i') . '</p>';
    echo '<table border="1"><thead><tr>';
    foreach ($rep['header'] as $h) echo '<th>' . e($h) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($rep['rows'] as $r) {
        echo '<tr>';
        foreach ($r as $i => $c) echo '<td>' . (in_array($i, $rep['money'], true) ? (float)$c : e((string)$c)) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></body></html>';
    exit;
}
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nama . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, $rep['header'], ';');
foreach ($rep['rows'] as $r) fputcsv($out, $r, ';');
fclose($out);
