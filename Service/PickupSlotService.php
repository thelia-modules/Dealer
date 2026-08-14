<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Model\DealerPickupConfig;
use Dealer\Model\DealerPickupConfigQuery;
use Dealer\Model\DealerQuery;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;

/**
 * Computes the pickup slots available for a dealer, applying its opening hours,
 * exceptional closures/openings (including unbounded weekly recurrence), the
 * preparation delay, the number of orderable days and the per-slot order quota.
 *
 * Fully self-contained inside the Dealer module: it reads only dealer_* tables.
 */
class PickupSlotService
{
    /** Hard limit on the calendar scan, so an over-constrained config cannot loop forever. */
    private const MAX_DAYS_SCAN = 60;

    /**
     * Fallback timezone when a dealer has none (or an invalid one). Schedule hours are naive
     * wall-clock times in the shop's timezone: "now" and the generated slot datetimes are
     * anchored to it, otherwise a server in another timezone (e.g. UTC) computes an offset
     * "now" and keeps already-past slots.
     */
    private const DEFAULT_TIMEZONE = 'Europe/Paris';

    /**
     * @return list<array{
     *     date: string,
     *     day: int,
     *     slots: list<array{time: string, datetime: string, remaining: int|null}>
     * }>
     */
    public function getAvailableSlots(int $dealerId, ?\DateTimeInterface $from = null): array
    {
        $config = $this->getConfig($dealerId);
        $tz = $this->timezoneFor($dealerId);
        $now = $from !== null
            ? \DateTimeImmutable::createFromInterface($from)->setTimezone($tz)
            : new \DateTimeImmutable('now', $tz);
        $earliest = $now->add(new \DateInterval('PT' . $config->getPrepDelayMinutes() . 'M'));

        $schedules = DealerShedulesQuery::create()->filterByDealerId($dealerId)->find();

        $days = [];
        $cursor = new \DateTimeImmutable($now->format('Y-m-d'));
        $scanned = 0;

        while (count($days) < $config->getOrderableDays() && $scanned < self::MAX_DAYS_SCAN) {
            $ranges = $this->resolveOpenRanges($schedules, $cursor);

            if ($ranges !== []) {
                $slots = $this->buildSlots($dealerId, $cursor, $ranges, $config, $earliest, $tz);

                if ($slots !== []) {
                    $days[] = [
                        'date' => $cursor->format('Y-m-d'),
                        'day' => (int) $cursor->format('N') - 1,
                        'slots' => $slots,
                    ];
                }
            }

            $cursor = $cursor->add(new \DateInterval('P1D'));
            $scanned++;
        }

        return $days;
    }

    /**
     * Resolve the effective open time ranges for a given date:
     * base weekly hours, minus closures (dated or unbounded weekly), plus exceptional openings.
     *
     * @param iterable<DealerShedules> $schedules all the dealer's schedule rows, fetched once
     * @return list<array{begin: string, end: string}> sorted, non-overlapping H:i:s ranges
     */
    private function resolveOpenRanges(iterable $schedules, \DateTimeImmutable $date): array
    {
        $numDay = (int) $date->format('N') - 1;

        // A schedule row applies on this date when it targets this weekday, OR targets no
        // weekday (day = null: a period-only entry) and its period covers the date. Null period
        // bounds are treated as open-ended, so filtering is done in PHP (SQL comparators would
        // silently drop null bounds).
        $openings = [];
        $closures = [];
        foreach ($schedules as $row) {
            if ($row->getRecurring()) {
                $recurringDate = $row->getPeriodBegin();
                if (!$recurringDate instanceof \DateTimeInterface
                    || $recurringDate->format('m-d') !== $date->format('m-d')) {
                    continue;
                }
            } else {
                $day = $row->getDay();
                if ($day !== null && (int) $day !== $numDay) {
                    continue;
                }
                if (!$this->periodAppliesOn($row->getPeriodBegin(), $row->getPeriodEnd(), $date)) {
                    continue;
                }
            }
            if ($row->getClosed()) {
                $closures[] = $row;
            } else {
                $openings[] = $row;
            }
        }

        $open = $this->mergeRanges($this->rangesFrom($openings));

        if ($open === []) {
            return [];
        }

        foreach ($closures as $closure) {
            // A closure with no explicit hours closes the whole day.
            if ($closure->getBegin() === null || $closure->getEnd() === null) {
                return [];
            }

            $open = $this->subtractRange(
                $open,
                $closure->getBegin()->format('H:i:s'),
                $this->normalizeEndTime($closure->getEnd()->format('H:i:s'))
            );
        }

        return $open;
    }

    /**
     * Whether a schedule row's period covers the given date, treating a null bound as open-ended
     * (null/null = unbounded weekly recurrence, applies every matching weekday).
     */
    private function periodAppliesOn(?\DateTimeInterface $begin, ?\DateTimeInterface $end, \DateTimeImmutable $date): bool
    {
        $day = $date->format('Y-m-d');

        return (!$begin instanceof \DateTimeInterface || $begin->format('Y-m-d') <= $day)
            && (!$end instanceof \DateTimeInterface || $end->format('Y-m-d') >= $day);
    }

    /**
     * @param iterable<DealerShedules> $schedules
     * @return list<array{begin: string, end: string}>
     */
    private function rangesFrom(iterable $schedules): array
    {
        $ranges = [];
        foreach ($schedules as $schedule) {
            $begin = $schedule->getBegin();
            $end = $schedule->getEnd();
            if ($begin instanceof \DateTimeInterface && $end instanceof \DateTimeInterface) {
                $ranges[] = [
                    'begin' => $begin->format('H:i:s'),
                    'end' => $this->normalizeEndTime($end->format('H:i:s')),
                ];
            }
        }

        return $ranges;
    }

    /**
     * @param list<array{begin: string, end: string}> $ranges
     * @return list<array{begin: string, end: string}>
     */
    private function mergeRanges(array $ranges): array
    {
        if ($ranges === []) {
            return [];
        }

        usort($ranges, static fn (array $a, array $b): int => $a['begin'] <=> $b['begin']);

        $merged = [array_shift($ranges)];
        foreach ($ranges as $range) {
            $last = &$merged[count($merged) - 1];
            if ($range['begin'] <= $last['end']) {
                $last['end'] = max($last['end'], $range['end']);
            } else {
                $merged[] = $range;
            }
            unset($last);
        }

        return $merged;
    }

    /**
     * @param list<array{begin: string, end: string}> $ranges
     * @return list<array{begin: string, end: string}>
     */
    private function subtractRange(array $ranges, string $closeBegin, string $closeEnd): array
    {
        $result = [];
        foreach ($ranges as $range) {
            // No overlap: keep as is.
            if ($closeEnd <= $range['begin'] || $closeBegin >= $range['end']) {
                $result[] = $range;
                continue;
            }
            // Left remainder.
            if ($closeBegin > $range['begin']) {
                $result[] = ['begin' => $range['begin'], 'end' => min($closeBegin, $range['end'])];
            }
            // Right remainder.
            if ($closeEnd < $range['end']) {
                $result[] = ['begin' => max($closeEnd, $range['begin']), 'end' => $range['end']];
            }
        }

        return $result;
    }

    /**
     * @param list<array{begin: string, end: string}> $ranges
     * @return list<array{time: string, datetime: string, remaining: int|null}>
     */
    private function buildSlots(
        int $dealerId,
        \DateTimeImmutable $date,
        array $ranges,
        DealerPickupConfig $config,
        \DateTimeImmutable $earliest,
        \DateTimeZone $tz
    ): array {
        // Guard against a 0/negative duration that would make the loop below never progress.
        $step = new \DateInterval('PT' . max(1, $config->getSlotDurationMinutes()) . 'M');
        $maxPerSlot = $config->getMaxOrdersPerSlot();
        $slots = [];

        foreach ($ranges as $range) {
            $slotStart = new \DateTimeImmutable($date->format('Y-m-d') . ' ' . $range['begin'], $tz);
            $rangeEnd = new \DateTimeImmutable($date->format('Y-m-d') . ' ' . $range['end'], $tz);

            while ($slotStart < $rangeEnd) {
                $slotEnd = $slotStart->add($step);
                if ($slotEnd > $rangeEnd) {
                    break;
                }

                if ($slotStart >= $earliest) {
                    $remaining = $this->remainingCapacity($dealerId, $slotStart, $maxPerSlot);
                    if ($remaining === null || $remaining > 0) {
                        $slots[] = [
                            'time' => $slotStart->format('H:i'),
                            'datetime' => $slotStart->format('Y-m-d H:i:s'),
                            'remaining' => $remaining,
                        ];
                    }
                }

                $slotStart = $slotEnd;
            }
        }

        return $slots;
    }

    /**
     * @return int|null null when the slot has no quota (unlimited), otherwise the remaining places
     */
    private function remainingCapacity(int $dealerId, \DateTimeImmutable $slotStart, int $maxPerSlot): ?int
    {
        if ($maxPerSlot <= 0) {
            return null;
        }

        $taken = DealerOrderPickupQuery::create()
            ->filterByDealerId($dealerId)
            ->filterByPickupDatetime($slotStart->format('Y-m-d H:i:s'))
            ->count();

        return max(0, $maxPerSlot - $taken);
    }

    /**
     * A midnight end time (00:00:00) means end-of-day. Expressed as 24:00:00 it keeps the
     * range non-empty for string comparisons, and \DateTimeImmutable rolls it to the next
     * day's midnight when building slots.
     */
    private function normalizeEndTime(string $end): string
    {
        return $end === '00:00:00' ? '24:00:00' : $end;
    }

    /**
     * The dealer's configured timezone, falling back to the default when unset or invalid.
     */
    private function timezoneFor(int $dealerId): \DateTimeZone
    {
        $name = DealerQuery::create()->findPk($dealerId)?->getTimezone();

        try {
            return new \DateTimeZone($name !== null && $name !== '' ? $name : self::DEFAULT_TIMEZONE);
        } catch (\Exception) {
            return new \DateTimeZone(self::DEFAULT_TIMEZONE);
        }
    }

    private function getConfig(int $dealerId): DealerPickupConfig
    {
        $config = DealerPickupConfigQuery::create()->findPk($dealerId);

        if ($config === null) {
            $config = (new DealerPickupConfig())
                ->setDealerId($dealerId)
                ->setPrepDelayMinutes(0)
                ->setOrderableDays(7)
                ->setSlotDurationMinutes(60)
                ->setMaxOrdersPerSlot(0);
        }

        return $config;
    }

    /**
     * Server-side re-validation of a chosen slot: it must start inside an open range for its day
     * (with room for a full slot) and still have remaining capacity. Used to reject forged/stale
     * choices at selection and before persisting the order pickup.
     */
    public function isSlotAvailable(int $dealerId, \DateTimeInterface $datetime): bool
    {
        $moment = \DateTimeImmutable::createFromInterface($datetime);
        $config = $this->getConfig($dealerId);

        if ($this->remainingCapacity($dealerId, $moment, $config->getMaxOrdersPerSlot()) === 0) {
            return false;
        }

        $schedules = DealerShedulesQuery::create()->filterByDealerId($dealerId)->find();
        $ranges = $this->resolveOpenRanges($schedules, new \DateTimeImmutable($moment->format('Y-m-d')));
        $duration = max(1, $config->getSlotDurationMinutes());
        $slotStart = $moment->format('H:i:s');
        $slotEnd = $moment->add(new \DateInterval('PT' . $duration . 'M'))->format('H:i:s');

        foreach ($ranges as $range) {
            if ($slotStart >= $range['begin'] && $slotEnd <= $range['end']) {
                return true;
            }
        }

        return false;
    }
}
