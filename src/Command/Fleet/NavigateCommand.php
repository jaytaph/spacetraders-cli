<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Response\Fleet\NavigateResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\NavigateCommand as ApiNavigateCommand;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NavigateCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:navigate';

    protected function configure(): void
    {
        $this->setDescription('Navigate ship to a different waypoint')
            ->setHelp('Navigate ship to a different waypoint')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
                new InputArgument('waypoint', InputArgument::REQUIRED, 'The waypoint symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiNavigateCommand(
            strval($input->getArgument('ship')),
            strval($input->getArgument('waypoint'))
        );
        $response = $api->execute($command);
        $result = NavigateResponse::fromJson($response->data);

        OutputTables::displayNavigation($output, $result->nav);
        OutputTables::displayFuel($output, $result->fuel);

        return Command::SUCCESS;
    }
}
