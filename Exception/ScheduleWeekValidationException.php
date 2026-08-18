<?php

declare(strict_types=1);

namespace Dealer\Exception;

/**
 * Carries the weekly-grid validation errors, keyed by weekday (0 = Monday … 6 = Sunday),
 * so the back-office can attach each message to its day row.
 */
class ScheduleWeekValidationException extends \RuntimeException
{
    /**
     * @param array<int, list<string>> $errorsByDay
     */
    public function __construct(
        private readonly array $errorsByDay,
    ) {
        parent::__construct('Invalid weekly schedule.');
    }

    /**
     * @return array<int, list<string>>
     */
    public function getErrorsByDay(): array
    {
        return $this->errorsByDay;
    }
}
