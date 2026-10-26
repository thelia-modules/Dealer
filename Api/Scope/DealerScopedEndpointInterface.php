<?php

declare(strict_types=1);

namespace Dealer\Api\Scope;

/**
 * Marks an API resource whose every operation is bounded by the store scope
 * (DealerScopeInterface rules), so that a project can check its own request parameters before
 * the payload is read.
 */
interface DealerScopedEndpointInterface
{
}
