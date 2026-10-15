<?php
require_once __DIR__ . '/config/database.php';

echo "=== ALL TABLES ===\n";
$tables = db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo $t . "\n";
}

echo "\n=== PARTNERS TABLE STRUCTURE ===\n";
$cols = db()->query('DESC partners')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . " (" . $c['Type'] . ")\n";
}
