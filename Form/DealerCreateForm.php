<?php

namespace Dealer\Form;

use Dealer\Dealer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;
use Thelia\Model\Country;
use Thelia\Model\CountryQuery;

class DealerCreateForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("title", TextType::class, array(
                "label" => $this->translator->trans("Title", [], Dealer::MESSAGE_DOMAIN),
                "required" => true,
            ))
            ->add("description", TextType::class, array(
                "label" => $this->translator->trans("Description", [], Dealer::MESSAGE_DOMAIN),
                "required" => false,
            ))
            ->add("big_description", TextareaType::class, array(
                "label" => $this->translator->trans("Complex Description", [], Dealer::MESSAGE_DOMAIN),
                "required" => false,
            ))
            ->add("hard_open_hour", TextType::class, array(
                "label" => $this->translator->trans("Open hour text", [], Dealer::MESSAGE_DOMAIN),
                "required" => false,
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
            ->add("locale", HiddenType::class, array(
                "constraints" => array(
                    new NotBlank(),
                ),
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

    public static function getName(): string
    {
        return "dealer_create";
    }
}
