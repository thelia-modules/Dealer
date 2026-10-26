<?php

declare(strict_types=1);

namespace Dealer\Api\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Dealer\Api\Resource\DealerPickupConfig;
use Dealer\Api\Scope\DealerScopeResolver;
use Dealer\Model\DealerQuery;
use Dealer\Service\DealerPickupConfigService;

/**
 * Reads the pickup settings of a store in scope; any other store is not found.
 */
final readonly class DealerPickupConfigProvider implements ProviderInterface
{
    public function __construct(
        private DealerScopeResolver $dealerScopeResolver,
        private DealerPickupConfigService $pickupConfigService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?DealerPickupConfig
    {
        $dealerId = (int) ($uriVariables['dealerId'] ?? 0);
        $this->dealerScopeResolver->assertReachable($dealerId);

        $dealer = DealerQuery::create()->findPk($dealerId);

        if (null === $dealer) {
            return null;
        }

        return DealerPickupConfig::fromStore($dealer, $this->pickupConfigService->get($dealerId));
    }
}
