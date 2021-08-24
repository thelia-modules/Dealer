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
use Thelia\Model\Folder;
use Thelia\Model\FolderQuery;

/**
 * Class FolderLinkForm
 * @package Dealer\Form
 */
class FolderLinkForm extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add("folder_id", ChoiceType::class, array(
                "choices" => $this->getAvailableFolder(),
                "label" => $this->translator->trans("Folder", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-folder-link-folder"],
                "required" => true,
                "attr" => array()
            ))
            ->add("dealer_id", ChoiceType::class, array(
                "choices" => $this->getAvailableDealer(),
                "label" => $this->translator->trans("Dealer", [], Dealer::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "attr-dealer-folder-link-dealer"],
                "required" => true,
                "attr" => array()
            ));
    }

    public static function getName()
    {
        return "dealer_folder_link_create";
    }

    protected function getAvailableFolder()
    {
        $folders = FolderQuery::create()->find();
        $choices = [];

        /** @var Folder $folder */
        foreach ($folders as $folder) {
            $choices[$folder->getTitle()] = $folder->getId();
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
