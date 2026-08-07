<?php

declare(strict_types=1);

namespace Dealer\EventListener;

use Dealer\Dealer;
use Dealer\Model\DealerOrderPickup;
use Dealer\Model\DealerOrderPickupQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;

/**
 * Persists the drive pickup slot chosen during checkout once the order is created.
 *
 * The choice is carried through the session (set by the front-office store-pickup
 * step); on ORDER_AFTER_CREATE it is written to dealer_order_pickup so the per-slot
 * quota can be accounted for.
 */
class OrderListener implements EventSubscriberInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
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
            $alreadyStored = DealerOrderPickupQuery::create()
                ->filterByOrderId($orderId)
                ->exists();

            if (!$alreadyStored) {
                (new DealerOrderPickup())
                    ->setOrderId($orderId)
                    ->setDealerId((int) $dealerId)
                    ->setPickupDatetime($pickupDatetime)
                    ->save();
            }

            $session->remove(Dealer::SESSION_PICKUP_DEALER_ID);
            $session->remove(Dealer::SESSION_PICKUP_DATETIME);
        } catch (\Throwable) {
            // Swallow: the order is already placed, the slot reservation is best-effort.
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
