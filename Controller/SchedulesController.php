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
use Dealer\Model\DealerShedules;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
}