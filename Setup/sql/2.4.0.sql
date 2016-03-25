SET FOREIGN_KEY_CHECKS = 0;


-- ---------------------------------------------------------------------
-- dealer_admin
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_admin`;

CREATE TABLE `dealer_admin`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `admin_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    PRIMARY KEY (`id`),
    INDEX `FI_dealer_admin_dealer_id` (`dealer_id`),
    INDEX `FI_dealer_admin_admin_id` (`admin_id`),
    CONSTRAINT `fk_dealer_admin_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_dealer_admin_admin_id`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_admin_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_admin_version`;

CREATE TABLE `dealer_admin_version`
(
    `id` INTEGER NOT NULL,
    `dealer_id` INTEGER NOT NULL,
    `admin_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0 NOT NULL,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    `dealer_id_version` INTEGER DEFAULT 0,
    PRIMARY KEY (`id`,`version`),
    CONSTRAINT `dealer_admin_version_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_admin` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;


ALTER TABLE `dealer_version`
ADD COLUMN  `dealer_admin_ids` TEXT AFTER `dealer_product_versions`,
ADD COLUMN  `dealer_admin_versions` TEXT AFTER `dealer_admin_ids`;



# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;