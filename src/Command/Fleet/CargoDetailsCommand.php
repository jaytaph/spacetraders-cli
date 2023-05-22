<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Command\Fleet\CargoDetailsCommand;
use Jaytaph\Spacetraders\Api\Response\Fleet\CargoDetailsResponse;
use App\Command\BaseCommand;
use App\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CargoDetailsCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:cargo:details';

    protected function configure(): void
    {
        $this->setDescription('Display ship cargo details')
            ->setHelp('Display ship cargo details')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new CargoDetailsCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = CargoDetailsResponse::fromJson($response->data);

        $output->writeln("Ship Cargo Details");
        $output->writeln("=================");

        OutputTables::displayCargo($output, $result->cargo);

        return Command::SUCCESS;
    }
}
