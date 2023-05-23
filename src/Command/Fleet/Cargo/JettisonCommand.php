<?php

namespace App\Command\Fleet\Cargo;

use App\Command\BaseCommand;
use App\OutputTables;
use Jaytaph\Spacetraders\Api\Command\Fleet\Cargo\JettisonCommand as ApiJettisonCommand;
use Jaytaph\Spacetraders\Api\Response\Fleet\Cargo\JettisonResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JettisonCommand extends BaseCommand
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
        $command = new ApiJettisonCommand(
            strval($input->getArgument('ship')),
            strval($input->getArgument('symbol')),
            intval($input->getArgument('units'))
        );
        $response = $api->execute($command);
        $result = JettisonResponse::fromJson($response->data);

        OutputTables::displayCargo($output, $result->cargo);

        return Command::SUCCESS;
    }
}
