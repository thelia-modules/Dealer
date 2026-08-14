SET FOREIGN_KEY_CHECKS = 0;

-- Per-dealer shop timezone used to compute pickup slots (defaults to Europe/Paris).
ALTER TABLE `dealer` ADD `timezone` VARCHAR(64) DEFAULT 'Europe/Paris' AFTER `longitude`;

SET FOREIGN_KEY_CHECKS = 1;
