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
use Thelia\Model\Content;
use Thelia\Model\ContentQuery;

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

    public static function getName(): string
    {
        return "dealer_content_link_create";
    }

    protected function getAvailableContent()
    {
        $locale = $this->getLocale();
        $contents = ContentQuery::create()->joinWithI18n($locale)->find();
        $choices = [];

        /** @var Content $content */
        foreach ($contents as $content) {
            $choices[$content->setLocale($locale)->getTitle()] = $content->getId();
        }
        return $choices;
    }

    protected function getAvailableDealer()
    {
        $locale = $this->getLocale();
        $dealers = DealerQuery::create()->joinWithI18n($locale)->find();
        $choices = [];
        foreach ($dealers as $dealer) {
            $choices[$dealer->setLocale($locale)->getTitle()] = $dealer->getId();
        }

        return $choices;
    }

    protected function getLocale(): string
    {
        $session = $this->request->hasSession() ? $this->request->getSession() : null;

        return $session?->getLang()?->getLocale() ?? 'en_US';
    }
}
