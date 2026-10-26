<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Dealer\Model\Map\DealerShedulesTableMap;
use Dealer\Api\Scope\DealerScopedResourceInterface;
use Dealer\Dealer as DealerModule;
use Dealer\Api\State\Processor\DealerScheduleProcessor;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Api\Bridge\Propel\Attribute\Column;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

/**
 * One row of the "Schedules" tab, either a usual opening slot (exceptional false: a weekday
 * and an opening and closing time) or an exceptional opening or closure (exceptional true:
 * a period, open with hours or closed, optionally restricted to a weekday or repeated every
 * year).
 */
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/dealer_schedules',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_SCHEDULE], summary: 'Opening hours of the stores in scope'),
        ),
        new Post(
            uriTemplate: '/admin/dealer_schedules',
            denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE, self::GROUP_ADMIN_CREATE]],
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_SCHEDULE],
                summary: 'Add a usual opening slot or an exceptional opening or closure',
                description: 'dealerId names the store; it may be left out when the request is bounded to one store. '
                    .'exceptional is required. Usual slot: day (0 = Monday … 6 = Sunday), begin and end '
                    .'(HH:MM, begin before end, end 00:00 closing at midnight); it cannot overlap another usual slot. '
                    .'Exceptional: a single date (periodBegin and periodEnd equal, YYYY-MM-DD), a period (periodEnd '
                    .'not before periodBegin), a yearly date (recurring true, periodBegin only) or a weekday (day, '
                    .'with or without a period). An opening has begin and end; a closure (closed true) has both '
                    .'for part of the day, or neither for the whole day. A slot cannot change kind afterwards.',
            ),
            processor: DealerScheduleProcessor::class,
        ),
        new Get(
            uriTemplate: '/admin/dealer_schedules/{id}',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_SCHEDULE], summary: 'Opening slot detail'),
        ),
        new Patch(
            uriTemplate: '/admin/dealer_schedules/{id}',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_SCHEDULE],
                summary: 'Update an opening slot',
                description: 'Partial update (Content-Type: application/merge-patch+json), same rules as the creation.',
            ),
            processor: DealerScheduleProcessor::class,
        ),
        new Delete(
            uriTemplate: '/admin/dealer_schedules/{id}',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_SCHEDULE], summary: 'Delete an opening slot'),
            processor: DealerScheduleProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
    validationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
)]
class DealerSchedule implements PropelResourceInterface, DealerScopedResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_ADMIN_READ = 'admin:dealer_schedule:read';
    public const GROUP_ADMIN_WRITE = 'admin:dealer_schedule:write';
    /** Written on creation only: a slot never moves to another store. */
    public const GROUP_ADMIN_CREATE = 'admin:dealer_schedule:create';

    private const TIME_PATTERN = '/^([01]\d|2[0-3]):[0-5]\d$/';
    private const DATE_PATTERN = '/^\d{4}-\d{2}-\d{2}$/';
    private const MIDNIGHT = '00:00';
    /** Kept for a value that is no text at all: no pattern accepts it, the violation names the field. */
    private const INVALID = '(invalid)';
    private const END_OF_DAY = '24:00';

    #[Groups([self::GROUP_ADMIN_READ, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?int $id = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_CREATE])]
    public ?int $dealerId = null;

    #[Assert\NotNull(message: 'Say whether the slot is usual (false) or exceptional (true).', groups: [self::GROUP_ADMIN_WRITE])]
    #[Column(propelFieldName: 'exception')]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?bool $exceptional = null;

    #[Assert\Range(min: 0, max: 6, groups: [self::GROUP_ADMIN_WRITE])]
    #[ApiProperty(description: 'Weekday, 0 = Monday … 6 = Sunday.')]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?int $day = null;

    #[ApiProperty(schema: ['type' => ['string', 'null'], 'example' => '09:00'])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?string $begin = null;

    #[ApiProperty(schema: ['type' => ['string', 'null'], 'example' => '19:00'])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?string $end = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public bool $closed = false;

    #[ApiProperty(schema: ['type' => ['string', 'null'], 'format' => 'date', 'example' => '2026-12-24'])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?string $periodBegin = null;

    #[ApiProperty(schema: ['type' => ['string', 'null'], 'format' => 'date', 'example' => '2026-12-26'])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?string $periodEnd = null;

    #[ApiProperty(description: 'Exceptional only: applies every year on the month and day of the period.')]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public bool $recurring = false;

    #[Assert\Length(max: 255, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?string $title = null;

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerShedulesTableMap();
    }

    public static function restrictToDealers(ModelCriteria $query, array $dealerIds): void
    {
        if (!$query instanceof DealerShedulesQuery) {
            throw new \LogicException(sprintf('Cannot bound a %s to a store.', $query::class));
        }

        $query->filterByDealerId($dealerIds, Criteria::IN);
    }

    public function owningDealerId(): ?int
    {
        return $this->dealerId;
    }

    public function assignDealer(int $dealerId): void
    {
        $this->dealerId = $dealerId;
    }

    /**
     * The format of each field, and what the back office refuses before it reaches
     * Dealer\Service\SchedulesService (the processor writes through it, which adds the overlap
     * check). Same reading as the slot engine: a missing periodEnd is open-ended.
     */
    #[Assert\Callback(groups: [self::GROUP_ADMIN_WRITE])]
    public function validateSlot(ExecutionContextInterface $context): void
    {
        foreach (['begin' => $this->begin, 'end' => $this->end] as $path => $time) {
            if (null !== $time && !preg_match(self::TIME_PATTERN, $time)) {
                $context->buildViolation('Expected a time as HH:MM.')->atPath($path)->addViolation();

                return;
            }
        }

        foreach (['periodBegin' => $this->periodBegin, 'periodEnd' => $this->periodEnd] as $path => $date) {
            if (null !== $date && (!preg_match(self::DATE_PATTERN, $date) || \DateTimeImmutable::createFromFormat('!Y-m-d', $date)?->format('Y-m-d') !== $date)) {
                $context->buildViolation('Expected a date as YYYY-MM-DD.')->atPath($path)->addViolation();

                return;
            }
        }

        $stored = $this->getPropelModel();

        if ($stored instanceof DealerShedules && null !== $this->exceptional && (bool) $stored->getException() !== $this->exceptional) {
            // Turned into the other kind, the row would keep the weekday and hours of the first
            // one: a closure of a Thursday tied to a Monday never applies.
            $context->buildViolation('A slot cannot change kind: delete it and create the other one.')->atPath('exceptional')->addViolation();

            return;
        }

        if (false === $this->exceptional) {
            $this->validateUsualSlot($context);
        }

        if (true === $this->exceptional) {
            $this->validateExceptionalSlot($context);
        }
    }

    private function validateUsualSlot(ExecutionContextInterface $context): void
    {
        if (null === $this->day) {
            $context->buildViolation('A usual slot needs a weekday.')->atPath('day')->addViolation();
        }

        if (null !== $this->periodBegin || null !== $this->periodEnd || $this->recurring || $this->closed) {
            $context->buildViolation('A usual slot has no period, recurrence or closure: send it as exceptional.')
                ->atPath('exceptional')
                ->addViolation();
        }

        $this->validateHours($context);
    }

    private function validateExceptionalSlot(ExecutionContextInterface $context): void
    {
        if (null === $this->periodBegin && null === $this->periodEnd && null === $this->day) {
            $context->buildViolation('An exceptional slot targets a date, a period or a weekday.')->atPath('periodBegin')->addViolation();
        }

        if ($this->recurring && null === $this->periodBegin) {
            $context->buildViolation('A yearly slot needs its date in periodBegin.')->atPath('periodBegin')->addViolation();
        }

        // An entry on no weekday with no end date applies every day from its start on: the
        // back office only writes one for a yearly date, and sends periodEnd = periodBegin for
        // a single date. Accepting it would close the store, or open it, for ever.
        if (!$this->recurring && null === $this->day && null !== $this->periodBegin && null === $this->periodEnd) {
            $context->buildViolation('Send periodEnd, equal to periodBegin for a single date.')->atPath('periodEnd')->addViolation();
        }

        if (null !== $this->periodBegin && null !== $this->periodEnd && $this->periodEnd < $this->periodBegin) {
            $context->buildViolation('The end date cannot be before the start date.')->atPath('periodEnd')->addViolation();
        }

        if (!$this->closed) {
            $this->validateHours($context);

            return;
        }

        // A closure is either the whole day (no hours) or a time range.
        if ((null === $this->begin) !== (null === $this->end)) {
            $context->buildViolation('A closure has both an opening and a closing time, or neither for the whole day.')
                ->atPath(null === $this->begin ? 'begin' : 'end')
                ->addViolation();

            return;
        }

        if (null !== $this->begin) {
            $this->validateHours($context);
        }
    }

    private function validateHours(ExecutionContextInterface $context): void
    {
        if (null === $this->begin || null === $this->end) {
            $context->buildViolation('An opening needs an opening and a closing time.')->atPath(null === $this->begin ? 'begin' : 'end')->addViolation();

            return;
        }

        // A closing time of 00:00 is the end of the day, as Dealer\Service\SchedulesService reads it.
        $end = self::MIDNIGHT === $this->end ? self::END_OF_DAY : $this->end;

        if ($this->begin >= $end) {
            $context->buildViolation('The opening time must be before the closing time.')->atPath('end')->addViolation();
        }
    }

    /**
     * The model hands a DateTime back, the payload a string, kept as sent so the validation sees
     * it: a DateTime-typed setter let the serializer turn "9:00", "24:00" or "2026-02-30" into a
     * date of its own choosing. Anything else is kept in a form the patterns reject.
     */
    private static function toText(mixed $value, string $format): ?string
    {
        return match (true) {
            null === $value => null,
            $value instanceof \DateTimeInterface => $value->format($format),
            \is_string($value) => $value,
            default => self::INVALID,
        };
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDealerId(): ?int
    {
        return $this->dealerId;
    }

    public function setDealerId(?int $dealerId): self
    {
        $this->dealerId = $dealerId;

        return $this;
    }

    public function getExceptional(): ?bool
    {
        return $this->exceptional;
    }

    public function setExceptional(?bool $exceptional): self
    {
        $this->exceptional = $exceptional;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getBegin(): ?string
    {
        return $this->begin;
    }

    public function setBegin(mixed $begin): self
    {
        $this->begin = self::toText($begin, 'H:i');

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(mixed $end): self
    {
        $this->end = self::toText($end, 'H:i');

        return $this;
    }

    public function getClosed(): bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getPeriodBegin(): ?string
    {
        return $this->periodBegin;
    }

    public function setPeriodBegin(mixed $periodBegin): self
    {
        $this->periodBegin = self::toText($periodBegin, 'Y-m-d');

        return $this;
    }

    public function getPeriodEnd(): ?string
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(mixed $periodEnd): self
    {
        $this->periodEnd = self::toText($periodEnd, 'Y-m-d');

        return $this;
    }

    public function getRecurring(): bool
    {
        return $this->recurring;
    }

    public function setRecurring(bool $recurring): self
    {
        $this->recurring = $recurring;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
