SET FOREIGN_KEY_CHECKS = 0;

-- Annual recurrence flag: when set, the schedule applies every year on period_begin's
-- month/day (the year is ignored), e.g. every 25/12.
ALTER TABLE `dealer_shedules` ADD `recurring` TINYINT(1) DEFAULT 0 AFTER `period_end`;

SET FOREIGN_KEY_CHECKS = 1;
