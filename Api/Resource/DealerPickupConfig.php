<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use Dealer\Model\Dealer as DealerModel;
use Dealer\Model\DealerPickupConfig as DealerPickupConfigModel;
use Dealer\Api\Scope\DealerScopedEndpointInterface;
use Dealer\Dealer as DealerModule;
use Dealer\Api\State\Processor\DealerPickupConfigProcessor;
use Dealer\Api\State\Provider\DealerPickupConfigProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The "Pickup config" tab of a store. Not a Propel resource: the row is created on the first
 * save (until then the store runs on defaults), and the timezone it shows lives on the store
 * itself. Both are read and written through Dealer\Service\DealerPickupConfigService, as the
 * back office does.
 */
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/dealer_pickup_configs/{dealerId}',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_PICKUP_CONFIG],
                summary: 'Pickup settings of a store',
                description: 'Defaults are returned while the store has never saved any.',
            ),
        ),
        new Patch(
            uriTemplate: '/admin/dealer_pickup_configs/{dealerId}',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_PICKUP_CONFIG],
                summary: 'Update the pickup settings of a store',
                description: 'Partial update (`Content-Type: application/merge-patch+json`).',
            ),
            processor: DealerPickupConfigProcessor::class,
        ),
    ],
    provider: DealerPickupConfigProvider::class,
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
    validationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
)]
final class DealerPickupConfig implements DealerScopedEndpointInterface
{
    public const GROUP_ADMIN_READ = 'admin:dealer_pickup_config:read';
    public const GROUP_ADMIN_WRITE = 'admin:dealer_pickup_config:write';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_ADMIN_READ])]
    public int $dealerId;

    // The identifiers Dealer\Service\DealerPickupConfigService accepts: Assert\Timezone also
    // lets through the ICU aliases (Asia/Calcutta), which the service then refuses.
    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\Choice(callback: [\DateTimeZone::class, 'listIdentifiers'], message: 'This value is not a valid timezone.', groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?string $timezone = null;

    #[ApiProperty(description: 'Minutes between the order and the first pickup slot.')]
    #[Assert\NotNull(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\GreaterThanOrEqual(0, groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\LessThanOrEqual(10080, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $prepDelayMinutes = null;

    #[ApiProperty(description: 'Number of days ahead a pickup can be booked.')]
    #[Assert\NotNull(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\GreaterThanOrEqual(1, groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\LessThanOrEqual(365, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $orderableDays = null;

    #[Assert\NotNull(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\GreaterThanOrEqual(1, groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\LessThanOrEqual(1440, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $slotDurationMinutes = null;

    #[ApiProperty(description: 'Orders allowed per slot, 0 for no limit.')]
    #[Assert\NotNull(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\GreaterThanOrEqual(0, groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\LessThanOrEqual(10000, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $maxOrdersPerSlot = null;

    /** The pickup settings a store runs on, defaults included (the row exists after the first save). */
    public static function fromStore(DealerModel $store, DealerPickupConfigModel $config): self
    {
        $resource = new self();
        $resource->dealerId = (int) $store->getId();
        $resource->timezone = $store->getTimezone();
        $resource->prepDelayMinutes = (int) $config->getPrepDelayMinutes();
        $resource->orderableDays = (int) $config->getOrderableDays();
        $resource->slotDurationMinutes = (int) $config->getSlotDurationMinutes();
        $resource->maxOrdersPerSlot = (int) $config->getMaxOrdersPerSlot();

        return $resource;
    }

    /** @return array{timezone: ?string, prepDelayMinutes: ?int, orderableDays: ?int, slotDurationMinutes: ?int, maxOrdersPerSlot: ?int} */
    public function toArray(): array
    {
        return [
            'timezone' => $this->timezone,
            'prepDelayMinutes' => $this->prepDelayMinutes,
            'orderableDays' => $this->orderableDays,
            'slotDurationMinutes' => $this->slotDurationMinutes,
            'maxOrdersPerSlot' => $this->maxOrdersPerSlot,
        ];
    }
}
