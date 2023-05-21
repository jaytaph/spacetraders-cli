<?php

namespace App\Command\System\Waypoint;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Command\System\Waypoint\MarketCommand as ApiMarketCommand;
use Jaytaph\Spacetraders\Api\Response\System\Waypoint\MarketResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarketCommand extends BaseCommand
{
    protected static $defaultName = 'waypoint:market';

    protected const SHOW_EXPORTS = 1;
    protected const SHOW_IMPORTS = 2;
    protected const SHOW_EXCHANGE = 4;
    protected const SHOW_TRANSACTIONS = 8;
    protected const SHOW_TRADEGOODS = 16;

    protected function configure(): void
    {
        $this->setDescription('Display waypoint market details')
            ->setHelp('Display waypoint market details')
            ->setDefinition([
                'system' => new InputArgument('system', InputArgument::REQUIRED, 'The system symbol'),
                'waypoint' => new InputArgument('waypoint', InputArgument::REQUIRED, 'The waypoint symbol'),
                'exports' => new InputOption('exports', 'e', InputOption::VALUE_NONE, 'Display exports'),
                'imports' => new InputOption('imports', 'i', InputOption::VALUE_NONE, 'Display imports'),
                'exchange' => new InputOption('exchange', 'x', InputOption::VALUE_NONE, 'Display exchange'),
                'transactions' => new InputOption('transactions', 't', InputOption::VALUE_NONE, 'Display transactions'),
                'tradegoods' => new InputOption('tradegoods', 'g', InputOption::VALUE_NONE, 'Display tradegoods'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $show = 0;
        if ($input->getOption('exports')) {
            $show |= self::SHOW_EXPORTS;
        }
        if ($input->getOption('imports')) {
            $show |= self::SHOW_IMPORTS;
        }
        if ($input->getOption('exchange')) {
            $show |= self::SHOW_EXCHANGE;
        }
        if ($input->getOption('transactions')) {
            $show |= self::SHOW_TRANSACTIONS;
        }
        if ($input->getOption('tradegoods')) {
            $show |= self::SHOW_TRADEGOODS;
        }
        if ($show == 0) {
            $show = -1; // Show all
        }


        $api = $this->getApi();
        $command = new ApiMarketCommand(
            strval($input->getArgument('system')),
            strval($input->getArgument('waypoint')),
        );
        $response = $api->execute($command);
        $result = MarketResponse::fromJson($response->data);

        $output->writeln("Waypoint Market Details");
        $output->writeln("=======================");
        $output->writeln("Symbol  : <info>" . $result->market->symbol . "</info>");
        $output->writeln("");

        if ($show & self::SHOW_EXPORTS) {
            $this->printSection($output, 'Exports', $result->market->exports);
        }
        if ($show & self::SHOW_IMPORTS) {
            $this->printSection($output, 'Imports', $result->market->imports);
        }
        if ($show & self::SHOW_EXCHANGE) {
            $this->printSection($output, 'Exchange', $result->market->exchange);
        }
        if ($show & self::SHOW_TRANSACTIONS) {
            $this->printTransactions($output, $result->market->transactions);
        }
        if ($show & self::SHOW_TRADEGOODS) {
            $this->printTradeGoods($output, $result->market->tradegoods);
        }

        return Command::SUCCESS;
    }

    protected function printSection(OutputInterface $output, string $header, array $items): void
    {
        $output->writeln("{$header}:");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Name',
            'Description',
        ]);

        foreach ($items as $item) {
            $table->addRow([
                $item->symbol,
                $item->name,
                wordwrap($item->description, 50, "\n", true)
            ]);
        }

        $table->render();
        $output->writeln("");
    }

    protected function printTransactions(OutputInterface $output, array $transactions): void
    {
        $output->writeln("Transactions:");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Ship',
            'Trade',
            'Type',
            'Units',
            'Price Per Unit',
            'Total Price',
            'Timestamp'
        ]);
        $style = new TableStyle();
        $style->setPadType(STR_PAD_LEFT);
        $table->setColumnStyle(4, $style);
        $table->setColumnStyle(5, $style);
        $table->setColumnStyle(6, $style);

        foreach ($transactions as $transaction) {
            $table->addRow([
                $transaction->waypointSymbol,
                $transaction->shipSymbol,
                $transaction->tradeSymbol,
                $transaction->type,
                $transaction->units,
                $transaction->pricePerUnit,
                $transaction->totalPrice,
                $transaction->timestamp->format('Y-m-d H:i:s')
            ]);
        }

        $table->render();
        $output->writeln("");
    }

    protected function printTradeGoods(OutputInterface $output, array $tradegoods): void
    {
        $output->writeln("Tradegoods:");

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'tradeVolume',
            'Supply',
            'Purchase Price',
            'Sell Price',
        ]);
        $style = new TableStyle();
        $style->setPadType(STR_PAD_LEFT);
        $table->setColumnStyle(2, $style);
        $table->setColumnStyle(3, $style);
        $table->setColumnStyle(4, $style);
        $table->setColumnStyle(5, $style);

        foreach ($tradegoods as $tradegood) {
            $table->addRow([
                $tradegood->symbol,
                $tradegood->tradeVolume,
                $tradegood->supply,
                $tradegood->purchasePrice,
                $tradegood->sellPrice,
            ]);
        }

        $table->render();
        $output->writeln("");
    }
}
