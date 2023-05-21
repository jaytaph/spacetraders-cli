<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Command\Fleet\CargoDetailCommand;
use Jaytaph\Spacetraders\Api\Command\Fleet\DetailCommand;
use Jaytaph\Spacetraders\Api\Component\Cargo;
use Jaytaph\Spacetraders\Api\Component\Crew;
use Jaytaph\Spacetraders\Api\Component\Frame;
use Jaytaph\Spacetraders\Api\Component\Fuel;
use Jaytaph\Spacetraders\Api\Component\Nav;
use Jaytaph\Spacetraders\Api\Component\Reactor;
use Jaytaph\Spacetraders\Api\Component\Ship;
use Jaytaph\Spacetraders\Api\Response\Fleet\CargoDetailResponse;
use Jaytaph\Spacetraders\Api\Response\Fleet\DetailResponse;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $command = new CargoDetailCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = CargoDetailResponse::fromJson($response->data);

        $output->writeln("Ship Cargo Details");
        $output->writeln("=================");

        OutputTables::displayCargo($output, $result->cargo);

        return Command::SUCCESS;
    }
}
