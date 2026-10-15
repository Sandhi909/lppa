<?php
require_once __DIR__ . '/config/database.php';

echo "=== STUDENTS TABLE STRUCTURE ===\n";
$cols = db()->query('DESC students')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . " (" . $c['Type'] . ")\n";
}
