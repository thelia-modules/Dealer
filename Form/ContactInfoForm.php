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
use Dealer\Model\Map\DealerContactInfoTableMap;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class ContactInfoForm
 * @package Dealer\Form
 */
class ContactInfoForm extends BaseForm
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
        $this->formBuilder
            ->add("type",'choice', array(
                "choices" => DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::CONTACT_TYPE),
                "label" => $this->translator->trans("Contact Type", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-contact-info-type"],
                "required" => true,
                "attr" => array()
            ))
            ->add("value", "text", array(
                "label" => $this->translator->trans("Value", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-contact-info-value"],
                "required" => true,
                "constraints" => array(new NotBlank(),),
                "attr" => array()
            ))
            ->add('contact_id', 'integer', array(
                "label" => $this->translator->trans("Contact", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-contact-info-contact_id"],
                "required" => true,
                "constraints" => array(new NotBlank(),),
                "attr" => array()
            ))
            ->add("locale", "text", array(
                "constraints" => array(
                    new NotBlank(),
                ),
                "label_attr" => array("for" => "locale_create"),
            ))
        ;
    }


    public function getName()
    {
        return "contact_info__create";
    }
}