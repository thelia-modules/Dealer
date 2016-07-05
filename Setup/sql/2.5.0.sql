ALTER TABLE `dealer_shedules`
  MODIFY COLUMN `day` int(11) DEFAULT NULL;
ALTER TABLE `dealer_shedules`
  MODIFY COLUMN `begin` time DEFAULT NULL;
ALTER TABLE `dealer_shedules`
  MODIFY COLUMN `end` time DEFAULT NULL;