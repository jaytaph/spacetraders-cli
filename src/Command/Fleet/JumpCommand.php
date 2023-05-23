<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Command\Fleet\JumpCommand as ApiJumpgateCommand;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\Api\Response\System\Waypoint\JumpgateResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JumpCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:jump';

    protected function configure(): void
    {
        $this->setDescription('Display info about jumpgate')
            ->setHelp('Display info about jumpgate')
            ->setDefinition([
                new InputArgument('system', InputArgument::REQUIRED, 'The system symbol'),
                new InputArgument('waypoint', InputArgument::REQUIRED, 'The waypoint symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiJumpgateCommand(
            strval($input->getArgument('system')),
            strval($input->getArgument('waypoint'))
        );
        $response = $api->execute($command);
        $result = JumpgateResponse::fromJson($response->data);

        $output->writeln("Faction   : <info>{$result->jumpgate->faction}</info>\n");
        $output->writeln("Jumprange : <info>{$result->jumpgate->jumprange}</info>\n");

        $table = new Table($output);
        $table->setHeaders(['Symbol', 'Sector', 'Type', 'Faction', 'X', 'Y', 'Distance']);
        foreach ($result->jumpgate->connections as $connection) {
            $table->addRow([
                $connection->symbol,
                $connection->sectorSymbol,
                $connection->type,
                $connection->factionSymbol,
                $connection->x,
                $connection->y,
                $connection->distance,
            ]);
        }
        $table->render();

        return Command::SUCCESS;
    }
}
