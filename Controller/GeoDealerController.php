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

use Dealer\Dealer;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/Dealer/location", name="dealer_location")
 * Class GeoDealerController
 * @package Dealer\Controller
 */
class GeoDealerController extends BaseAdminController
{
    protected $service;

    /**
     * Save changes on a modified object, and either go back to the object list, or stay on the edition page.
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     * @Route("/update", name="_update", methods="POST")
     */
    public function processUpdateAction(RequestStack $requestStack)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, Dealer::getModuleCode(),
                AccessManager::VIEW)
        ) {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;
        $request = $requestStack->getCurrentRequest();
        // Create the Form from the request
        $changeForm = $this->getForm($request);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($changeForm, "POST");

            // Get the form field values
            $data = $form->getData();

            $updatedObject = $this->getService()->updateFromArray($data);

            // Check if object exist
            if (!$updatedObject) {
                throw new \LogicException(
                    Translator::getInstance()->trans("No %obj was updated.", ['%obj' => 'Dealer'])
                );
            }
            // If we have to stay on the same page, do not redirect to the successUrl,
            // just redirect to the edit page again.
            if ($request->get('save_mode') === 'stay') {
                $id = $request->query->get("dealer_id");

                return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
                    ["dealer_id" => $id, ]));
            }

            // Redirect to the success URL
            return $this->generateSuccessRedirect($changeForm);
        } catch (FormValidationException $ex) {
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
            /*} catch (\Exception $ex) {
                // Any other error
                $error_msg = $ex->getMessage();*/
        }

        if (false !== $error_msg) {
            // At this point, the form has errors, and should be redisplayed.
            $this->setupFormErrorContext(
                Translator::getInstance()->trans("%obj modification", ['%obj' => 'Dealer']),
                $error_msg,
                $changeForm,
                $ex
            );

            $id = $this->getRequest()->query->get("dealer_id");

            return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
                ["dealer_id" => $id, ]));
        }
    }

    protected function getForm($data = null)
    {
        if (!is_array($data)) {
            $data = [];
        }

        return $this->createForm("dealer-geo", FormType::class, $data);
    }

    protected function getService()
    {
        if (!$this->service) {
            $this->service = $this->getContainer()->get("dealer_geo_service");
        }

        return $this->service;
    }
}
