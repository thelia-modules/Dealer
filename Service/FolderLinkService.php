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

namespace Dealer\Service;

use Dealer\Event\DealerEvents;
use Dealer\Event\DealerFolderLinkEvent;
use Dealer\Model\DealerFolder;
use Dealer\Model\DealerFolderQuery;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FolderLinkService
 * @package Dealer\Service
 */
class FolderLinkService extends AbstractBaseService implements BaseServiceInterface
{

    const EVENT_CREATE = DealerEvents::DEALER_FOLDER_LINK_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_FOLDER_LINK_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_FOLDER_LINK_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::DEALER_FOLDER_LINK_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_FOLDER_LINK_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_FOLDER_LINK_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::DEALER_FOLDER_LINK_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_FOLDER_LINK_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_FOLDER_LINK_UPDATE_AFTER;

    /**
     * @inheritDoc
     */
    protected function createProcess(Event $event)
    {
        /** @var DealerFolderLinkEvent $event */
        $event->getDealerFolderLink()->save();
    }

    /**
     * @inheritDoc
     */
    protected function updateProcess(Event $event)
    {
        /** @var DealerFolderLinkEvent $event */
        $event->getDealerFolderLink()->save();
    }

    protected function deleteProcess(Event $event)
    {
        /** @var DealerFolderLinkEvent $event */
        $event->getDealerFolderLink()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerFolderLinkEvent();
        $event->setDealerFolderLink($link);

        $this->create($event);

        return $event->getDealerFolderLink();
    }

    public function updateFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerFolderLinkEvent();
        $event->setDealerFolderLink($link);

        $this->update($event);

        return $event->getDealerFolderLink();
    }

    public function deleteFromId($id)
    {
        $link = DealerFolderQuery::create()->findOneById($id);
        if ($link) {
            $event = new DealerFolderLinkEvent();
            $event->setDealerFolderLink($link);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerFolder();

        if (isset($data['id'])) {
            $link = DealerFolderQuery::create()->findOneById($data['id']);
            if ($link) {
                $model = $link;
            }
        }

        if(isset($data["folder_id"]) && isset($data["dealer_id"])){
            $link = DealerFolderQuery::create()->filterByDealerId($data["dealer_id"])->filterByFolderId($data["folder_id"])->findOne();
            if ($link) {
                throw new \Exception("A link already exist",403);
            }

            $model->setContentId($data["folder_id"]);
            $model->setDealerId($data["dealer_id"]);
        }

        return $model;
    }


}