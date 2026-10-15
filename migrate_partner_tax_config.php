<?php
require_once __DIR__ . '/config/database.php';

echo "=== Creating partner_tax_config table ===\n";

$sql = "
CREATE TABLE IF NOT EXISTS partner_tax_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    tax_enabled TINYINT(1) DEFAULT 0,
    tax_percentage DECIMAL(5,2) DEFAULT 0,
    tax_method ENUM('none', 'percentage', 'manual') DEFAULT 'none',
    tax_manual_nominal DECIMAL(14,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_partner_year (partner_id, academic_year_id),
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
)
";

try {
    db()->exec($sql);
    echo "✓ Tabel partner_tax_config berhasil dibuat.\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Verifikasi struktur tabel ===\n";
$cols = db()->query('DESC partner_tax_config')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . " (" . $c['Type'] . ")\n";
}
