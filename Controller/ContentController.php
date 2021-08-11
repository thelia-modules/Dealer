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
use Dealer\Model\DealerContent;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Tools\TokenProvider;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/Dealer/content", name="dealer_content")
 * Class ContentController
 * @package Dealer\Controller
 */
class ContentController extends BaseController
{
    const CONTROLLER_ENTITY_NAME = "dealer_content_link";
    const CONTROLLER_CHECK_RESOURCE = Dealer::RESOURCES_ASSOCIATED;
    /**
     * @inheritDoc
     */
    protected function getListRenderTemplate()
    {
        $id = $this->getRequest()->query->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit#associated",
            ["dealer_id" => $id, ]));
    }

    /**
     * @inheritDoc
     */
    protected function redirectToListTemplate()
    {
        $id = $this->getRequest()->request->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit#associated",
            ["dealer_id" => $id, ]));
    }

    /**
     * @inheritDoc
     */
    protected function getEditRenderTemplate()
    {
        // TODO: Implement getEditRenderTemplate() method.
    }

    /**
     * @inheritDoc
     */
    protected function getCreateRenderTemplate()
    {
        // TODO: Implement getCreateRenderTemplate() method.
    }

    /**
     * @inheritDoc
     */
    protected function getObjectId($object)
    {
        /** @var DealerContent $object */
        return $object->getId();
    }

    /**
     * @inheritDoc
     */
    protected function getExistingObject(Request $request)
    {
        // TODO: Implement getExistingObject() method.
    }

    /**
     * @inheritDoc
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
            $this->service = $this->getContainer()->get("dealer_content_link_service");
        }

        return $this->service;
    }

    /**
     * Method to get Base Creation Form
     * @return \Thelia\Form\BaseForm
     */
    protected function getCreationForm()
    {
        return $this->createForm(static::CONTROLLER_ENTITY_NAME . "_create");
    }

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @Route("", name="_create", methods="POST")
     */
    public function createAction()
    {
        return parent::createAction();
    }

    /**
     * Delete an object
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     * @Route("/delete", name="_delete", methods="POST")
     */
    public function deleteAction(TokenProvider $tokenProvider, RequestStack $requestStack, ParserContext $parserContext)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, Dealer::getModuleCode(),
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
            $data = [
                "content_id" => $requestStack->getCurrentRequest()->request->get(static::CONTROLLER_ENTITY_NAME . "_id"),
                "dealer_id" => $requestStack->getCurrentRequest()->request->get("dealer_id"),
            ];
            $this->getService()->deleteFromArray($data);
            $con->commit();

            if ($requestStack->getCurrentRequest()->request->get("success_url") == null) {
                return $this->redirectToListTemplate();
            } else {
                return new RedirectResponse(URL::getInstance()->absoluteUrl($requestStack->getCurrentRequest()->request->get("success_url")));
            }
        } catch (\Exception $e) {
            $con->rollBack();

            return $this->renderAfterDeleteError($e, $parserContext);
        }
    }
}
