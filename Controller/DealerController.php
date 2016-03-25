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
use Dealer\Dealer as DealerModule;
use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Tools\URL;

/**
 * Class DealerController
 * @package Dealer\Controller
 */
class DealerController extends BaseController
{
    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {
        return DealerQuery::create()->findPk($this->getRequest()->query->get("dealer_id"));
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param mixed $object
     */
    protected function hydrateObjectForm($object)
    {
        $data = array(
            "id" => $object->getId(),
            "title" => $object->getTitle(),
            "address1" => $object->getAddress1(),
            "address2" => $object->getAddress2(),
            "address3" => $object->getAddress3(),
            "zipcode" => $object->getZipcode(),
            "city" => $object->getCity(),
            "country_id" => $object->getCountryId(),
            "description" => $object->getDescription(),
            "latitude" => $object->getLatitude(),
            "longitude" => $object->getLongitude(),
        );

        return $this->getUpdateForm($data);
    }

    const CONTROLLER_ENTITY_NAME = "dealer";

    /**
     * Use to get render of list
     * @return mixed
     */
    protected function getListRenderTemplate()
    {
        $dealers = $this->getAdminDealer();
        $ids = "";
        foreach ($dealers as $dealer){
            $ids=$dealer->getId();
        }
        return $this->render("dealers",["dealers" => $ids]);
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
        // TODO: Implement getCreateRenderTemplate() method.
    }

    /**
     * @return mixed
     */
    protected function getObjectId($object)
    {
        /** @var Dealer $object */
        return $object->getId();
    }

    protected function redirectToListTemplate()
    {
        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer"));
    }

    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->query->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
            ["dealer_id" => $id,]));
    }

    public function toggleVisibleAction($id)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, DealerModule::getModuleCode(),
                AccessManager::UPDATE)
        ) {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;

        $retour = [];
        $code = 200;


        $con = Propel::getConnection();
        $con->beginTransaction();

        try {

            $updatedObject = $this->getService()->toggleVisibilityFromId($id);

            // Check if object exist
            if (!$updatedObject) {
                throw new \LogicException(
                    $this->getTranslator()->trans("No %obj was updated.", ['%obj' => static::CONTROLLER_ENTITY_NAME])
                );
            }

            $con->commit();

            $retour["message"] = "Visibility was updated";


        } catch (\Exception $ex) {
            $con->rollBack();
            // Any other error
            $retour["message"] = "Visibility can be updated";
            $code = 500;
            $retour["error"] = $ex->getMessage();
        }

        return JsonResponse::create($retour, $code);
    }

}