
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
    `visible` TINYINT DEFAULT 0 NOT NULL,
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
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_country_id` (`country_id`),
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
    `day` INTEGER,
    `begin` TIME,
    `end` TIME,
    `closed` TINYINT(1) DEFAULT 0,
    `period_begin` DATE,
    `period_end` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_shedules_dealer_id` (`dealer_id`),
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
    `is_default` TINYINT(1) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_contact_dealer_id` (`dealer_id`),
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
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_contact_info_dealer_contact_id` (`contact_id`),
    CONSTRAINT `fk_dealer_contact_info_dealer_contact_id`
        FOREIGN KEY (`contact_id`)
        REFERENCES `dealer_contact` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_content
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_content`;

CREATE TABLE `dealer_content`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `content_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_content_dealer_id` (`dealer_id`),
    INDEX `fi_dealer_content_content_id` (`content_id`),
    CONSTRAINT `fk_dealer_content_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_dealer_content_content_id`
        FOREIGN KEY (`content_id`)
        REFERENCES `content` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_folder
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_folder`;

CREATE TABLE `dealer_folder`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `folder_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_folder_dealer_id` (`dealer_id`),
    INDEX `fi_dealer_fodler_folder_id` (`folder_id`),
    CONSTRAINT `fk_dealer_folder_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_dealer_fodler_folder_id`
        FOREIGN KEY (`folder_id`)
        REFERENCES `folder` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_brand
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_brand`;

CREATE TABLE `dealer_brand`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `brand_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_brand_dealer_id` (`dealer_id`),
    INDEX `fi_dealer_brand_brand_id` (`brand_id`),
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
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_product
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_product`;

CREATE TABLE `dealer_product`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_product_dealer_id` (`dealer_id`),
    INDEX `fi_dealer_product_product_id` (`product_id`),
    CONSTRAINT `fk_dealer_product_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_dealer_product_product_id`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

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
    PRIMARY KEY (`id`),
    INDEX `fi_dealer_admin_dealer_id` (`dealer_id`),
    INDEX `fi_dealer_admin_admin_id` (`admin_id`),
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
-- dealer_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_i18n`;

CREATE TABLE `dealer_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `access` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `dealer_i18n_fk_de5aa8`
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
    CONSTRAINT `dealer_contact_i18n_fk_ad8fa2`
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
    CONSTRAINT `dealer_contact_info_i18n_fk_58a41a`
        FOREIGN KEY (`id`)
        REFERENCES `dealer_contact_info` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
