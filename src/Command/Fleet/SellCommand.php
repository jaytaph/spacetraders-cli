<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\SellResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\SellCommand as ApiSellCommand;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SellCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:cargo:sell';

    protected function configure(): void
    {
        $this->setDescription('Sell cargo')
            ->setHelp('Sell cargo')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
                new InputArgument('symbol', InputArgument::REQUIRED, 'Symbol to sell'),
                new InputArgument('units', InputArgument::REQUIRED, 'nr of units'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiSellCommand(
            strval($input->getArgument('ship')),
            strval($input->getArgument('symbol')),
            intval($input->getArgument('units'))
        );
        $response = $api->execute($command);
        $result = SellResponse::fromJson($response->data);

        OutputTables::displayAgent($output, $result->agent);
        OUtputTables::displayCargo($output, $result->cargo);
        OUtputTables::displayTransactions($output, $result->transaction);

        return Command::SUCCESS;
    }
}
