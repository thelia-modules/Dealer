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
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;

/**
 * Class ProductLinkForm
 * @package Dealer\Form
 */
class ProductLinkForm extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add("product_id", "choice", array(
                "choices" => $this->getAvailableProduct(),
                "label" => $this->translator->trans("Product", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-product-link-product"],
                "required" => true,
                "attr" => array()
            ))
            ->add("dealer_id", "choice", array(
                "choices" => $this->getAvailableDealer(),
                "label" => $this->translator->trans("Dealer", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-product-link-dealer"],
                "required" => true,
                "attr" => array()
            ));
    }

    public function getName()
    {
        return "dealer_product_link_create";
    }

    protected function getAvailableProduct()
    {
        $products = ProductQuery::create()->limit(100)->find();
        $choices = [];

        /** @var Product $product */
        foreach ($products as $product) {
            $choices[$product->getId()] = $product->getTitle();
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