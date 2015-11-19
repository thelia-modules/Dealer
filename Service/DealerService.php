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

    protected function createProcess(Event $event)
    {
        $event->getDealer()->save();
    }

    protected function updateProcess(Event $event)
    {
        $event->getDealer()->save();
    }

    public function createFromArray($data, $locale = null)
    {
        $dealer = $this->hydrateObjectArray($data,$locale);

        $event = new DealerEvent();
        $event->setDealer($dealer);

        $this->create($event);

        return $event->getDealer();
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
        $model->setTitle($data['title']);
        $model->setAddress1($data['address1']);
        $model->setZipcode($data['zipcode']);
        $model->setCity($data['city']);
        $model->setCountryId($data['country_id']);


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