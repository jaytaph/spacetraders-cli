<?php

namespace App\Command\Faction;

use Jaytaph\Spacetraders\Api\Command\Faction\DetailsCommand;
use Jaytaph\Spacetraders\Api\Response\Faction\DetailsResponse;
use App\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetailsCommand extends BaseCommand
{
    protected static $defaultName = 'faction:details';

    protected function configure(): void
    {
        $this->setDescription('Display faction details')
            ->setHelp('Display faction details')
            ->setDefinition([
                'faction' => new InputArgument('faction', InputArgument::REQUIRED, 'The faction symbol'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->getApi();
        $command = new DetailsCommand(strval($input->getArgument('faction')));
        $response = $api->execute($command);
        $result = DetailsResponse::fromJson($response->data);

        $output->writeln("Faction Details");
        $output->writeln("==============");
        $output->writeln("Name         : <info>" . $result->faction->name . "</info>");
        $output->writeln("Description  : <info>" . $result->faction->description . "</info>");
        $output->writeln("Symbol       : <info>" . $result->faction->symbol . "</info>");
        $output->writeln("Headquarters : <info>" . $result->faction->headquarters . "</info>");

        foreach ($result->faction->traits as $trait) {
            $output->writeln("Trait         : <info>" . $trait->symbol . "</info>");
            $output->writeln("  Name        : <info>" . $trait->name . "</info>");
            $output->writeln("  Description : <info>" . $trait->description . "</info>");
        }

        return Command::SUCCESS;
    }
}
