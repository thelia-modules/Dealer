<?php

declare(strict_types=1);

namespace Dealer\Twig;

use Dealer\Service\PickupSlotService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Exposes the dealer pickup slots to the front-office Twig templates.
 *
 * Usage: {% set days = dealer_pickup_slots(dealer_id) %}
 *        {% set days = dealer_pickup_slots(dealer_id, '2026-08-10') %}
 */
final class DealerPickupExtension extends AbstractExtension
{
    public function __construct(private readonly PickupSlotService $pickupSlotService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dealer_pickup_slots', $this->getPickupSlots(...)),
        ];
    }

    /**
     * @return list<array{date: string, day: int, slots: list<array{time: string, datetime: string, remaining: int|null}>}>
     */
    public function getPickupSlots(int $dealerId, string|\DateTimeInterface|null $from = null): array
    {
        $fromDate = match (true) {
            $from instanceof \DateTimeInterface => $from,
            is_string($from) => new \DateTimeImmutable($from),
            default => null,
        };

        return $this->pickupSlotService->getAvailableSlots($dealerId, $fromDate);
    }
}
