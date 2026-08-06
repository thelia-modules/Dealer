<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Model\DealerPickupConfig;
use Dealer\Model\DealerPickupConfigQuery;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Propel\Runtime\ActiveQuery\Criteria;

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
     * @return list<array{
     *     date: string,
     *     day: int,
     *     slots: list<array{time: string, datetime: string, remaining: int|null}>
     * }>
     */
    public function getAvailableSlots(int $dealerId, ?\DateTimeInterface $from = null): array
    {
        $config = $this->getConfig($dealerId);
        $now = $from !== null ? \DateTimeImmutable::createFromInterface($from) : new \DateTimeImmutable();
        $earliest = $now->add(new \DateInterval('PT' . $config->getPrepDelayMinutes() . 'M'));

        $days = [];
        $cursor = new \DateTimeImmutable($now->format('Y-m-d'));
        $scanned = 0;

        while (count($days) < $config->getOrderableDays() && $scanned < self::MAX_DAYS_SCAN) {
            $ranges = $this->resolveOpenRanges($dealerId, $cursor);

            if ($ranges !== []) {
                $slots = $this->buildSlots($dealerId, $cursor, $ranges, $config, $earliest);

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
     * @return list<array{begin: string, end: string}> sorted, non-overlapping H:i:s ranges
     */
    private function resolveOpenRanges(int $dealerId, \DateTimeImmutable $date): array
    {
        $numDay = (int) $date->format('N') - 1;

        $base = $this->rangesFrom(
            DealerShedulesQuery::create()
                ->filterByDealerId($dealerId)
                ->filterByDay($numDay)
                ->filterByClosed(false)
                ->filterByPeriodNull()
                ->find()
        );

        $exceptionalOpen = $this->rangesFrom(
            DealerShedulesQuery::create()
                ->filterByDealerId($dealerId)
                ->filterByDay($numDay)
                ->filterByClosed(false)
                ->filterByPeriodBegin($date->format('Y-m-d'), Criteria::LESS_EQUAL)
                ->filterByPeriodEnd($date->format('Y-m-d'), Criteria::GREATER_EQUAL)
                ->find()
        );

        $open = $this->mergeRanges([...$base, ...$exceptionalOpen]);

        if ($open === []) {
            return [];
        }

        // Closures: unbounded weekly (period null) OR dated period covering this date.
        $closures = DealerShedulesQuery::create()
            ->filterByDealerId($dealerId)
            ->filterByDay($numDay)
            ->filterByClosed(true)
            ->find();

        foreach ($closures as $closure) {
            if (!$this->closureAppliesOn($closure, $date)) {
                continue;
            }

            // A closure with no explicit hours closes the whole day.
            if ($closure->getBegin() === null || $closure->getEnd() === null) {
                return [];
            }

            $open = $this->subtractRange(
                $open,
                $closure->getBegin()->format('H:i:s'),
                $closure->getEnd()->format('H:i:s')
            );
        }

        return $open;
    }

    private function closureAppliesOn(DealerShedules $closure, \DateTimeImmutable $date): bool
    {
        $begin = $closure->getPeriodBegin();
        $end = $closure->getPeriodEnd();

        // Unbounded weekly closure ("every Saturday").
        if ($begin === null && $end === null) {
            return true;
        }

        // Dated recurrence ("every Saturday from X to Y") or one-off dated closure.
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
                $ranges[] = ['begin' => $begin->format('H:i:s'), 'end' => $end->format('H:i:s')];
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
        \DateTimeImmutable $earliest
    ): array {
        $step = new \DateInterval('PT' . $config->getSlotDurationMinutes() . 'M');
        $maxPerSlot = $config->getMaxOrdersPerSlot();
        $slots = [];

        foreach ($ranges as $range) {
            $slotStart = new \DateTimeImmutable($date->format('Y-m-d') . ' ' . $range['begin']);
            $rangeEnd = new \DateTimeImmutable($date->format('Y-m-d') . ' ' . $range['end']);

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
}
