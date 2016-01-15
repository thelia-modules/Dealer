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
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;


/**
 * Class SchedulesController
 * @package Dealer\Controller
 */
class SchedulesController extends BaseController
{
    const CONTROLLER_ENTITY_NAME = "dealer-schedules";

    /**
     * Use to get render of list
     * @return mixed
     */
    protected function getListRenderTemplate()
    {
        $id = $this->getRequest()->request->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
            ["dealer_id" => $id,]));
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToListTemplate()
    {
        $id = $this->getRequest()->request->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
            ["dealer_id" => $id,]));
    }

    /**
     * Use to get Edit render
     * @return mixed
     */
    protected function getEditRenderTemplate()
    {
        return $this->render("dealer-edit");
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
        $object->getId();
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
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
    public function createAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, Dealer::getModuleCode(),
                AccessManager::CREATE)
        ) {
            return $response;
        }

        // Create the Creation Form
        $creationForm = $this->getCreationForm($this->getRequest());

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($creationForm, "POST");
            // Get the form field values
            $data = $form->getData();
            $dataAM = $this->formatData($data);
            if (null != $dataAM) {
                $this->getService()->createFromArray($dataAM, $this->getCurrentEditionLocale());
            }
            $dataPM = $this->formatData($data, 'PM');
            if (null != $dataPM) {
                $this->getService()->createFromArray($dataPM, $this->getCurrentEditionLocale());
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
                $this->getTranslator()->trans("%obj creation", ['%obj' => static::CONTROLLER_ENTITY_NAME]),
                $error_msg,
                $creationForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->getListRenderTemplate();
        }

    }

    protected function formatData($data, $type = "AM")
    {
        $retour = $data;
        if (isset($data["begin" . $type]) && $data["begin" . $type] != "") {
            $retour["begin"] = $data["begin" . $type];
        } else {
            return null;
        }
        if (isset($data["end" . $type]) && $data["end" . $type] != "") {
            $retour["end"] = $data["end" . $type];
        } else {
            return null;
        }

        return $retour;
    }

    public function cloneAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, Dealer::getModuleCode(),
                AccessManager::CREATE)
        ) {
            return $response;
        }

        // Create the Creation Form
        $cloneForm = $this->getCloneForm($this->getRequest());

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
        return $this->createForm(static::CONTROLLER_ENTITY_NAME . ".clone");
    }
}