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

use Dealer\Event\DealerProductLinkEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\DealerProduct;
use Dealer\Model\DealerProductQuery;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProductLinkService
 * @package Dealer\Service
 */
class ProductLinkService extends AbstractBaseService implements BaseServiceInterface
{

    const EVENT_CREATE = DealerEvents::DEALER_PRODUCT_LINK_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_PRODUCT_LINK_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_PRODUCT_LINK_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::DEALER_PRODUCT_LINK_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_PRODUCT_LINK_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_PRODUCT_LINK_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::DEALER_PRODUCT_LINK_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_PRODUCT_LINK_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_PRODUCT_LINK_UPDATE_AFTER;

    /**
     * @inheritDoc
     */
    protected function createProcess(Event $event)
    {
        /** @var DealerProductLinkEvent $event */
        $event->getDealerProductLink()->save();
    }

    /**
     * @inheritDoc
     */
    protected function updateProcess(Event $event)
    {
        /** @var DealerProductLinkEvent $event */
        $event->getDealerProductLink()->save();
    }

    protected function deleteProcess(Event $event)
    {
        /** @var DealerProductLinkEvent $event */
        $event->getDealerProductLink()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerProductLinkEvent();
        $event->setDealerProductLink($link);

        $this->create($event);

        return $event->getDealerProductLink();
    }

    public function updateFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerProductLinkEvent();
        $event->setDealerProductLink($link);

        $this->update($event);

        return $event->getDealerProductLink();
    }

    public function deleteFromId($id)
    {
        $link = DealerProductQuery::create()->findOneById($id);
        if ($link) {
            $event = new DealerProductLinkEvent();
            $event->setDealerProductLink($link);

            $this->delete($event);
        }
    }

    public function deleteFromArray($data)
    {
        $link = null;

        if(isset($data["product_id"]) && isset($data["dealer_id"])){
            $link = DealerProductQuery::create()->filterByDealerId($data["dealer_id"])->filterByProductId($data["product_id"])->findOne();
        }

        if ($link) {
            $event = new DealerProductLinkEvent();
            $event->setDealerProductLink($link);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerProduct();

        if (isset($data['id'])) {
            $link = DealerProductQuery::create()->findOneById($data['id']);
            if ($link) {
                $model = $link;
            }
        }

        if(isset($data["product_id"]) && isset($data["dealer_id"])){
            $link = DealerProductQuery::create()->filterByDealerId($data["dealer_id"])->filterByProductId($data["product_id"])->findOne();
            if ($link) {
                throw new \Exception("A link already exist",403);
            }

            $model->setProductId($data["product_id"]);
            $model->setDealerId($data["dealer_id"]);
        }

        return $model;
    }
}