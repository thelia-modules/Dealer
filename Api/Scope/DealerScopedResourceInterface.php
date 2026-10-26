<?php

declare(strict_types=1);

namespace Dealer\Api\Scope;

use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * A Propel API resource that belongs to one store.
 *
 * Reads are bounded in the query (DealerScopeExtension), which also covers the read step of
 * PATCH and DELETE: a row of a store out of scope is not found. Writes are checked against the
 * store the row belongs to (DealerScopedPersistProcessor).
 */
interface DealerScopedResourceInterface extends DealerScopedEndpointInterface
{
    /**
     * @param list<int> $dealerIds
     */
    public static function restrictToDealers(ModelCriteria $query, array $dealerIds): void;

    /** The store the row belongs to, as the payload sets it; null when the payload names none. */
    public function owningDealerId(): ?int;

    /** Binds a row whose payload names no store to the only store in scope. */
    public function assignDealer(int $dealerId): void;
}
