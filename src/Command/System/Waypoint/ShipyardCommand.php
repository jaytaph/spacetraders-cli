<?php

namespace App\Command\System\Waypoint;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Command\System\Waypoint\ShipyardCommand as ApiShipyardCommand;
use Jaytaph\Spacetraders\Api\Response\System\Waypoint\ShipyardResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShipyardCommand extends BaseCommand
{
    protected static $defaultName = 'waypoint:shipyard';

    protected function configure(): void
    {
        $this->setDescription('Display waypoint shipyard details')
            ->setHelp('Display waypoint shipyard details')
            ->setDefinition([
                'system' => new InputArgument('system', InputArgument::REQUIRED, 'The system symbol'),
                'waypoint' => new InputArgument('waypoint', InputArgument::REQUIRED, 'The waypoint symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiShipyardCommand(
            strval($input->getArgument('system')),
            strval($input->getArgument('waypoint')),
        );
        $response = $api->execute($command);
        $result = ShipyardResponse::fromJson($response->data);

        $output->writeln("Waypoint Shipyard Details");
        $output->writeln("=======================");
        $output->writeln("Symbol    : <info>" . $result->shipyard->symbol . "</info>");

        $types = [];
        foreach ($result->shipyard->shipTypes as $type) {
            $types[] = $type->type;
        }
        $output->writeln("Ship Types : <info>" . implode(', ', $types) . "</info>");

        $this->printTransactions($output, $result->shipyard->transactions);
        $this->printShips($output, $result->shipyard->shipPurchases);

        return Command::SUCCESS;
    }

    protected function printTransactions(OutputInterface $output, array $transactions): void
    {
        $output->writeln("Transactions:");

        $table = new Table($output);
        $table->setHeaders([
            'Waypoint',
            'Ship',
            'Price',
            'Agent',
            'Timestamp'
        ]);
        $style = new TableStyle();
        $style->setPadType(STR_PAD_LEFT);
        $table->setColumnStyle(2, $style);

        foreach ($transactions as $transaction) {
            $table->addRow([
                $transaction->waypointSymbol,
                $transaction->shipSymbol,
                $transaction->price,
                $transaction->agentSymbol,
                $transaction->timestamp->format('Y-m-d H:i:s'),
            ]);
        }

        $table->render();
        $output->writeln("");
    }

    protected function printShips(OutputInterface $output, array $ships): void
    {
        $output->writeln("Ships:");

        $table = new Table($output);
        $table->setHeaders([
            'Type',
            'Name',
            'Price',
            'Frame',
            'Reactor',
            'Engine',
            'Modules',
            'Mounts',
        ]);
//        $style = new TableStyle();
//        $style->setPadType(STR_PAD_LEFT);
//        $table->setColumnStyle(2, $style);
//        $table->setColumnStyle(3, $style);
//        $table->setColumnStyle(4, $style);
//        $table->setColumnStyle(5, $style);

        foreach ($ships as $ship) {
            $modules = [];
            foreach ($ship->modules as $module) {
                $modules[] = $module->name;
            }

            $mounts = [];
            foreach ($ship->mounts as $mount) {
                $mounts[] = $mount->name;
            }

            $table->addRow([
                $ship->type,
                $ship->name,
                $ship->purchasePrice,
                $ship->frame->name,
                $ship->reactor->name,
                $ship->engine->name,
                join(", ", $modules),
                join(", ", $mounts),
            ]);
        }

        $table->setVertical(true);
        $table->render();
        $output->writeln("");
    }
}
