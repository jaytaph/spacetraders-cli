<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\RefuelResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\RefuelCommand as ApiRefuelCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefuelCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:refuel';

    protected function configure(): void
    {
        $this->setDescription('Refuel ship')
            ->setHelp('Refuel ship')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiRefuelCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = RefuelResponse::fromJson($response->data);

        $output->writeln("Fuel is now: <info>{$result->fuel->current}/{$result->fuel->capacity}</info>");
        $output->writeln("You have <info>{$result->agent->credits}</info> credits left");

        return Command::SUCCESS;
    }
}
