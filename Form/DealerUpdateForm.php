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

/**
 * Class DealerUpdateForm
 * @package Dealer\Form
 */
class DealerUpdateForm extends DealerForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add('id', 'integer', array(
                "label" => $this->translator->trans("Title", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.title"],
                "required" => true,
                "constraints" => array(new NotBlank(),),
                "attr" => array()
            ))
            ->add("latitude", "number", array(
                "label" => $this->translator->trans("Latitude", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.lat"],
                "required" => false,
                "constraints" => array(),
                "attr" => array("step" => "0.01",)
            ))
            ->add("longitude", "number", array(
                "label" => $this->translator->trans("Longitude", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.lon"],
                "required" => false,
                "constraints" => array(),
                "attr" => array("step" => "0.01",)
            ))
        ;
    }
}