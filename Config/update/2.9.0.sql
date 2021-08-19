
-- ---------------------------------------------------------------------
-- customer_favorite_dealer
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_favorite_dealer`;

CREATE TABLE `customer_favorite_dealer`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `customer_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fi_customer_favorite_dealer_dealer_id` (`dealer_id`),
    INDEX `customer_favorite_dealer_fi_7e8f3e` (`customer_id`),
    CONSTRAINT `fk_customer_favorite_dealer_dealer_id`
        FOREIGN KEY (`dealer_id`)
            REFERENCES `dealer` (`id`)
            ON UPDATE RESTRICT
            ON DELETE CASCADE,
    CONSTRAINT `customer_favorite_dealer_fk_7e8f3e`
        FOREIGN KEY (`customer_id`)
            REFERENCES `customer` (`id`)
            ON DELETE CASCADE
) ENGINE=InnoDB;