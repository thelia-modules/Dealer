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
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class GeoDealerForm
 * @package Dealer\Form
 */
class GeoDealerForm extends BaseForm
{

    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('id', 'integer', array(
                "label" => $this->translator->trans("Id", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.geo.id"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("latitude", "number", array(
                "label" => $this->translator->trans("Latitude", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-geo-lat"],
                "required" => false,
                "constraints" => array(),
                "attr" => array("step" => "0.01", )
            ))
            ->add("longitude", "number", array(
                "label" => $this->translator->trans("Longitude", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-geo-lon"],
                "required" => false,
                "constraints" => array(),
                "attr" => array("step" => "0.01", )
            ));
    }


    public function getName()
    {
        return "dealer_geo";
    }
}
