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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class SchedulesForm
 * @package Dealer\Form
 */
class SchedulesForm extends BaseForm
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
            ->add("day", ChoiceType::class, [
                "choices" => $this->getDay(),
                "label" => $this->translator->trans("Day", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-day"],
                "required" => false,
                "multiple" => true,
                "attr" => array()
            ])
            ->add("beginAM", TimeType::class, [
                "label" => $this->translator->trans("Begin", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-begin"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("endAM", TimeType::class, [
                "label" => $this->translator->trans("End", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-end"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("beginPM", TimeType::class, [
                "label" => $this->translator->trans("Begin", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-beginPM"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("endPM", TimeType::class, [
                "label" => $this->translator->trans("End", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-endPM"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("period_begin", DateType::class, [
                "label" => $this->translator->trans("Period Begin", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-period-begin"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("period_end", DateType::class, [
                "label" => $this->translator->trans("Period End", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-period-end"],
                "input" => "string",
                "widget" => "single_text",
                "required" => false,
                "attr" => array()
            ])
            ->add("closed", IntegerType::class, [
                "label" => $this->translator->trans("Closed", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-closed"],
                "required" => true,
                "attr" => array()
            ])
            ->add('dealer_id', IntegerType::class, array(
                "label" => $this->translator->trans("Dealer", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-dealer_id"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ));
    }

    protected function getDay()
    {
        return [
            $this->translator->trans("Monday", [], Dealer::MESSAGE_DOMAIN) => 0,
            $this->translator->trans("Tuesday", [], Dealer::MESSAGE_DOMAIN) => 1,
            $this->translator->trans("Wednesday", [], Dealer::MESSAGE_DOMAIN) => 2,
            $this->translator->trans("Thursday", [], Dealer::MESSAGE_DOMAIN) => 3,
            $this->translator->trans("Friday", [], Dealer::MESSAGE_DOMAIN) => 4,
            $this->translator->trans("Saturday", [], Dealer::MESSAGE_DOMAIN) => 5,
            $this->translator->trans("Sunday", [], Dealer::MESSAGE_DOMAIN) => 6
        ];
    }

    public static function getName()
    {
        return "schedules_create";
    }
}
