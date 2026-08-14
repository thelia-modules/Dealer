<?php

declare(strict_types=1);

namespace Dealer\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

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
