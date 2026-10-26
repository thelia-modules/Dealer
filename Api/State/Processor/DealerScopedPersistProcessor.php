<?php

declare(strict_types=1);

namespace Dealer\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Dealer\Api\Scope\DealerScopedResourceInterface;
use Dealer\Api\Scope\DealerScopeResolver;
use Thelia\Api\Bridge\Propel\State\PropelPersistProcessor;

/**
 * Writes a store resource through the core Propel processor once its store is known to be in
 * scope. A POST reads no row first, so this is where its store is checked; a PATCH has already
 * been bounded by its read.
 */
final readonly class DealerScopedPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private PropelPersistProcessor $persistProcessor,
        private DealerScopeResolver $dealerScopeResolver,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof DealerScopedResourceInterface) {
            throw new \LogicException(sprintf('%s only writes %s resources.', self::class, DealerScopedResourceInterface::class));
        }

        $data->assignDealer($this->dealerScopeResolver->resolveWrittenDealerId($data->owningDealerId()));

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
