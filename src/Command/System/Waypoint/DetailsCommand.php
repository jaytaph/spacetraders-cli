<?php

namespace App\Command\System\Waypoint;

use Jaytaph\Spacetraders\Api\Command\System\Waypoint\DetailCommand;
use Jaytaph\Spacetraders\Api\Response\System\Waypoint\DetailResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetailsCommand extends BaseCommand
{
    protected static $defaultName = 'waypoint:details';

    protected function configure(): void
    {
        $this->setDescription('Display waypoint details')
            ->setHelp('Display waypoint details')
            ->setDefinition([
                'system' => new InputArgument('system', InputArgument::REQUIRED, 'The system symbol'),
                'waypoint' => new InputArgument('waypoint', InputArgument::REQUIRED, 'The waypoint symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new DetailCommand(
            strval($input->getArgument('system')),
            strval($input->getArgument('waypoint')),
        );
        $response = $api->execute($command);
        $result = DetailResponse::fromJson($response->data);

        $output->writeln("Waypoint Details");
        $output->writeln("==============");
        $output->writeln("Symbol  : <info>" . $result->waypoint->symbol . "</info>");
        $output->writeln("System  : <info>" . $result->waypoint->systemSymbol . "</info>");
        $output->writeln("Type    : <info>" . $result->waypoint->type . "</info>");
        $output->writeln("Coords  : <info>" . $result->waypoint->x . "," . $result->waypoint->y . "</info>");
        $output->writeln("Faction : <info>" . $result->waypoint->faction . "</info>");
        if ($result->waypoint->chart) {
            $output->writeln(
                "Chart   : <info>" .
                $result->waypoint->chart->waypointSymbol .
                " (" . $result->waypoint->chart->submittedBy . ")</info>"
            );
        }

        $orbitals = [];
        foreach ($result->waypoint->orbitals as $orbital) {
            $orbitals[] = $orbital->symbol;
        }
        $output->writeln("Orbitals: <info>" . join(", ", $orbitals) . "</info>");

        $output->writeln("Traits:");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Name',
            'Description',
        ]);

        foreach ($result->waypoint->traits as $trait) {
            $table->addRow([
                $trait->symbol,
                $trait->name,
                wordwrap($trait->description, 50, "\n", true)
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
