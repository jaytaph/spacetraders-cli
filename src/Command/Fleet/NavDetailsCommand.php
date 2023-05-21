<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Command\Fleet\NavDetailCommand;
use Jaytaph\Spacetraders\Api\Response\Fleet\NavDetailResponse;
use App\Command\BaseCommand;
use App\OutputTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NavDetailsCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:details:nav';

    protected function configure(): void
    {
        $this->setDescription('Display ship nav details')
            ->setHelp('Display ship nav details')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new NavDetailCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = NavDetailResponse::fromJson($response->data);

        $output->writeln("Ship Nav Details");
        $output->writeln("=================");

        OutputTables::displayNavigation($output, $result->nav);

        return Command::SUCCESS;
    }
}
