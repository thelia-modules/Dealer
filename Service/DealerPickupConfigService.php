<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Model\DealerPickupConfig;
use Dealer\Model\DealerPickupConfigQuery;
use Dealer\Model\DealerQuery;

/**
 * Read/write access to the per-dealer pickup configuration
 * (preparation delay, orderable days, slot duration, per-slot quota).
 *
 * The timezone lives on the dealer itself — it qualifies the store, not the slot
 * settings — but it is edited from the same screen, hence its writer here.
 */
class DealerPickupConfigService
{
    public function get(int $dealerId): DealerPickupConfig
    {
        return DealerPickupConfigQuery::create()->findPk($dealerId)
            ?? (new DealerPickupConfig())
                ->setDealerId($dealerId)
                ->setPrepDelayMinutes(0)
                ->setOrderableDays(7)
                ->setSlotDurationMinutes(60)
                ->setMaxOrdersPerSlot(0);
    }

    public function save(
        int $dealerId,
        int $prepDelayMinutes,
        int $orderableDays,
        int $slotDurationMinutes,
        int $maxOrdersPerSlot
    ): DealerPickupConfig {
        $config = DealerPickupConfigQuery::create()->findOneByDealerId($dealerId)
            ?? (new DealerPickupConfig())->setDealerId($dealerId);

        $config
            ->setPrepDelayMinutes($prepDelayMinutes)
            ->setOrderableDays($orderableDays)
            ->setSlotDurationMinutes($slotDurationMinutes)
            ->setMaxOrdersPerSlot($maxOrdersPerSlot)
            ->save();

        return $config;
    }

    public function saveTimezone(int $dealerId, string $timezone): void
    {
        $dealer = DealerQuery::create()->findPk($dealerId);

        if ($dealer === null || !in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            return;
        }

        $dealer->setTimezone($timezone)->save();
    }
}
