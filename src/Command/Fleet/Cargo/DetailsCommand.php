<?php

namespace App\Command\Fleet\Cargo;

use App\Command\BaseCommand;
use App\OutputTables;
use Jaytaph\Spacetraders\Api\Command\Fleet\Cargo\DetailsCommand as ApiDetailsCommand;
use Jaytaph\Spacetraders\Api\Response\Fleet\Cargo\DetailsResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetailsCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:cargo:details';

    protected function configure(): void
    {
        $this->setDescription('Display ship cargo details')
            ->setHelp('Display ship cargo details')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiDetailsCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = DetailsResponse::fromJson($response->data);

        $output->writeln("Ship Cargo Details");
        $output->writeln("=================");

        OutputTables::displayCargo($output, $result->cargo);

        return Command::SUCCESS;
    }
}
