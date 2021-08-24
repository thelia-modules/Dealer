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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class SchedulesCloneForm
 * @package Dealer\Form
 */
class SchedulesCloneForm extends BaseForm
{

    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('id', IntegerType::class, array(
                "label" => $this->translator->trans("Id", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-clone-id"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("day", ChoiceType::class, [
                "choices" => $this->getDay(),
                "label" => $this->translator->trans("Day", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-schedules-clone-day"],
                "required" => true,
                "attr" => array()
            ]);
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
        return "schedules_clone";
    }
}
