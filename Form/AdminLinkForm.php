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

namespace Dealer\Form;

use Dealer\Dealer;
use Dealer\Model\DealerQuery;
use Thelia\Form\BaseForm;
use Thelia\Model\Admin;
use Thelia\Model\AdminQuery;

/**
 * Class AdminLinkForm
 * @package Dealer\Form
 */
class AdminLinkForm extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add("admin_id", "choice", array(
                "choices" => $this->getAvailableAdmin(),
                "label" => $this->translator->trans("Admin", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-admin-link-admin"],
                "required" => true,
                "attr" => array()
            ))
            ->add("dealer_id", "choice", array(
                "choices" => $this->getAvailableDealer(),
                "label" => $this->translator->trans("Dealer", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-admin-link-dealer"],
                "required" => true,
                "attr" => array()
            ));
    }

    public function getName()
    {
        return "dealer_admin_link_create";
    }

    protected function getAvailableAdmin()
    {
        $admins = AdminQuery::create()->find();
        $choices = [];

        /** @var Admin $admin */
        foreach ($admins as $admin) {
            $choices[$admin->getId()] = $admin->getUsername();
        }

        return $choices;
    }

    protected function getAvailableDealer()
    {
        $dealers = DealerQuery::create()->find();
        $choices = [];
        foreach ($dealers as $dealer) {
            $choices[$dealer->getId()] = $dealer->getTitle();
        }

        return $choices;
    }
}
