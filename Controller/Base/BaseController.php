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

namespace Dealer\Controller\Base;

use Dealer\Dealer;
use Dealer\Form\DealerForm;
use Dealer\Model\DealerQuery;
use Propel\Generator\Model\Database;
use Propel\Runtime\Propel;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Thelia;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\TokenProvider;
use Thelia\Tools\URL;

/**
 * Class BaseController
 * @package Dealer\Controller\Base
 */
abstract class BaseController extends BaseAdminController
{
    protected $useFallbackTemplate = true;
    /**
     * Name of entity associated with controller
     */
    const CONTROLLER_ENTITY_NAME = null;
    /**
     * Name of resource to check
     */
    const CONTROLLER_CHECK_RESOURCE = '';

    /**
     * Current Service Associated to controller
     */
    protected $service;


    // ABSTRACT FUNCTIONS

    /**
     * Use to get render of list
     * @return mixed
     */
    abstract protected function getListRenderTemplate();

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    abstract protected function redirectToListTemplate();

    /**
     * Use to get Edit render
     * @return mixed
     */
    abstract protected function getEditRenderTemplate();

    /**
     * Use to get Create render
     * @return mixed
     */
    abstract protected function getCreateRenderTemplate();

    /**
     * @return mixed
     */
    abstract protected function getObjectId($object);

    /**
     * Load an existing object from the database
     */
    abstract protected function getExistingObject(Request $request);

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param mixed $object
     */
    abstract protected function hydrateObjectForm($object);


    // PUBLIC METHODS

    /**
     * The default action is displaying the list.
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function defaultAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(static::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
                AccessManager::VIEW)
        ) {
            return $response;
        }

        return $this->getListRenderTemplate();
    }

    /**
     * Create an object
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(static::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
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

            $createdObject = $this->getService()->createFromArray($data, $this->getCurrentEditionLocale());


            // Substitute _ID_ in the URL with the ID of the created object
            $successUrl = str_replace('_ID_', $this->getObjectId($createdObject), $creationForm->getSuccessUrl());

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
            return $this->getListRenderTemplate();
        }
    }

    /**
     * Load a object for modification, and display the edit template.
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function updateAction(ParserContext $parserContext, RequestStack $requestStack)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(static::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
                AccessManager::UPDATE)
        ) {
            return $response;
        }

        // Load object if exist
        if (null !== $object = $this->getExistingObject($requestStack->getCurrentRequest())) {
            // Hydrate the form abd pass it to the parser
            $changeForm = $this->hydrateObjectForm($object);

            // Pass it to the parser
            $parserContext->addForm($changeForm);
        }

        // Render the edition template.
        return $this->getEditRenderTemplate();
    }

    /**
     * Save changes on a modified object, and either go back to the object list, or stay on the edition page.
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function processUpdateAction(RequestStack $requestStack)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(static::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
                AccessManager::UPDATE)
        ) {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;

        // Create the Form from the request
        $changeForm = $this->getUpdateForm($this->getRequest());


        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($changeForm, "POST");

            // Get the form field values
            $data = $form->getData();

            $updatedObject = $this->getService()->updateFromArray($data, $this->getCurrentEditionLocale());

            // Check if object exist
            if (!$updatedObject) {
                throw new \LogicException(
                    Translator::getInstance()->trans("No %obj was updated.", ['%obj' => static::CONTROLLER_ENTITY_NAME])
                );
            }

            $con->commit();
            // If we have to stay on the same page, do not redirect to the successUrl,
            // just redirect to the edit page again.
            if ($requestStack->getCurrentRequest()->get('save_mode') === 'stay') {
                return $this->redirectToEditionTemplate($requestStack->getCurrentRequest());
            }

            // Redirect to the success URL
            return $this->generateSuccessRedirect($changeForm);
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
            // At this point, the form has errors, and should be redisplayed.
            $this->setupFormErrorContext(
                Translator::getInstance()->trans("%obj modification", ['%obj' => static::CONTROLLER_ENTITY_NAME]),
                $error_msg,
                $changeForm,
                $ex
            );

            return $this->getEditRenderTemplate();
        }
    }

    /**
     * Delete an object
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function deleteAction(TokenProvider $tokenProvider, RequestStack $requestStack, ParserContext $parserContext)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(static::CONTROLLER_CHECK_RESOURCE, Dealer::getModuleCode(),
                AccessManager::DELETE)
        ) {
            return $response;
        }

        $con = Propel::getConnection();
        $con->beginTransaction();
        try {
            // Check token
            $tokenProvider->checkToken(
                $requestStack->getCurrentRequest()->query->get("_token")
            );

            $this->getService()->deleteFromId($requestStack->getCurrentRequest()->request->get(static::CONTROLLER_ENTITY_NAME . "_id"));
            $con->commit();
            if ($requestStack->getCurrentRequest()->request->get("success_url") == null) {
                return $this->redirectToListTemplate();
            } else {
                return new RedirectResponse(URL::getInstance()->absoluteUrl($this->getRequest()->request->get("success_url")));
            }
        } catch (\Exception $e) {
            $con->rollBack();

            return $this->renderAfterDeleteError($e, $parserContext);
        }
    }

    // HELPERS
    /**
     * Method to get current controller associated service
     * @return object
     */
    protected function getService()
    {
        if (!$this->service) {
            $this->service = $this->getContainer()->get(static::CONTROLLER_ENTITY_NAME . "_service");
        }

        return $this->service;
    }

    /**
     * Method to get Base Creation Form
     * @return \Thelia\Form\BaseForm
     */
    protected function getCreationForm()
    {
        return $this->createForm(static::CONTROLLER_ENTITY_NAME . ".create");
    }

    /**
     * Method to get Base Update Form
     * @param array $data
     * @return \Thelia\Form\BaseForm
     */
    protected function getUpdateForm($data = [])
    {
        if (!is_array($data)) {
            $data = [];
        }

        return $this->createForm(static::CONTROLLER_ENTITY_NAME . ".update", FormType::class, $data);
    }

    /**
     * @param \Exception $e
     * @return \Thelia\Core\HttpFoundation\Response
     */
    protected function renderAfterDeleteError(\Exception $e, ParserContext $parserContext)
    {
        $errorMessage = sprintf(
            "Unable to delete '%s'. Error message: %s",
            static::CONTROLLER_ENTITY_NAME,
            $e->getMessage()
        );

        $parserContext
            ->setGeneralError($errorMessage);

        return $this->defaultAction();
    }

    protected function checkUserAccessDealer(SecurityContext $securityContext, $id = null)
    {
        $admin = $securityContext->getAdminUser();
        if (in_array("SUPERADMIN", $admin->getRoles())) {
            return null;
        }

        $dealers = DealerQuery::create()->filterById($id)->useDealerAdminQuery()->filterByAdminId($admin->getId())->endUse()->find();

        if (count($dealers) > 0) {
            return null;
        }

        return $this->errorPage(Translator::getInstance()->trans("Sorry, you're not allowed to perform this action"), 403);
    }

    protected function getAdminDealer(SecurityContext $securityContext)
    {
        $admin = $securityContext->getAdminUser();

        if ($admin === null) {
            return null;
        }

        if (in_array("SUPERADMIN", $admin->getRoles())) {
            return DealerQuery::create()->find();
        }

        return DealerQuery::create()->useDealerAdminQuery()->filterByAdminId($admin->getId())->endUse()->find();
    }
}
