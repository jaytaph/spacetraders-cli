<?php

namespace App\Command\Faction;

use Jaytaph\Spacetraders\Api\Api;
use Jaytaph\Spacetraders\Api\Response\Faction\ListResponse;
use Jaytaph\Spacetraders\Api\Command\Faction\ListCommand as ApiListCommand;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected static $defaultName = 'faction:list';

    protected function configure(): void
    {
        $this->setDescription('Display factions')
            ->setHelp('Display factions')
            ->setDefinition([
                new InputOption('page', 'p', InputArgument::OPTIONAL, 'Page number', 1),
                new InputOption('limit', 'l', InputArgument::OPTIONAL, 'Limit', 10),
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
        $table->setHeaders(['Symbol', 'Name', 'Description', 'Traits']);

        foreach ($result->factions as $faction) {
            $traits = [];
            foreach ($faction->traits as $trait) {
                $traits[] = $trait->name;
            }

            $table->addRow([
                $faction->symbol,
                $faction->name,
                wordwrap($faction->description),
                join(', ', $traits),
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
