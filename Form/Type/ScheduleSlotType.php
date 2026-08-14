<?php

declare(strict_types=1);

namespace Dealer\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * A single pickup time range (one "créneau"): begin/end times.
 * Used as the entry type of the schedules "slots" collection so a schedule
 * can be created with an arbitrary number of ranges instead of a fixed AM/PM pair.
 */
final class ScheduleSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('begin', TimeType::class, [
                'input' => 'string',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('end', TimeType::class, [
                'input' => 'string',
                'widget' => 'single_text',
                'required' => false,
            ]);
    }
}
