<?php

declare(strict_types=1);

namespace Dealer\Api\State\Processor;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Dealer\Api\Resource\DealerSchedule;
use Dealer\Api\Scope\DealerScopeResolver;
use Dealer\Model\DealerShedules;
use Dealer\Service\SchedulesService;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Thelia\Api\Bridge\Propel\Service\ApiResourcePropelTransformerService;

/**
 * Writes the opening hours through Dealer\Service\SchedulesService, as the back office does:
 * its rules (time and period ranges, recurrence, overlap between slots of the same kind) are
 * the ones a row has to pass to stay editable in the back office, and its events fire. A rule
 * it refuses answers 422.
 */
final readonly class DealerScheduleProcessor implements ProcessorInterface
{
    public function __construct(
        private SchedulesService $schedulesService,
        private DealerScopeResolver $dealerScopeResolver,
        private ApiResourcePropelTransformerService $transformerService,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?DealerSchedule
    {
        if (!$data instanceof DealerSchedule) {
            throw new \LogicException(sprintf('%s only writes %s resources.', self::class, DealerSchedule::class));
        }

        // A PATCH or a DELETE reaches a row read bounded to the stores in scope; it keeps its store.
        $stored = $data->getPropelModel();
        $dealerId = $stored instanceof DealerShedules
            ? (int) $stored->getDealerId()
            : $this->dealerScopeResolver->resolveWrittenDealerId($data->owningDealerId());

        if ($operation instanceof DeleteOperationInterface) {
            $this->schedulesService->deleteFromId((int) $uriVariables['id'], $dealerId);

            return null;
        }

        $row = [
            'dealer_id' => $dealerId,
            'exception' => (bool) $data->getExceptional(),
            'day' => $data->getDay(),
            'begin' => $data->getBegin(),
            'end' => $data->getEnd(),
            'period_begin' => $data->getPeriodBegin(),
            'period_end' => $data->getPeriodEnd(),
            'closed' => $data->getClosed(),
            'recurring' => $data->getRecurring(),
            'title' => $data->getTitle(),
        ];

        try {
            $schedule = isset($uriVariables['id'])
                ? $this->schedulesService->updateFromArray(['id' => (int) $uriVariables['id']] + $row)
                : $this->schedulesService->createFromArray($row);
        } catch (\RuntimeException $exception) {
            throw new ValidationException(new ConstraintViolationList([
                new ConstraintViolation($exception->getMessage(), null, [], $data, '', $row),
            ]));
        }

        /** @var DealerSchedule $resource */
        $resource = $this->transformerService->modelToResource(
            resourceClass: DealerSchedule::class,
            propelModel: $schedule,
            context: $operation->getNormalizationContext() ?? [],
            withAddon: false,
        );

        return $resource;
    }
}
