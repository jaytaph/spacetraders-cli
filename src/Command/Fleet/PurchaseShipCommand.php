<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Command\Fleet\PurchaseShipCommand as ApiPurchaseCommand;
use Jaytaph\Spacetraders\Api\Response\Fleet\PurchaseResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurchaseShipCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:purchase:ship';

    protected function configure(): void
    {
        $this->setDescription('Purchase a ship')
            ->setHelp('Purchase a ship')
            ->setDefinition([
                new InputArgument('shiptype', InputArgument::REQUIRED, 'The ship type'),
                new InputArgument('waypoint', InputArgument::REQUIRED, 'Waypoint to purchase ship at'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiPurchaseCommand(
            strval($input->getArgument('shiptype')),
            strval($input->getArgument('waypoint'))
        );
        $response = $api->execute($command);
        $result = PurchaseResponse::fromJson($response->data);

        $output->writeln("Ship Purchase Details");
        $output->writeln("====================");
        $output->writeln("Agent:");
        $output->writeln("  Account ID   : <info>" . $result->agent->accountId . "</info>");
        $output->writeln("  Symbol       : <info>" . $result->agent->callsign . "</info>");
        $output->writeln("  Headquarters : <info>" . $result->agent->headquarters . "</info>");
        $output->writeln("  Credits      : <info>" . $result->agent->credits . "</info>");
        $output->writeln("Ship:");
        $output->writeln("  Ship         : <info>" . $result->ship->symbol . "</info>");
        $output->writeln("Transaction:");
        $output->writeln("  Waypoint     : <info>" . $result->transaction->waypointSymbol . "</info>");
        $output->writeln("  Ship         : <info>" . $result->transaction->shipSymbol . "</info>");
        $output->writeln("  Price        : <info>" . $result->transaction->price . "</info>");
        $output->writeln("  Agent        : <info>" . $result->transaction->agentSymbol . "</info>");
        $output->writeln("  Timestamp    : <info>" . $result->transaction->timestamp->format('Y-m-d H:i:s') . "</info>");

        return Command::SUCCESS;
    }
}
