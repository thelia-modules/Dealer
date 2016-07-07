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
use Dealer\Event\DealerSchedulesEvent;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class SchedulesService
 * @package Dealer\Service
 */
class SchedulesService extends AbstractBaseService implements BaseServiceInterface
{
    const EVENT_CREATE = DealerEvents::DEALER_SCHEDULES_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_SCHEDULES_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_SCHEDULES_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::DEALER_SCHEDULES_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_SCHEDULES_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_SCHEDULES_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::DEALER_SCHEDULES_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_SCHEDULES_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_SCHEDULES_UPDATE_AFTER;

    protected function createProcess(Event $event)
    {
        $event->getDealerSchedules()->save();
    }

    protected function updateProcess(Event $event)
    {
        $event->getDealerSchedules()->save();
    }

    protected function deleteProcess(Event $event)
    {
        $event->getDealerSchedules()->delete();
    }

    public function createFromArray($data, $locale = null)
    {
        $dealer_schedules = $this->hydrateObjectArray($data, $locale);

        $event = new DealerSchedulesEvent();
        $event->setDealerSchedules($dealer_schedules);

        $this->create($event);

        return $event->getDealerSchedules();
    }

    public function cloneFromArray($data)
    {
        $dealer_schedules = $this->hydrateObjectArray($data);
        $clone = $dealer_schedules->copy();

        $event = new DealerSchedulesEvent();
        $event->setDealerSchedules($clone);

        $this->create($event);

        return $event->getDealerSchedules();
    }

    public function updateFromArray($data, $locale = null)
    {
        $dealer_schedules = $this->hydrateObjectArray($data, $locale);

        $event = new DealerSchedulesEvent();
        $event->setDealerSchedules($dealer_schedules);

        $this->update($event);

        return $event->getDealerSchedules();
    }

    public function deleteFromId($id)
    {
        $dealer = DealerShedulesQuery::create()->findOneById($id);

        if ($dealer) {
            $event = new DealerSchedulesEvent();
            $event->setDealerSchedules($dealer);

            $this->delete($event);
        }
    }

    protected function hydrateObjectArray($data, $locale = null)
    {
        $model = new DealerShedules();

        if (isset($data['id'])) {
            $dealer = DealerShedulesQuery::create()->findOneById($data['id']);
            if ($dealer) {
                $model = $dealer;
            }
        }

        // Require Field
        if (array_key_exists('day', $data) && $data['day'] !== array()) {
            $model->setDay($data['day']);
        }
        if (isset($data['begin'])) {
            $model->setBegin($data['begin']);
        }
        if (isset($data['end'])) {
            $model->setEnd($data['end']);
        }
        if (isset($data['period_begin'])) {
            $model->setPeriodBegin($data['period_begin']);
        }
        if (isset($data['period_end'])) {
            $model->setPeriodEnd($data['period_end']);
        }
        if (isset($data['dealer_id'])) {
            $model->setDealerId($data['dealer_id']);
        }
        if (isset($data['closed'])) {
            $model->setClosed($data['closed']);
        }


        return $model;
    }
}
