<?php

declare(strict_types=1);

namespace Dealer\Controller;

use Dealer\Dealer;
use Dealer\Service\DealerPickupConfigService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

/**
 * Saves the per-dealer pickup configuration from the dealer edit page.
 */
class PickupConfigController extends BaseAdminController
{
    #[Route('/admin/module/Dealer/pickup-config/update', name: 'dealer_pickup_config_update', methods: ['POST'])]
    public function processUpdateAction(RequestStack $requestStack, DealerPickupConfigService $configService)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, Dealer::getModuleCode(), AccessManager::UPDATE)) {
            return $response;
        }

        $request = $requestStack->getCurrentRequest();
        $form = $this->createForm('dealer-pickup-config', FormType::class, []);
        $error_msg = false;

        try {
            $validatedForm = $this->validateForm($form, 'POST');
            $data = $validatedForm->getData();

            $configService->save(
                (int) $data['dealer_id'],
                (int) $data['prep_delay_minutes'],
                (int) $data['orderable_days'],
                (int) $data['slot_duration_minutes'],
                (int) $data['max_orders_per_slot'],
            );

            return $this->redirectToDealer((int) $data['dealer_id']);
        } catch (FormValidationException $ex) {
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            // Flash the error so it survives the redirect and is shown on the dealer edit page.
            $request->getSession()->getFlashBag()->add('dealer_error', $error_msg);
        }

        $formData = $request->request->all('dealer-pickup-config');
        $dealerId = (int) ($formData['dealer_id'] ?? $request->request->get('dealer_id'));

        return $this->redirectToDealer($dealerId);
    }

    private function redirectToDealer(int $dealerId): RedirectResponse
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl('/admin/module/Dealer/dealer/edit', ['dealer_id' => $dealerId]) . '#pickup-config'
        );
    }
}
