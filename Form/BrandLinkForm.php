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
use Thelia\Model\Brand;
use Thelia\Model\BrandQuery;

/**
 * Class BrandLinkForm
 * @package Dealer\Form
 */
class BrandLinkForm extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add("brand_id", "choice", array(
                "choices" => $this->getAvailableBrand(),
                "label" => $this->translator->trans("Brand", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-brand-link-brand"],
                "required" => true,
                "attr" => array()
            ))
            ->add("dealer_id", "choice", array(
                "choices" => $this->getAvailableDealer(),
                "label" => $this->translator->trans("Dealer", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-brand-link-dealer"],
                "required" => true,
                "attr" => array()
            ));
    }

    public function getName()
    {
        return "dealer_brand_link_create";
    }

    protected function getAvailableBrand()
    {
        $brands = BrandQuery::create()->find();
        $choices = [];

        /** @var Brand $brand */
        foreach ($brands as $brand) {
            $choices[$brand->getId()] = $brand->getTitle();
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