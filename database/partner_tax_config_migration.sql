-- Migration SQL for partner_tax_config table
-- Generated: 2026-09-24
-- Purpose: Tambah tabel pajak per mitra per tahun ajaran

-- Create partner_tax_config table
CREATE TABLE IF NOT EXISTS `partner_tax_config` (
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
    INDEX `idx_academic_year_id` (`academic_year_id`),
    CONSTRAINT `fk_ptc_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ptc_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copy existing tax data from partners table to partner_tax_config (only if tax_enabled = 1)
-- This creates default entries for all active academic years
INSERT IGNORE INTO `partner_tax_config` (`partner_id`, `academic_year_id`, `tax_enabled`, `tax_percentage`, `tax_method`, `tax_manual_nominal`)
SELECT 
    p.`id` as partner_id,
    a.`id` as academic_year_id,
    COALESCE(p.`tax_enabled`, 0) as tax_enabled,
    COALESCE(p.`tax_percentage`, 0.00) as tax_percentage,
    COALESCE(p.`tax_method`, 'none') as tax_method,
    COALESCE(p.`tax_manual_nominal`, 0.00) as tax_manual_nominal
FROM `partners` p
CROSS JOIN `academic_years` a
WHERE p.`tax_enabled` = 1;
