<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\DockResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\DockCommand as ApiDockCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:dock';

    protected function configure(): void
    {
        $this->setDescription('Move ship to dock')
            ->setHelp('Move ship to dock')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiDockCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = DockResponse::fromJson($response->data);

        $output->writeln("Ship is now docked on {$result->nav->waypoint}");

        return Command::SUCCESS;
    }
}
