<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Response\Fleet\OrbitResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\OrbitCommand as ApiOrbitCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrbitCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:orbit';

    protected function configure(): void
    {
        $this->setDescription('Move ship to orbit')
            ->setHelp('Move ship to orbit')
            ->setDefinition([
                new InputArgument('ship', InputArgument::REQUIRED, 'The ship symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiOrbitCommand(strval($input->getArgument('ship')));
        $response = $api->execute($command);
        $result = OrbitResponse::fromJson($response->data);

        $output->writeln("Ship is now in orbit around {$result->nav->waypoint}");

        return Command::SUCCESS;
    }
}
