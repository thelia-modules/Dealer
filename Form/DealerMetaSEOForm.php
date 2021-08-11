<?php

namespace Dealer\Form;

use Dealer\Dealer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

class DealerMetaSEOForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("dealer_id", HiddenType::class, array(
                "required" => true,
                "constraints" => array(new NotBlank(), ),
                "attr" => array()
            ))
            ->add("meta_title", TextType::class, array(
                "label" => $this->translator->trans("Title SEO", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "meta_title"],
                "required" => false,
                "attr" => array()
            ))
            ->add("meta_description", TextType::class, array(
                "label" => $this->translator->trans("Description SEO", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "dealer.description"],
                "required" => false,
                "attr" => array()
            ))
            ->add("meta_keywords", TextType::class, array(
                "label" => $this->translator->trans("Keywords SEO", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "meta_keywords"],
                "required" => false,
                "attr" => array()
            ))
            ->add("slug", TextType::class, array(
                "label" => $this->translator->trans("Slug SEO", [], Dealer::MESSAGE_DOMAIN),
                "required" => false,
                "attr" => array(),
                "label_attr" => array("for" => "slug"),
            ))
            ->add("meta_json", TextareaType::class, array(
                "label" => $this->translator->trans("Meta json", [], Dealer::MESSAGE_DOMAIN),
                "required" => false,
                "attr" => array(),
                "label_attr" => array("for" => "meta_json"),
            ));
    }
    public static function getName()
    {
        return "dealer_meta_seo";
    }
}
