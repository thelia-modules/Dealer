<?php

declare(strict_types=1);

namespace Dealer\Api\Scope;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * One rule bounding the stores the admin API may read and write in the current request.
 *
 * Every rule applies: the stores reachable are the intersection of what each one returns. The
 * module ships the back-office rule (AdministeredDealersScope); a project adds its own, for
 * instance to bound every call to the store a request parameter names.
 */
#[AutoconfigureTag(self::TAG)]
interface DealerScopeInterface
{
    public const TAG = 'dealer.api.scope';

    /**
     * @return list<int>|null the ids of the stores this rule lets through, null for no restriction
     */
    public function dealerIds(): ?array;
}
