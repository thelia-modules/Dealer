SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `dealer_shedules` ADD `title` VARCHAR(255) AFTER `period_end`;

-- ---------------------------------------------------------------------
-- dealer_pickup_config
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_pickup_config`;

CREATE TABLE `dealer_pickup_config`
(
    `dealer_id` INTEGER NOT NULL,
    `prep_delay_minutes` INTEGER DEFAULT 0 NOT NULL,
    `orderable_days` INTEGER DEFAULT 7 NOT NULL,
    `slot_duration_minutes` INTEGER DEFAULT 60 NOT NULL,
    `max_orders_per_slot` INTEGER DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`dealer_id`),
    CONSTRAINT `fk_dealer_pickup_config_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- dealer_order_pickup
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dealer_order_pickup`;

CREATE TABLE `dealer_order_pickup`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dealer_id` INTEGER NOT NULL,
    `order_id` INTEGER NOT NULL,
    `pickup_datetime` DATETIME NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_dealer_order_pickup_slot` (`dealer_id`, `pickup_datetime`),
    INDEX `fi_dealer_order_pickup_order_id` (`order_id`),
    CONSTRAINT `fk_dealer_order_pickup_dealer_id`
        FOREIGN KEY (`dealer_id`)
        REFERENCES `dealer` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_dealer_order_pickup_order_id`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
