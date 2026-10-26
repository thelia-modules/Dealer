<?php

declare(strict_types=1);

namespace Dealer\Api\Scope;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * The stores the current request may reach: the intersection of every DealerScopeInterface
 * rule. A store out of scope answers like a missing row (404), so the answer tells nothing
 * about it.
 */
final readonly class DealerScopeResolver
{
    /**
     * @param iterable<DealerScopeInterface> $scopes
     */
    public function __construct(
        #[AutowireIterator(DealerScopeInterface::TAG)]
        private iterable $scopes,
    ) {
    }

    /** @return list<int>|null null when no rule restricts the stores */
    public function dealerIds(): ?array
    {
        $dealerIds = null;

        foreach ($this->scopes as $scope) {
            $allowed = $scope->dealerIds();

            if (null === $allowed) {
                continue;
            }

            $dealerIds = null === $dealerIds ? $allowed : array_values(array_intersect($dealerIds, $allowed));
        }

        return $dealerIds;
    }

    public function assertReachable(int $dealerId): void
    {
        $dealerIds = $this->dealerIds();

        if (null !== $dealerIds && !\in_array($dealerId, $dealerIds, true)) {
            throw new NotFoundHttpException(sprintf('Store %d not found.', $dealerId));
        }
    }

    /**
     * The store a write names, or the only store in scope when it names none (a request bounded
     * to one store does not have to repeat it).
     */
    public function resolveWrittenDealerId(?int $named): int
    {
        if (null === $named) {
            $dealerIds = $this->dealerIds();

            if ([] === $dealerIds) {
                throw new NotFoundHttpException('No store in scope.');
            }

            if (null === $dealerIds || 1 !== \count($dealerIds)) {
                throw new UnprocessableEntityHttpException('dealerId: the store is required.');
            }

            return $dealerIds[0];
        }

        $this->assertReachable($named);

        return $named;
    }
}
