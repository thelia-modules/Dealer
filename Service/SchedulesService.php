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
use Dealer\Model\Map\DealerShedulesTableMap;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\Event;
use Thelia\Core\Event\ActionEvent;

/**
 * Class SchedulesService
 * @package Dealer\Service
 */
class SchedulesService extends AbstractBaseService implements BaseServiceInterface
{
    const MAX_DAYS_SEARCH = 30;

    const EVENT_CREATE = DealerEvents::DEALER_SCHEDULES_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_SCHEDULES_CREATE_BEFORE;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_SCHEDULES_CREATE_AFTER;
    const EVENT_DELETE = DealerEvents::DEALER_SCHEDULES_DELETE;
    const EVENT_DELETE_BEFORE = DealerEvents::DEALER_SCHEDULES_DELETE_BEFORE;
    const EVENT_DELETE_AFTER = DealerEvents::DEALER_SCHEDULES_DELETE_AFTER;
    const EVENT_UPDATE = DealerEvents::DEALER_SCHEDULES_UPDATE;
    const EVENT_UPDATE_BEFORE = DealerEvents::DEALER_SCHEDULES_UPDATE_BEFORE;
    const EVENT_UPDATE_AFTER = DealerEvents::DEALER_SCHEDULES_UPDATE_AFTER;

    protected function createProcess(ActionEvent $event)
    {
        $event->getDealerSchedules()->save();
    }

    protected function updateProcess(ActionEvent $event)
    {
        $event->getDealerSchedules()->save();
    }

    protected function deleteProcess(ActionEvent $event)
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

    /**
     * @param $idDealer
     * @param $dateStart
     * @param $numberMaxDays
     * @param bool $hardOnly
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getOpenDays($idDealer, $dateStart, $numberMaxDays, $hardOnly = false)
    {
        $days = [];
        $i = 0;

        while ($i < self::MAX_DAYS_SEARCH && count($days) < $numberMaxDays) {


            if (null !== $day = $this->findOpenDay($idDealer, $dateStart)) {
                $day['hardHours'] = $this->findHardHours($idDealer, $day['num_day'], $day['date'], $hardOnly);
                $day['hours'] = $this->findOpenHours($idDealer, $day['num_day'], $day['date'], $day['hardHours']);
                $days[] = $day;
            }
            $dateStart->add(new \DateInterval('P1D'));
            $i++;
        }
        return $days;
    }

    /**
     * @param $idDealer
     * @param $dateDay
     * @return array|null
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function findOpenDay($idDealer, $dateDay)
    {
        $numDay = $dateDay->format('N') - 1;

        DealerShedulesTableMap::clearInstancePool();

        // Recherche des ouverture classique pour un jour donné
        $shedules = DealerShedulesQuery::create()
            ->filterByDealerId($idDealer)
            ->filterByDay($numDay)
            ->filterByPeriodNull()
            ->filterByClosed(0)
            ->find();

        $days = $shedules->getData();

        if (count($days) > 0) {
            DealerShedulesTableMap::clearInstancePool();
            // Recherche des fermetures exeptionnelles pour un jour donné et une date donnée
            $shedulesClosed = DealerShedulesQuery::create()
                ->filterByDealerId($idDealer)
                ->filterByDay($numDay)
                ->filterByClosed(1)
                ->filterByPeriodBegin($dateDay, Criteria::LESS_EQUAL)
                ->filterByPeriodEnd($dateDay, Criteria::GREATER_EQUAL)
                ->find();

            $daysclosed = $shedulesClosed->getData();

            if (count($daysclosed) == 0) {
                return [
                    'date' => $dateDay->format('Y-m-d'),
                    'day' => $dateDay->format('l'),
                    'num_day' => $dateDay->format('N') - 1
                ];
            }

            //on calcule le nombre d'heure dispo par rapport au nombre d'heure prevu
            $cptHourClassic = 0;
            $cptHourExep = 0;
            /** @var DealerShedules $shedule */
            foreach ($shedules as $shedule) {
                $tot = date_diff($shedule->getEnd(), $shedule->getBegin());

                $cptHourClassic += $tot->format('%h');
            }
            /** @var DealerShedules $daysclose */
            foreach ($daysclosed as $daysclose) {
                $tot = date_diff($daysclose->getEnd(), $daysclose->getBegin());

                $cptHourExep += $tot->format('%h');
            }
            if ($cptHourExep < $cptHourClassic) {
                return [
                    'date' => $dateDay->format('Y-m-d'),
                    'day' => $dateDay->format('l'),
                    'num_day' => $dateDay->format('N') - 1
                ];
            }
            return null;
        }

        DealerShedulesTableMap::clearInstancePool();
        // Recherche des ouvertures exeptionnelles pour un jour donné et une date donnée
        $shedulesOpen = DealerShedulesQuery::create()
            ->filterByDealerId($idDealer)
            ->filterByDay($numDay)
            ->filterByClosed(0)
            ->filterByPeriodBegin($dateDay, Criteria::LESS_EQUAL)
            ->filterByPeriodEnd($dateDay, Criteria::GREATER_EQUAL)
            ->find();

        $daysOpen = $shedulesOpen->getData();

        if (0 != count($daysOpen)) {
            //une ouverture a été trouvée on prend le jour en question
            return [
                'date' => $dateDay->format('Y-m-d'),
                'day' => $dateDay->format('l'),
                'num_day' => $dateDay->format('N') - 1
            ];
        }
        return null;
    }

    /**
     * @param $idDealer
     * @param $numDay
     * @param null $date
     * @param bool $harOnly
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function findHardHours($idDealer, $numDay, $date = null, $harOnly = false)
    {
        DealerShedulesTableMap::clearInstancePool();

        $shedulesHardDay = DealerShedulesQuery::create()
            ->filterByDealerId($idDealer)
            ->filterByDay($numDay)
            ->filterByPeriodBegin(null)
            ->filterByPeriodEnd(null)
            ->find();

        $tabHardHours = [];

        /** @var DealerShedules $range */
        foreach ($shedulesHardDay->getData() as $range) {
            $h = $range->getBegin();
            while ($h <= $range->getEnd()) {
                $tabHardHours[] = $h->format('H:i:s');
                $h->add(new \DateInterval('PT1H'));
            }
        }

        if ($harOnly === false && $date !== null) {
            DealerShedulesTableMap::clearInstancePool();
            $shedulesExpt = DealerShedulesQuery::create()
                ->filterByDealerId($idDealer)
                ->filterByDay($numDay)
                ->filterByClosed(0)
                ->filterByPeriodBegin($date, Criteria::LESS_EQUAL)
                ->filterByPeriodEnd($date, Criteria::GREATER_EQUAL)
                ->find();

            /** @var DealerShedules $sheduleExpt */
            foreach ($shedulesExpt as $sheduleExpt) {
                $h = $sheduleExpt->getBegin();
                while ($h <= $sheduleExpt->getEnd()) {
                    $htemp = $h->format('H:i:s');
                    if (!in_array($htemp, $tabHardHours)) {
                        $tabHardHours[] = $htemp;
                    }
                    $h->add(new \DateInterval('PT1H'));
                }
            }
        }

        return $tabHardHours;
    }

    /**
     * @param $idDealer
     * @param $numDay
     * @param $date
     * @param null $delay
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public
    function findOpenHours(
        $idDealer,
        $numDay,
        $date,
        $hardHours
    ) {
        DealerShedulesTableMap::clearInstancePool();

        $shedulesExpt = DealerShedulesQuery::create()
            ->filterByDealerId($idDealer)
            ->filterByDay($numDay)
            ->filterByPeriodBegin($date, Criteria::LESS_EQUAL)
            ->filterByPeriodEnd($date, Criteria::GREATER_EQUAL)
            ->find();

        $excludeHours = [];
        $exeptionOpenHour = [];

        /** @var DealerShedules $sheduleExpt */
        foreach ($shedulesExpt as $sheduleExpt) {

            $h = $sheduleExpt->getBegin();
            while ($h <= $sheduleExpt->getEnd()) {
                if (!$sheduleExpt->getClosed()) {
                    $exeptionOpenHour[] = $h->format('H:i:s');
                } else {
                    $excludeHours[] = $h->format('H:i:s');
                }
                $h->add(new \DateInterval('PT1H'));
            }
        }

        $tabHours = [];

        /** @var DealerShedules $range */
        foreach ($hardHours as $h) {
            if (!in_array($h, $excludeHours)) {
                $tabHours[] = $h;
            }
        }

        foreach ($exeptionOpenHour as $openHour) {
            if (!in_array($openHour, $tabHours)) {
                $tabHours[] = $openHour;
            }
        }

        sort($tabHours);

        return $tabHours;
    }
}
