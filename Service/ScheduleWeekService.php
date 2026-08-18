<?php

declare(strict_types=1);

namespace Dealer\Service;

use Dealer\Dealer;
use Dealer\Exception\ScheduleWeekValidationException;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Propel\Runtime\Propel;
use Thelia\Core\Translation\Translator;

/**
 * Atomic replacement of a dealer's base weekly opening hours: the back-office
 * grid posts the whole week at once, the previous base rows are swapped for the
 * new ones in one transaction, and validation covers the whole week so every
 * error is reported in a single round-trip, attached to its weekday.
 *
 * Exceptional entries (closures, exceptional openings) are not touched.
 */
class ScheduleWeekService
{
    /**
     * @param array<int|string, list<array{begin?: string|null, end?: string|null}>> $week
     *        slot lists indexed by weekday (0 = Monday … 6 = Sunday)
     *
     * @throws ScheduleWeekValidationException when at least one slot is invalid
     */
    public function replaceWeek(int $dealerId, array $week): void
    {
        $slotsByDay = $this->normalize($week);
        $this->validate($slotsByDay);

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            DealerShedulesQuery::create()
                ->filterByDealerId($dealerId)
                ->filterByException(false)
                ->delete($con);

            foreach ($slotsByDay as $day => $slots) {
                foreach ($slots as $slot) {
                    (new DealerShedules())
                        ->setDealerId($dealerId)
                        ->setDay($day)
                        ->setBegin($slot['begin'])
                        ->setEnd($slot['end'])
                        ->setClosed(false)
                        ->setException(false)
                        ->setRecurring(false)
                        ->save($con);
                }
            }

            $con->commit();
        } catch (\Throwable $exception) {
            $con->rollBack();

            throw $exception;
        }
    }

    /**
     * @return array<int, list<array{begin: string, end: string}>>
     */
    private function normalize(array $week): array
    {
        $slotsByDay = [];

        foreach ($week as $day => $slots) {
            $day = (int) $day;

            if ($day < 0 || $day > 6 || !is_array($slots)) {
                continue;
            }

            foreach ($slots as $slot) {
                $begin = $this->normalizeTime($slot['begin'] ?? null);
                $end = $this->normalizeTime($slot['end'] ?? null);

                if ($begin === null && $end === null) {
                    continue;
                }

                $slotsByDay[$day][] = ['begin' => (string) $begin, 'end' => (string) $end];
            }
        }

        return $slotsByDay;
    }

    private function normalizeTime(mixed $time): ?string
    {
        if (!is_string($time) || trim($time) === '') {
            return null;
        }

        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', trim($time), $matches) !== 1
            || (int) $matches[1] > 23 || (int) $matches[2] > 59) {
            return '';
        }

        return sprintf('%02d:%s:00', (int) $matches[1], $matches[2]);
    }

    /**
     * @param array<int, list<array{begin: string, end: string}>> $slotsByDay
     *
     * @throws ScheduleWeekValidationException
     */
    private function validate(array $slotsByDay): void
    {
        $translator = Translator::getInstance();
        $errors = [];

        foreach ($slotsByDay as $day => $slots) {
            foreach ($slots as $slot) {
                if ($slot['begin'] === '' || $slot['end'] === '') {
                    $errors[$day][] = $translator->trans(
                        'Each time slot needs a valid begin and end time.',
                        [],
                        Dealer::MESSAGE_DOMAIN
                    );

                    continue 2;
                }
            }

            usort($slots, static fn (array $a, array $b): int => $a['begin'] <=> $b['begin']);

            $previous = null;
            foreach ($slots as $slot) {
                $end = $slot['end'] === '00:00:00' ? '24:00:00' : $slot['end'];

                if ($end <= $slot['begin']) {
                    $errors[$day][] = $translator->trans(
                        'The end time (%end) must be after the begin time (%begin).',
                        ['%begin' => substr($slot['begin'], 0, 5), '%end' => substr($slot['end'], 0, 5)],
                        Dealer::MESSAGE_DOMAIN
                    );

                    continue;
                }

                if ($previous !== null && $slot['begin'] < $previous['end']) {
                    $errors[$day][] = $translator->trans(
                        'The %begin - %end time slot overlaps an existing slot (%exBegin - %exEnd).',
                        [
                            '%begin' => substr($slot['begin'], 0, 5),
                            '%end' => substr($slot['end'], 0, 5),
                            '%exBegin' => substr($previous['begin'], 0, 5),
                            '%exEnd' => substr($previous['end'], 0, 5),
                        ],
                        Dealer::MESSAGE_DOMAIN
                    );

                    continue;
                }

                $previous = ['begin' => $slot['begin'], 'end' => $end];
            }
        }

        if ($errors !== []) {
            throw new ScheduleWeekValidationException($errors);
        }
    }
}
