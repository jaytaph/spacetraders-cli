<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\ChartResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\ChartCommand as ApiChartCommand;
use App\Command\BaseCommand;
use App\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChartCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:chart';

    protected function configure(): void
    {
        $this->setDescription('Chart waypoints')
            ->setHelp('Chart waypoints')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiChartCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = ChartResponse::fromJson($response->data);

        $output->writeln('Chart details:');
        $output->writeln("---------------");
        $output->writeln("  Waypoint     : <info>" . $result->chart->waypointSymbol . "</info>\n");
        $output->writeln("  Submitted by : <info>" . $result->chart->submittedBy . "</info>\n");
        $output->writeln("  Submitted on : <info>" . $result->chart->submittedOn->format('Y-m-d H:i:s') . "</info>\n");

        OutputTables::displayWaypoints($output, [$result->waypoint]);

        return Command::SUCCESS;
    }
}
