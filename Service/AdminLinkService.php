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

use Dealer\Event\DealerAdminLinkEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\DealerAdmin;
use Dealer\Model\DealerAdminQuery;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AdminLinkService
 * @package Dealer\Service
 */
class AdminLinkService extends AbstractBaseService implements BaseServiceInterface
{

    const EVENT_CREATE = DealerEvents::ADMIN_LINK_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::ADMIN_LINK_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::ADMIN_LINK_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::ADMIN_LINK_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::ADMIN_LINK_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::ADMIN_LINK_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::ADMIN_LINK_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::ADMIN_LINK_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::ADMIN_LINK_UPDATE_AFTER;

    /**
     * @inheritDoc
     */
    protected function createProcess(Event $event)
    {
        /** @var DealerAdminLinkEvent $event */
        $event->getDealerAdminLink()->save();
    }

    /**
     * @inheritDoc
     */
    protected function updateProcess(Event $event)
    {
        /** @var DealerAdminLinkEvent $event */
        $event->getDealerAdminLink()->save();
    }

    protected function deleteProcess(Event $event)
    {
        /** @var DealerAdminLinkEvent $event */
        $event->getDealerAdminLink()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerAdminLinkEvent();
        $event->setDealerAdminLink($link);

        $this->create($event);

        return $event->getDealerAdminLink();
    }

    public function updateFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerAdminLinkEvent();
        $event->setDealerAdminLink($link);

        $this->update($event);

        return $event->getDealerAdminLink();
    }

    public function deleteFromId($id)
    {
        $link = DealerAdminQuery::create()->findOneById($id);
        if ($link) {
            $event = new DealerAdminLinkEvent();
            $event->setDealerAdminLink($link);

            $this->delete($event);
        }
    }

    public function deleteFromArray($data)
    {
        $link = null;

        if(isset($data["admin_id"]) && isset($data["dealer_id"])){
            $link = DealerAdminQuery::create()->filterByDealerId($data["dealer_id"])->filterByAdminId($data["admin_id"])->findOne();
        }

        if ($link) {
            $event = new DealerAdminLinkEvent();
            $event->setDealerAdminLink($link);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerAdmin();

        if (isset($data['id'])) {
            $link = DealerAdminQuery::create()->findOneById($data['id']);
            if ($link) {
                $model = $link;
            }
        }

        if(isset($data["admin_id"]) && isset($data["dealer_id"])){
            $link = DealerAdminQuery::create()->filterByDealerId($data["dealer_id"])->filterByAdminId($data["admin_id"])->findOne();
            if ($link) {
                throw new \Exception("A link already exist",403);
            }

            $model->setAdminId($data["admin_id"]);
            $model->setDealerId($data["dealer_id"]);
        }

        return $model;
    }
}