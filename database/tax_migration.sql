-- Migration SQL for partner_tax_config table
generated_at: 2026-09-24

-- Drop table if exists
DROP TABLE IF EXISTS `partner_tax_config`;

-- Create partner_tax_config table
CREATE TABLE `partner_tax_config` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `partner_id` INT NOT NULL,
    `academic_year_id` INT NOT NULL,
    `tax_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `tax_percentage` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax_method` ENUM('none','percentage','manual','deduct_share') NOT NULL DEFAULT 'none',
    `tax_manual_nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_partner_academic_year` (`partner_id`,`academic_year_id`),
    INDEX `idx_partner_id` (`partner_id`),
    INDEX `idx_academic_year_id` (`academic_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copy existing tax data from partners table to partner_tax_config
-- For each partner and academic year combination
INSERT INTO `partner_tax_config` (`partner_id`, `academic_year_id`, `tax_enabled`, `tax_percentage`, `tax_method`, `tax_manual_nominal`)
SELECT 
    p.`id` as partner_id,
    a.`id` as academic_year_id,
    COALESCE(p.`tax_enabled`, 0) as tax_enabled,
    COALESCE(p.`tax_percentage`, 0.00) as tax_percentage,
    COALESCE(p.`tax_method`, 'none') as tax_method,
    COALESCE(p.`tax_manual_nominal`, 0.00) as tax_manual_nominal
FROM `partners` p
CROSS JOIN `academic_years` a
WHERE EXISTS (
    SELECT 1 FROM `partners` p2 
    WHERE p2.`id` = p.`id` AND p2.`tax_enabled` = 1
);

-- Log the migration
INSERT INTO `audit_logs` (`user_id`, `aktivitas`, `tabel`, `data_id`, `data_lama`, `data_baru`, `created_at`)
VALUES 
(NULL, 'Migration: Membuat tabel partner_tax_config dan menyalin data pajak mitra', 'partner_tax_config', NULL, NULL, NULL, NOW());