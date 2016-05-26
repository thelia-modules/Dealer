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
 * Class SchedulesUpdateForm
 * @package Dealer\Form
 */
class SchedulesUpdateForm extends SchedulesForm
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
                "label_attr" => ["for" => "attr-dealer-schedules-id"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->remove("beginPM")
            ->remove("endPM")
            ->remove("beginAM")
            ->remove("endAM")
            ->remove("day")
            ->add("day", "choice", [
                "choices" => $this->getDay(),
                "label" => $this->translator->trans("Day", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-day"],
                "required" => true,
                "attr" => array()
            ])
            ->add("begin", "time", [
                "label" => $this->translator->trans("Begin", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-begin"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("end", "time", [
                "label" => $this->translator->trans("End", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-end"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
        ;
    }

    public function getName()
    {
        return "schedules_update";
    }
}
