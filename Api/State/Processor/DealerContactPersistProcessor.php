<?php

declare(strict_types=1);

namespace Dealer\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Dealer\Api\Resource\DealerContact;
use Dealer\Api\Scope\DealerScopeResolver;
use Dealer\Model\DealerContactQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;
use Thelia\Config\DatabaseConfiguration;

/**
 * A store has one default contact at most, as the back office keeps it
 * (Dealer\Service\ContactService::setDefault): a contact written as default takes the flag off
 * the store's other contacts. Both writes share one transaction, the store's contacts locked
 * first, so a failure or two concurrent calls never leave two defaults, or none.
 */
final readonly class DealerContactPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private DealerScopedPersistProcessor $dealerScopedPersistProcessor,
        private DealerScopeResolver $dealerScopeResolver,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof DealerContact || !$data->getIsDefault()) {
            return $this->dealerScopedPersistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $connection = Propel::getWriteConnection(DatabaseConfiguration::THELIA_CONNECTION_NAME);
        $connection->beginTransaction();

        try {
            $dealerId = $this->dealerScopeResolver->resolveWrittenDealerId($data->owningDealerId());
            DealerContactQuery::create()->filterByDealerId($dealerId)->lockForUpdate()->find($connection);

            $contact = $this->dealerScopedPersistProcessor->process($data, $operation, $uriVariables, $context);

            DealerContactQuery::create()
                ->filterByDealerId($dealerId)
                ->filterByIsDefault(true)
                ->filterById($contact->getId(), Criteria::NOT_EQUAL)
                ->update(['IsDefault' => false, 'UpdatedAt' => new \DateTime()], $connection);

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();

            throw $exception;
        }

        return $contact;
    }
}
