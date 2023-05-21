<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Response\Fleet\RefineResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\RefineCommand as ApiRefineCommand;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefineCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:refine';

    protected function configure(): void
    {
        $this->setDescription('Refine materials')
            ->setHelp('Refine materials')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
                new InputArgument('produce', InputArgument::REQUIRED, 'The product to refine'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiRefineCommand(
            strval($input->getArgument('ship')),
            strval($input->getArgument('produce'))
        );
        $response = $api->execute($command);
        $result = RefineResponse::fromJson($response->data);

        $table = new Table($output);
        $table->setHeaders(['Product', 'Quantity']);
        foreach ($result->produced as $produced) {
            $table->addRow([$produced->tradeSymbol, $produced->units]);
        }
        $table->render();

        $table = new Table($output);
        $table->setHeaders(['Product', 'Quantity']);
        foreach ($result->consumed as $consumed) {
            $table->addRow([$consumed->tradeSymbol, $consumed->units]);
        }
        $table->render();


        return Command::SUCCESS;
    }
}
