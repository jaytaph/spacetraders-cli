<?php

namespace App\Command\System\Waypoint;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Response\System\Waypoint\ListResponse;
use Jaytaph\Spacetraders\Api\Command\System\Waypoint\ListCommand as ApiListCommand;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected static $defaultName = 'waypoint:list';

    protected function configure(): void
    {
        $this->setDescription('Display systems')
            ->setHelp('Display systems')
            ->setDefinition([
                new InputArgument('symbol', InputArgument::REQUIRED, 'Symbol of system to display'),
                new InputOption('page', 'p', InputArgument::OPTIONAL, 'Page number', 1),
                new InputOption('limit', 'l', InputArgument::OPTIONAL, 'Limit', 10),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiListCommand(
            $input->getArgument('symbol'),
            intval($input->getOption('page')),
            intval($input->getOption('limit'))
        );
        $response = $api->execute($command);
        $result = ListResponse::fromJson($response->data, $response->meta);

        OutputTables::displayWaypoints($output, $result->waypoints);

        return Command::SUCCESS;
    }
}
