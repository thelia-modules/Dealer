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

use Dealer\Dealer;
use Dealer\Event\DealerEvents;
use Dealer\Event\DealerSchedulesEvent;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Dealer\Model\Map\DealerShedulesTableMap;
use Dealer\Service\Base\AbstractBaseService;
use Dealer\Service\Base\BaseServiceInterface;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Translation\Translator;

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
        $this->assertValidSchedule($event->getDealerSchedules());
        $event->getDealerSchedules()->save();
    }

    protected function updateProcess(ActionEvent $event)
    {
        $this->assertValidSchedule($event->getDealerSchedules());
        $event->getDealerSchedules()->save();
    }

    /**
     * Full consistency check of a schedule row: time range, period range, recurrence
     * prerequisites, and real calendar overlap detection against the other opening
     * entries of the dealer (whatever their period or recurrence mode).
     *
     * Overlaps are only rejected between entries of the same kind (base vs base,
     * exceptional opening vs exceptional opening): an exceptional opening overlapping
     * the base hours is a legitimate extension, and closures are subtractive so they
     * cannot conflict.
     *
     * @throws \RuntimeException when the entry is invalid
     */
    protected function assertValidSchedule(DealerShedules $schedule): void
    {
        $translator = Translator::getInstance();
        $begin = $schedule->getBegin();
        $end = $schedule->getEnd();
        $hasBegin = $begin instanceof \DateTimeInterface;
        $hasEnd = $end instanceof \DateTimeInterface;
        $isClosed = (bool) $schedule->getClosed();

        // Only a closure may omit its hours (it then closes the whole day). An opening
        // without hours would be silently ignored by the slot engine.
        if ((!$hasBegin || !$hasEnd) && (!$isClosed || $hasBegin !== $hasEnd)) {
            throw new \RuntimeException(
                $translator->trans(
                    'An opening entry must define both a begin and an end time.',
                    [],
                    Dealer::MESSAGE_DOMAIN
                )
            );
        }

        if ($hasBegin && $hasEnd) {
            $beginStr = $begin->format('H:i:s');
            $endStr = $this->normalizeEndTime($end->format('H:i:s'));

            if ($endStr <= $beginStr) {
                throw new \RuntimeException(
                    $translator->trans(
                        'The end time (%end) must be after the begin time (%begin).',
                        ['%begin' => $begin->format('H:i'), '%end' => $end->format('H:i')],
                        Dealer::MESSAGE_DOMAIN
                    )
                );
            }
        }

        $periodBegin = $schedule->getPeriodBegin();
        $periodEnd = $schedule->getPeriodEnd();

        if ($periodBegin instanceof \DateTimeInterface && $periodEnd instanceof \DateTimeInterface
            && $periodEnd->format('Y-m-d') < $periodBegin->format('Y-m-d')) {
            throw new \RuntimeException(
                $translator->trans(
                    'The period end date (%end) must not be before its begin date (%begin).',
                    ['%begin' => $periodBegin->format('Y-m-d'), '%end' => $periodEnd->format('Y-m-d')],
                    Dealer::MESSAGE_DOMAIN
                )
            );
        }

        if ($schedule->getRecurring() && !$periodBegin instanceof \DateTimeInterface) {
            throw new \RuntimeException(
                $translator->trans('A yearly recurring entry requires a date.', [], Dealer::MESSAGE_DOMAIN)
            );
        }

        if (!$schedule->getException() && $schedule->getDay() === null) {
            // A base row with no weekday would silently apply every single day.
            throw new \RuntimeException(
                $translator->trans('Base opening hours must target a weekday.', [], Dealer::MESSAGE_DOMAIN)
            );
        }

        if (!$isClosed && $hasBegin && $hasEnd) {
            $this->assertNoOpeningOverlap($schedule, $beginStr, $endStr);
        }
    }

    /**
     * Reject an opening entry whose time range overlaps another opening entry of the
     * same kind on at least one real calendar date. Same-kind pairs are compared on
     * their concrete applicability (weekday, period, recurrence) over a bounded
     * horizon, so a dated exception is caught against a weekly or yearly one.
     */
    private function assertNoOpeningOverlap(DealerShedules $schedule, string $beginStr, string $endStr): void
    {
        $query = DealerShedulesQuery::create()
            ->filterByDealerId($schedule->getDealerId())
            ->filterByClosed(0)
            ->filterByException($schedule->getException());

        if ($schedule->getId()) {
            $query->filterById($schedule->getId(), Criteria::NOT_EQUAL);
        }

        /** @var DealerShedules $existing */
        foreach ($query->find() as $existing) {
            $existingBegin = $existing->getBegin();
            $existingEnd = $existing->getEnd();

            if (!$existingBegin instanceof \DateTimeInterface || !$existingEnd instanceof \DateTimeInterface) {
                continue;
            }

            // Two time ranges overlap when each starts before the other ends.
            if ($beginStr >= $this->normalizeEndTime($existingEnd->format('H:i:s'))
                || $existingBegin->format('H:i:s') >= $endStr) {
                continue;
            }

            $conflictDate = $this->firstCommonDate($schedule, $existing);

            if ($conflictDate === null) {
                continue;
            }

            $parameters = [
                '%begin' => $schedule->getBegin()->format('H:i'),
                '%end' => $schedule->getEnd()->format('H:i'),
                '%exBegin' => $existingBegin->format('H:i'),
                '%exEnd' => $existingEnd->format('H:i'),
                '%date' => $conflictDate->format('d/m/Y'),
            ];

            throw new \RuntimeException(
                $schedule->getException()
                    ? Translator::getInstance()->trans(
                        'The %begin - %end time slot overlaps an existing slot (%exBegin - %exEnd) on %date.',
                        $parameters,
                        Dealer::MESSAGE_DOMAIN
                    )
                    : Translator::getInstance()->trans(
                        'The %begin - %end time slot overlaps an existing slot (%exBegin - %exEnd).',
                        $parameters,
                        Dealer::MESSAGE_DOMAIN
                    )
            );
        }
    }

    /**
     * First calendar date on which both entries apply, scanned from today over a bounded
     * horizon (one year, extended to the latest concrete period bound, capped at three
     * years). Base rows repeat weekly for ever, so any same-weekday pair matches fast;
     * null means the two applicabilities never meet within the horizon.
     */
    private function firstCommonDate(DealerShedules $a, DealerShedules $b): ?\DateTimeImmutable
    {
        $cursor = new \DateTimeImmutable('today');
        $horizon = $cursor->add(new \DateInterval('P1Y'));

        foreach ([$a->getPeriodEnd(), $b->getPeriodEnd()] as $bound) {
            if ($bound instanceof \DateTimeInterface && $bound->format('Y-m-d') > $horizon->format('Y-m-d')) {
                $horizon = \DateTimeImmutable::createFromInterface($bound);
            }
        }

        $cap = $cursor->add(new \DateInterval('P3Y'));
        if ($horizon > $cap) {
            $horizon = $cap;
        }

        while ($cursor <= $horizon) {
            if (ScheduleApplicability::appliesOn($a, $cursor) && ScheduleApplicability::appliesOn($b, $cursor)) {
                return $cursor;
            }
            $cursor = $cursor->add(new \DateInterval('P1D'));
        }

        return null;
    }

    protected function deleteProcess(ActionEvent $event)
    {
        $event->getDealerSchedules()->delete();
    }

    /**
     * Treat a midnight end time (00:00:00) as end-of-day (24:00:00) so a slot closing
     * at midnight compares as after its begin time.
     */
    private function normalizeEndTime(string $end): string
    {
        return $end === '00:00:00' ? '24:00:00' : $end;
    }

    public function createFromArray($data, $locale = null)
    {
        $dealer_schedules = $this->hydrateObjectArray($data, $locale);

        $event = new DealerSchedulesEvent();
        $event->setDealerSchedules($dealer_schedules);

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

        // Weekday is optional: an empty value means a period-only entry (day = null).
        if (array_key_exists('day', $data)) {
            $day = $data['day'];
            if ($day === array() || $day === '' || $day === null) {
                $model->setDay(null);
            } elseif (!is_array($day)) {
                $model->setDay((int) $day);
            }
        }
        // Posted keys always win, an empty value clearing the column: this is what lets
        // the back-office turn a partial closure into a full-day one, or remove a period.
        foreach (['begin', 'end', 'period_begin', 'period_end'] as $field) {
            if (array_key_exists($field, $data)) {
                $setter = 'set' . str_replace('_', '', ucwords($field, '_'));
                $model->$setter($data[$field] !== '' ? $data[$field] : null);
            }
        }
        if (isset($data['dealer_id'])) {
            $model->setDealerId($data['dealer_id']);
        }
        if (isset($data['closed'])) {
            $model->setClosed((bool) $data['closed']);
        }
        if (array_key_exists('recurring', $data)) {
            $model->setRecurring((bool) $data['recurring']);
        }
        if (array_key_exists('exception', $data)) {
            $model->setException((bool) $data['exception']);
        }
        if (array_key_exists('title', $data)) {
            $model->setTitle($data['title'] !== '' ? $data['title'] : null);
        }

        return $model;
    }

    /**
     * TODO: this legacy day/hour resolution engine is NOT recurring-aware (it ignores the
     * `recurring` column) and duplicates PickupSlotService. Its only caller is the
     * GrandPanierBio Smarty plugin, unused by the active flexy front. Migrate that caller to
     * PickupSlotService (which handles recurrence) and remove these methods.
     *
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
