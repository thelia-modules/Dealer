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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;
use Thelia\Model\Country;
use Thelia\Model\CountryQuery;

/**
 * Class DealerForm
 * @package Dealer\Form
 */
class DealerForm extends BaseForm
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
            ->add("title", TextType::class, array(
                "label" => $this->translator->trans("Title", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.title"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("description", TextType::class, array(
                "label" => $this->translator->trans("Description", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.description"],
                "required" => false,
                "attr" => array()
            ))
            ->add("big_description", TextType::class, array(
                "label" => $this->translator->trans("Complex Description", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.big_description"],
                "required" => false,
                "attr" => array()
            ))
            ->add("hard_open_hour", TextType::class, array(
                "label" => $this->translator->trans("Open hour text", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.hard_open_hour"],
                "required" => false,
                "attr" => array()
            ))
            ->add("access", TextType::class, array(
                "label" => $this->translator->trans("Access", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.access"],
                "required" => false,
                "attr" => array()
            ))
            ->add("address1", TextType::class, array(
                "label" => $this->translator->trans("Address1", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.address1"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("address2", TextType::class, array(
                "label" => $this->translator->trans("Address2", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.address2"],
                "required" => false,
                "attr" => array()
            ))
            ->add("address3", TextType::class, array(
                "label" => $this->translator->trans("Address3", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.address3"],
                "required" => false,
                "attr" => array()
            ))
            ->add("zipcode", TextType::class, array(
                "label" => $this->translator->trans("Zipcode", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.zipcode"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("city", TextType::class, array(
                "label" => $this->translator->trans("City", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.city"],
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("country_id", ChoiceType::class, array(
                "choices" => $this->getCountries(),
                "label" => $this->translator->trans("Country", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.country"],
                "required" => true,
                "attr" => array()
            ))
            ->add("locale", TextType::class, array(
                "constraints" => array(
                    new NotBlank(),
                ),
                "label_attr" => array("for" => "locale_create"),
            ));
    }

    protected function getCountries()
    {
        $countries = CountryQuery::create()->find();
        $retour = [];
        /** @var Country $country */
        foreach ($countries as $country) {
            $retour[$country->getTitle()] = $country->getId();
        }

        return $retour;
    }

    public static function getName()
    {
        return "dealer_create";
    }
}
