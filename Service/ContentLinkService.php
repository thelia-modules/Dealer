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

use Dealer\Event\DealerContentLinkEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\DealerContent;
use Dealer\Model\DealerContentQuery;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;
use Thelia\Core\Event\ActionEvent;

/**
 * Class ContentLinkService
 * @package Dealer\Service
 */
class ContentLinkService extends AbstractBaseService implements BaseServiceInterface
{
    const EVENT_CREATE = DealerEvents::DEALER_CONTENT_LINK_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_CONTENT_LINK_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_CONTENT_LINK_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::DEALER_CONTENT_LINK_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_CONTENT_LINK_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_CONTENT_LINK_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::DEALER_CONTENT_LINK_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_CONTENT_LINK_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_CONTENT_LINK_UPDATE_AFTER;

    /**
     * @inheritDoc
     */
    protected function createProcess(ActionEvent $event)
    {
        /** @var DealerContentLinkEvent $event */
        $event->getDealerContentLink()->save();
    }

    /**
     * @inheritDoc
     */
    protected function updateProcess(ActionEvent $event)
    {
        /** @var DealerContentLinkEvent $event */
        $event->getDealerContentLink()->save();
    }

    protected function deleteProcess(ActionEvent $event)
    {
        /** @var DealerContentLinkEvent $event */
        $event->getDealerContentLink()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerContentLinkEvent();
        $event->setDealerContentLink($link);

        $this->create($event);

        return $event->getDealerContentLink();
    }

    public function updateFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerContentLinkEvent();
        $event->setDealerContentLink($link);

        $this->update($event);

        return $event->getDealerContentLink();
    }

    public function deleteFromId($id)
    {
        $link = DealerContentQuery::create()->findOneById($id);
        if ($link) {
            $event = new DealerContentLinkEvent();
            $event->setDealerContentLink($link);

            $this->delete($event);
        }
    }

    public function deleteFromArray($data)
    {
        $link = null;

        if (isset($data["content_id"]) && isset($data["dealer_id"])) {
            $link = DealerContentQuery::create()->filterByDealerId($data["dealer_id"])->filterByContentId($data["content_id"])->findOne();
        }

        if ($link) {
            $event = new DealerContentLinkEvent();
            $event->setDealerContentLink($link);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerContent();

        if (isset($data['id'])) {
            $link = DealerContentQuery::create()->findOneById($data['id']);
            if ($link) {
                $model = $link;
            }
        }

        if (isset($data["content_id"]) && isset($data["dealer_id"])) {
            $link = DealerContentQuery::create()->filterByDealerId($data["dealer_id"])->filterByContentId($data["content_id"])->findOne();
            if ($link) {
                throw new \Exception("A link already exist", 403);
            }

            $model->setContentId($data["content_id"]);
            $model->setDealerId($data["dealer_id"]);
        }

        return $model;
    }
}
