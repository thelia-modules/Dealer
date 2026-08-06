<?php

declare(strict_types=1);

namespace Dealer\Commands;

use Dealer\Service\PickupSlotService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;

/**
 * Lists the available pickup slots for a dealer, applying its opening hours,
 * exceptional closures, preparation delay, orderable days and per-slot quota.
 */
class PickupSlotsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dealer:pickup:slots')
            ->setDescription('List the available pickup slots for a dealer')
            ->addArgument('dealer_id', InputArgument::REQUIRED, 'The dealer id')
            ->addArgument('from', InputArgument::OPTIONAL, 'Start date/time (any parseable date, defaults to now)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var PickupSlotService $service */
        $service = $this->getContainer()->get(PickupSlotService::class);

        $dealerId = (int) $input->getArgument('dealer_id');
        $from = $input->getArgument('from');
        $fromDate = $from !== null ? new \DateTimeImmutable($from) : null;

        $days = $service->getAvailableSlots($dealerId, $fromDate);

        if ($days === []) {
            $output->writeln('<comment>No available slot found.</comment>');

            return 0;
        }

        foreach ($days as $day) {
            $output->writeln(sprintf('<info>%s</info> (day %d)', $day['date'], $day['day']));
            foreach ($day['slots'] as $slot) {
                $remaining = $slot['remaining'] === null ? 'unlimited' : (string) $slot['remaining'];
                $output->writeln(sprintf('  - %s  (remaining: %s)', $slot['time'], $remaining));
            }
        }

        return 0;
    }
}
