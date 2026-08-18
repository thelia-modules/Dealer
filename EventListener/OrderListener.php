<?php

declare(strict_types=1);

namespace Dealer\EventListener;

use Dealer\Dealer;
use Dealer\Model\DealerOrderPickup;
use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Service\PickupSlotService;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Domain\Checkout\Exception\InvalidDeliveryException;
use Thelia\Log\Tlog;
use Thelia\Model\ModuleQuery;

/**
 * Persists the drive pickup slot chosen during checkout once the order is created.
 *
 * The choice is carried through the session (set by the front-office store-pickup step).
 * Two listeners bracket the core order creation on ORDER_PAY:
 *  - before it, the slot is re-validated under a named database lock, so a slot that
 *    filled up (or expired) aborts the checkout with a clear message instead of
 *    producing a paid order without any pickup slot;
 *  - after it, the reservation is written to dealer_order_pickup, still under the same
 *    lock, which closes the check-then-insert race between two concurrent checkouts.
 *
 * The slot is only enforced when the order's delivery module is one of the configured
 * pickup modules (config value `dealer_pickup_delivery_modules`, default "LocalPickup"),
 * so a stale session choice cannot block a home delivery order.
 */
class OrderListener implements EventSubscriberInterface
{
    private const LOCK_TIMEOUT_SECONDS = 5;

    private ?string $heldLockName = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly PickupSlotService $pickupSlotService,
    ) {
    }

    /**
     * Runs before the core order creation (priority > 128): a full, stale or forged slot
     * must abort the checkout while nothing has been persisted yet.
     */
    public function assertPickupSlotAvailable(OrderEvent $event): void
    {
        $session = $this->requestStack->getCurrentRequest()?->getSession();

        if ($session === null) {
            return;
        }

        $dealerId = $session->get(Dealer::SESSION_PICKUP_DEALER_ID);
        $pickupDatetime = $session->get(Dealer::SESSION_PICKUP_DATETIME);

        if (empty($dealerId) || empty($pickupDatetime) || !$this->isPickupDelivery($event)) {
            return;
        }

        try {
            $moment = new \DateTimeImmutable((string) $pickupDatetime);
        } catch (\Exception) {
            $session->remove(Dealer::SESSION_PICKUP_DEALER_ID);
            $session->remove(Dealer::SESSION_PICKUP_DATETIME);

            throw $this->slotUnavailableException();
        }

        // Serialize concurrent checkouts on the same slot: the lock is held across the
        // core order creation and released by savePickupSlot() in the same request.
        $this->acquireSlotLock((int) $dealerId, $moment);

        if (!$this->pickupSlotService->isSlotAvailable((int) $dealerId, $moment)) {
            $this->releaseSlotLock();

            throw $this->slotUnavailableException();
        }
    }

    /**
     * Runs after the core order creation (priority < 128): records the reservation,
     * then releases the lock taken by assertPickupSlotAvailable().
     */
    public function savePickupSlot(OrderEvent $event): void
    {
        try {
            $session = $this->requestStack->getCurrentRequest()?->getSession();

            if ($session === null) {
                return;
            }

            $dealerId = $session->get(Dealer::SESSION_PICKUP_DEALER_ID);
            $pickupDatetime = $session->get(Dealer::SESSION_PICKUP_DATETIME);

            if (empty($dealerId) || empty($pickupDatetime) || !$this->isPickupDelivery($event)) {
                return;
            }

            // The order is created by Thelia\Action\Order::create() earlier in ORDER_PAY;
            // the persisted order (with its id) is exposed via getPlacedOrder(), not getOrder().
            $order = $event->getPlacedOrder();

            if ($order === null || $order->getId() === null) {
                return;
            }

            $orderId = $order->getId();

            // A pickup-slot persistence issue past this point must never abort the
            // customer's order: the slot was validated under lock just before creation.
            try {
                $moment = new \DateTimeImmutable((string) $pickupDatetime);

                if (!DealerOrderPickupQuery::create()->filterByOrderId($orderId)->exists()) {
                    (new DealerOrderPickup())
                        ->setOrderId($orderId)
                        ->setDealerId((int) $dealerId)
                        ->setPickupDatetime($moment->format('Y-m-d H:i:s'))
                        ->save();
                }

                $session->remove(Dealer::SESSION_PICKUP_DEALER_ID);
                $session->remove(Dealer::SESSION_PICKUP_DATETIME);
            } catch (\Throwable $e) {
                Tlog::getInstance()->error(
                    'Failed to persist dealer pickup slot for order ' . $orderId . ': ' . $e->getMessage()
                );
            }
        } finally {
            $this->releaseSlotLock();
        }
    }

    private function isPickupDelivery(OrderEvent $event): bool
    {
        $moduleId = $event->getOrder()?->getDeliveryModuleId();

        if (empty($moduleId)) {
            // Cannot tell: keep the historical behaviour and treat the session choice as binding.
            return true;
        }

        $code = ModuleQuery::create()->findPk($moduleId)?->getCode();

        if ($code === null) {
            return true;
        }

        $configured = (string) Dealer::getConfigValue(Dealer::CONFIG_PICKUP_DELIVERY_MODULES, 'LocalPickup');
        $codes = array_filter(array_map('trim', explode(',', $configured)));

        return in_array($code, $codes, true);
    }

    private function acquireSlotLock(int $dealerId, \DateTimeImmutable $moment): void
    {
        $name = 'dealer_pickup_' . $dealerId . '_' . $moment->format('YmdHis');

        $statement = Propel::getConnection()->prepare('SELECT GET_LOCK(?, ?)');
        $statement->execute([$name, self::LOCK_TIMEOUT_SECONDS]);

        if ((int) $statement->fetchColumn() !== 1) {
            // Another checkout holds the slot for longer than the timeout: fail closed.
            throw $this->slotUnavailableException();
        }

        $this->heldLockName = $name;
    }

    private function releaseSlotLock(): void
    {
        if ($this->heldLockName === null) {
            return;
        }

        $statement = Propel::getConnection()->prepare('SELECT RELEASE_LOCK(?)');
        $statement->execute([$this->heldLockName]);
        $this->heldLockName = null;
    }

    private function slotUnavailableException(): InvalidDeliveryException
    {
        return new InvalidDeliveryException(
            Translator::getInstance()->trans(
                'The selected pickup slot is no longer available. Please choose another one.',
                [],
                Dealer::MESSAGE_DOMAIN
            )
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Thelia\Action\Order::create() listens to ORDER_PAY at priority 128:
            // validate the slot before it runs, persist the reservation after it.
            TheliaEvents::ORDER_PAY => [
                ['assertPickupSlotAvailable', 192],
                ['savePickupSlot', 64],
            ],
        ];
    }
}
