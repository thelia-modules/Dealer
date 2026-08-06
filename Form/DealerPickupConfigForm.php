<?php

declare(strict_types=1);

namespace Dealer\Form;

use Dealer\Dealer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Per-dealer pickup configuration: preparation delay, orderable days,
 * slot duration and per-slot order quota.
 */
class DealerPickupConfigForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add('dealer_id', IntegerType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('prep_delay_minutes', IntegerType::class, [
                'label' => $this->translator->trans('Preparation delay (minutes)', [], Dealer::MESSAGE_DOMAIN),
                'label_attr' => ['for' => 'attr-dealer-pickup-prep-delay'],
                'required' => true,
                'constraints' => [new NotBlank(), new GreaterThanOrEqual(0)],
            ])
            ->add('orderable_days', IntegerType::class, [
                'label' => $this->translator->trans('Number of orderable days', [], Dealer::MESSAGE_DOMAIN),
                'label_attr' => ['for' => 'attr-dealer-pickup-orderable-days'],
                'required' => true,
                'constraints' => [new NotBlank(), new GreaterThanOrEqual(1)],
            ])
            ->add('slot_duration_minutes', IntegerType::class, [
                'label' => $this->translator->trans('Slot duration (minutes)', [], Dealer::MESSAGE_DOMAIN),
                'label_attr' => ['for' => 'attr-dealer-pickup-slot-duration'],
                'required' => true,
                'constraints' => [new NotBlank(), new GreaterThanOrEqual(1)],
            ])
            ->add('max_orders_per_slot', IntegerType::class, [
                'label' => $this->translator->trans('Max orders per slot (0 = unlimited)', [], Dealer::MESSAGE_DOMAIN),
                'label_attr' => ['for' => 'attr-dealer-pickup-max-orders'],
                'required' => true,
                'constraints' => [new NotBlank(), new GreaterThanOrEqual(0)],
            ]);
    }

    public static function getName(): string
    {
        return 'dealer-pickup-config';
    }
}
