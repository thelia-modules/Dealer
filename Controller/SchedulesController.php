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
use Dealer\Model\DealerShedules;
use Propel\Runtime\Propel;
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
        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans("%obj creation", ['%obj' => static::CONTROLLER_ENTITY_NAME]),
                $error_msg,
                $creationForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->generateErrorRedirect($creationForm);
        }
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
     */
    #[Route('/delete', name: '_delete', methods: ['POST'])]
    public function deleteAction(TokenProvider $tokenProvider, RequestStack $requestStack, ParserContext $parserContext)
    {
        return parent::deleteAction($tokenProvider, $requestStack, $parserContext); // TODO: Change the autogenerated stub
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

            return $this->redirectToDealerEdit((int) ($data['dealer_id'] ?? $this->resolveDealerId()));
        } catch (FormValidationException $ex) {
            $con->rollBack();
            $errorMessage = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $con->rollBack();
            $errorMessage = $ex->getMessage();
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

    /**
     */
    #[Route('/clone', name: '_clone', methods: ['POST'])]
    public function cloneAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(self::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
                AccessManager::CREATE)
        ) {
            return $response;
        }

        // Create the Creation Form
        $cloneForm = $this->getCloneForm();

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($cloneForm, "POST");
            // Get the form field values
            $data = $form->getData();

            $this->getService()->cloneFromArray($data);


            // Substitute _ID_ in the URL with the ID of the created object
            $successUrl = $cloneForm->getSuccessUrl();

            $con->commit();

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
        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj creation", ['%obj' => static::CONTROLLER_ENTITY_NAME]),
                $error_msg,
                $cloneForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->getListRenderTemplate();
        }
    }

    /**
     * Method to get Base Clone Form
     * @return \Thelia\Form\BaseForm
     */
    protected function getCloneForm()
    {
        return $this->createForm(static::CONTROLLER_ENTITY_NAME . "_clone");
    }
}
