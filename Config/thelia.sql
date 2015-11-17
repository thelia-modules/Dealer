
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- dealer
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer`;

CREATE TABLE `dealer`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `address1` VARCHAR(255) NOT NULL,
    `address2` VARCHAR(255),
    `address3` VARCHAR(255),
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `country_id` INTEGER NOT NULL,
    `latitude` DECIMAL(16,13) DEFAULT 0,
    `longitude` DECIMAL(16,13) DEFAULT 0,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    PRIMARY KEY (`id`),
    INDEX `FI_dealer_country_id` (`country_id`),
    CONSTRAINT `fk_dealer_country_id`
        FOREIGN KEY (`country_id`)
        REFERENCES `country` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_shedules
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_shedules`;

CREATE TABLE `dealer_shedules`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `day` INTEGER NOT NULL,
    `begin` TIME NOT NULL,
    `end` TIME NOT NULL,
    `period_begin` DATE,
    `period_end` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    PRIMARY KEY (`id`),
    INDEX `FI_dealer_shedules_dealer_id` (`dealer_id`),
    CONSTRAINT `fk_dealer_shedules_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_contact
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_contact`;

CREATE TABLE `dealer_contact`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    PRIMARY KEY (`id`),
    INDEX `FI_dealer_contact_dealer_id` (`dealer_id`),
    CONSTRAINT `fk_dealer_contact_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_contact_info
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_contact_info`;

CREATE TABLE `dealer_contact_info`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `contact_id` INTEGER NOT NULL,
    `contact_type` TINYINT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    PRIMARY KEY (`id`),
    INDEX `FI_dealer_contact_info_dealer_contact_id` (`contact_id`),
    CONSTRAINT `fk_dealer_contact_info_dealer_contact_id`
        FOREIGN KEY (`contact_id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_i18n`;

CREATE TABLE `dealer_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `dealer_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_contact_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_contact_i18n`;

CREATE TABLE `dealer_contact_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `dealer_contact_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_contact` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_contact_info_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_contact_info_i18n`;

CREATE TABLE `dealer_contact_info_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `dealer_contact_info_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_contact_info` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_version`;

CREATE TABLE `dealer_version`
(
    `id` INTEGER NOT NULL,
    `address1` VARCHAR(255) NOT NULL,
    `address2` VARCHAR(255),
    `address3` VARCHAR(255),
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `country_id` INTEGER NOT NULL,
    `latitude` DECIMAL(16,13) DEFAULT 0,
    `longitude` DECIMAL(16,13) DEFAULT 0,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0 NOT NULL,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    `dealer_shedules_ids` TEXT,
    `dealer_shedules_versions` TEXT,
    `dealer_contact_ids` TEXT,
    `dealer_contact_versions` TEXT,
    `dealer_contact_info_ids` TEXT,
    `dealer_contact_info_versions` TEXT,
    PRIMARY KEY (`id`,`version`),
    CONSTRAINT `dealer_version_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_shedules_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_shedules_version`;

CREATE TABLE `dealer_shedules_version`
(
    `id` INTEGER NOT NULL,
    `dealer_id` INTEGER NOT NULL,
    `day` INTEGER NOT NULL,
    `begin` TIME NOT NULL,
    `end` TIME NOT NULL,
    `period_begin` DATE,
    `period_end` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0 NOT NULL,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    `dealer_id_version` INTEGER DEFAULT 0,
    PRIMARY KEY (`id`,`version`),
    CONSTRAINT `dealer_shedules_version_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_shedules` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_contact_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_contact_version`;

CREATE TABLE `dealer_contact_version`
(
    `id` INTEGER NOT NULL,
    `dealer_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0 NOT NULL,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    `dealer_id_version` INTEGER DEFAULT 0,
    PRIMARY KEY (`id`,`version`),
    CONSTRAINT `dealer_contact_version_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_contact` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_contact_info_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_contact_info_version`;

CREATE TABLE `dealer_contact_info_version`
(
    `id` INTEGER NOT NULL,
    `contact_id` INTEGER NOT NULL,
    `contact_type` TINYINT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `version` INTEGER DEFAULT 0 NOT NULL,
    `version_created_at` DATETIME,
    `version_created_by` VARCHAR(100),
    `contact_id_version` INTEGER DEFAULT 0,
    PRIMARY KEY (`id`,`version`),
    CONSTRAINT `dealer_contact_info_version_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_contact_info` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
