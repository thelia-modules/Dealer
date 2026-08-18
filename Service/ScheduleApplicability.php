<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Model\DealerShedules;

/**
 * Single source of truth for "does this schedule row apply on this calendar date?".
 * Shared by the pickup slot engine, the schedule validation and the legacy accessors,
 * so every consumer agrees on weekday, period and recurrence semantics.
 */
final class ScheduleApplicability
{
    public static function appliesOn(DealerShedules $row, \DateTimeImmutable $date): bool
    {
        if ($row->getRecurring()) {
            // Yearly recurrence: the stored period_begin carries the month/day, its year is ignored.
            $recurringDate = $row->getPeriodBegin();

            return $recurringDate instanceof \DateTimeInterface
                && $recurringDate->format('m-d') === $date->format('m-d');
        }

        $day = $row->getDay();
        if ($day !== null && (int) $day !== ((int) $date->format('N') - 1)) {
            return false;
        }

        return self::periodCovers($row->getPeriodBegin(), $row->getPeriodEnd(), $date);
    }

    /**
     * A null bound is open-ended (null/null = applies on every matching weekday).
     */
    public static function periodCovers(?\DateTimeInterface $begin, ?\DateTimeInterface $end, \DateTimeImmutable $date): bool
    {
        $day = $date->format('Y-m-d');

        return (!$begin instanceof \DateTimeInterface || $begin->format('Y-m-d') <= $day)
            && (!$end instanceof \DateTimeInterface || $end->format('Y-m-d') >= $day);
    }
}
