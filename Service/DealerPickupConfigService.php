<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Model\DealerPickupConfig;
use Dealer\Model\DealerPickupConfigQuery;

/**
 * Read/write access to the per-dealer pickup configuration
 * (preparation delay, orderable days, slot duration, per-slot quota).
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
}
