<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Model\DealerOrderPickupQuery;
use Dealer\Model\DealerPickupConfig;
use Dealer\Model\DealerQuery;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\OrderStatus;

/**
 * Computes the pickup slots available for a dealer, applying its opening hours,
 * exceptional closures/openings (including unbounded weekly recurrence), the
 * preparation delay, the number of orderable days and the per-slot order quota.
 *
 * Fully self-contained inside the Dealer module: it reads only dealer_* tables
 * (plus the order status, to free the quota held by cancelled orders).
 */
class PickupSlotService
{
    /** Hard limit on the calendar scan, so an over-constrained config cannot loop forever. */
    private const MAX_DAYS_SCAN = 60;

    private const DEFAULT_TIMEZONE = 'Europe/Paris';

    public function __construct(
        private readonly DealerPickupConfigService $configService,
    ) {
    }

    /**
     * The upcoming pickup days, each with the slots the customer may see. A saturated slot
     * is listed with 'full' set, and a day whose slots are all full is kept: the shop must
     * look busy, not closed. Only days with no slot at all (closed, or entirely behind the
     * preparation delay) drop out.
     *
     * @return list<array{
     *     date: string,
     *     day: int,
     *     slots: list<array{time: string, datetime: string, remaining: int|null, full: bool}>
     * }>
     */
    public function getAvailableSlots(int $dealerId, ?\DateTimeInterface $from = null): array
    {
        $config = $this->configService->get($dealerId);
        $tz = $this->timezoneFor($dealerId);
        $now = $from !== null
            ? \DateTimeImmutable::createFromInterface($from)->setTimezone($tz)
            : new \DateTimeImmutable('now', $tz);
        $earliest = $now->add(new \DateInterval('PT' . $config->getPrepDelayMinutes() . 'M'));

        $schedules = DealerShedulesQuery::create()->filterByDealerId($dealerId)->find();

        $days = [];
        $cursor = new \DateTimeImmutable($now->format('Y-m-d'), $tz);
        $scanned = 0;

        // One query for the whole scan window: the quota already consumed on each slot.
        $taken = $this->takenBySlot(
            $dealerId,
            $config,
            $cursor,
            $cursor->add(new \DateInterval('P' . self::MAX_DAYS_SCAN . 'D'))
        );

        while (count($days) < $config->getOrderableDays() && $scanned < self::MAX_DAYS_SCAN) {
            $ranges = $this->resolveOpenRanges($schedules, $cursor);

            if ($ranges !== []) {
                $slots = $this->buildSlots($cursor, $ranges, $config, $earliest, $tz, $taken);

                // A saturated day keeps its (full) slots, so it stays here; only a day
                // without any slot at all — closed, or entirely too early — is skipped.
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
     * Strict server-side re-validation of a chosen slot: it is valid only if the slot
     * engine would offer it right now — same grid, same preparation delay, same
     * orderable-days window, same remaining capacity. Rejects forged datetimes (past,
     * off-grid, beyond the window) as well as slots that filled up in the meantime.
     *
     * Full slots are still listed by getAvailableSlots() so the customer can see them
     * greyed out; they are never selectable, hence the explicit rejection here.
     */
    public function isSlotAvailable(int $dealerId, \DateTimeInterface $datetime): bool
    {
        $target = \DateTimeImmutable::createFromInterface($datetime)->format('Y-m-d H:i:s');
        $targetDate = substr($target, 0, 10);

        foreach ($this->getAvailableSlots($dealerId) as $day) {
            if ($day['date'] !== $targetDate) {
                continue;
            }

            foreach ($day['slots'] as $slot) {
                if ($slot['datetime'] === $target) {
                    return !$slot['full'];
                }
            }
        }

        return false;
    }

    /**
     * Lax variant for back-office overrides: the slot only has to start inside an open
     * range of its day (with room for a full slot). No preparation delay, no window, no
     * capacity check — the administrator takes responsibility for those.
     */
    public function isSlotWithinOpeningHours(int $dealerId, \DateTimeInterface $datetime): bool
    {
        $moment = \DateTimeImmutable::createFromInterface($datetime);
        $config = $this->configService->get($dealerId);

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

    /**
     * Remaining capacity of one slot, quota-aware (null = unlimited). Cancelled and
     * refunded orders do not consume the quota.
     */
    public function remainingCapacity(int $dealerId, \DateTimeInterface $slotStart): ?int
    {
        $maxPerSlot = $this->configService->get($dealerId)->getMaxOrdersPerSlot();

        if ($maxPerSlot <= 0) {
            return null;
        }

        $taken = DealerOrderPickupQuery::create()
            ->filterByDealerId($dealerId)
            ->filterByPickupDatetime($slotStart->format('Y-m-d H:i:s'))
            ->useOrderQuery()
                ->useOrderStatusQuery()
                    ->filterByCode([OrderStatus::CODE_CANCELED, OrderStatus::CODE_REFUNDED], Criteria::NOT_IN)
                ->endUse()
            ->endUse()
            ->count();

        return max(0, $maxPerSlot - $taken);
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
        $openings = [];
        $closures = [];
        foreach ($schedules as $row) {
            if (!ScheduleApplicability::appliesOn($row, $date)) {
                continue;
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
     * A slot whose quota is exhausted is kept in the list, flagged 'full': the customer
     * must see it, greyed out and unselectable, instead of a hole in the grid. Slots
     * before the preparation delay stay out — too early is not the same as full.
     *
     * @param array<string, int> $taken orders already consuming each slot, keyed by 'Y-m-d H:i:s'
     * @return list<array{time: string, datetime: string, remaining: int|null, full: bool}>
     */
    private function buildSlots(
        \DateTimeImmutable $date,
        array $ranges,
        DealerPickupConfig $config,
        \DateTimeImmutable $earliest,
        \DateTimeZone $tz,
        array $taken
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
                    $remaining = $maxPerSlot > 0
                        ? max(0, $maxPerSlot - ($taken[$slotStart->format('Y-m-d H:i:s')] ?? 0))
                        : null;

                    $slots[] = [
                        'time' => $slotStart->format('H:i'),
                        'datetime' => $slotStart->format('Y-m-d H:i:s'),
                        'remaining' => $remaining,
                        'full' => $remaining !== null && $remaining <= 0,
                    ];
                }

                $slotStart = $slotEnd;
            }
        }

        return $slots;
    }

    /**
     * Orders consuming quota on each slot of the scan window, in a single query.
     * Cancelled and refunded orders are excluded: they release their slot.
     *
     * @return array<string, int> counts keyed by 'Y-m-d H:i:s'
     */
    private function takenBySlot(
        int $dealerId,
        DealerPickupConfig $config,
        \DateTimeImmutable $windowBegin,
        \DateTimeImmutable $windowEnd
    ): array {
        if ($config->getMaxOrdersPerSlot() <= 0) {
            return [];
        }

        $datetimes = DealerOrderPickupQuery::create()
            ->filterByDealerId($dealerId)
            ->filterByPickupDatetime([
                'min' => $windowBegin->format('Y-m-d 00:00:00'),
                'max' => $windowEnd->format('Y-m-d 23:59:59'),
            ])
            ->useOrderQuery()
                ->useOrderStatusQuery()
                    ->filterByCode([OrderStatus::CODE_CANCELED, OrderStatus::CODE_REFUNDED], Criteria::NOT_IN)
                ->endUse()
            ->endUse()
            ->select(['pickup_datetime'])
            ->find();

        $taken = [];
        foreach ($datetimes as $datetime) {
            $key = substr((string) $datetime, 0, 19);
            $taken[$key] = ($taken[$key] ?? 0) + 1;
        }

        return $taken;
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

    private function timezoneFor(int $dealerId): \DateTimeZone
    {
        $name = DealerQuery::create()->findPk($dealerId)?->getTimezone();

        try {
            return new \DateTimeZone($name !== null && $name !== '' ? $name : self::DEFAULT_TIMEZONE);
        } catch (\Exception) {
            return new \DateTimeZone(self::DEFAULT_TIMEZONE);
        }
    }
}
