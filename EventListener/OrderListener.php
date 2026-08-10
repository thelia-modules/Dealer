<?php

declare(strict_types=1);

namespace Dealer\EventListener;

use Dealer\Dealer;
use Dealer\Model\DealerOrderPickup;
use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Service\PickupSlotService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Log\Tlog;

/**
 * Persists the drive pickup slot chosen during checkout once the order is created.
 *
 * The choice is carried through the session (set by the front-office store-pickup
 * step); on ORDER_PAY it is re-validated and written to dealer_order_pickup so the
 * per-slot quota can be accounted for.
 */
class OrderListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly PickupSlotService $pickupSlotService,
    ) {
    }

    public function savePickupSlot(OrderEvent $event): void
    {
        $session = $this->requestStack->getCurrentRequest()?->getSession();

        if ($session === null) {
            return;
        }

        $dealerId = $session->get(Dealer::SESSION_PICKUP_DEALER_ID);
        $pickupDatetime = $session->get(Dealer::SESSION_PICKUP_DATETIME);

        if (empty($dealerId) || empty($pickupDatetime)) {
            return;
        }

        // The order is created by Thelia\Action\Order::create() earlier in ORDER_PAY;
        // the persisted order (with its id) is exposed via getPlacedOrder(), not getOrder().
        $order = $event->getPlacedOrder();

        if ($order === null || $order->getId() === null) {
            return;
        }

        $orderId = $order->getId();

        // A pickup-slot persistence issue must never abort the customer's order.
        try {
            $moment = new \DateTimeImmutable($pickupDatetime);

            // Re-validate at persistence time: reject a forged, stale or now-full slot
            // rather than saving an invalid/overbooked reservation.
            if (!$this->pickupSlotService->isSlotAvailable((int) $dealerId, $moment)) {
                Tlog::getInstance()->warning(sprintf(
                    'Dealer pickup slot no longer available at order creation (order %d, dealer %s, %s) — not recorded.',
                    $orderId,
                    $dealerId,
                    $pickupDatetime
                ));
            } elseif (!DealerOrderPickupQuery::create()->filterByOrderId($orderId)->exists()) {
                (new DealerOrderPickup())
                    ->setOrderId($orderId)
                    ->setDealerId((int) $dealerId)
                    ->setPickupDatetime($moment->format('Y-m-d H:i:s'))
                    ->save();
            }

            $session->remove(Dealer::SESSION_PICKUP_DEALER_ID);
            $session->remove(Dealer::SESSION_PICKUP_DATETIME);
        } catch (\Throwable $e) {
            // The order is already placed; the reservation is best-effort — log for diagnosis.
            Tlog::getInstance()->error('Failed to persist dealer pickup slot for order ' . $orderId . ': ' . $e->getMessage());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // This twig core has no ORDER_AFTER_CREATE; ORDER_PAY carries the freshly
            // created order (dispatched from CheckoutPaymentService::pay()).
            TheliaEvents::ORDER_PAY => ['savePickupSlot', 128],
        ];
    }
}
