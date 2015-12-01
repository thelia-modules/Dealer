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

use Dealer\Event\DealerContactEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\Base\DealerContactQuery;
use Dealer\Model\DealerContact;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ContactService
 * @package Dealer\Service
 */
class ContactService extends AbstractBaseService implements BaseServiceInterface
{
    const EVENT_CREATE = DealerEvents::DEALER_CONTACT_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_CONTACT_CREATE_AFTER;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_CONTACT_CREATE_BEFORE;
    const EVENT_DELETE = DealerEvents::DEALER_CONTACT_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_CONTACT_DELETE_AFTER;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_CONTACT_DELETE_BEFORE;
    const EVENT_UPDATE = DealerEvents::DEALER_CONTACT_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_CONTACT_UPDATE_AFTER;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_CONTACT_UPDATE_BEFORE;

    protected function createProcess(Event $event)
    {
        $event->getDealerContact()->save();
    }

    protected function updateProcess(Event $event)
    {
        $event->getDealerContact()->save();
    }

    protected function deleteProcess(Event $event)
    {
        $event->getDealerContact()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $dealer_contact = $this->hydrateObjectArray($data, $locale);

        $event = new DealerContactEvent();
        $event->setDealerContact($dealer_contact);

        $this->create($event);

        return $event->getDealerContact();
    }

    public function updateFromArray($data, $locale = null)
    {
        $dealer_contact = $this->hydrateObjectArray($data, $locale);

        $event = new DealerContactEvent();
        $event->setDealerContact($dealer_contact);

        $this->update($event);

        return $event->getDealerContact();
    }

    public function deleteFromId($id)
    {
        $dealer = $this->hydrateObjectArray(['id' => $id]);

        $event = new DealerContactEvent();
        $event->setDealerContact($dealer);

        $this->delete($event);
    }

    public function setDefault($data)
    {
        if (isset($data['is_default']) && $data['is_default']) {
            $this->resetDefault($data);
        }

        $dealer_contact = $this->hydrateObjectArray($data);

        $event = new DealerContactEvent();
        $event->setDealerContact($dealer_contact);

        $this->update($event);
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerContact();

        if (isset($data['id'])) {
            $dealer = DealerContactQuery::create()->findOneById($data['id']);
            if ($dealer) {
                $model = $dealer;
            }
        }

        if ($locale) {
            $model->setLocale($locale);
        }

        // Require Field
        if (isset($data['label'])) {
            $model->setLabel($data['label']);
        }
        if (isset($data['dealer_id'])) {
            $model->setDealerId($data['dealer_id']);
        }
        if (isset($data['is_default'])) {
            $model->setIsDefault($data['is_default']);
        }

        return $model;
    }

    protected function resetDefault($data)
    {

        if (isset($data['id'])) {
            $dealer = DealerContactQuery::create()->findOneById($data['id']);
            $defaultsContacts = DealerContactQuery::create()->filterByDealerId($dealer->getDealerId())->filterByIsDefault(true)->find();

            foreach ($defaultsContacts as $defaultsContact) {
                $defaultsContact
                    ->setIsDefault(false)
                    ->save();
            }
        }

    }
}