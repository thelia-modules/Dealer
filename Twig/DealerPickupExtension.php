<?php

declare(strict_types=1);

namespace Dealer\Twig;

use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Model\DealerQuery;
use Dealer\Service\PickupSlotService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Exposes the dealer pickup data to the Twig templates (front-office and back-office).
 *
 *  {% set days = dealer_pickup_slots(dealer_id) %}        available slots for a dealer
 *  {% set pickup = dealer_order_pickup(order_id) %}       the pickup booked for an order
 */
final class DealerPickupExtension extends AbstractExtension
{
    public function __construct(
        private readonly PickupSlotService $pickupSlotService,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dealer_pickup_slots', $this->getPickupSlots(...)),
            new TwigFunction('dealer_order_pickup', $this->getOrderPickup(...)),
        ];
    }

    /**
     * The pickup days offered to the customer. Slots flagged 'full' are meant to be
     * rendered greyed out and unselectable, not hidden.
     *
     * @return list<array{date: string, day: int, slots: list<array{time: string, datetime: string, remaining: int|null, full: bool}>}>
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

    /**
     * The pickup slot booked for an order, or null when the order has no drive pickup.
     *
     * @return array{dealer_id: int, dealer_title: string, dealer_address: string, datetime: \DateTimeInterface}|null
     */
    public function getOrderPickup(int $orderId): ?array
    {
        $pickup = DealerOrderPickupQuery::create()->findOneByOrderId($orderId);

        if ($pickup === null) {
            return null;
        }

        $locale = $this->requestStack->getCurrentRequest()?->getSession()?->getLang()?->getLocale() ?? 'fr_FR';

        $dealer = DealerQuery::create()->findPk($pickup->getDealerId());
        $title = '';
        $address = '';

        if ($dealer !== null) {
            $dealer->setLocale($locale);
            $title = $dealer->getTitle();
            $address = implode(', ', array_filter([$dealer->getAddress1(), $dealer->getZipcode(), $dealer->getCity()]));
        }

        return [
            'dealer_id' => $pickup->getDealerId(),
            'dealer_title' => $title,
            'dealer_address' => $address,
            'datetime' => $pickup->getPickupDatetime(),
        ];
    }
}
