<?php

declare(strict_types=1);

namespace Dealer\Controller;

use Dealer\Dealer;
use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Service\PickupSlotService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;
use Thelia\Tools\TokenProvider;
use Thelia\Tools\URL;

/**
 * Lets the back-office move the pickup slot booked on an order: after adding an
 * exceptional closure, the manager re-schedules the impacted pickups and warns the
 * customers himself. Two modes: pick one of the slots the engine currently offers
 * (validated like a customer choice), or force a free datetime — the manager takes
 * responsibility for hours and quota, which is exactly the closure use case.
 */
class OrderPickupController extends BaseAdminController
{
    #[Route('/admin/module/Dealer/order-pickup/update', name: 'dealer_order_pickup_update', methods: ['POST'])]
    public function updateAction(
        Request $request,
        TokenProvider $tokenProvider,
        PickupSlotService $pickupSlotService
    ): RedirectResponse {
        if (null !== $response = $this->checkAuth(Dealer::RESOURCES_SCHEDULES, Dealer::getModuleCode(), AccessManager::UPDATE)) {
            return $response;
        }

        $orderId = (int) $request->request->get('order_id');
        $backUrl = $this->resolveBackUrl($request, $orderId);
        $translator = Translator::getInstance();
        $flashBag = $request->getSession()->getFlashBag();

        try {
            $tokenProvider->checkToken((string) $request->query->get('_token'));

            $pickup = DealerOrderPickupQuery::create()->findOneByOrderId($orderId);

            if ($pickup === null) {
                throw new \RuntimeException(
                    $translator->trans('This order has no drive pickup to edit.', [], Dealer::MESSAGE_DOMAIN)
                );
            }

            $mode = (string) $request->request->get('mode', 'slot');
            $rawDatetime = (string) $request->request->get(
                $mode === 'custom' ? 'custom_datetime' : 'pickup_datetime'
            );

            if (trim($rawDatetime) === '') {
                throw new \RuntimeException(
                    $translator->trans('Please choose a pickup slot.', [], Dealer::MESSAGE_DOMAIN)
                );
            }

            try {
                $moment = new \DateTimeImmutable($rawDatetime);
            } catch (\Exception) {
                throw new \RuntimeException(
                    $translator->trans('The pickup date is not valid.', [], Dealer::MESSAGE_DOMAIN)
                );
            }

            if ($mode !== 'custom'
                && !$pickupSlotService->isSlotAvailable($pickup->getDealerId(), $moment)) {
                throw new \RuntimeException(
                    $translator->trans(
                        'This slot is no longer offered (full, past or outside the opening hours). Use the free datetime mode to force it.',
                        [],
                        Dealer::MESSAGE_DOMAIN
                    )
                );
            }

            $pickup->setPickupDatetime($moment->format('Y-m-d H:i:s'))->save();

            $flashBag->add(
                'success',
                $translator->trans(
                    'The pickup slot has been moved to %datetime. Remember to warn the customer.',
                    ['%datetime' => $moment->format('d/m/Y H:i')],
                    Dealer::MESSAGE_DOMAIN
                )
            );

            if ($mode === 'custom'
                && !$pickupSlotService->isSlotWithinOpeningHours($pickup->getDealerId(), $moment)) {
                $flashBag->add(
                    'warning',
                    $translator->trans(
                        'Note: this datetime is outside the store opening hours.',
                        [],
                        Dealer::MESSAGE_DOMAIN
                    )
                );
            }
        } catch (\RuntimeException $exception) {
            $flashBag->add('danger', $exception->getMessage());
        } catch (\Exception $exception) {
            Tlog::getInstance()->error('Order pickup update failed: ' . $exception->getMessage());
            $flashBag->add(
                'danger',
                $translator->trans('The pickup slot could not be updated.', [], Dealer::MESSAGE_DOMAIN)
            );
        }

        return new RedirectResponse($backUrl);
    }

    /**
     * Go back to the order edit page the form came from; the posted success_url is
     * only trusted when it stays on this host.
     */
    private function resolveBackUrl(Request $request, int $orderId): string
    {
        $posted = (string) $request->request->get('success_url');

        if ($posted !== '' && str_starts_with($posted, '/') && !str_starts_with($posted, '//')) {
            return URL::getInstance()->absoluteUrl($posted);
        }

        return URL::getInstance()->absoluteUrl('/admin/order/update/' . $orderId);
    }
}
