SET FOREIGN_KEY_CHECKS = 0;
-- ---------------------------------------------------------------------
-- dealer_brand
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_brand`;

CREATE TABLE `dealer_brand`
(
  `id`                 INTEGER NOT NULL AUTO_INCREMENT,
  `dealer_id`          INTEGER NOT NULL,
  `brand_id`           INTEGER NOT NULL,
  `created_at`         DATETIME,
  `updated_at`         DATETIME,
  `version`            INTEGER          DEFAULT 0,
  `version_created_at` DATETIME,
  `version_created_by` VARCHAR(100),
  PRIMARY KEY (`id`),
  INDEX `FI_dealer_brand_dealer_id` (`dealer_id`),
  INDEX `FI_dealer_brand_brand_id` (`brand_id`),
  CONSTRAINT `fk_dealer_brand_dealer_id`
  FOREIGN KEY (`dealer_id`)
  REFERENCES `dealer` (`id`)
    ON UPDATE RESTRICT
    ON DELETE CASCADE,
  CONSTRAINT `fk_dealer_brand_brand_id`
  FOREIGN KEY (`brand_id`)
  REFERENCES `brand` (`id`)
    ON UPDATE RESTRICT
    ON DELETE CASCADE
)
  ENGINE = InnoDB;
-- ---------------------------------------------------------------------
-- dealer_brand_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_brand_version`;

CREATE TABLE `dealer_brand_version`
(
  `id`                 INTEGER           NOT NULL,
  `dealer_id`          INTEGER           NOT NULL,
  `brand_id`           INTEGER           NOT NULL,
  `created_at`         DATETIME,
  `updated_at`         DATETIME,
  `version`            INTEGER DEFAULT 0 NOT NULL,
  `version_created_at` DATETIME,
  `version_created_by` VARCHAR(100),
  `dealer_id_version`  INTEGER DEFAULT 0,
  PRIMARY KEY (`id`, `version`),
  CONSTRAINT `dealer_brand_version_FK_1`
  FOREIGN KEY (`id`)
  REFERENCES `dealer_brand` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

ALTER TABLE `dealer_version` ADD `dealer_brand_ids` TEXT AFTER`dealer_folder_versions`;
ALTER TABLE `dealer_version` ADD `dealer_brand_versions` TEXT AFTER`dealer_brand_ids`;


# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;