<?php

declare(strict_types=1);

namespace Dealer\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Dealer\Api\Resource\DealerPickupConfig;
use Dealer\Service\DealerPickupConfigService;
use Propel\Runtime\Propel;
use Thelia\Config\DatabaseConfiguration;

/**
 * Saves the pickup settings and the store timezone together, as the "Pickup config" tab does.
 * The resource reaching here was read by DealerPickupConfigProvider, so its store is in scope.
 */
final readonly class DealerPickupConfigProcessor implements ProcessorInterface
{
    public function __construct(
        private DealerPickupConfigService $pickupConfigService,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): DealerPickupConfig
    {
        if (!$data instanceof DealerPickupConfig) {
            throw new \LogicException(sprintf('%s only writes %s resources.', self::class, DealerPickupConfig::class));
        }

        $connection = Propel::getWriteConnection(DatabaseConfiguration::THELIA_CONNECTION_NAME);
        $connection->beginTransaction();

        try {
            $this->pickupConfigService->saveTimezone($data->dealerId, (string) $data->timezone);
            $this->pickupConfigService->save(
                $data->dealerId,
                (int) $data->prepDelayMinutes,
                (int) $data->orderableDays,
                (int) $data->slotDurationMinutes,
                (int) $data->maxOrdersPerSlot,
            );
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();

            throw $exception;
        }

        return $data;
    }
}
