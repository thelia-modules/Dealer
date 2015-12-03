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

use Dealer\Event\DealerContactInfoEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\DealerContactInfo;
use Dealer\Model\DealerContactInfoQuery;
use Dealer\Model\Map\DealerContactInfoTableMap;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ContactInfoService
 * @package Dealer\Service
 */
class ContactInfoService extends AbstractBaseService implements BaseServiceInterface
{

    const EVENT_CREATE = DealerEvents::DEALER_CONTACT_INFO_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_CONTACT_INFO_CREATE_AFTER;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_CONTACT_INFO_CREATE_BEFORE;
    const EVENT_DELETE = DealerEvents::DEALER_CONTACT_INFO_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_CONTACT_INFO_DELETE_AFTER;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_CONTACT_INFO_DELETE_BEFORE;
    const EVENT_UPDATE = DealerEvents::DEALER_CONTACT_INFO_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_CONTACT_INFO_UPDATE_AFTER;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_CONTACT_INFO_UPDATE_BEFORE;

    protected function createProcess(Event $event)
    {
        $event->getDealerContactInfo()->save();
    }

    protected function updateProcess(Event $event)
    {
        $event->getDealerContactInfo()->save();
    }

    protected function deleteProcess(Event $event)
    {
        $event->getDealerContactInfo()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $dealer_contact = $this->hydrateObjectArray($data, $locale);

        $event = new DealerContactInfoEvent();
        $event->setDealerContactInfo($dealer_contact);

        $this->create($event);

        return $event->getDealerContactInfo();
    }

    public function updateFromArray($data, $locale = null)
    {
        $dealer_contact = $this->hydrateObjectArray($data, $locale);

        $event = new DealerContactInfoEvent();
        $event->setDealerContactInfo($dealer_contact);

        $this->update($event);

        return $event->getDealerContactInfo();
    }

    public function deleteFromId($id)
    {
        $dealer = DealerContactInfoQuery::create()->findOneById($id);

        if ($dealer) {
            $event = new DealerContactInfoEvent();
            $event->setDealerContactInfo($dealer);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerContactInfo();

        if (isset($data['id'])) {
            $dealer = DealerContactInfoQuery::create()->findOneById($data['id']);
            if ($dealer) {
                $model = $dealer;
            }
        }

        if ($locale) {
            $model->setLocale($locale);
        }

        // Require Field
        if (isset($data['value'])) {
            $model->setValue($data['value']);
        }
        if (isset($data['type'])) {
            $model->setContactType(DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::CONTACT_TYPE)[$data['type']]);
        }
        if (isset($data['contact_id'])) {
            $model->setContactId($data['contact_id']);
        }

        return $model;
    }
}