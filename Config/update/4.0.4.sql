-- Persist the schedule kind explicitly: base weekly hours vs exceptional entry.
-- Backfill from the former inference (any period bound, closure or recurrence = exceptional).
ALTER TABLE `dealer_shedules` ADD `exception` TINYINT(1) DEFAULT 0 AFTER `closed`;

UPDATE `dealer_shedules`
SET `exception` = 1
WHERE `closed` = 1
   OR `recurring` = 1
   OR `period_begin` IS NOT NULL
   OR `period_end` IS NOT NULL;

-- One pickup reservation per order.
ALTER TABLE `dealer_order_pickup` ADD UNIQUE `unique_dealer_order_pickup_order` (`order_id`);
