<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/

namespace Dealer\Controller;

use Dealer\Controller\Base\BaseController;
use Dealer\Dealer;
use Dealer\Exception\ScheduleWeekValidationException;
use Dealer\Model\DealerShedules;
use Dealer\Service\PickupSlotService;
use Dealer\Service\ScheduleWeekService;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\TokenProvider;
use Thelia\Tools\URL;

/**
 * Class SchedulesController
 * @package Dealer\Controller
 */
#[Route('/admin/module/Dealer/schedules', name: 'dealer_schedules')]
class SchedulesController extends BaseController
{
    const CONTROLLER_ENTITY_NAME = "dealer-schedules";
    const CONTROLLER_CHECK_RESOURCE = Dealer::RESOURCES_SCHEDULES;

    /**
     * Use to get render of list
     * @return mixed
     */
    protected function getListRenderTemplate()
    {
        $id = $this->getRequest()->request->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
            ["dealer_id" => $id, ]));
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToListTemplate()
    {
        $id = $this->getRequest()->request->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
            ["dealer_id" => $id, ]));
    }

    /**
     * Use to get Edit render
     * @return mixed
     */
    protected function getEditRenderTemplate()
    {
        // The dealer-edit page is owned by DealerController (it supplies all the Twig
        // form views). Sub-controllers cannot render it, so redirect back to it instead.
        return $this->redirectToDealerEdit($this->resolveDealerId());
    }

    /**
     * Use to get Create render
     * @return mixed
     */
    protected function getCreateRenderTemplate()
    {
        return $this->render("dealer-edit");
    }

    /**
     * @return mixed
     */
    protected function getObjectId($object)
    {
        /** @var DealerShedules $object */
        return $object->getId();
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject(Request $request)
    {
        // TODO: Implement getExistingObject() method.
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param mixed $object
     */
    protected function hydrateObjectForm($object)
    {
        // TODO: Implement hydrateObjectForm() method.
    }

    /**
     * Method to get current controller associated service
     * @return object
     */
    protected function getService()
    {
        if (!$this->service) {
            $this->service = $this->getContainer()->get("dealer_schedules_service");
        }

        return $this->service;
    }

    /**
     * Create an object
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    #[Route('', name: '_create', methods: ['POST'])]
    public function createAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(self::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
                AccessManager::CREATE)
        ) {
            return $response;
        }

        // Create the Creation Form
        $creationForm = $this->getCreationForm();

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($creationForm, "POST");
            // Get the form field values
            $data = $form->getData();

            $slots = $this->extractSlots($data);
            $days = empty($data["day"]) ? [null] : $data["day"];
            $locale = $this->getCurrentEditionLocale();

            foreach ($days as $day) {
                $base = $data;
                $base["day"] = $day;

                if ($slots === []) {
                    $base["begin"] = null;
                    $base["end"] = null;
                    $this->getService()->createFromArray($base, $locale);

                    continue;
                }

                foreach ($slots as $slot) {
                    $row = $base;
                    $row["begin"] = $slot["begin"];
                    $row["end"] = $slot["end"];
                    $this->getService()->createFromArray($row, $locale);
                }
            }


            // Substitute _ID_ in the URL with the ID of the created object
            $successUrl = $creationForm->getSuccessUrl();

            $con->commit();

            if ($this->getRequest()->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            // Redirect to the success URL
            return $this->generateRedirect($successUrl);
        } catch (FormValidationException $ex) {
            $con->rollBack();
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $con->rollBack();
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'message' => $error_msg], 422);
        }

        // The redirect that follows would lose a ParserContext error (it only lives for
        // the current request): carry the message through the session like the update path.
        $this->getRequest()->getSession()->getFlashBag()->add('dealer_error', $error_msg);

        return $this->redirectToDealerEdit($this->resolveDealerId());
    }

    /**
     * Replace the whole base weekly grid in one call. Expects a JSON body
     * {dealer_id: int, week: {0: [{begin, end}, …], …}} and a CSRF token in the
     * _token query parameter; responds with JSON so the grid can show every
     * validation error inline, attached to its weekday, without a page reload.
     */
    #[Route('/week', name: '_week', methods: ['POST'])]
    public function saveWeekAction(
        Request $request,
        TokenProvider $tokenProvider,
        ScheduleWeekService $scheduleWeekService
    ): JsonResponse {
        if (null !== $this->checkAuth(self::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(), AccessManager::UPDATE)) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        try {
            $tokenProvider->checkToken((string) $request->query->get('_token'));

            $payload = json_decode($request->getContent(), true, 8, \JSON_THROW_ON_ERROR);
            $dealerId = (int) ($payload['dealer_id'] ?? 0);

            if ($dealerId <= 0 || !is_array($payload['week'] ?? null)) {
                throw new \InvalidArgumentException('Invalid payload.');
            }

            $scheduleWeekService->replaceWeek($dealerId, $payload['week']);

            return new JsonResponse(['success' => true]);
        } catch (ScheduleWeekValidationException $exception) {
            return new JsonResponse([
                'success' => false,
                'errors' => $exception->getErrorsByDay(),
            ], 422);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => Translator::getInstance()->trans(
                    'The schedule could not be saved: %error',
                    ['%error' => $exception->getMessage()],
                    Dealer::MESSAGE_DOMAIN
                ),
            ], 422);
        }
    }

    /**
     * What the customers currently see: the upcoming pickup days and slots computed
     * by the slot engine, so the manager can check the effect of the hours,
     * exceptions and pickup settings without leaving the tab.
     */
    #[Route('/preview', name: '_preview', methods: ['GET'])]
    public function previewAction(Request $request, PickupSlotService $pickupSlotService): JsonResponse
    {
        if (null !== $this->checkAuth(self::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(), AccessManager::VIEW)) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        $dealerId = (int) $request->query->get('dealer_id');

        if ($dealerId <= 0) {
            return new JsonResponse(['success' => false, 'message' => 'Missing dealer_id'], 400);
        }

        return new JsonResponse([
            'success' => true,
            'days' => $pickupSlotService->getAvailableSlots($dealerId),
        ]);
    }

    /**
     * @return list<array{begin: string, end: string}>
     */
    protected function extractSlots($data): array
    {
        $slots = [];
        foreach ($data["slots"] ?? [] as $slot) {
            if (!empty($slot["begin"]) && !empty($slot["end"])) {
                $slots[] = ["begin" => $slot["begin"], "end" => $slot["end"]];
            }
        }

        return $slots;
    }

    /**
     * Deletion with a visible outcome: the parent implementation loses its error in a
     * redirect chain, this one reports through JSON (AJAX) or the session flash.
     */
    #[Route('/delete', name: '_delete', methods: ['POST'])]
    public function deleteAction(TokenProvider $tokenProvider, RequestStack $requestStack, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(self::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(), AccessManager::DELETE)) {
            return $response;
        }

        try {
            $tokenProvider->checkToken(
                (string) $this->getRequest()->query->get('_token')
            );

            $this->getService()->deleteFromId(
                $this->getRequest()->request->get(static::CONTROLLER_ENTITY_NAME . '_id')
            );

            if ($this->getRequest()->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }
        } catch (\Exception $exception) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => $exception->getMessage()], 422);
            }

            $this->getRequest()->getSession()->getFlashBag()->add('dealer_error', $exception->getMessage());
        }

        return $this->redirectToDealerEdit($this->resolveDealerId());
    }

    /**
     */
    #[Route('/update', name: '_update', methods: ['POST'])]
    public function processUpdateAction(RequestStack $requestStack)
    {
        if (null !== $response = $this->checkAuth(self::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(), AccessManager::UPDATE)) {
            return $response;
        }

        $changeForm = $this->getUpdateForm($this->getRequest());
        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            $form = $this->validateForm($changeForm, 'POST');
            $data = $form->getData();

            $this->getService()->updateFromArray($data, $this->getCurrentEditionLocale());
            $con->commit();

            if ($this->getRequest()->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            return $this->redirectToDealerEdit((int) ($data['dealer_id'] ?? $this->resolveDealerId()));
        } catch (FormValidationException $ex) {
            $con->rollBack();
            $errorMessage = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $con->rollBack();
            $errorMessage = $ex->getMessage();
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'message' => $errorMessage], 422);
        }

        $this->getRequest()->getSession()->getFlashBag()->add('dealer_error', $errorMessage);

        return $this->redirectToDealerEdit($this->resolveDealerId());
    }

    /**
     * Resolve the dealer id from the posted form (create/update prefixes) or the request.
     */
    private function resolveDealerId(): ?int
    {
        $request = $this->getRequest();

        foreach (['dealer-schedules_update', 'dealer-schedules_create'] as $formName) {
            $formData = $request->request->all($formName);
            if (!empty($formData['dealer_id'])) {
                return (int) $formData['dealer_id'];
            }
        }

        $id = $request->request->get('dealer_id') ?? $request->query->get('dealer_id');

        return $id !== null ? (int) $id : null;
    }

    private function redirectToDealerEdit(?int $dealerId): RedirectResponse
    {
        return new RedirectResponse(URL::getInstance()->absoluteUrl(
            '/admin/module/Dealer/dealer/edit',
            ['dealer_id' => $dealerId]
        ) . '#schedules');
    }
}
