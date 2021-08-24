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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ContactInfoUpdateForm
 * @package Dealer\Form
 */
class ContactInfoUpdateForm extends ContactInfoForm
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
            ->add('id', IntegerType::class, array(
                "label" => $this->translator->trans("Id", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-contact-info-id"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
        ;
    }

    public static function getName()
    {
        return "contact_info_update";
    }
}
