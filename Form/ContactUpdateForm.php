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
 * Class ContactUpdateForm
 * @package Dealer\Form
 */
class ContactUpdateForm extends ContactForm
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
                "label" => $this->translator->trans("Id", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-contact-id"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
        ;
    }

    public function getName()
    {
        return "contact_update";
    }
}
