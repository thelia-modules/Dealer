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
use Dealer\Model\DealerQuery;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Thelia\Form\BaseForm;
use Thelia\Model\ContentQuery;
use Thelia\Model\Content;

/**
 * Class ContentLinkForm
 * @package Dealer\Form
 */
class ContentLinkForm extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add("content_id", ChoiceType::class, array(
                "choices" => $this->getAvailableContent(),
                "label" => $this->translator->trans("Content", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-content-link-content"],
                "required" => true,
                "attr" => array()
            ))
            ->add("dealer_id", ChoiceType::class, array(
                "choices" => $this->getAvailableDealer(),
                "label" => $this->translator->trans("Dealer", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-content-link-dealer"],
                "required" => true,
                "attr" => array()
            ));
    }

    public static function getName()
    {
        return "dealer_content_link_create";
    }

    protected function getAvailableContent()
    {
        $contents = ContentQuery::create()->find();
        $choices = [];

        /** @var Content $content */
        foreach ($contents as $content) {
            $choices[$content->getTitle()] = $content->getId();
        }
        return $choices;
    }

    protected function getAvailableDealer()
    {
        $dealers = DealerQuery::create()->find();
        $choices = [];
        foreach ($dealers as $dealer) {
            $choices[$dealer->getTitle()] = $dealer->getId();
        }

        return $choices;
    }
}
