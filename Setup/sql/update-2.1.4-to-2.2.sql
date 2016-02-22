-- ---------------------------------------------------------------------
-- dealer_product
-- ---------------------------------------------------------------------

CREATE TABLE `dealer_product`
(
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `dealer_id` INTEGER NOT NULL,
  `product_id` INTEGER NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `version` INTEGER DEFAULT 0,
  `version_created_at` DATETIME,
  `version_created_by` VARCHAR(100),
  PRIMARY KEY (`id`),
  INDEX `FI_dealer_product_dealer_id` (`dealer_id`),
  INDEX `FI_dealer_product_product_id` (`product_id`),
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

CREATE TABLE `dealer_product_version`
(
  `id` INTEGER NOT NULL,
  `dealer_id` INTEGER NOT NULL,
  `product_id` INTEGER NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `version` INTEGER DEFAULT 0 NOT NULL,
  `version_created_at` DATETIME,
  `version_created_by` VARCHAR(100),
  `dealer_id_version` INTEGER DEFAULT 0,
  `product_id_version` INTEGER DEFAULT 0,
  PRIMARY KEY (`id`,`version`),
  CONSTRAINT `dealer_product_version_FK_1`
  FOREIGN KEY (`id`)
  REFERENCES `dealer_product` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `dealer_version`
ADD COLUMN `dealer_product_ids` TEXT AFTER `dealer_brand_versions`,
ADD COLUMN `dealer_product_versions` TEXT AFTER `dealer_product_ids`;