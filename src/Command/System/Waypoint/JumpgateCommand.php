<?php

namespace App\Command\System\Waypoint;

use Jaytaph\Spacetraders\Api\Command\System\Waypoint\JumpgateCommand as ApiJumpgateCommand;
use Jaytaph\Spacetraders\Api\Response\System\Waypoint\JumpgateResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JumpgateCommand extends BaseCommand
{
    protected static $defaultName = 'waypoint:jumpgate';

    protected function configure(): void
    {
        $this->setDescription('Display waypoint jumpgate details')
            ->setHelp('Display waypoint jumpgate details')
            ->setDefinition([
                'system' => new InputArgument('system', InputArgument::REQUIRED, 'The system symbol'),
                'waypoint' => new InputArgument('waypoint', InputArgument::REQUIRED, 'The waypoint symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiJumpgateCommand(
            strval($input->getArgument('system')),
            strval($input->getArgument('waypoint')),
        );
        $response = $api->execute($command);
        $result = JumpgateResponse::fromJson($response->data);

        $output->writeln("Waypoint Jumpgate Details");
        $output->writeln("=======================");
        $output->writeln("Jump range : <info>" . $result->jumpgate->jumprange . "</info>");
        $output->writeln("Faction    : <info>" . $result->jumpgate->faction . "</info>");

        $this->printConnections($output, $result->jumpgate->connections);

        return Command::SUCCESS;
    }

    protected function printConnections(OutputInterface $output, array $connections): void
    {
        $output->writeln("Connections:");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Sector',
            'Type',
            'Faction',
            'Coord',
            'Distance',
        ]);
//        $style = new TableStyle();
//        $style->setPadType(STR_PAD_LEFT);
//        $table->setColumnStyle(2, $style);
//        $table->setColumnStyle(3, $style);
//        $table->setColumnStyle(4, $style);
//        $table->setColumnStyle(5, $style);

        foreach ($connections as $connection) {
            $table->addRow([
                $connection->symbol,
                $connection->sectorSymbol,
                $connection->type,
                $connection->factionSymbol,
                $connection->x . "," . $connection->y,
                $connection->distance,
            ]);
        }

        $table->render();
        $output->writeln("");
    }
}
