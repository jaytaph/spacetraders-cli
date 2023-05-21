<?php

namespace App\Command\Fleet;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Response\Fleet\ListResponse;
use Jaytaph\Spacetraders\Api\Command\Fleet\ListCommand as ApiListCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected static $defaultName = 'fleet:list';

    protected function configure(): void
    {
        $this->setDescription('Display ships in fleet')
            ->setHelp('Display ships in fleet')
            ->setDefinition([
                new InputOption('page', 'p', InputOption::VALUE_OPTIONAL, 'Page number', 1),
                new InputOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit', 10),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new ApiListCommand(
            intval($input->getOption('page')),
            intval($input->getOption('limit'))
        );
        $response = $api->execute($command);
        $result = ListResponse::fromJson($response->data, $response->meta);

        $table = new Table($output);
        $table->setHeaders([
            'Symbol',
            'Registration',
            'Nav Status',
            'Nav Waypoint',
            'Fuel Capacity',
            'Fuel Consumed',
            'Fuel Current',
        ]);

        foreach ($result->ships as $ship) {
            $table->addRow([
                $ship->symbol,
                $ship->registrationName,
                $ship->nav->status,
                $ship->nav->waypoint,
                $ship->fuel->capacity,
                $ship->fuel->consumedAmount,
                $ship->fuel->current
            ]);
        }

        $table->render();


        return Command::SUCCESS;
    }
}
