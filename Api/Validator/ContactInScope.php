<?php

declare(strict_types=1);

namespace Dealer\Api\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * The contact a contact detail is written on exists and belongs to a store in scope. Both
 * failures answer the same violation, so the answer tells nothing about a contact of another
 * store.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ContactInScope extends Constraint
{
    public string $message = 'Unknown contact.';
}
