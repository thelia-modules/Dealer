<?php

declare(strict_types=1);

namespace Dealer\Api\Extension;

use ApiPlatform\Metadata\Operation;
use Dealer\Api\Scope\DealerScopedResourceInterface;
use Dealer\Api\Scope\DealerScopeResolver;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Api\Bridge\Propel\Extension\QueryCollectionExtensionInterface;
use Thelia\Api\Bridge\Propel\Extension\QueryItemExtensionInterface;

/**
 * Bounds every read of a store resource to the stores in scope: the collections, the item
 * reads, and the read step of PATCH and DELETE. A row of another store is not found.
 */
final readonly class DealerScopeExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private DealerScopeResolver $dealerScopeResolver,
    ) {
    }

    public function applyToCollection(ModelCriteria $query, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($query, $resourceClass);
    }

    public function applyToItem(ModelCriteria $query, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($query, $resourceClass);
    }

    private function restrict(ModelCriteria $query, string $resourceClass): void
    {
        if (!is_subclass_of($resourceClass, DealerScopedResourceInterface::class)) {
            return;
        }

        $dealerIds = $this->dealerScopeResolver->dealerIds();

        if (null !== $dealerIds) {
            $resourceClass::restrictToDealers($query, $dealerIds);
        }
    }
}
