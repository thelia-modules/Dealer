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

    /*
     * -----------------------------------------------------------------------------------
     * Deprecated adapters kept for the legacy Smarty front. They expose day and hour
     * arrays built from the very same rules as PickupSlotService (base weekly hours,
     * minus the closures, plus the exceptional openings, yearly recurrence and
     * open-ended periods included), without its slot grid, preparation delay or quotas.
     * -----------------------------------------------------------------------------------
     */

    /**
     * @deprecated use PickupSlotService::getAvailableSlots()
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
        // One query for the whole scan: applicability is then resolved in PHP, date by date.
        $schedules = DealerShedulesQuery::create()->filterByDealerId($idDealer)->find();

        $date = $this->toDate($dateStart);
        $days = [];
        $scanned = 0;

        while ($scanned < self::MAX_DAYS_SEARCH && count($days) < $numberMaxDays) {
            if ($this->openRangesOn($schedules, $date) !== []) {
                $numDay = (int) $date->format('N') - 1;
                $hardHours = $this->hourGridOn($schedules, $date, $numDay, (bool) $hardOnly);

                $days[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('l'),
                    'num_day' => $numDay,
                    'hardHours' => $hardHours,
                    'hours' => $this->openHoursOn($schedules, $date, $hardHours),
                ];
            }

            $date = $date->add(new \DateInterval('P1D'));
            ++$scanned;
        }

        return $days;
    }

    /**
     * A day is open as soon as one open time range survives the closures of that date,
     * base hours and exceptional openings taken together.
     *
     * @deprecated use PickupSlotService::getAvailableSlots()
     *
     * @param $idDealer
     * @param $dateDay
     * @return array|null
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function findOpenDay($idDealer, $dateDay)
    {
        $schedules = DealerShedulesQuery::create()->filterByDealerId($idDealer)->find();
        $date = $this->toDate($dateDay);

        if ($this->openRangesOn($schedules, $date) === []) {
            return null;
        }

        return [
            'date' => $date->format('Y-m-d'),
            'day' => $date->format('l'),
            'num_day' => (int) $date->format('N') - 1,
        ];
    }

    /**
     * Hourly grid of the base opening hours of a weekday, plus the exceptional openings
     * of $date unless only the hard hours are wanted.
     *
     * @deprecated use PickupSlotService::getAvailableSlots()
     *
     * @param $idDealer
     * @param $numDay
     * @param null $date
     * @param bool $harOnly
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function findHardHours($idDealer, $numDay, $date = null, $harOnly = false)
    {
        $schedules = DealerShedulesQuery::create()->filterByDealerId($idDealer)->find();

        return $this->hourGridOn(
            $schedules,
            $date === null ? null : $this->toDate($date),
            (int) $numDay,
            (bool) $harOnly
        );
    }

    /**
     * The hours of $date really open: the grid received, minus the closures, plus the
     * exceptional openings (which therefore win over a partial closure).
     *
     * $numDay is kept for signature compatibility only: the weekday is read from $date.
     *
     * @deprecated use PickupSlotService::getAvailableSlots()
     *
     * @param $idDealer
     * @param $numDay
     * @param $date
     * @param $hardHours
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function findOpenHours(
        $idDealer,
        $numDay,
        $date,
        $hardHours
    ) {
        $schedules = DealerShedulesQuery::create()->filterByDealerId($idDealer)->find();

        return $this->openHoursOn($schedules, $this->toDate($date), (array) $hardHours);
    }

    /**
     * Effective open time ranges of a date: base hours and exceptional openings, minus
     * the closures (a closure without hours closes the whole day). Same rules as
     * PickupSlotService::resolveOpenRanges(), deliberately duplicated so these adapters
     * stay independent from the slot engine and its configuration.
     *
     * Overlapping openings are not merged: subtracting a closure from each range
     * separately is enough to tell whether anything remains open.
     *
     * @param iterable<DealerShedules> $schedules
     * @return list<array{0: string, 1: string}> H:i:s ranges
     */
    private function openRangesOn(iterable $schedules, \DateTimeImmutable $date): array
    {
        $open = [];
        $closures = [];

        foreach ($schedules as $row) {
            if (!ScheduleApplicability::appliesOn($row, $date)) {
                continue;
            }

            $begin = $row->getBegin();
            $end = $row->getEnd();
            $hasHours = $begin instanceof \DateTimeInterface && $end instanceof \DateTimeInterface;

            if ($row->getClosed()) {
                if (!$hasHours) {
                    return [];
                }

                $closures[] = [$begin->format('H:i:s'), $this->normalizeEndTime($end->format('H:i:s'))];
                continue;
            }

            if ($hasHours) {
                $open[] = [$begin->format('H:i:s'), $this->normalizeEndTime($end->format('H:i:s'))];
            }
        }

        foreach ($closures as [$closeBegin, $closeEnd]) {
            $remaining = [];

            foreach ($open as [$begin, $end]) {
                // No overlap: keep as is.
                if ($closeEnd <= $begin || $closeBegin >= $end) {
                    $remaining[] = [$begin, $end];
                    continue;
                }
                // Left remainder.
                if ($closeBegin > $begin) {
                    $remaining[] = [$begin, min($closeBegin, $end)];
                }
                // Right remainder.
                if ($closeEnd < $end) {
                    $remaining[] = [max($closeEnd, $begin), $end];
                }
            }

            $open = $remaining;
        }

        return $open;
    }

    /**
     * @param iterable<DealerShedules> $schedules
     * @return list<string> hourly steps, 'H:i:s'
     */
    private function hourGridOn(iterable $schedules, ?\DateTimeImmutable $date, int $numDay, bool $hardOnly): array
    {
        $hours = [];

        foreach ($schedules as $row) {
            // The base weekly hours: neither an exceptional entry nor a closure.
            if ($row->getException() || $row->getClosed() || !$this->targetsDay($row, $numDay, $date)) {
                continue;
            }

            $hours = array_merge($hours, $this->hourSteps($row));
        }

        if (!$hardOnly && $date !== null) {
            foreach ($schedules as $row) {
                if (!$row->getException() || $row->getClosed() || !ScheduleApplicability::appliesOn($row, $date)) {
                    continue;
                }

                $hours = array_merge($hours, $this->hourSteps($row));
            }
        }

        return $this->sortedHours($hours);
    }

    /**
     * @param iterable<DealerShedules> $schedules
     * @param list<string> $hardHours
     * @return list<string> hourly steps, 'H:i:s'
     */
    private function openHoursOn(iterable $schedules, \DateTimeImmutable $date, array $hardHours): array
    {
        $closures = [];
        $extraHours = [];

        foreach ($schedules as $row) {
            if (!ScheduleApplicability::appliesOn($row, $date)) {
                continue;
            }

            $begin = $row->getBegin();
            $end = $row->getEnd();
            $hasHours = $begin instanceof \DateTimeInterface && $end instanceof \DateTimeInterface;

            if ($row->getClosed()) {
                if (!$hasHours) {
                    // No hours on a closure: the whole day is closed, as in PickupSlotService.
                    return [];
                }

                $closures[] = [$begin->format('H:i:s'), $this->normalizeEndTime($end->format('H:i:s'))];
                continue;
            }

            if ($row->getException()) {
                $extraHours = array_merge($extraHours, $this->hourSteps($row));
            }
        }

        $hours = [];
        foreach ($hardHours as $hour) {
            foreach ($closures as [$closeBegin, $closeEnd]) {
                if ($hour >= $closeBegin && $hour <= $closeEnd) {
                    continue 2;
                }
            }

            $hours[] = $hour;
        }

        return $this->sortedHours(array_merge($hours, $extraHours));
    }

    /**
     * Does a row target that weekday? With a date at hand the full applicability rules
     * apply (weekday, period, yearly recurrence); without one, only the weekday can be
     * compared, a row with no weekday matching every day.
     */
    private function targetsDay(DealerShedules $row, int $numDay, ?\DateTimeImmutable $date): bool
    {
        if ($date !== null) {
            return ScheduleApplicability::appliesOn($row, $date);
        }

        return $row->getDay() === null || (int) $row->getDay() === $numDay;
    }

    /**
     * Hourly steps of a time range, end included: the historical grid of the legacy front.
     *
     * @return list<string> 'H:i:s'
     */
    private function hourSteps(DealerShedules $row): array
    {
        $begin = $row->getBegin();
        $end = $row->getEnd();

        if (!$begin instanceof \DateTimeInterface || !$end instanceof \DateTimeInterface) {
            return [];
        }

        $limit = $this->normalizeEndTime($end->format('H:i:s'));
        $cursor = new \DateTimeImmutable('1970-01-01 ' . $begin->format('H:i:s'));
        $hours = [];

        while ($cursor->format('H:i:s') <= $limit) {
            $hours[] = $cursor->format('H:i:s');
            $next = $cursor->add(new \DateInterval('PT1H'));

            // Stepping past midnight would restart the day: stop there.
            if ($next->format('H:i:s') <= $cursor->format('H:i:s')) {
                break;
            }

            $cursor = $next;
        }

        return $hours;
    }

    /**
     * @param list<string> $hours
     * @return list<string>
     */
    private function sortedHours(array $hours): array
    {
        $hours = array_values(array_unique($hours));
        sort($hours);

        return $hours;
    }

    /**
     * Calendar date of a \DateTimeInterface or of a 'Y-m-d' string, time dropped.
     * The argument is never mutated, unlike the historical implementation.
     */
    private function toDate($date): \DateTimeImmutable
    {
        $moment = $date instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromInterface($date)
            : new \DateTimeImmutable((string) $date);

        return new \DateTimeImmutable($moment->format('Y-m-d'));
    }
}
