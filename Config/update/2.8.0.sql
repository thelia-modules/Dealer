SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `dealer_i18n` ADD `hard_open_hour` VARCHAR(255) AFTER `big_description` ;
SET FOREIGN_KEY_CHECKS = 1;