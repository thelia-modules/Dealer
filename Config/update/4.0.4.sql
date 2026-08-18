-- One pickup reservation per order. Deduplicate first (the constraint did not exist
-- before, so a replayed ORDER_PAY may have written twice), then enforce it. These two
-- statements run first because they are the most likely to fail on real data: the
-- update engine stops at the first error and does not bump the module version, so a
-- rerun must not stumble on half-applied statements.
DELETE p1 FROM `dealer_order_pickup` p1
    INNER JOIN `dealer_order_pickup` p2
        ON p1.`order_id` = p2.`order_id` AND p1.`id` > p2.`id`;

ALTER TABLE `dealer_order_pickup` ADD UNIQUE `unique_dealer_order_pickup_order` (`order_id`);

-- Persist the schedule kind explicitly: base weekly hours vs exceptional entry.
ALTER TABLE `dealer_shedules` ADD `exception` TINYINT(1) DEFAULT 0 AFTER `closed`;

-- Backfill from the former inference: any period bound, closure or recurrence is an
-- exceptional entry. A row without a weekday cannot be a base row either (the weekly
-- grid could not display it, and would drop it on its first save): it becomes an
-- exceptional opening, visible and editable in the exceptions list.
UPDATE `dealer_shedules`
SET `exception` = 1
WHERE `closed` = 1
   OR `recurring` = 1
   OR `period_begin` IS NOT NULL
   OR `period_end` IS NOT NULL
   OR `day` IS NULL;
