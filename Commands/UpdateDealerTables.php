<?php


namespace Dealer\Commands;


use Dealer\Service\UpdateTablesService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;

class UpdateDealerTables extends ContainerAwareCommand
{
    /**
     * Sets the command name and description
     */
    protected function configure()
    {
        $this
            ->setName("dealer:update:tables")
            ->setDescription("Update dealer tables from dealer_tab to dealer")
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UpdateTablesService $service */
        $service = $this->getContainer()->get(UpdateTablesService::SERVICE_ID);
        $response = $service->updateTables();

        if (!is_array($response)) {
            $output->writeln($response);
        }

        $output->writeln('Update successful');
        if (!empty($response)) {
            $output->writeln('However these countries were not found and defaulted as France');
            foreach ($response as $countryNotFound) {
                $output->writeln($countryNotFound);
            }
        }
    }
}