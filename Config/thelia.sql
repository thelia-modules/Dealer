
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- dealer_tab
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_tab`;

CREATE TABLE `dealer_tab`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `company` VARCHAR(255) NOT NULL,
    `address1` VARCHAR(255) NOT NULL,
    `address2` VARCHAR(255),
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `schedule` VARCHAR(255),
    `phone_number` VARCHAR(255),
    `web_site` VARCHAR(255),
    `latitude` FLOAT DEFAULT 0,
    `longitude` FLOAT DEFAULT 0,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
