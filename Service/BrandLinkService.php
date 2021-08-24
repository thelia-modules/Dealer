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

use Dealer\Event\DealerBrandLinkEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\DealerBrand;
use Dealer\Model\DealerBrandQuery;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;
use Thelia\Core\Event\ActionEvent;

/**
 * Class BrandLinkService
 * @package Dealer\Service
 */
class BrandLinkService extends AbstractBaseService implements BaseServiceInterface
{

    const EVENT_CREATE = DealerEvents::DEALER_BRAND_LINK_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_BRAND_LINK_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_BRAND_LINK_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::DEALER_BRAND_LINK_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_BRAND_LINK_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_BRAND_LINK_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::DEALER_BRAND_LINK_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_BRAND_LINK_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_BRAND_LINK_UPDATE_AFTER;

    /**
     * @inheritDoc
     */
    protected function createProcess(ActionEvent $event)
    {
        /** @var DealerBrandLinkEvent $event */
        $event->getDealerBrandLink()->save();
    }

    /**
     * @inheritDoc
     */
    protected function updateProcess(ActionEvent $event)
    {
        /** @var DealerBrandLinkEvent $event */
        $event->getDealerBrandLink()->save();
    }

    protected function deleteProcess(ActionEvent $event)
    {
        /** @var DealerBrandLinkEvent $event */
        $event->getDealerBrandLink()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerBrandLinkEvent();
        $event->setDealerBrandLink($link);

        $this->create($event);

        return $event->getDealerBrandLink();
    }

    public function updateFromArray($data, $locale = null)
    {
        $link = $this->hydrateObjectArray($data, $locale);

        $event = new DealerBrandLinkEvent();
        $event->setDealerBrandLink($link);

        $this->update($event);

        return $event->getDealerBrandLink();
    }

    public function deleteFromId($id)
    {
        $link = DealerBrandQuery::create()->findOneById($id);
        if ($link) {
            $event = new DealerBrandLinkEvent();
            $event->setDealerBrandLink($link);

            $this->delete($event);
        }
    }

    public function deleteFromArray($data)
    {
        $link = null;

        if (isset($data["brand_id"]) && isset($data["dealer_id"])) {
            $link = DealerBrandQuery::create()->filterByDealerId($data["dealer_id"])->filterByBrandId($data["brand_id"])->findOne();
        }

        if ($link) {
            $event = new DealerBrandLinkEvent();
            $event->setDealerBrandLink($link);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerBrand();

        if (isset($data['id'])) {
            $link = DealerBrandQuery::create()->findOneById($data['id']);
            if ($link) {
                $model = $link;
            }
        }

        if (isset($data["brand_id"]) && isset($data["dealer_id"])) {
            $link = DealerBrandQuery::create()->filterByDealerId($data["dealer_id"])->filterByBrandId($data["brand_id"])->findOne();
            if ($link) {
                throw new \Exception("A link already exist", 403);
            }

            $model->setBrandId($data["brand_id"]);
            $model->setDealerId($data["dealer_id"]);
        }

        return $model;
    }
}
