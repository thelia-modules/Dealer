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

use Dealer\Event\DealerEvent;
use Dealer\Event\DealerEvents;
use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Dealer\Service\Base\BaseService;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DealerService
 * @package Dealer\Service
 */
class DealerService extends BaseService
{
    const EVENT_CREATE = DealerEvents::DEALER_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_CREATE_AFTER;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_CREATE_BEFORE;
    const EVENT_DELETE = DealerEvents::DEALER_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_DELETE_AFTER;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_DELETE_BEFORE;

    protected function createProcess(Event $event)
    {
        $event->getDealer()->save();
    }

    protected function updateProcess(Event $event)
    {
        $event->getDealer()->save();
    }

    protected function deleteProcess(Event $event)
    {
        $event->getDealer()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $dealer = $this->hydrateObjectArray($data, $locale);

        $event = new DealerEvent();
        $event->setDealer($dealer);

        $this->create($event);

        return $event->getDealer();
    }

    public function deleteFromId($id)
    {
        $dealer = $this->hydrateObjectArray(['id' => $id]);

        $event = new DealerEvent();
        $event->setDealer($dealer);

        $this->delete($event);
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new Dealer();

        if (isset($data['id'])) {
            $dealer = DealerQuery::create()->findOneById($data['id']);
            if ($dealer) {
                $model = $dealer;
            }
        }

        if ($locale) {
            $model->setLocale($locale);
        }

        // Require Field
        if (isset($data['title'])) {
            $model->setTitle($data['title']);
        }
        if (isset($data['address1'])) {
            $model->setAddress1($data['address1']);
        }
        if (isset($data['zipcode'])) {
            $model->setZipcode($data['zipcode']);
        }
        if (isset($data['city'])) {
            $model->setCity($data['city']);
        }
        if (isset($data['country_id'])) {
            $model->setCountryId($data['country_id']);
        }

        //  Optionnal Field
        if (isset($data['description'])) {
            $model->setDescription($data['description']);
        }
        if (isset($data['address2'])) {
            $model->setAddress2($data['address2']);
        }
        if (isset($data['address3'])) {
            $model->setAddress3($data['address3']);
        }

        return $model;
    }

}