<?php

declare(strict_types=1);

namespace Dealer\Api\Validator;

use Dealer\Api\Scope\DealerScopeResolver;
use Dealer\Model\DealerContactQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ContactInScopeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly DealerScopeResolver $dealerScopeResolver,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ContactInScope) {
            throw new UnexpectedTypeException($constraint, ContactInScope::class);
        }

        if (null === $value) {
            return;
        }

        $query = DealerContactQuery::create()->filterById((int) $value);
        $dealerIds = $this->dealerScopeResolver->dealerIds();

        if (null !== $dealerIds) {
            $query->filterByDealerId($dealerIds, Criteria::IN);
        }

        if (0 === $query->count()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
