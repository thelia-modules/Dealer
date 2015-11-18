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
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;

/**
 * Class BaseController
 * @package Dealer\Controller\Base
 */
abstract class BaseController extends BaseAdminController
{
    /**
     * Name of entity associated with controller
     */
    const CONTROLLER_ENTITY_NAME = null;

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
     * Use to get Edit render
     * @return mixed
     */
    abstract protected function getEditRenderTemplate();

    /**
     * Use to get Create render
     * @return mixed
     */
    abstract protected function getCreateRenderTemplate();

    // PUBLIC METHODS

    /**
     * The default action is displaying the list.
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function defaultAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, Dealer::getModuleCode(),
                AccessManager::VIEW)
        ) {
            return $response;
        }

        return $this->getListRenderTemplate();
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

        return $this->createForm(static::CONTROLLER_ENTITY_NAME . ".update", $data);
    }


}