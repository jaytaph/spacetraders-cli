<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\JettisonCargoResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\JettisonCargoCommand as ApiJettisonCargoCommand;
use App\Command\BaseCommand;
use Jaytaph\Spacetraders\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JettisonCargoCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:cargo:jettison';

    protected function configure(): void
    {
        $this->setDescription('Jettison some cargo')
            ->setHelp('Jettison some cargo')
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
        $command = new ApiJettisonCargoCommand(
            strval($input->getArgument('ship')),
            strval($input->getArgument('symbol')),
            intval($input->getArgument('units'))
        );
        $response = $api->execute($command);
        $result = JettisonCargoResponse::fromJson($response->data);

        OutputTables::displayCargo($output, $result->cargo);

        return Command::SUCCESS;
    }
}
